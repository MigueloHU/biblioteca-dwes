<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0 || $id === 1) {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

// POST -> activar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPost = (int)($_POST["id"] ?? 0);

    if ($idPost <= 0 || $idPost === 1) {
        header("Location: " . APP_URL . "/profesores/index.php");
        exit;
    }

    try {
        $sql = "UPDATE profesores SET estado = 1 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":id" => $idPost]);

        header("Location: " . APP_URL . "/profesores/index.php?ok=1");
        exit;
    } catch (PDOException $e) {
        header("Location: " . APP_URL . "/profesores/index.php?error=1");
        exit;
    }
}

// GET -> cargar datos y mostrar confirmación
$sql = "SELECT id, apellido1, apellido2, nombre, email, estado
        FROM profesores
        WHERE id = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$profe = $stmt->fetch();

if (!$profe || (int)$profe["estado"] === 1) {
    header("Location: " . APP_URL . "/profesores/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3 text-success">Activar profesor</h2>

<div class="alert alert-info">
  Confirma la activación del profesor.
</div>

<p class="mb-1"><strong>Profesor:</strong> <?php echo htmlspecialchars($profe["apellido1"] . " " . $profe["apellido2"] . ", " . $profe["nombre"]); ?></p>
<p class="mb-3"><strong>Email:</strong> <?php echo htmlspecialchars($profe["email"]); ?></p>

<form method="post">
  <input type="hidden" name="id" value="<?php echo (int)$profe["id"]; ?>">
  <button class="btn btn-success">Sí, activar</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/profesores/index.php">Cancelar</a>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
