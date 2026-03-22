<?php
    session_start();

    include 'include/header.php';
    require_once 'include/connessioneDB.php';
    require_once 'model/Vino.php';


    // 1. Recuperiamo la lista globale dei vini
    $vini_globali = getListaViniDB($conn);

    // 2. AGGIUNGI IL PEZZO MANCANTE (Il contorno)
    foreach ($vini_globali as $key => $vino) {
        // Per ogni vino, interroga la tabella immagini_vini tramite l'ID
        $vini_globali[$key]['galleria'] = getURLGalleriaImmaginiDB($conn, $vino['id']);
    }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Lovely Wine | Vetrina</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400;600&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="css/style.css">

    </head>
    <body>
        <?php include 'view/listaViniVetrina.php'; ?>
        <?php include 'include/footer.php'; ?>
    </body>
</html>