<?php

  //model/Vino.php
  
  require_once __DIR__ . '/VinoHelper.php';

  // Costante: definisce il valore di default (in Locale) se manca nel Database
  define('DEFAULT_SALVATAGGIO_IMMAGINE', '0');

  /**
   * Configurazione TIPO SALVATAGGIO IMMAGINI sul DB:
   * Legge dal DB se salvare in Locale (0) o Drive (1)
   */
  function getTipoSalvataggioImmagine($conn) {
      $sql = "SELECT valore FROM configurazioni WHERE chiave = 'sorgente_immagini'";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $res = $stmt->get_result()->fetch_assoc();

      // Ritorna il valore del DB o la costante se il DB è vuoto
      return $res ? $res['valore'] : DEFAULT_SALVATAGGIO_IMMAGINE;
  }
  


  /**
   * Inserimento nuovo Vino: uploadFile + insert nel DB
   * e' questa la funzione chiamata dal controller
   */

  function aggiungiVino($conn, $id_utente, $nome_vino, $cantina, $anno, $prezzo, $copertina_vino_file_php, $galleria_vino_files_php) {

      /* RENDO LE INSERT NELLE TABELLE Vini_Utenti e Immagini_Vini TRANSAZIONALI:
       *  Se fallisce una delle due --> faccio il rollback anche dell'altra
       *  --> o  tutto o niente
      */

    // 1. Inizio Transazione
    $conn->autocommit(FALSE);
    $conn->begin_transaction();

    try {
        // 2. Determina dove salvare (Locale/Cloud)
        $tipo_url = getTipoSalvataggioFromSessione($conn);

        // 3. Inserimento record base
        $id_vino = insertVinoDB($conn, $id_utente, $nome_vino, $cantina, $anno, $prezzo);
        
        if (!$id_vino){ 
        	throw new Exception("Errore inserimento base vino");
        }

        // 4. Gestione Copertina (se presente)
        if (!empty($copertina_vino_file_php['name'])) {
            $urlCopertina = uploadFile($tipo_url, $copertina_vino_file_php, $id_utente, $id_vino, true);
           
            if ($urlCopertina == null) {
            	throw new Exception("Errore upload copertina");
			}
           
           $checkCopertina = insertCopertinaVinoDB($conn, $urlCopertina, $tipo_url, $id_utente, $id_vino);
           
           if (!$checkCopertina) {
           		throw new Exception("Errore salvataggio URL copertina nel DB");
           }
        }

        // 5. Gestione Galleria (se presente)
        if (!empty($galleria_vino_files_php['name'][0])) {
            $successoGalleria = aggiungiGalleriaVino($conn, $id_utente, $id_vino, $galleria_vino_files_php, $tipo_url);
           
            if (!$successoGalleria) {
            	throw new Exception("Errore durante il caricamento della galleria");
            }
        }

        // 6. Se siamo arrivati qui, tutto è andato bene
        $conn->commit();
        $conn->autocommit(TRUE);
        return $id_vino;

    } catch (Exception $e) {
        // 7. Qualcosa è fallito: annulliamo tutte le modifiche al DB
        $conn->rollback();
        $conn->autocommit(TRUE);
        
        // Log dell'errore (opzionale)
        error_log("Errore aggiungiVino: " . $e->getMessage());
        $_SESSION['errore'] = "DEBUG: " . $e->getMessage();
        
        return false; 
    }
  }
  
   /**
   * Gestisce l'upload e il salvataggio di più immagini per un vino esistente
   */
  function aggiungiGalleriaVino($conn, $id_utente, $id_vino, $galleria_files, $tipo_url) {
  
    if (!$galleria_files || !is_array($galleria_files['name'])){ 
    	return false;
    }
	
    $esito = true;
    
    foreach ($galleria_files['name'] as $key => $val) {
        if ($galleria_files['error'][$key] === UPLOAD_ERR_OK) {
            
            // Normalizziamo l'array per uploadFile
            $file_tmp = [
                'name'     => $galleria_files['name'][$key],
                'type'     => $galleria_files['type'][$key],
                'tmp_name' => $galleria_files['tmp_name'][$key],
                'error'    => $galleria_files['error'][$key],
                'size'     => $galleria_files['size'][$key]
            ];

			// Upload File galleria
            $urlFoto = uploadFile($tipo_url, $file_tmp, $id_utente, $id_vino, false);
            
            // Insert File galleria sul DB
            if ($urlFoto) {
                insertGalleriaVinoDB($conn, $urlFoto, $tipo_url, $id_vino);
            } else {
                $esito = false;
            }
        }
    }
    return $esito;
   }

  /**
   * Cancellazione Vino: eliminaFile + deleteVino DB
   * e' questa la funzione chiamata dal controller
   */
  function cancellazioneVino($conn, $id_vino, $id_utente){
  
  	//1. ELIMINO COPERTINA
  	$url_param = getURLImmagineCopertinaDB($conn, $id_vino, $id_utente);
    
    // Controllo che l'immagine del vino esista e la cancello
    if ($url_param) {
        
        $urlCopertina = $url_param['urlCopertina'];
        $tipo_url = $url_param['tipo_url'];

        // Elimino fisicamente immagine copertina
        eliminaFile($tipo_url,$urlCopertina);
    }
    
    //2. ELIMINO GALLERIA dal server
    
    //3. ELIMINO GALLERIA dal DB
    
    //4. ELIMINO dal DB
    return deleteVinoDB($conn, $id_vino, $id_utente);
  }
  

  /**
   * Salva i dati del vino nel Database
   */
  function insertVinoDB($conn, $id_utente, $nome_vino, $cantina, $anno, $prezzo) {
  
      $sql = "INSERT INTO vini_utenti (id_utente, nome_vino, cantina, anno, prezzo) 
              VALUES (?, ?, ?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("issid", $id_utente, $nome_vino, $cantina, $anno, $prezzo);

      $stmt->execute();
      
      $id_vino = $conn->insert_id;
      
      return $id_vino;
  }
  
  /**
   * Salva immgine copertina del vino nel Database
   */
  function insertCopertinaVinoDB($conn, $urlCopertina, $tipo_url, $id_utente, $id_vino){
  	$sql = "UPDATE vini_utenti SET urlCopertina = ?, tipo_url = ? WHERE id = ? AND id_utente = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssii", $urlCopertina, $tipo_url, $id_vino, $id_utente);

    return $stmt->execute();
  }
  
  function insertGalleriaVinoDB($conn, $url, $tipo_url, $id_vino){
  	  $sql = "INSERT INTO immagini_vini (url, tipo_url, id_vino) 
              VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ssi", $url, $tipo_url, $id_vino);

      return $stmt->execute();
  }
  
  /**
   * Cancella il vino dal DB 
   */
  function deleteVinoDB($conn, $id_vino, $id_utente) {
     
      $sql_delete = "DELETE FROM vini_utenti WHERE id = ? AND id_utente = ?";
      $stmt_del = $conn->prepare($sql_delete);
      $stmt_del->bind_param("ii", $id_vino, $id_utente);

      return $stmt_del->execute();
  }
  
  /**
   * Cancella galleria vino dal DB 
   */
  function deleteGalleriaVinoDB($conn, $id_vino) {
     
      $sql_delete = "DELETE FROM immagini_vini WHERE id_vino = ?";
      $stmt_del = $conn->prepare($sql_delete);
      $stmt_del->bind_param("i", $id_vino);

      return $stmt_del->execute();
  }
  
  /**
   * Recupero URL immagine copertina vino
   */
  function getURLImmagineCopertinaDB($conn, $id_vino, $id_utente){
  	$sql = "SELECT urlCopertina, tipo_url FROM vini_utenti WHERE id = ? AND id_utente = ?";
    $stmt_info = $conn->prepare($sql);
    $stmt_info->bind_param("ii", $id_vino, $id_utente);
    $stmt_info->execute();
    $url_param = $stmt_info->get_result()->fetch_assoc();

    // Restituisci l'intero array (che contiene sia 'urlCopertina' che 'tipo_url')
    return $url_param;
  }

  /**
   * Recupero URL galleria immagini vino
   */
  function getURLGalleriaImmaginiDB($conn, $id_vino){
    $sql = "SELECT url, tipo_url FROM immagini_vini WHERE id_vino = ?";
    $stmt_info = $conn->prepare($sql);
    $stmt_info->bind_param("i", $id_vino);
    $stmt_info->execute();
    // Usa fetch_all per ottenere tutte le foto, non solo una
    return $stmt_info->get_result()->fetch_all(MYSQLI_ASSOC);
  }
  
  /**
   * Recupera la lista dei vini dell'utente
   */
  function getListaViniByIdUtenteDB($conn, $id_utente) {
  
      $sql = "SELECT *
              FROM vini_utenti 
              WHERE id_utente = ? 
              ORDER BY created_at DESC";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $id_utente);
      $stmt->execute();
      $result = $stmt->get_result();

      $vini = [];
      
      while ($row = $result->fetch_assoc()) {
          $vini[] = $row;
      }

      return $vini;
  }  
  

  /**
   * Recupera un singolo vino per ID con i campi espliciti
  */
  function getVinoByIdDB($conn, $id_vino, $id_utente) {

      $sql = "SELECT *
              FROM vini_utenti 
              WHERE id = ? AND id_utente = ?";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ii", $id_vino, $id_utente);
      $stmt->execute();

      // Restituisce il vino trovato o null
      return $stmt->get_result()->fetch_assoc();
  }
  
   /**
   * Recupera tutti i vini e nome utenti
  */
  function getListaViniDB($conn) {
    $sql = "SELECT v.*, u.username 
            FROM vini_utenti v 
            JOIN utenti u ON v.id_utente = u.id 
            ORDER BY v.created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}
   
  

 
