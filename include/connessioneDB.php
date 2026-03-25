<?php
    //$conn = mysqli_connect("localhost","root","","my_vmartucci");

    $host = "mylovelywine-db-mylovelywine.a.aivencloud.com";
    $user = "avnadmin";
    $pass = "AVNS_GmH8d95xhk-qpUwYaB8";
    $db = "defaultdb";
    $port = 11228;


    $conn = mysqli_connect($host, $user ,$pass,$db ,$port);


    if(!$conn){
        die("Connessione fallitaa: " . mysqli_connect_error());
    }


?>