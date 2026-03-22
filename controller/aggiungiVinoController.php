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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_vino'] ?? '';
    $cantina = $_POST['cantina'] ?? '';
    $anno = $_POST['anno'] ?? '';
    $prezzo = $_POST['prezzo'] ?? '';
    $copertina_vino = $_FILES['copertina_vino'] ?? null;
    $galleria_vino = $_FILES['galleria_vino'] ?? null;

    // chiamo Vino Model che fa tutto (upload + DB)
    $res = aggiungiVino($conn, $id_utente, $nome, $cantina, $anno, $prezzo, $copertina_vino, $galleria_vino);

    if ($res) {
        $_SESSION['messaggio'] = "Vino aggiunto con successo!";
        header("Location: ../view/listaVini.php"); // Redirect al SUCCESSO
        exit(); // FERMA TUTTO QUI
    } else {
        // Se il Model non ha già impostato un errore specifico (es. nel catch), mettiamo quello generico
        if (!isset($_SESSION['errore'])) {
            $_SESSION['errore'] = "Errore durante l'aggiunta (Errore Generico).";
        }
        header("Location: ../view/aggiungiVino.php"); 
        exit();
    }
}

// Se qualcuno prova ad accedere al controller senza POST (es. via URL), lo rimandiamo alla lista
header("Location: ../view/listaVini.php");
exit();