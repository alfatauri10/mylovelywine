<?php
  // controller/loginController.php
  session_start();
  require_once '../include/connessioneDB.php';
  require_once '../model/User.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $user = findUserByUsername($conn, $_POST['username']);

      if ($user && password_verify($_POST['password'], $user['password'])) {
          // Login OK: salviamo i dati in sessione
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];

          header("Location: ../index.php");
          exit();
      } else {
          // Login Fallito
          header("Location: ../view/login.php?error=1");
          exit();
      }
  }