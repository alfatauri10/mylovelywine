<?php
// Inizia la sessione per poterla distruggere
session_start();

// Rimuove tutte le variabili di sessione
$_SESSION = array();

// Se desideri distruggere anche il cookie di sessione (opzionale ma consigliato per massima sicurezza)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Distrugge definitivamente la sessione sul server
session_destroy();

// Reindirizza l'utente alla home page
header("Location: ../index.php");
exit();