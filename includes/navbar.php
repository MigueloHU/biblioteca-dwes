<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logeado = isset($_SESSION["usuario"]);
$perfil = $logeado ? ($_SESSION["usuario"]["perfil"] ?? "") : "";
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav me-auto">
        <?php if ($logeado) { ?>
          <li class="nav-item"><a class="nav-link" href="index.php">Libros</a></li>
          <li class="nav-item"><a class="nav-link" href="prestamos/listar.php">Préstamos</a></li>
          <li class="nav-item"><a class="nav-link" href="reservas/listar.php">Reservas</a></li>

          <?php if ($perfil === "ADMIN") { ?>
            <li class="nav-item"><a class="nav-link" href="usuarios/listar.php">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="logs/listar.php">Log</a></li>
          <?php } ?>
        <?php } ?>
      </ul>

      <ul class="navbar-nav">
        <?php if ($logeado) { ?>
          <li class="nav-item">
            <span class="navbar-text me-3">
              <?php echo htmlspecialchars($_SESSION["usuario"]["nombre"]); ?> (<?php echo htmlspecialchars($perfil); ?>)
            </span>
          </li>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="logout.php">Salir</a></li>
        <?php } else { ?>
          <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="login.php">Login</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>
