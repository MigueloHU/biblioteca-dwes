<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

$sql = "SELECT *
        FROM libros
        WHERE id = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$libro = $stmt->fetch();

if (!$libro) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3">Detalle del libro</h2>

<div class="card shadow-sm">
  <div class="card-body">

    <div class="row g-4">

      <!-- Portada -->
      <div class="col-md-3 text-center">
        <?php if (!empty($libro["portada"])): ?>
          <img
            src="<?php echo APP_URL . '/' . ltrim($libro["portada"], '/'); ?>"
            alt="Portada"
            class="img-fluid rounded shadow-sm"
          >
        <?php else: ?>
          <div class="text-muted">Sin portada</div>
        <?php endif; ?>
      </div>

      <!-- Datos -->
      <div class="col-md-9">

        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($libro["isbn"]); ?></p>
        <p><strong>Título:</strong> <?php echo htmlspecialchars($libro["titulo"]); ?></p>
        <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro["autor"]); ?></p>
        <p><strong>Género:</strong> <?php echo htmlspecialchars($libro["genero"] ?? "-"); ?></p>
        <p><strong>Fecha publicación:</strong> <?php echo htmlspecialchars($libro["fecha_publicacion"] ?? "-"); ?></p>
        <p><strong>Editorial:</strong> <?php echo htmlspecialchars($libro["editorial"] ?? "-"); ?></p>
        <p><strong>Precio:</strong>
          <?php
            if ($libro["precio"] === null) {
                echo "-";
            } else {
                echo number_format((float)$libro["precio"], 2) . " €";
            }
          ?>
        </p>

        <p><strong>Descripción:</strong></p>
        <div class="border rounded p-2 bg-light">
          <?php echo nl2br(htmlspecialchars($libro["descripcion"] ?? "")); ?>
        </div>

      </div>
    </div>

    <hr>

    <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/index.php">Volver</a>
    <a class="btn btn-outline-primary" href="<?php echo APP_URL; ?>/libros/editar.php?id=<?php echo urlencode($libro["id"]); ?>">Editar</a>

  </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
