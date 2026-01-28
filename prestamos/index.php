<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../includes/header.php";

// Listar préstamos
$sql = "SELECT p.id, p.fecha_inicio, p.fecha_fin, p.estado,
               l.titulo, l.autor,
               pr.email
        FROM prestamos p
        JOIN libros l ON l.id = p.libro_id
        JOIN profesores pr ON pr.id = p.profesor_id
        ORDER BY p.id DESC";

$stmt = $pdo->query($sql);
$prestamos = $stmt->fetchAll();
?>

<h2 class="h4 mb-3">Préstamos</h2>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Préstamo registrado correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">No se pudo registrar el préstamo.</div>
<?php endif; ?>

<?php if (isset($_GET["devuelto"])): ?>
  <div class="alert alert-success">Libro devuelto correctamente.</div>
<?php endif; ?>


<div class="card shadow-sm">
  <div class="card-body">

    <?php if (count($prestamos) === 0): ?>
      <div class="alert alert-info mb-0">No hay préstamos registrados.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Libro</th>
              <th>Profesor</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($prestamos as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p["id"]); ?></td>
                <td><?php echo htmlspecialchars($p["titulo"]); ?> (<?php echo htmlspecialchars($p["autor"]); ?>)</td>
                <td><?php echo htmlspecialchars($p["email"]); ?></td>
                <td><?php echo htmlspecialchars($p["fecha_inicio"]); ?></td>
                <td><?php echo htmlspecialchars($p["fecha_fin"] ?? "-"); ?></td>
                <td><?php echo htmlspecialchars($p["estado"]); ?></td>
                <td class="text-end">
                  <?php if ($p["estado"] === "ACTIVO"): ?>
                    <a class="btn btn-sm btn-outline-danger"
                       href="<?php echo APP_URL; ?>/prestamos/devolver.php?id=<?php echo urlencode($p["id"]); ?>">
                       Devolver
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
