<?php
// controller/listaViniController.php
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


// 1. Recupera la lista base dei vini
$vini = getListaViniByIdUtenteDB($conn, $id_utente);

// 2. Modifichiamo l'array $vini aggiungendo la galleria a ogni elemento
if ($vini) {
    foreach ($vini as $key => $vino) {
        // Chiamiamo la funzione del model usando l'ID del vino corrente
        $galleria = getURLGalleriaImmaginiDB($conn, $vino['id']);
        
        // Aggiungiamo il risultato dentro l'array $vini alla posizione corretta
        $vini[$key]['galleria'] = $galleria;
    }
}