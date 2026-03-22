<?php
    session_start();

    // 1. Controllo se l'utente è loggato
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // 2. Recupero i dati dalla GET
    $id_vino = $_GET['id'] ?? null;
    $nome_vino = $_GET['nome'] ?? 'questa bottiglia';

    // 3. Se non c'è l'ID, torniamo alla lista
    if (!$id_vino) {
        header("Location: listaViniUtente.php");
        exit();
    }

    $error = $_SESSION['errore'] ?? null;
    unset($_SESSION['errore']);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Conferma Eliminazione - My Lovely Wine</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body class="body-login">
        <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
            <div class="container-form text-center">

                <h1 style="font-weight: 500; letter-spacing: 5px; text-transform: uppercase; color: #2d1b10; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 0;">
                    Elimina Vino
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#d9534f" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </h1>
                <p class="login-subtitle" style="margin-bottom: 25px;">Sei sicuro di voler procedere?</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger" style="border-radius: 0; font-size: 0.8rem; border: none; background-color: #f8d7da; color: #721c24; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div style="background-color: #fcfaf7; border: 1px solid #eeeae3; padding: 30px; margin-bottom: 30px;">
                    <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; color: #8c8479; margin-bottom: 10px;">Stai per rimuovere:</p>
                    <h2 style="font-size: 1.4rem; color: #2d1b10; font-weight: 500; margin: 0;">
                        <?php echo htmlspecialchars($nome_vino); ?>
                    </h2>
                </div>

                <form action="../controller/cancellaVinoController.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo $id_vino; ?>">

                    <button type="submit" class="btn-login-action" style="background-color: #d9534f; border-color: #d43f3a;">
                        Sì, elimina dalla cantina
                    </button>
                </form>

                <div class="mt-4">
                    <a href="listaViniUtente.php" class="gold-link" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                        ← No, mantieni il vino
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>