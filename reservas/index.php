<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../includes/header.php";

$perfil = $_SESSION["perfil"] ?? "PROFESOR";
$profesorId = (int)($_SESSION["usuario_id"] ?? 0);

if ($perfil === "ADMIN") {
    $sql = "SELECT r.id, r.fecha, r.estado,
                   l.titulo, l.autor,
                   p.email
            FROM reservas r
            JOIN libros l ON l.id = r.libro_id
            JOIN profesores p ON p.id = r.profesor_id
            ORDER BY r.id DESC";
    $stmt = $pdo->query($sql);
    $reservas = $stmt->fetchAll();
} else {
    $sql = "SELECT r.id, r.fecha, r.estado,
                   l.titulo, l.autor
            FROM reservas r
            JOIN libros l ON l.id = r.libro_id
            WHERE r.profesor_id = :profesor_id
            ORDER BY r.id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":profesor_id" => $profesorId]);
    $reservas = $stmt->fetchAll();
}
?>

<h2 class="h4 mb-3">Reservas</h2>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Reserva registrada correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["ya"])): ?>
  <div class="alert alert-warning">Ya tienes una reserva activa (en espera) para ese libro.</div>
<?php endif; ?>

<?php if (isset($_GET["cancelada"])): ?>
  <div class="alert alert-success">Reserva cancelada correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">No se pudo completar la operación.</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">

    <?php if (count($reservas) === 0): ?>
      <div class="alert alert-info mb-0">No hay reservas registradas.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Libro</th>
              <?php if ($perfil === "ADMIN"): ?>
                <th>Profesor</th>
              <?php endif; ?>
              <th>Fecha</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservas as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r["id"]); ?></td>
                <td><?php echo htmlspecialchars($r["titulo"]); ?> (<?php echo htmlspecialchars($r["autor"]); ?>)</td>

                <?php if ($perfil === "ADMIN"): ?>
                  <td><?php echo htmlspecialchars($r["email"]); ?></td>
                <?php endif; ?>

                <td><?php echo htmlspecialchars($r["fecha"]); ?></td>
                <td><?php echo htmlspecialchars($r["estado"]); ?></td>

                <td class="text-end">
                  <?php if ($r["estado"] === "EN_ESPERA"): ?>
                    <a class="btn btn-sm btn-outline-danger"
                       href="<?php echo APP_URL; ?>/reservas/cancelar.php?id=<?php echo urlencode($r["id"]); ?>">
                      Cancelar
                    </a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
