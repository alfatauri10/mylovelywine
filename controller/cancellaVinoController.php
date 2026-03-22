<?php
  // controller/aggiungiVinoController.php
  require_once '../include/connessioneDB.php';
  require_once '../model/Vino.php';

   // session_start() va SEMPRE all'inizio, prima di ogni logica
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  // Recuperiamo l'ID dell'utente dalla sessione
  $id_utente = $_SESSION['user_id'] ?? null;

  if (!$id_utente) {
      header("Location: login.php");
      exit();
  }
  
  $id_vino = $_GET['id'] ?? null;

  if ($id_vino) {
      // Vino Model si occupa di cancellare sia il file che la riga DB
      $res = cancellazioneVino($conn, $id_vino, $id_utente);

      if ($res) {
          $_SESSION['messaggio'] = "Vino rimosso correttamente.";
          header("Location: ../view/listaVini.php?msg=deleted");
      } else {
		$_SESSION['errore'] = "Impossibile eliminare il vino.";
    	header("Location: ../view/listaVini.php?msg=error");
    	exit();      
      }
  }

 // Se qualcuno prova ad accedere al controller senza POST (es. via URL), lo rimandiamo alla lista
  header("Location: ../view/listaVini.php");
  exit();