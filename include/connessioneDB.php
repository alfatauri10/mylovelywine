<?php
    //$conn = mysqli_connect("localhost","root","","my_vmartucci");


    $host = "mylovelywine-db-mylovelywine.a.aivencloud.com";
    $user = "avnadmin";
    $pass = "AVNS_GmH8d95xhk-qpUwYaB8"; // Verifica che sia corretta
    $db = "defaultdb";
    $port = 11228;

    // 1. Inizializza mysqli
    $conn = mysqli_init();


    // 2. Se hai scaricato il certificato CA da Aiven (consigliato), indicalo qui.
    // Se vuoi solo forzare l'SSL senza verificare il certificato:
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

    // 3. Tenta la connessione includendo la porta
    if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL)) {
        die("Errore di connessione: " . mysqli_connect_error());
    }

