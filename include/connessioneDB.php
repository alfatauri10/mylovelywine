<?php
    $host = "mylovelywine-db-mylovelywine.a.aivencloud.com";
    $user = "avnadmin";
    $pass = "AVNS_GmH8d95xhk-qpUwYaB8";
    $db   = "defaultdb";
    $port = 11228;

    // 1. Inizializziamo la connessione in modo speciale per l'SSL
    $conn = mysqli_init();

    // 2. Diciamo a PHP che la connessione DEVE essere cifrata (SSL)
    // Su Altervista non serve il certificato fisico .pem, basta forzare l'uso dell'SSL
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

    // 3. Proviamo a connetterci
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
        die("Connessione fallita: " . mysqli_connect_error());
    }

?>
