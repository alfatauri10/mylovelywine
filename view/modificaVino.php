<?php
session_start();

require_once '../include/connessioneDB.php';
require_once '../model/Vino.php';

// Controllo se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['user_id'];
$id_vino = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Recupero dati vino e galleria completa (con ID per eliminazione/sostituzione)
$vino = getVinoByIdDB($conn, $id_vino, $id_utente);
$galleria = getGalleriaCompletaVinoDB($conn, $id_vino);

if (!$vino) {
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
    <title>Modifica <?php echo htmlspecialchars($vino['nome_vino']); ?> - My Lovely Wine</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="body-login">
<div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="container-form text-center" style="max-width: 850px;">

        <h1 style="font-weight: 500; letter-spacing: 5px; text-transform: uppercase; color: #2d1b10; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 0;">
            Modifica Bottiglia
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
                <input type="text" name="nome_vino" class="form-control-custom" value="<?php echo htmlspecialchars($vino['nome_vino']); ?>" required>
            </div>

            <div class="form-group text-start">
                <label class="form-label-custom">Cantina / Produttore</label>
                <input type="text" name="cantina" class="form-control-custom" value="<?php echo htmlspecialchars($vino['cantina']); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group text-start">
                        <label class="form-label-custom">Anno</label>
                        <input type="number" name="anno" class="form-control-custom" value="<?php echo $vino['anno']; ?>" required min="1900" max="2099">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group text-start">
                        <label class="form-label-custom">Prezzo (€)</label>
                        <input type="text" name="prezzo" class="form-control-custom" value="<?php echo number_format($vino['prezzo'], 2, '.', ''); ?>" required>
                    </div>
                </div>
            </div>

            <hr style="border-color: #eeeae3; margin: 30px 0;">

            <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; color: #a67c52; text-align: left; margin-bottom: 20px;">Foto di Copertina</h4>
            <div class="row align-items-center mb-4">
                <div class="col-md-3 text-start">
                    <div class="wine-thumb-container" style="width: 120px; height: 120px; margin: 0;">
                        <img src="../<?php echo $vino['urlCopertina'] ?: 'uploads/vini/default/iconaVino.png'; ?>"
                             class="wine-thumb" alt="Foto Vino"
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.src='../uploads/vini/default/iconaVino.png';">
                    </div>
                </div>
                <div class="col-md-9 text-start">
                    <label class="form-label-custom">Sostituisci Copertina</label>
                    <input type="file" name="copertina" class="form-control" accept="image/*"
                           style="border-radius: 0; border: 1px solid #eeeae3; font-size: 0.8rem; color: #8c8479; padding: 10px;">
                    <small class="text-muted" style="font-size: 0.7rem;">Lascia vuoto per mantenere quella attuale.</small>
                </div>
            </div>

            <hr style="border-color: #eeeae3; margin: 30px 0;">

            <h4 style="font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; color: #a67c52; text-align: left; margin-bottom: 20px;">Galleria Immagini</h4>

            <div class="row g-3 mb-4">
                <?php foreach ($galleria as $foto): ?>
                    <div class="col-md-4">
                        <div class="card p-2 shadow-sm" style="border-radius: 0; border: 1px solid #eeeae3;">
                            <img src="../<?php echo $foto['url']; ?>" class="img-fluid mb-2" style="height: 120px; width: 100%; object-fit: cover;">
                            <div class="d-flex flex-column gap-2 text-start">
                                <label style="font-size: 0.65rem; text-transform: uppercase; color: #a67c52;">Sostituisci:</label>
                                <input type="file" name="sostituisci_foto[<?php echo $foto['id']; ?>]" class="form-control form-control-sm" style="font-size: 0.7rem; border-radius: 0;">

                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="elimina_foto[]" value="<?php echo $foto['id']; ?>" id="del<?php echo $foto['id']; ?>">
                                    <label class="form-check-label text-danger" for="del<?php echo $foto['id']; ?>" style="font-size: 0.7rem; text-transform: uppercase; font-weight: 500;">Elimina</label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="form-group text-start mb-5">
                <label class="form-label-custom">Aggiungi nuove foto (multiple)</label>
                <input type="file" name="nuova_galleria[]" class="form-control" multiple accept="image/*"
                       style="border-radius: 0; border: 1px solid #eeeae3; font-size: 0.8rem; color: #8c8479; padding: 10px;">
            </div>

            <button type="submit" class="btn-login-action">
                Salva Tutte le Modifiche
            </button>
        </form>

        <div class="mt-4">
            <a href="listaViniUtente.php" class="gold-link" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                ← Annulla e torna in cantina
            </a>
        </div>
    </div>
</div>
</body>
</html>