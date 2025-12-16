<?php
require_once __DIR__ . "/includes/auth.php";
$titulo = "Inicio | Biblioteca";
require_once __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body p-4 text-center">
        <h1 class="h4 mb-3">Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></h1>

        <p class="text-muted mb-4">
          Has iniciado sesión con: <strong><?php echo htmlspecialchars($_SESSION["email"]); ?></strong>
        </p>

        <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
