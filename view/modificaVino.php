<?php
    session_start();
    require_once '../include/connessioneDB.php';
    require_once '../model/Vino.php';

    // 1. Controllo Sicurezza Sessione
    if (!isset($_SESSION['id_utente'])) {
        header("Location: ../view/login.php");
        exit();
    }

    $id_utente = $_SESSION['id_utente'];
    $id_vino = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    // Recupero dati vino e galleria
    $vino = getVinoByIdDB($conn, $id_vino, $id_utente);
    $galleria = getGalleriaCompletaVinoDB($conn, $id_vino);

    if (!$vino) {
        header("Location: listaViniUtente.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Modifica <?php echo htmlspecialchars($vino['nome_vino']); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body class="body-login">
        <div class="container py-5">
            <div class="container-form mx-auto" style="max-width: 900px; background: #fff; padding: 40px; border-radius: 8px;">
                <form action="../controller/modificaVinoController.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_vino" value="<?php echo $vino['id']; ?>">

                    <h2 class="mb-4 text-uppercase" style="letter-spacing: 2px;">Modifica Bottiglia</h2>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom text-start d-block">Nome del Vino</label>
                            <input type="text" name="nome" class="form-control-custom" value="<?php echo htmlspecialchars($vino['nome_vino']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom text-start d-block">Cantina</label>
                            <input type="text" name="cantina" class="form-control-custom" value="<?php echo htmlspecialchars($vino['cantina']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom text-start d-block">Anno</label>
                            <input type="number" name="anno" class="form-control-custom" value="<?php echo htmlspecialchars($vino['anno']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom text-start d-block">Prezzo (€)</label>
                            <input type="text" name="prezzo" class="form-control-custom" value="<?php echo htmlspecialchars($vino['prezzo']); ?>" required>
                        </div>
                    </div>

                    <hr>

                    <h4 class="text-start mb-3">Foto di Copertina</h4>
                    <div class="row align-items-center mb-4 text-start">
                        <div class="col-md-3">
                            <img src="../<?php echo $vino['urlCopertina']; ?>" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label-custom">Sostituisci Copertina</label>
                            <input type="file" name="copertina" class="form-control">
                            <small class="text-muted">Lascia vuoto per mantenere quella attuale.</small>
                        </div>
                    </div>

                    <hr>

                    <h4 class="text-start mb-3">Galleria Immagini</h4>
                    <div class="row g-3 mb-4">
                        <?php foreach ($galleria as $foto): ?>
                            <div class="col-md-4">
                                <div class="card p-2 border shadow-sm">
                                    <img src="../<?php echo $foto['url']; ?>" class="img-fluid mb-2" style="height: 120px; object-fit: cover;">
                                    <div class="d-flex flex-column gap-2">
                                        <input type="file" name="sostituisci_foto[<?php echo $foto['id']; ?>]" class="form-control form-control-sm">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="elimina_foto[]" value="<?php echo $foto['id']; ?>" id="del<?php echo $foto['id']; ?>">
                                            <label class="form-check-label text-danger small" for="del<?php echo $foto['id']; ?>">Elimina questa foto</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group text-start mb-5">
                        <label class="form-label-custom">Aggiungi nuove foto alla galleria (multiple)</label>
                        <input type="file" name="nuova_galleria[]" class="form-control" multiple accept="image/*">
                    </div>

                    <button type="submit" class="btn-login-action w-100 py-3">Salva Tutte le Modifiche</button>
                    <div class="mt-3">
                        <a href="dettaglioVino.php?id=<?php echo $vino['id']; ?>" class="text-decoration-none text-muted small">Annulla e torna indietro</a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>