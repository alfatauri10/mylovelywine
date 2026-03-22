<!DOCTYPE html>
<html lang="it">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accedi - My Lovely Wine</title>

        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body class="body-login">

        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="container-form text-center">
                <h1 style="font-weight: 500; letter-spacing: 5px; text-transform: uppercase; color: #2d1b10; margin-bottom: 10px; display: flex; align-items: center; justify-content: center;">
                    Accedi
                    <svg class="icon-calice" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 22h8"></path>
                        <path d="M12 11V22"></path>
                        <path d="M19 8c0 3.5-3 6-7 6s-7-2.5-7-6V2h14v6Z"></path>
                    </svg>
                </h1>
                <p class="login-subtitle">Bentornato nel club</p>

                <form action="../controller/loginController.php" method="POST">
                    <div class="form-group text-start mb-4">
                        <label class="form-label-custom">Username</label>
                        <input type="text" name="username" class="form-control-custom" placeholder="Il tuo nome utente" required>
                    </div>

                    <div class="form-group text-start mb-4">
                        <label class="form-label-custom">Password</label>
                        <input type="password" name="password" class="form-control-custom" placeholder="La tua chiave segreta" required>
                    </div>

                    <button type="submit" class="btn-login-action mt-2">
                        Stappa e Entra
                    </button>
                </form>

                <div class="mt-4">
                    <p class="login-footer-text">
                        Non sei ancora dei nostri?
                        <a href="registrazione.php" class="gold-link">Unisciti al Club</a>
                    </p>
                    <a href="../index.php" class="back-home-link">
                        ← Torna alla Home
                    </a>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-login-error mt-4">
                        Credenziali errate
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </body>

</html>
