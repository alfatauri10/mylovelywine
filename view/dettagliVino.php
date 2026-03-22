<div class="wine-row">
    <div class="wine-header">
        <div class="wine-thumb-container">
            <img src="../<?php echo $vino['urlCopertina'] ?: 'uploads/vini/default/iconaVino.png'; ?>"
                 class="wine-thumb" alt="Foto Vino"
                 onerror="this.src='../uploads/vini/default/iconaVino.png';">
        </div>

        <div class="wine-info-main">
            <h3><?php echo htmlspecialchars($vino['nome_vino']); ?></h3>
            <p class="wine-subtitle"><?php echo htmlspecialchars($vino['cantina']); ?></p>

            <div class="wine-specs-minimal">
                <?php if ($vino['anno'] > 0): ?><span>Annata <?php echo $vino['anno']; ?></span><?php endif; ?>
                <?php if ($vino['prezzo'] > 0): ?>
                    <span class="dot-sep">·</span>
                    <span><?php echo number_format($vino['prezzo'], 2, ',', '.'); ?> €</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="wine-info-meta">
            <?php if (!isset($nascondiAutore) || !$nascondiAutore): ?>
                <b>@<?php echo htmlspecialchars($vino['username']); ?></b>
                <span><?php echo date('d/m/Y', strtotime($vino['created_at'])); ?></span>
            <?php endif; ?>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 5px;">
                <?php if (isset($mostraModifica) && $mostraModifica): ?>
                    <a href="modificaVino.php?id=<?php echo $vino['id']; ?>"
                       style="color: #a67c52; text-decoration: none; font-size: 0.7rem; font-weight: 500; text-transform: uppercase; border: 1px solid #eeeae3; padding: 5px 10px; display: inline-block;">
                        Modifica
                    </a>
                <?php endif; ?>

                <?php if (isset($mostraElimina) && $mostraElimina): ?>
                <!--
                    <a href="../controller/cancellaVinoController.php?id=<?php echo $vino['id']; ?>"
                       style="color: #d9534f; text-decoration: none; font-size: 0.7rem; font-weight: 500; text-transform: uppercase; border: 1px solid #f8d7da; padding: 5px 10px; display: inline-block;"
                       onclick="return confirm('Elimino questo vino?')">
                        Elimina
                    </a>
                -->
                    <a href="cancellaVino.php?id=<?php echo $vino['id']; ?>&nome=<?php echo urlencode($vino['nome_vino']); ?>"
                       style="color: #a67c52; text-decoration: none; font-size: 0.7rem; font-weight: 500; text-transform: uppercase; border: 1px solid #eeeae3; padding: 5px 10px; display: inline-block;">
                        Elimina
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="wine-footer-details">
        <details class="wine-details">
            <summary>Note di degustazione & Gallery</summary>
        </details>
    </div>
</div>