<?php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/conexion.php";
require_once __DIR__ . "/includes/header.php";

// ----------------------
// PAGINACIÓN
// ----------------------
$porPagina = 5;
$pagina = isset($_GET["pagina"]) ? (int)$_GET["pagina"] : 1;
if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $porPagina;

// Total de libros
$sqlTotal = "SELECT COUNT(*) FROM libros";
$totalLibros = (int)$pdo->query($sqlTotal)->fetchColumn();
$totalPaginas = (int)ceil($totalLibros / $porPagina);

// Libros de la página actual + disponibilidad calculada por préstamos activos
$sql = "SELECT
          l.id, l.isbn, l.titulo, l.fecha_publicacion, l.editorial, l.precio, l.portada,
          CASE
            WHEN EXISTS (
              SELECT 1
              FROM prestamos p
              WHERE p.libro_id = l.id AND p.estado = 'ACTIVO'
            )
            THEN 'PRESTADO'
            ELSE 'DISPONIBLE'
          END AS disponibilidad
        FROM libros l
        ORDER BY l.id DESC
        LIMIT :inicio, :cantidad";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
$stmt->bindValue(":cantidad", $porPagina, PDO::PARAM_INT);
$stmt->execute();
$libros = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 m-0">Libros</h2>
    <a class="btn btn-primary" href="<?php echo APP_URL; ?>/libros/crear.php">+ Nuevo libro</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <?php if (count($libros) === 0): ?>
            <div class="alert alert-info mb-0">No hay libros registrados.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ISBN</th>
                            <th>Título</th>
                            <th>Fecha</th>
                            <th>Editorial</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Portada</th>
                            <th>Disponibilidad</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($libros as $l): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($l["isbn"]); ?></td>
                                <td><?php echo htmlspecialchars($l["titulo"]); ?></td>
                                <td><?php echo htmlspecialchars($l["fecha_publicacion"] ?? ""); ?></td>
                                <td><?php echo htmlspecialchars($l["editorial"] ?? ""); ?></td>

                                <td class="text-end">
                                    <?php
                                    if ($l["precio"] === null) {
                                        echo "-";
                                    } else {
                                        echo number_format((float)$l["precio"], 2) . " €";
                                    }
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php if (!empty($l["portada"])): ?>
                                        <img
                                            src="<?php echo APP_URL . '/' . ltrim($l["portada"], '/'); ?>"
                                            alt="Portada"
                                            style="height:40px;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (($l["disponibilidad"] ?? "") === "DISPONIBLE"): ?>
                                        <span class="badge bg-success">DISPONIBLE</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">PRESTADO</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="<?php echo APP_URL; ?>/libros/detalle.php?id=<?php echo urlencode($l["id"]); ?>">Ver</a>

                                    <?php if (($l["disponibilidad"] ?? "") === "DISPONIBLE"): ?>
                                        <a class="btn btn-sm btn-outline-success"
                                           href="<?php echo APP_URL; ?>/prestamos/prestar.php?libro_id=<?php echo urlencode($l["id"]); ?>">
                                            Prestar
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-success" disabled>Prestar</button>

                                        <a class="btn btn-sm btn-outline-warning"
                                           href="<?php echo APP_URL; ?>/reservas/reservar.php?libro_id=<?php echo urlencode($l["id"]); ?>">
                                            Reservar
                                        </a>
                                    <?php endif; ?>

                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?php echo APP_URL; ?>/libros/editar.php?id=<?php echo urlencode($l["id"]); ?>">Editar</a>

                                    <a class="btn btn-sm btn-outline-danger"
                                       href="<?php echo APP_URL; ?>/libros/eliminar.php?id=<?php echo urlencode($l["id"]); ?>">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINACIÓN -->
            <nav class="mt-3">
                <ul class="pagination justify-content-center mb-0">

                    <li class="page-item <?php echo ($pagina <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>">&laquo;</a>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">
                            Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?>
                        </span>
                    </li>

                    <li class="page-item <?php echo ($pagina >= $totalPaginas) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>">&raquo;</a>
                    </li>

                </ul>
            </nav>

        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
