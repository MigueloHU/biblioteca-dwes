<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/includes/header.php";

$id = (int)($_SESSION["usuario_id"] ?? 0);

// Cargar usuario
$sql = "SELECT id, apellido1, apellido2, nombre, email, password, avatar
        FROM profesores
        WHERE id = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$u = $stmt->fetch();

if (!$u) {
    header("Location: " . APP_URL . "/logout.php");
    exit;
}

$ok = false;
$error = false;

// Si POST -> actualizar (solo sus propios datos)
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $apellido1 = trim($_POST["apellido1"] ?? "");
    $apellido2 = trim($_POST["apellido2"] ?? "");
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $avatar = trim($_POST["avatar"] ?? "");

    if ($apellido1 === "" || $apellido2 === "" || $nombre === "" || $email === "" || $password === "") {
        $error = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
    } else {
        try {
            $sqlUp = "UPDATE profesores
                      SET apellido1 = :apellido1,
                          apellido2 = :apellido2,
                          nombre = :nombre,
                          email = :email,
                          password = :password,
                          avatar = :avatar
                      WHERE id = :id";

            $stmt = $pdo->prepare($sqlUp);
            $stmt->execute([
                ":apellido1" => $apellido1,
                ":apellido2" => $apellido2,
                ":nombre" => $nombre,
                ":email" => $email,
                ":password" => $password,
                ":avatar" => ($avatar !== "" ? $avatar : null),
                ":id" => $id
            ]);

            // Actualizar sesión (por si cambió el email)
            $_SESSION["email"] = $email;

            $ok = true;

            // Recargar datos actualizados
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id]);
            $u = $stmt->fetch();

        } catch (PDOException $e) {
            $error = true;
        }
    }
}
?>

<h2 class="h4 mb-3">Editar mi perfil</h2>

<?php if ($ok): ?>
  <div class="alert alert-success">Perfil actualizado correctamente.</div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger">No se pudo actualizar. Revisa los datos (email válido / no vacío).</div>
<?php endif; ?>

<form method="post" class="card shadow-sm p-3">
  <div class="row g-3">

    <div class="col-md-4">
      <label class="form-label" for="apellido1">Apellido 1</label>
      <input class="form-control" type="text" id="apellido1" name="apellido1" required
             value="<?php echo htmlspecialchars($u["apellido1"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="apellido2">Apellido 2</label>
      <input class="form-control" type="text" id="apellido2" name="apellido2" required
             value="<?php echo htmlspecialchars($u["apellido2"]); ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="nombre">Nombre</label>
      <input class="form-control" type="text" id="nombre" name="nombre" required
             value="<?php echo htmlspecialchars($u["nombre"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="email">Email</label>
      <input class="form-control" type="email" id="email" name="email" required
             value="<?php echo htmlspecialchars($u["email"]); ?>">
    </div>

    <div class="col-md-6">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" type="text" id="password" name="password" required
             value="<?php echo htmlspecialchars($u["password"]); ?>">
    </div>

    <div class="col-md-12">
      <label class="form-label" for="avatar">Avatar (ruta)</label>
      <input class="form-control" type="text" id="avatar" name="avatar"
             value="<?php echo htmlspecialchars($u["avatar"] ?? ""); ?>">
    </div>

  </div>

  <hr class="my-3">

  <button class="btn btn-primary">Guardar cambios</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/perfil.php">Volver</a>
</form>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
