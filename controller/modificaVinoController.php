<?php
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_utente = $_SESSION['id_utente'];
        $id_vino   = filter_input(INPUT_POST, 'id_vino', FILTER_SANITIZE_NUMBER_INT);

        // 2. Recupero e Sanificazione dati testuali
        $nome = $_POST['nome_vino'] ?? '';
        $cantina = $_POST['cantina'] ?? '';
        $anno = $_POST['anno'] ?? '';
        $prezzo = $_POST['prezzo'] ?? '';
        $file_copertina = (isset($_FILES['copertina']) && $_FILES['copertina']['error'] !== UPLOAD_ERR_NO_FILE) ? $_FILES['copertina'] : null;
        $nuova_galleria = (isset($_FILES['nuova_galleria']) && $_FILES['nuova_galleria']['error'][0] !== UPLOAD_ERR_NO_FILE) ? $_FILES['nuova_galleria'] : null;
        $foto_da_eliminare = $_POST['elimina_foto'] ?? [];
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
            $_SESSION['messaggio'] = "Vino modificato con successo!";
            header("Location: ../view/listaViniUtente.php"); // Redirect al SUCCESSO
            exit(); // FERMA TUTTO QUI
        } else {
            // Se il Model non ha già impostato un errore specifico (es. nel catch), mettiamo quello generico
            if (!isset($_SESSION['errore'])) {
                $_SESSION['errore'] = "Errore durante la modifica (Errore Generico).";
            }
            header("Location: ../view/modificaVino.php?id=$id_vino");
            exit();

        }


    } else {
        // Se si tenta un accesso GET diretto al controller senza passare dalla view
        header("Location: ../view/listaViniUtente.php");
        exit();
    }