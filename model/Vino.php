<?php

  //model/Vino.php
  
  require_once __DIR__ . '/VinoHelper.php';

  // Costante: definisce il valore di default (in Locale) se manca nel Database
  define('DEFAULT_SALVATAGGIO_IMMAGINE', '0');


  /* INIZIO - FUNZIONI RICHIAMATE DAI CONTROLLER */

    /**
     * Inserimento nuovo Vino: uploadFile + insert nel DB
     * Richiamata da aggiungiVinoController
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

    /*
     * Richiamata da modificaVinoController
     *
    */
    function modificaVino($conn, $id_vino, $id_utente, $nome, $cantina, $anno, $prezzo, $file_copertina_php, $galleria_vino_files_php) {

        // --- IL CONTROLLO DI SICUREZZA ---
        // Verifichiamo PRIMA DI TUTTO se l'utente ha il permesso di cancellare QUESTO vino
        if (!isVinoDellUtenteDB($conn, $id_vino, $id_utente)) {
            // Se non è suo, interrompiamo tutto
            return false;
        }

        // 1. Inizio Transazione
        $conn->autocommit(FALSE);
        $conn->begin_transaction();

        try {
            // 2. Aggiornamento dati vino
            $sql = "UPDATE vini SET nome_vino = ?, cantina = ?, anno = ?, prezzo = ? 
                        WHERE id = ? AND user_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssidii", $nome, $cantina, $anno, $prezzo, $id_vino, $id_utente);
            $successo = $stmt->execute();

            if (!$successo)
                return false;

            $tipo_url = getTipoSalvataggioFromSessione($conn);

            // 3. Aggiorna Nuova Copertina (se l'utente ha caricato un file)
            if ($file_copertina_php && $file_copertina_php['error'] === UPLOAD_ERR_OK) {

                //3.1 aggiorno sul server
                uploadFile($tipo_url, $file_copertina_php, $id_utente, $id_vino, true);

                $nuovo_path = uploadFile($tipo_url, $file_copertina_php, $id_utente, $id_vino, true);

                if ($nuovo_path) {
                    //3.2 aggiorno path sul DB (l'estensione potrebbe essere cambiata (da .jpg a .png))
                    $sql_foto = "UPDATE vini SET urlCopertina = ? WHERE id = ?";
                    $stmt_f = $conn->prepare($sql_foto);
                    $stmt_f->bind_param("si", $nuovo_path, $id_vino);
                    $stmt_f->execute();
                }
            }

            // 4. Gestione Galleria
            if (!empty($galleria_vino_files_php['name'][0])) {
                $successoGalleria = aggiungiGalleriaVino($conn, $id_utente, $id_vino, $galleria_vino_files_php, $tipo_url);

                if (!$successoGalleria) {
                    throw new Exception("Errore durante il caricamento della galleria");
                }
            }
            // 5. Se siamo arrivati qui, tutto è andato bene
            $conn->commit();
            $conn->autocommit(TRUE);
            return $id_vino;

        } catch (Exception $e) {
            // 6. Qualcosa è fallito: annulliamo tutte le modifiche al DB
            $conn->rollback();
            $conn->autocommit(TRUE);

            // Log dell'errore (opzionale)
            error_log("Errore modificaVino: " . $e->getMessage());
            $_SESSION['errore'] = "DEBUG: " . $e->getMessage();

            return false;
        }
    }

    /**
     * Cancellazione Vino: eliminaFile + deleteVino DB
     * Richiamata da cancellaVinoController
     */
    function cancellaVino($conn, $id_vino, $id_utente){

        // --- IL CONTROLLO DI SICUREZZA ---
        // Verifichiamo PRIMA DI TUTTO se l'utente ha il permesso di cancellare QUESTO vino
        if (!isVinoDellUtenteDB($conn, $id_vino, $id_utente)) {
            // Se non è suo, interrompiamo tutto
            return false;
        }

        //1. ELIMINO COPERTINA
        $url_param = getURLImmagineCopertinaDB($conn, $id_vino, $id_utente);

        // Controllo che l'immagine del vino esista e la cancello
        if ($url_param) {

            $urlCopertina = $url_param['urlCopertina'];
            $tipo_url = $url_param['tipo_url'];

            // Elimino fisicamente immagine copertina
            eliminaFile($tipo_url,$urlCopertina);
        }

        //2. ELIMINO GALLERIA dal server e dal DB
        eliminaGalleriaVino($conn, $id_vino);


        //3. ELIMINO vino dal DB
        return deleteVinoDB($conn, $id_vino, $id_utente);
    }

    /**
     * Recupero URL galleria immagini vino
     * Richiamata da ListaViniController
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
     * Richiamata da ListaViniController
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
     * Verifica se quel vino è di quell'utente
     * Richiamata da ModificaVinoController
     */
    function isVinoDellUtenteDB($conn, $id_vino, $id_utente) {
        $sql = "SELECT count(*) as totale
            FROM vini_utenti 
            WHERE id = ? AND id_utente = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_vino, $id_utente);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Restituisce true se il conteggio è maggiore di 0
        return ($row['totale'] > 0);
    }

    /**
     * Recupera tutti i vini e nome utenti
     *   Richiamata da index
     */
    function getListaViniDB($conn) {
        $sql = "SELECT v.*, u.username 
                FROM vini_utenti v 
                JOIN utenti u ON v.id_utente = u.id 
                ORDER BY v.created_at DESC";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }


  /* FINE - FUNZIONI RICHIAMATE DAI CONTROLLER */


  /* INIZIO - FUNZIONI INTERNE ACCESSO AL DB */

    /**
     * Salva i dati del vino nel Database
     * Richiamata da Vino.aggiungiVino()
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
     * Richiamata da Vino.aggiungiVino()
     */
    function insertCopertinaVinoDB($conn, $urlCopertina, $tipo_url, $id_utente, $id_vino){
        $sql = "UPDATE vini_utenti SET urlCopertina = ?, tipo_url = ? WHERE id = ? AND id_utente = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ssii", $urlCopertina, $tipo_url, $id_vino, $id_utente);

        return $stmt->execute();
    }

    /**
     * Salva galleria immgini copertina del vino nel Database
     * Richiamata da Vino.aggiungiVino()
     */
    function insertGalleriaVinoDB($conn, $url, $tipo_url, $id_vino){
        $sql = "INSERT INTO immagini_vini (url, tipo_url, id_vino) 
                  VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $url, $tipo_url, $id_vino);

        return $stmt->execute();
    }

    /**
     * Cancella il vino dal DB
     * Richiamata da Vino.cancellaVino()
     */
    function deleteVinoDB($conn, $id_vino, $id_utente) {

        $sql_delete = "DELETE FROM vini_utenti WHERE id = ? AND id_utente = ?";
        $stmt_del = $conn->prepare($sql_delete);
        $stmt_del->bind_param("ii", $id_vino, $id_utente);

        return $stmt_del->execute();
    }

    /**
     * Cancella galleria vino dal DB
     * Richiamata da Vino.cancellaVino()
     */
    function deleteGalleriaVinoDB($conn, $id_vino) {

        $sql_delete = "DELETE FROM immagini_vini WHERE id_vino = ?";
        $stmt_del = $conn->prepare($sql_delete);
        $stmt_del->bind_param("i", $id_vino);

        return $stmt_del->execute();
    }

    /**
     * Recupero URL immagine copertina vino
     * Richiamata da Vino.cancellaVino
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
     * Legge dal DB per ogni immagine del vino URL e Tipo_url
     * Richiamata da Vino.eliminaGalleriaVino())
     */
    function getGalleriaVinoDB($conn, $id_vino) {
        $sql = "SELECT url_foto, tipo_url FROM immagini_vini WHERE id_vino = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_vino);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

  /* FINE - FUNZIONI INTERNE ACCESSO AL DB */

  /* INIZIO - FUNZIONI INTERNE DI UTILITA' */

    /**
     * Gestisce l'upload e il salvataggio di più immagini per un vino esistente
     * Salva sia il file sul server che l'url sul db
     * Richiamata da Vino.aggiungiVino()
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
     * Gestisce la cancellazione di più immagini per un vino esistente
     * Elimina sia il file dal server che l'url sul db
     * Richiamata da Vino.cancellaVino()
     */
    function eliminaGalleriaVino($conn, $id_vino) {
        // 1. Recuperiamo tutti i file della galleria associati a questo vino
        // Assumiamo che tu abbia una funzione che restituisce un array di record (url e tipo_url)
        $immagini = getGalleriaVinoDB($conn, $id_vino);

        if (!$immagini || empty($immagini)) {
            return true; // Nulla da eliminare, consideriamolo un successo
        }

        $esito = true;

        // 2. Ciclo per l'eliminazione FISICA dei file
        foreach ($immagini as $foto) {
            $url = $foto['url'];
            $tipo_url = $foto['tipo_url'];

            if (!eliminaFile($tipo_url, $url)) {
                // Se un file non viene eliminato, segniamo l'errore ma continuiamo con gli altri
                $esito = false;
            }
        }

        // 3. Eliminazione di TUTTI i record della galleria dal DB in un colpo solo
        if (!deleteGalleriaVinoDB($conn, $id_vino)) {
            $esito = false;
        }

        return $esito;
    }

    /* FINE - FUNZIONI INTERNE DI UTILITA' */



