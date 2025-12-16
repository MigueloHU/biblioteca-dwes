<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/auth.php";
requireLogin();

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";
?>

<div class="container py-4">
  <h1 class="h4">Listado general (base)</h1>
  <p class="text-muted mb-3">En la siguiente fase cargaremos los libros desde la base de datos.</p>

  <div class="alert alert-info">
    Sesión activa. Ya puedes acceder a las secciones según tu perfil.
  </div>

  <button class="btn btn-danger" disabled>Listado PDF (se implementa en Fase 5)</button>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
