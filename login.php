<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/conexion.php";

session_start();

$email = "";
$password = "";
$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($email === "") {
        $errores["email"] = "El email es obligatorio.";
    }
    if ($password === "") {
        $errores["password"] = "La contraseña es obligatoria.";
    }

    if (count($errores) === 0) {
        $cn = getConexion();

        $sql = "SELECT id, nombre, apellido1, email, password, perfil, estado, avatar
                FROM profesores
                WHERE email = :email
                LIMIT 1";

        $st = $cn->prepare($sql);
        $st->execute([":email" => $email]);
        $usuario = $st->fetch();

        if (!$usuario) {
            $errores["general"] = "Credenciales incorrectas.";
        } else if ((int)$usuario["estado"] !== 1) {
            $errores["general"] = "Usuario desactivado.";
        } else if (!password_verify($password, $usuario["password"])) {
            $errores["general"] = "Credenciales incorrectas.";
        } else {
            $_SESSION["usuario"] = [
                "id" => (int)$usuario["id"],
                "nombre" => $usuario["nombre"],
                "apellido1" => $usuario["apellido1"],
                "email" => $usuario["email"],
                "perfil" => $usuario["perfil"],
                "avatar" => $usuario["avatar"]
            ];

            header("Location: index.php");
            exit;
        }
    }
}

require_once __DIR__ . "/includes/header.php";
require_once __DIR__ . "/includes/navbar.php";
?>

<div class="container py-5 d-flex justify-content-center">
  <div class="card shadow" style="max-width: 420px; width:100%;">
    <div class="card-body p-4">
      <h1 class="h4 text-center mb-4"><?php echo APP_NAME; ?> - Login</h1>

      <?php if (isset($errores["general"])) { ?>
        <div class="alert alert-danger"><?php echo $errores["general"]; ?></div>
      <?php } ?>

      <form method="post" action="">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
          <?php if (isset($errores["email"])) { ?>
            <div class="text-danger small"><?php echo $errores["email"]; ?></div>
          <?php } ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input class="form-control" type="password" name="password">
          <?php if (isset($errores["password"])) { ?>
            <div class="text-danger small"><?php echo $errores["password"]; ?></div>
          <?php } ?>
        </div>

        <button class="btn btn-primary w-100" type="submit">Entrar</button>
      </form>

      <hr>
      <p class="mb-0 small text-muted">
        Admin inicial: <strong>admin@biblioteca.local</strong> / <strong>admin1234</strong>
      </p>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
