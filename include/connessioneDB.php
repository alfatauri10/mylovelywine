<?php
    //$conn = mysqli_connect("localhost","root","","my_vmartucci");

    $host = "mylovelywine-db-mylovelywine.a.aivencloud.com";
    $user = "avnadmin";
    $pass = "AVNS_GmH8d95xhk-qpUwYaB8";
    $db = "defaultdb";
    $port = 11228;

    $conn = mysqli_init();

    // 2. Imposta il percorso del certificato (anche se lo ignoreremo, serve a mysqli per attivare l'SSL)
    $ca_cert = __DIR__ . "/ca.pem";

    // Configuriamo l'SSL. Se il file non venisse letto bene, il flag sotto ci salverà.
   // mysqli_ssl_set($conn, NULL, NULL, $ca_cert, NULL, NULL);

    // 3. Tenta la connessione con i FLAG COMBINATI
    // Il simbolo "|" unisce i due comandi: "Usa SSL" + "Non verificare il certificato"
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
        die("Errore critico di connessione: " . mysqli_connect_error());
    }

    // Se arrivi qui, è fatta!
    // echo "Connessione stabilita con successo!";
    ?>