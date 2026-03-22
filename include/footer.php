<?php
    // Rilevamento posizione
    $currentFile = basename($_SERVER['PHP_SELF']);
    $inViewFolder = (strpos($_SERVER['PHP_SELF'], '/view/') !== false);

    // Variabili di percorso universali
    $root = $inViewFolder ? '../' : '';
    $viewDir = $inViewFolder ? '' : 'view/';
?>

<footer style="padding: 30px 0; border-top: 1px solid #f2f0eb; text-align: center; color: #ccc; font-size: 0.8rem;">
  &copy; <?php echo date('Y'); ?> My Lovely Wine
</footer>