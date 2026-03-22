<?php
    session_start();
    require_once '../include/connessioneDB.php';
    require_once '../model/Vino.php';

    // 1. Controllo se l'utente è loggato
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $id_utente = $_SESSION['user_id'];
    $id_vino = $_GET['id'] ?? null;

    // 2. Controllo se l'ID del vino è presente e se appartiene all'utente
    if (!$id_vino) {
        header("Location: listaViniUtente.php");
        exit();
    }

    // 3. Recupero i dati del vino da modificare
    $vino = getVinoPerModifica($conn, $id_vino, $id_utente);

    if (!$vino) {
        header("Location: listaViniUtente.php");
        exit();
    }

    // 3. Leggi l'errore dalla SESSIONE
    $error = $_SESSION['errore'] ?? null;
    unset($_SESSION['errore']);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifica Vino - My Lovely Wine</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body class="body-login">
        <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
            <div class="container-form text-center">

                <h1 style="font-weight: 500; letter-spacing: 5px; text-transform: uppercase; color: #2d1b10; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 0;">
                    Modifica Vino
                    <svg class="icon-bottiglia-sinuosa" width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 2h4M11 2v4.5c0 1.5-1 2.5-2 3.5S7 12 7 15v4c0 1.5 1 3 2.5 3h5c1.5 0 2.5-1.5 2.5-3v-4c0-3-1-4-2-5s-2-2-2-3.5V2"></path>
                        <path d="M14 11.5c1 1.5 1.5 3 1.5 5.5" stroke-width="0.8" opacity="0.5"></path>
                    </svg>
                </h1>
                <p class="login-subtitle" style="margin-bottom: 25px;">Aggiorna i dettagli della tua bottiglia</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger" style="border-radius: 0; font-size: 0.8rem; border: none; background-color: #f8d7da; color: #721c24; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="../controller/modificaVinoController.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id_vino" value="<?php echo $vino['id']; ?>">

                    <div class="form-group text-start">
                        <label class="form-label-custom">Nome del Vino</label>
                        <input type="text" name="nome_vino" class="form-control-custom"
                               value="<?php echo htmlspecialchars($vino['nome_vino']); ?>" required>
                    </div>

                    <div class="form-group text-start">
                        <label class="form-label-custom">Cantina / Produttore</label>
                        <input type="text" name="cantina" class="form-control-custom"
                               value="<?php echo htmlspecialchars($vino['cantina']); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group text-start">
                                <label class="form-label-custom">Anno</label>
                                <input type="number" name="anno" class="form-control-custom"
                                       value="<?php echo $vino['anno']; ?>" required min="1900" max="2099">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group text-start">
                                <label class="form-label-custom">Prezzo (€)</label>
                                <input type="text" name="prezzo" class="form-control-custom"
                                       value="<?php echo number_format($vino['prezzo'], 2, '.', ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-start mb-4">
                        <label class="form-label-custom">Cambia Foto Copertina (Opzionale)</label>
                        <input type="file" name="copertina_vino" class="form-control" accept="image/*"
                               style="border-radius: 0; border: 1px solid #eeeae3; font-size: 0.8rem; color: #8c8479; padding: 10px;">
                        <small style="color: #ccc; font-size: 0.65rem;">Lascia vuoto per mantenere la foto attuale</small>
                    </div>

                    <div class="form-group text-start mb-4">
                        <label class="form-label-custom">Aggiungi foto alla Galleria (Opzionale)</label>
                        <input type="file"
                               name="galleria_vino[]"
                               class="form-control"
                               accept="image/*"
                               multiple
                               style="border-radius: 0; border: 1px solid #eeeae3; font-size: 0.8rem; color: #8c8479; padding: 10px;">
                    </div>

                    <button type="submit" class="btn-login-action">
                        Aggiorna in Cantina
                    </button>
                </form>

                <div class="mt-4">
                    <a href="listaViniUtente.php" class="gold-link" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                        ← Annulla e torna alla mia cantina
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>