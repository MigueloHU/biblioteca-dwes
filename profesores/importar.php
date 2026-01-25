<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3">Importar profesores desde Excel</h2>

<div class="alert alert-info">
  Sube un archivo <strong>.xlsx</strong> con las columnas:
  <strong>id, apellido1, apellido2, nombre, email, perfil, avatar, estado</strong>.
</div>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Importación finalizada correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">No se pudo importar. Revisa el archivo y el formato.</div>
<?php endif; ?>

<form method="post" action="procesar_importacion.php" enctype="multipart/form-data" class="card shadow-sm p-3">
  <div class="mb-3">
    <label class="form-label" for="excel">Archivo Excel (.xlsx)</label>
    <input class="form-control" type="file" id="excel" name="excel" accept=".xlsx" required>
  </div>

  <button class="btn btn-primary">Importar</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/profesores/index.php">Volver</a>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
