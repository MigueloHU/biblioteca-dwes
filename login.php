<?php
$titulo = "Login | Biblioteca";
require_once __DIR__ . "/includes/header.php";
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-7 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">

        <h1 class="h4 mb-3 text-center">Acceso a la Biblioteca</h1>

        <?php if (isset($_GET["error"])): ?>
          <div class="alert alert-danger text-center">
            Email o contraseña incorrectos
          </div>
        <?php endif; ?>

        <form method="post" action="auth/comprobar_login.php">

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              required
            >
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              required
            >
          </div>

          <button type="submit" class="btn btn-primary w-100">
            Entrar
          </button>

        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
