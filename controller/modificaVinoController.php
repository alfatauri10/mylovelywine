<?php
    session_start();
    require_once '../model/connessioneDB.php';
    require_once '../model/Vino.php';

    // 1. Controllo Sicurezza Sessione
    if (!isset($_SESSION['id_utente'])) {
        header("Location: ../view/login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_utente = $_SESSION['id_utente'];
        $id_vino   = filter_input(INPUT_POST, 'id_vino', FILTER_SANITIZE_NUMBER_INT);

        // 2. Recupero e Sanificazione dati testuali
        $nome    = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $cantina = filter_input(INPUT_POST, 'cantina', FILTER_SANITIZE_SPECIAL_CHARS);
        $anno    = filter_input(INPUT_POST, 'anno', FILTER_SANITIZE_NUMBER_INT);
        $prezzo  = filter_input(INPUT_POST, 'prezzo', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // 3. Recupero File e Array dalla View

        // Copertina singola
        $file_copertina = (isset($_FILES['copertina']) && $_FILES['copertina']['error'] !== UPLOAD_ERR_NO_FILE) ? $_FILES['copertina'] : null;

        // Nuove foto per la galleria (campo multiplo)
        $nuova_galleria = (isset($_FILES['nuova_galleria']) && $_FILES['nuova_galleria']['error'][0] !== UPLOAD_ERR_NO_FILE) ? $_FILES['nuova_galleria'] : null;

        // Foto della galleria da ELIMINARE (array di ID dalle checkbox)
        $foto_da_eliminare = $_POST['elimina_foto'] ?? [];

        // Foto della galleria da SOSTITUIRE (array di file indicizzati per ID)
        $foto_da_sostituire = (isset($_FILES['sostituisci_foto'])) ? $_FILES['sostituisci_foto'] : [];

        // 4. Chiamata alla UNICA funzione del Model
        // Questa funzione gestisce internamente Transazione, Upload, Delete e Update
        $esito = modificaVino(
            $conn,
            $id_vino,
            $id_utente,
            $nome,
            $cantina,
            $anno,
            $prezzo,
            $file_copertina,
            $nuova_galleria,
            $foto_da_eliminare,
            $foto_da_sostituire
        );

        // 5. Gestione Risposta
        if ($esito) {
            // Se tutto è andato bene, svuotiamo eventuali errori vecchi e andiamo al dettaglio
            unset($_SESSION['errore']);
            header("Location: ../view/dettaglioVino.php?id=$id_vino&msg=updated");
        } else {
            // Se c'è stato un errore (gestito nel try-catch del model), $_SESSION['errore'] è già piena
            header("Location: ../view/modificaVino.php?id=$id_vino");
        }
        exit();

    } else {
        // Se si tenta un accesso GET diretto al controller senza passare dalla view
        header("Location: ../view/listaViniUtente.php");
        exit();
    }