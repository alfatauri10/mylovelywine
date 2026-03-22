<main>
    <section class="hero-section">
        <h2>La nostra Cantina</h2>
        <p>Le ultime bottiglie stappate dalla community</p>
    </section>

    <div class="vetrina-container">
        <?php if (empty($vini_globali)): ?>
            <div style="text-align: center; padding: 50px; color: #ccc;">
                <p>La vetrina è ancora vuota. Inizia tu a condividere un vino!</p>
            </div>
        <?php else: ?>
            <?php foreach ($vini_globali as $vino): ?>
                <?php include 'dettagliVino.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>