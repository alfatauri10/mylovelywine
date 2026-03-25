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
/*
    if (file_exists($ca_cert)) {
        echo "✅ Il file ca.pem è stato trovato!<br>";
        echo "Percorso reale: " . realpath($ca_cert) . "<br>";
    } else {
        die("❌ ERRORE: Il file ca.pem NON è nella cartella " . __DIR__);
    }
*/
    mysqli_ssl_set($conn, NULL, NULL, $ca_cert, NULL, NULL);

    // 3. Connessione con il flag DONT_VERIFY
    // Questo flag è magico: risolve il 99% dei problemi su hosting condivisi
    $success = mysqli_real_connect(
        $conn,
        $host,
        $user,
        $pass,
        $db,
        $port,
        NULL,
        MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
    );

    if (!$success) {
        die("Errore di connessione: " . mysqli_connect_error());
    }

    echo "Connessione riuscita!";
/*
       // Non usare mysqli_ssl_set qui se continua a darti errore

    $success = mysqli_real_connect(
        $conn,
        $host,
        $user,
        $pass,
        $db,
        $port,
        NULL,
        MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT
    );

    if (!$success) {
        die("Errore di connessione: " . mysqli_connect_error());
    }
*/

?>
