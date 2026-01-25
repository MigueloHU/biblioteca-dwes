<?php
// includes/navbar.php
require_once __DIR__ . "/../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo APP_URL; ?>/index.php"><?php echo APP_NAME; ?></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/index.php">Libros</a>
                </li>
                <?php if (($_SESSION["perfil"] ?? "") === "ADMIN"): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL; ?>/profesores/index.php">Profesores</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/prestamos/index.php">Préstamos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo APP_URL; ?>/reservas/index.php">Reservas</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3 text-white">
                <span class="small">
                    <?php echo htmlspecialchars($_SESSION["email"] ?? ""); ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="<?php echo APP_URL; ?>/logout.php">Salir</a>
            </div>
        </div>
    </div>
</nav>