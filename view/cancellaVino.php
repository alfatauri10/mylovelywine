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
                    <svg class="icon-bottiglia-eliminazione" width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="#d9534f" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 2h4M11 2v4.5c0 1.5-1 2.5-2 3.5S7 12 7 15v4c0 1.5 1 3 2.5 3h5c1.5 0 2.5-1.5 2.5-3v-4c0-3-1-4-2-5s-2-2-2-3.5V2"></path>
                        <path d="M14 11.5c1 1.5 1.5 3 1.5 5.5" stroke-width="0.8" opacity="0.5"></path>

                        <line x1="8" y1="12" x2="16" y2="20" stroke-width="2"></line>
                        <line x1="16" y1="12" x2="8" y2="20" stroke-width="2"></line>
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

                    <button type="submit" class="btn-login-action">
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