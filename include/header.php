<?php
    // Rilevamento posizione
    $currentFile = basename($_SERVER['PHP_SELF']);
    $inViewFolder = (strpos($_SERVER['PHP_SELF'], '/view/') !== false);

    // Variabili di percorso universali
    $root = $inViewFolder ? '../' : '';
    $viewDir = $inViewFolder ? '' : 'view/';
?>

<header class="main-header">
    <div class="header-content-wrapper">

        <a href="<?php echo $root; ?>index.php" class="logo">
            My Lovely Wine
            <svg class="icon-calice" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 22h8"></path>
                <path d="M12 11V22"></path>
                <path d="M19 8c0 3.5-3 6-7 6s-7-2.5-7-6V2h14v6Z"></path>
            </svg>
        </a>

        <nav class="auth-nav">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="user-welcome">
                    Ciao, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!
                </span>

                <?php if ($currentFile == 'index.php'): ?>
                    <a href="view/listaViniUtente.php" class="nav-link">Cantina</a>
                <?php else: ?>
                    <a href="<?php echo $root; ?>index.php" class="nav-link">Vetrina</a>
                <?php endif; ?>

                <a href="<?php echo $root; ?>controller/logoutController.php" class="btn-elegant">Esci</a>

            <?php else: ?>
                <a href="<?php echo $viewDir; ?>login.php" class="nav-link">Accedi</a>
                <a href="<?php echo $viewDir; ?>registrazione.php" class="btn-elegant">Unisciti al club</a>
            <?php endif; ?>
        </nav>
    </div>
</header>