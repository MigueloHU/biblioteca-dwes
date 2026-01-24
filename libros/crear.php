<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3">Alta de libro</h2>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Libro creado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">Revisa los campos. (ISBN / Fecha / Vacíos)</div>
<?php endif; ?>

<form method="post" action="guardar.php" class="card shadow-sm p-3">

  <div class="row g-3">

    <div class="col-md-4">
      <label class="form-label" for="isbn">ISBN</label>
      <input class="form-control" type="text" id="isbn" name="isbn" maxlength="20" required>
    </div>

    <div class="col-md-2">
      <label class="form-label" for="ejemplar">Ejemplar</label>
      <input class="form-control" type="number" id="ejemplar" name="ejemplar" min="1" value="1" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="titulo">Título</label>
      <input class="form-control" type="text" id="titulo" name="titulo" required>
    </div>

    <div class="col-md-6">
      <label class="form-label" for="autor">Autor</label>
      <input class="form-control" type="text" id="autor" name="autor" required>
    </div>

    <div class="col-md-3">
      <label class="form-label" for="genero">Género</label>
      <input class="form-control" type="text" id="genero" name="genero">
    </div>

    <div class="col-md-3">
      <label class="form-label" for="fecha_publicacion">Fecha publicación (mm/aaaa)</label>
      <input class="form-control" type="text" id="fecha_publicacion" name="fecha_publicacion" placeholder="03/2024">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="editorial">Editorial</label>
      <input class="form-control" type="text" id="editorial" name="editorial">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="portada">Portada (ruta o URL)</label>
      <input class="form-control" type="text" id="portada" name="portada" placeholder="assets/img/portadas/libro.jpg">
    </div>

    <div class="col-md-3">
      <label class="form-label" for="precio">Precio</label>
      <input class="form-control" type="number" step="0.01" id="precio" name="precio" min="0">
    </div>

    <div class="col-12">
      <label class="form-label" for="descripcion">Descripción</label>
      <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
    </div>

  </div>

  <hr class="my-3">

  <button class="btn btn-primary">Guardar</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/index.php">Volver</a>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
