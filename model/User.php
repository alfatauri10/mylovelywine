<?php
// model/User.php

/**
 * Registra un nuovo utente con il ruolo USER (ID 3)
 */
function registraUtente($conn, $nome, $cognome, $username, $email, $password) {

    /* RENDO LE INSERT NELLE TABELLE Utenti e Utenti_Ruoli TRANSAZIONALI:
     *  Se fallisce una delle due --> faccio il rollback anche dell'altra
     *  --> o  tutto o niente
    */
    $conn->autocommit(FALSE);
    $conn->begin_transaction();

    try {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 2. Salva l'utente
        $sql = "INSERT INTO utenti (nome, cognome, username, email, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nome, $cognome, $username, $email, $password_hash);
        $stmt->execute(); // Se fallisce qui, ora salta direttamente al catch

        $id_utente = $conn->insert_id;

        // 3. Salva il ruolo
        $sql_r = "INSERT INTO utenti_ruoli (id_utente, id_ruolo) VALUES (?, ?)";
        $stmt_r = $conn->prepare($sql_r);
        $id_ruolo = 3; 
        
        // CORREZIONE: Usavi $id_ruolo ma avevi definito $ruolo_id prima
        $stmt_r->bind_param("ii", $id_utente, $id_ruolo);
        $stmt_r->execute();

        // 4. Se tutto è OK, conferma
        $conn->commit();
        $conn->autocommit(TRUE); // Riattiva l'autocommit
        return $id_utente;

    } catch (Exception $e) {
        // 5. Ora il rollback funzionerà perché l'eccezione viene catturata
        $conn->rollback();
        $conn->autocommit(TRUE);
        // echo $e->getMessage(); // per debug
        return false;
    }
}

/**
 * Cerca un utente per il Login
 */
function findUserByUsername($conn, $username) {
    $sql = "SELECT * FROM utenti WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
