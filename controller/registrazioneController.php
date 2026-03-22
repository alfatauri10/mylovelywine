<?php
// controller/registrazioneController.php
require_once '../include/connessioneDB.php';
require_once '../model/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $res = registraUtente($conn, $_POST['nome'], $_POST['cognome'], $_POST['username'], $_POST['email'], $_POST['password']);

    // DEBUG: Vediamo cosa restituisce la funzione
    /*
    echo "<pre>";
    echo "Valore di res: ";
    var_dump($res); 
    echo "Errore Database: " . $conn->error; // Mostra l'ultimo errore SQL se presente
    echo "</pre>";
    die("Fine Debug"); // Ferma tutto qui e non fare il redirect
    */


    if ($res) {
        header("Location: ../index.php?msg=success");
        exit();
    } else {
        header("Location: ../view/registrazione.php?error=1");
        exit();
    }

}