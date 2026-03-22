<?php
    session_start();
    // CARICAMENTO MODELLI E CONNESSIONE (Solo qui!)
    require_once '../model/connessione.php';
    require_once '../model/Vino.php';

    // 1. Controllo Sicurezza
    if (!isset($_SESSION['id_utente'])) {
        header("Location: login.php");
        exit();
    }

    $id_utente = $_SESSION['id_utente'];

    // --- LOGICA DI CARICAMENTO (GET) ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $id_vino = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (!$id_vino) {
            header("Location: listaViniUtente.php");
            exit();
        }

        // Recupero i dati: la variabile $vino sarà "vista" dalla View inclusa sotto
        $vino = getVinoByIdDB($conn, $id_vino, $id_utente);

        if (!$vino) {
            $_SESSION['errore'] = "Vino non trovato o permessi insufficienti.";
            header("Location: listaViniUtente.php");
            exit();
        }

        // Richiamo la View: non serve ri-caricare connessione o modelli nella View
        include '../view/modificaVinoView.php';
        exit();
    }

    // --- LOGICA DI SALVATAGGIO (POST) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_vino = filter_input(INPUT_POST, 'id_vino', FILTER_SANITIZE_NUMBER_INT);
        $nome    = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $cantina = filter_input(INPUT_POST, 'cantina', FILTER_SANITIZE_SPECIAL_CHARS);
        $anno    = filter_input(INPUT_POST, 'anno', FILTER_SANITIZE_NUMBER_INT);
        $prezzo  = filter_input(INPUT_POST, 'prezzo', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        $file_copertina = (isset($_FILES['copertina']) && $_FILES['copertina']['error'] !== UPLOAD_ERR_NO_FILE) ? $_FILES['copertina'] : null;
        $files_galleria = (isset($_FILES['galleria']) && $_FILES['galleria']['error'][0] !== UPLOAD_ERR_NO_FILE) ? $_FILES['galleria'] : null;

        $esito = modificaVino($conn, $id_vino, $id_utente, $nome, $cantina, $anno, $prezzo, $file_copertina, $files_galleria);

        if ($esito) {
            header("Location: ../view/dettaglioVino.php?id=$id_vino&msg=updated");
        } else {
            $_SESSION['errore'] = "Errore durante il salvataggio.";
            header("Location: modificaVinoController.php?id=$id_vino");
        }
        exit();
    }