<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/includes/header.php";

$id = (int)($_SESSION["usuario_id"] ?? 0);

$sql = "SELECT id, apellido1, apellido2, nombre, email, perfil, avatar, estado
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
?>

<h2 class="h4 mb-3">Mi perfil</h2>

<div class="card shadow-sm">
  <div class="card-body">
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($u["nombre"]); ?></p>
    <p><strong>Apellidos:</strong> <?php echo htmlspecialchars($u["apellido1"] . " " . $u["apellido2"]); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($u["email"]); ?></p>
    <p><strong>Perfil:</strong> <?php echo htmlspecialchars($u["perfil"]); ?></p>
    <p><strong>Estado:</strong> <?php echo ((int)$u["estado"] === 1) ? "ACTIVO" : "INACTIVO"; ?></p>

    <p class="mb-1"><strong>Avatar:</strong></p>
    <?php if (!empty($u["avatar"])): ?>
      <img src="<?php echo APP_URL . '/' . ltrim($u["avatar"], '/'); ?>" alt="Avatar" style="height:80px;" class="rounded">
    <?php else: ?>
      <div class="text-muted">Sin avatar</div>
    <?php endif; ?>

    <hr>

    <a class="btn btn-primary" href="<?php echo APP_URL; ?>/perfil_editar.php">Editar perfil</a>
  </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
