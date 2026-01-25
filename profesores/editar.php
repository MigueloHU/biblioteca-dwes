<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

$sql = "SELECT id, apellido1, apellido2, nombre, email, password, perfil, avatar, estado
        FROM profesores
        WHERE id = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$profe = $stmt->fetch();

if (!$profe) {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3">Editar profesor</h2>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Profesor actualizado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">No se pudo actualizar. Revisa los datos (email válido / duplicado).</div>
<?php endif; ?>

<form method="post" action="actualizar.php" class="card shadow-sm p-3">

  <input type="hidden" name="id" value="<?php echo htmlspecialchars($profe["id"]); ?>">

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" for="apellido1">Apellido 1</label>
      <input class="form-control" type="text" id="apellido1" name="apellido1" required
             value="<?php echo htmlspecialchars($profe["apellido1"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="apellido2">Apellido 2</label>
      <input class="form-control" type="text" id="apellido2" name="apellido2" required
             value="<?php echo htmlspecialchars($profe["apellido2"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="nombre">Nombre</label>
      <input class="form-control" type="text" id="nombre" name="nombre" required
             value="<?php echo htmlspecialchars($profe["nombre"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="email">Email</label>
      <input class="form-control" type="email" id="email" name="email" required
             value="<?php echo htmlspecialchars($profe["email"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" type="text" id="password" name="password" required
             value="<?php echo htmlspecialchars($profe["password"]); ?>">
      <div class="form-text">Autenticación básica (texto plano).</div>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="perfil">Perfil</label>
      <select class="form-select" id="perfil" name="perfil">
        <option value="PROFESOR" <?php echo ($profe["perfil"] === "PROFESOR") ? "selected" : ""; ?>>PROFESOR</option>
        <option value="ADMIN" <?php echo ($profe["perfil"] === "ADMIN") ? "selected" : ""; ?>>ADMIN</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="estado">Estado</label>
      <select class="form-select" id="estado" name="estado">
        <option value="1" <?php echo ((int)$profe["estado"] === 1) ? "selected" : ""; ?>>ACTIVO</option>
        <option value="0" <?php echo ((int)$profe["estado"] === 0) ? "selected" : ""; ?>>INACTIVO</option>
      </select>
      <?php if ((int)$profe["id"] === 1): ?>
        <div class="form-text">El admin (id=1) no se desactiva desde aquí.</div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="avatar">Avatar (ruta)</label>
      <input class="form-control" type="text" id="avatar" name="avatar"
             value="<?php echo htmlspecialchars($profe["avatar"] ?? ""); ?>">
    </div>
  </div>

  <hr class="my-3">

  <button class="btn btn-primary">Guardar cambios</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/profesores/index.php">Volver</a>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
