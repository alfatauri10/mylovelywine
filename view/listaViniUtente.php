<?php
// view/listaViniUtente.php
session_start();
include '../include/header.php';
require_once '../controller/listaViniController.php';

$utente = $_SESSION['username'] ?? 'Utente';
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cantina di <?php echo htmlspecialchars($utente); ?></title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>

        <div class="vetrina-container">
            <div class="hero-section">
                <h2>Cantina di <?php echo htmlspecialchars($utente); ?></h2>
                <p>La tua collezione privata</p>
            </div>

            <?php if (isset($_SESSION['messaggio'])): ?>
                <div class="alert alert-success text-uppercase small fw-bold" style="letter-spacing: 1px; border-radius: 0; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; margin-bottom: 20px;">
                    <?php echo $_SESSION['messaggio']; unset($_SESSION['messaggio']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($vini)): ?>
                <div class="text-center" style="background: #fff; padding: 50px; border: 1px dashed #eeeae3;">
                    <p style="color: #a67c52; text-transform: uppercase; letter-spacing: 2px; font-weight: 500;">Non hai ancora inserito nessun vino.</p>
                    <a href="aggiungiVino.php" class="gold-link">Aggiungi il tuo primo vino</a>
                </div>
            <?php else: ?>
                <div style="text-align: right; margin-bottom: 30px;">
                    <a href="aggiungiVino.php" class="btn-login-action" style="padding: 10px 20px; font-size: 0.8rem; text-decoration: none; display: inline-block; width: auto;">+ Aggiungi Vino</a>
                </div>

                <div class="wine-list">
                    <?php foreach ($vini as $vino): ?>
                        <?php
                        // Impostiamo le variabili per "configurare" il componente
                        $nascondiAutore = true;
                        $mostraAzioni = true;

                        include 'dettagliVino.php';
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="text-center" style="margin-top: 50px; padding-bottom: 50px;">
                <a href="../index.php" class="back-home-link">← Torna alla Home</a>
            </div>
        </div>

        <?php include '../include/footer.php'; ?>

    </body>
</html>