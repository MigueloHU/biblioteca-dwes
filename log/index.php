<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../includes/header.php";

$porPagina = 10;
$pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $porPagina;

// Total
$stmt = $pdo->query("CALL sp_log_total()");
$row = $stmt->fetch();
$total = (int)($row["total"] ?? 0);
$stmt->closeCursor();

$totalPaginas = (int)ceil($total / $porPagina);

// Listado
$stmt = $pdo->prepare("CALL sp_log_listar(:inicio, :cantidad)");
$stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
$stmt->bindValue(":cantidad", $porPagina, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();
$stmt->closeCursor();
?>

<h2 class="h4 mb-3">Log de actividad</h2>

<div class="card shadow-sm">
  <div class="card-body">

    <?php if (count($logs) === 0): ?>
      <div class="alert alert-info mb-0">No hay acciones registradas.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Fecha/Hora</th>
              <th>Tipo</th>
              <th>Tabla</th>
              <th>Registro</th>
              <th>Usuario</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $l): ?>
              <tr>
                <td><?php echo htmlspecialchars($l["id"]); ?></td>
                <td><?php echo htmlspecialchars($l["fecha_hora"]); ?></td>
                <td><?php echo htmlspecialchars($l["tipo"]); ?></td>
                <td><?php echo htmlspecialchars($l["tabla_afectada"]); ?></td>
                <td><?php echo htmlspecialchars($l["registro_id"] ?? "-"); ?></td>
                <td><?php echo htmlspecialchars($l["email"]); ?></td>
                <td><?php echo htmlspecialchars($l["descripcion"] ?? ""); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <nav class="mt-3">
        <ul class="pagination justify-content-center mb-0">
          <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">&laquo;</a>
          </li>
          <li class="page-item disabled">
            <span class="page-link">Página <?php echo $pagina; ?> de <?php echo max(1, $totalPaginas); ?></span>
          </li>
          <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">&raquo;</a>
          </li>
        </ul>
      </nav>

    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
