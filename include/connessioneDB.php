<?php
    //$conn = mysqli_connect("localhost","root","","my_vmartucci");

    $host = "mylovelywine-db-mylovelywine.a.aivencloud.com";
    $user = "avnadmin";
    $pass = "AVNS_GmH8d95xhk-qpUwYaB8";
    $db = "defaultdb";
    $port = 11228;

    $conn = mysqli_init();

    // Specifichiamo il percorso del file ca.pem che hai appena caricato
    $ca_cert = __DIR__ . "/ca.pem";

    // Impostiamo l'SSL usando il file certificato
    mysqli_ssl_set($conn, NULL, NULL, $ca_cert, NULL, NULL);

    // Connessione
    $success = mysqli_real_connect(
        $conn,
        $host,
        $user,
        $pass,
        $db,
        $port,
        NULL,
        MYSQLI_CLIENT_SSL
    );

    if (!$success) {
        die("Errore di connessione: " . mysqli_connect_error());
    }
?>
