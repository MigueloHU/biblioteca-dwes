<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../includes/header.php";

$sql = "SELECT id, apellido1, apellido2, nombre, email, perfil, avatar, estado
        FROM profesores
        ORDER BY id ASC";
$stmt = $pdo->query($sql);
$profes = $stmt->fetchAll();
?>

<h2 class="h4 mb-3">Profesores</h2>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="text-muted small">
    Gestión de usuarios (alta, edición y desactivación).
  </div>
  <a class="btn btn-primary" href="<?php echo APP_URL; ?>/profesores/crear.php">+ Nuevo profesor</a>
  <a class="btn btn-outline-primary" href="<?php echo APP_URL; ?>/profesores/importar.php">Importar Excel</a>

</div>

<?php if (isset($_GET["ok"])): ?>
  <div class="alert alert-success">Operación realizada correctamente.</div>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
  <div class="alert alert-danger">No se pudo completar la operación.</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">

    <?php if (count($profes) === 0): ?>
      <div class="alert alert-info mb-0">No hay profesores registrados.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Apellidos</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Perfil</th>
              <th>Estado</th>
              <th class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($profes as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p["id"]); ?></td>
                <td><?php echo htmlspecialchars($p["apellido1"] . " " . $p["apellido2"]); ?></td>
                <td><?php echo htmlspecialchars($p["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($p["email"]); ?></td>
                <td><?php echo htmlspecialchars($p["perfil"]); ?></td>
                <td>
                  <?php if ((int)$p["estado"] === 1): ?>
                    <span class="badge bg-success">ACTIVO</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">INACTIVO</span>
                  <?php endif; ?>
                </td>

                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary"
                     href="<?php echo APP_URL; ?>/profesores/detalle.php?id=<?php echo urlencode($p["id"]); ?>">Ver</a>

                  <a class="btn btn-sm btn-outline-primary"
                     href="<?php echo APP_URL; ?>/profesores/editar.php?id=<?php echo urlencode($p["id"]); ?>">Editar</a>

                  <?php if ((int)$p["id"] !== 1): ?>
                    <?php if ((int)$p["estado"] === 1): ?>
                      <a class="btn btn-sm btn-outline-danger"
                         href="<?php echo APP_URL; ?>/profesores/desactivar.php?id=<?php echo urlencode($p["id"]); ?>">
                         Desactivar
                      </a>
                    <?php else: ?>
                      <a class="btn btn-sm btn-outline-success"
                         href="<?php echo APP_URL; ?>/profesores/activar.php?id=<?php echo urlencode($p["id"]); ?>">
                         Activar
                      </a>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="text-muted small">Admin</span>
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
