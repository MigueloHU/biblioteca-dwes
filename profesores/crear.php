<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../includes/header.php";

$errores = [];
$val = [
    "apellido1" => "",
    "apellido2" => "",
    "nombre" => "",
    "email" => "",
    "password" => "",
    "perfil" => "PROFESOR",
    "avatar" => "",
    "estado" => "1"
];

if (isset($_GET["error"])) {
    // Si venimos de guardar.php con error y valores en querystring (simple)
    $val["apellido1"] = $_GET["apellido1"] ?? "";
    $val["apellido2"] = $_GET["apellido2"] ?? "";
    $val["nombre"] = $_GET["nombre"] ?? "";
    $val["email"] = $_GET["email"] ?? "";
    $val["perfil"] = $_GET["perfil"] ?? "PROFESOR";
    $val["avatar"] = $_GET["avatar"] ?? "";
    $val["estado"] = $_GET["estado"] ?? "1";
}
?>

<h2 class="h4 mb-3">Nuevo profesor</h2>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">Revisa los campos. (vacíos / email inválido / duplicado)</div>
<?php endif; ?>

<form method="post" action="guardar.php" class="card shadow-sm p-3">

  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label" for="apellido1">Apellido 1</label>
      <input class="form-control" type="text" id="apellido1" name="apellido1" required
             value="<?php echo htmlspecialchars($val["apellido1"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="apellido2">Apellido 2</label>
      <input class="form-control" type="text" id="apellido2" name="apellido2" required
             value="<?php echo htmlspecialchars($val["apellido2"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="nombre">Nombre</label>
      <input class="form-control" type="text" id="nombre" name="nombre" required
             value="<?php echo htmlspecialchars($val["nombre"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="email">Email</label>
      <input class="form-control" type="email" id="email" name="email" required
             value="<?php echo htmlspecialchars($val["email"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" type="text" id="password" name="password" required
             value="<?php echo htmlspecialchars($val["password"]); ?>">
      <div class="form-text">De momento usamos password en texto plano (autenticación básica).</div>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="perfil">Perfil</label>
      <select class="form-select" id="perfil" name="perfil">
        <option value="PROFESOR" <?php echo ($val["perfil"] === "PROFESOR") ? "selected" : ""; ?>>PROFESOR</option>
        <option value="ADMIN" <?php echo ($val["perfil"] === "ADMIN") ? "selected" : ""; ?>>ADMIN</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="estado">Estado</label>
      <select class="form-select" id="estado" name="estado">
        <option value="1" <?php echo ($val["estado"] === "1") ? "selected" : ""; ?>>ACTIVO</option>
        <option value="0" <?php echo ($val["estado"] === "0") ? "selected" : ""; ?>>INACTIVO</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="avatar">Avatar (ruta)</label>
      <input class="form-control" type="text" id="avatar" name="avatar"
             value="<?php echo htmlspecialchars($val["avatar"]); ?>">
    </div>
  </div>

  <hr class="my-3">

  <button class="btn btn-primary">Guardar</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/profesores/index.php">Volver</a>

</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
