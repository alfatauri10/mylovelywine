<?php
// VinoHelper.php

// Funzioni di utilità per Vino.php
/*
 * gestione file 
 * sessioni
*/

/**
   * Persistenza in sessione della configurazione TIPO SALVATAGGIO IMMAGINI
   */
  function getTipoSalvataggioFromSessione($conn) {
  	  //se non è presente in sessione, lo legge da DB e lo salva
      if (!isset($_SESSION['tipo_salvataggio'])) {
          $_SESSION['tipo_salvataggio'] = getTipoSalvataggioImmagine($conn);
      }
      return $_SESSION['tipo_salvataggio'];
  }
  
   /**
   * Elimina fisicamente il file 
   */
  function eliminaFile($tipo_url, $percorso_file) {
  	if ($tipo_url == '0') {
          // Caricamento Locale con URL=PATH RELATIVO
          return eliminaFileInLocale($percorso_file);
      } else {
          // Qui andrà la logica per salvataggio in cloud in futuro
          return eliminaFileInDrive($percorso_file);
      }
      return false;
  }
 
   /**
   * Elimina fisicamente il file dal server
   */
  function eliminaFileInLocale($percorso_file) {
    if (empty($percorso_file)) {
        return false;
    }

	$file_fisico = "../" . $percorso_file;
  	if (file_exists($file_fisico)) {
    	return unlink($file_fisico);
  	}
              
    return false;
}

  /**
   * TODO: Elimina fisicamente il file dal cloud
   */
  function eliminaFileInDrive($percorso_file) {
 	//TODO   
 }

  /**
   * Salva il file caricato 
   */
 function uploadFile($tipo_url, $file_php, $id_utente, $id_vino, $isCopertina) {
      if ($tipo_url == '0') {
          // Caricamento Locale con URL=PATH RELATIVO
          $url = uploadFileInLocale($file_php, $id_utente, $id_vino, $isCopertina);
      } else {
          // Qui andrà la logica per salvataggio in cloud in futuro
          $url = uploadFileInDrive($file_php, $id_utente, $id_vino, $isCopertina);
      }
      return $url;
   }
      
  /**
   * Salva il file nella cartella dell'utente sul server
   */
  function uploadFileInLocale($file_php, $id_utente, $id_vino, $isCopertina) {
  
      // Se non c'è il file o c'è un errore di caricamento, esci subito
      if (!isset($file_php) || $file_php['error'] != UPLOAD_ERR_OK) {
          return null;
      }
      
      // Percorso della cartella utente
      $cartella = "../uploads/vini/user_" . $id_utente . "/vino_" . $id_vino . "/";

      // Crea la cartella se non esiste
      // 0777 imposta i permessi di lettura e scrittura
      // true crea le sottocartelle
      if (!is_dir($cartella)) {
          mkdir($cartella, 0777, true); 
      }

      // Crea un nome file unico con la sua estensione originale
      $estensione = pathinfo($file_php['name'], PATHINFO_EXTENSION);
      $nome_file = ($isCopertina ? "copertina." : uniqid() . ".") . $estensione;
      $destinazione = $cartella . $nome_file;

      // Sposta il file dal deposito temporaneo (dove PHP salva i file uploadati) alla cartella finale
      if (move_uploaded_file($file_php['tmp_name'], $destinazione)) {
          return "uploads/vini/user_" . $id_utente . "/vino_" . $id_vino . "/" . $nome_file;
      }

      return null;
  }
  
  /* TODO: Salva il file caricato nella cartella dell'utente sul cloud */
  function uploadFileInDrive($file_php, $id_utente, $id_vino, $isCopertina) {
  	//TODO
  }
