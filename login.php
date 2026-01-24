<?php
require_once __DIR__ . "/includes/header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya está logueado, fuera del login
if (isset($_SESSION["usuario_id"])) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}
?>

<h1 class="mb-4">Login</h1>

<?php if (isset($_GET["error"])): ?>
    <div class="alert alert-danger">Email o contraseña incorrectos.</div>
<?php endif; ?>

<form method="post" action="auth/comprobar_login.php" class="card p-3 shadow-sm">

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Entrar</button>

</form>

<?php
require_once __DIR__ . "/includes/footer.php";
?>
