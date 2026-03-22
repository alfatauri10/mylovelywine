<?php
// Messaggi di errore
$error = $_SESSION['errore'] ?? null;
unset($_SESSION['errore']);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Modifica Vino - My Lovely Wine</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body class="body-login">
        <div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
            <div class="container-form text-center">

                <h1 class="text-uppercase" style="letter-spacing: 5px; color: #2d1b10;">
                    Modifica Bottiglia
                    <svg class="icon-bottiglia-sinuosa" width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 2h4M11 2v4.5c0 1.5-1 2.5-2 3.5S7 12 7 15v4c0 1.5 1 3 2.5 3h5c1.5 0 2.5-1.5 2.5-3v-4c0-3-1-4-2-5s-2-2-2-3.5V2"></path>
                        <path d="M14 11.5c1 1.5 1.5 3 1.5 5.5" stroke-width="0.8" opacity="0.5"></path>
                    </svg>
                </h1>

                <p class="login-subtitle">Stai modificando: <strong><?php echo htmlspecialchars($vino['nome_vino'] ?? ''); ?></strong></p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="modificaVinoController.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="id_vino" value="<?php echo $vino['id']; ?>">

                    <div class="form-group text-start mb-3">
                        <label class="form-label-custom">Nome del Vino</label>
                        <input type="text" name="nome" class="form-control-custom"
                               value="<?php echo htmlspecialchars($vino['nome_vino'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group text-start mb-3">
                        <label class="form-label-custom">Cantina / Produttore</label>
                        <input type="text" name="cantina" class="form-control-custom"
                               value="<?php echo htmlspecialchars($vino['cantina'] ?? ''); ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group text-start mb-3">
                                <label class="form-label-custom">Anno</label>
                                <input type="number" name="anno" class="form-control-custom"
                                       value="<?php echo htmlspecialchars($vino['anno'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group text-start mb-3">
                                <label class="form-label-custom">Prezzo (€)</label>
                                <input type="text" name="prezzo" class="form-control-custom"
                                       value="<?php echo htmlspecialchars($vino['prezzo'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-start mb-4">
                        <label class="form-label-custom">Cambia Copertina</label>
                        <input type="file" name="copertina" class="form-control">
                    </div>

                    <button type="submit" class="btn-login-action w-100">Salva Modifiche</button>
                </form>

                <div class="mt-4">
                    <a href="listaViniUtente.php" class="gold-link">← Torna alla cantina</a>
                </div>
            </div>
        </div>
    </body>
</html>