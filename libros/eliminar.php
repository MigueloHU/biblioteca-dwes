<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";
require_once __DIR__ . "/../config/log.php";


// Obtener ID
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

// Si viene por POST → borrar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPost = (int)($_POST["id"] ?? 0);

    $sqlInfo = "SELECT titulo FROM libros WHERE id = :id LIMIT 1";
    $stmtInfo = $pdo->prepare($sqlInfo);
    $stmtInfo->execute([":id" => $idPost]);
    $info = $stmtInfo->fetch();
    $tituloLibro = $info ? $info["titulo"] : "";


    if ($idPost > 0) {
        $sql = "DELETE FROM libros WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":id" => $idPost]);
    }

    registrar_log($pdo, "BAJA", "libros", $idPost, (int)$_SESSION["usuario_id"], "Baja de libro: " . $tituloLibro);


    header("Location: " . APP_URL . "/index.php");
    exit;
}

// Si es GET → cargar datos del libro para confirmar
$sql = "SELECT titulo, autor FROM libros WHERE id = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$libro = $stmt->fetch();

if (!$libro) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3 text-danger">Eliminar libro</h2>

<div class="alert alert-danger">
    <strong>Atención:</strong> esta acción no se puede deshacer.
</div>

<p>
    ¿Seguro que deseas eliminar el libro:<br>
    <strong><?php echo htmlspecialchars($libro["titulo"]); ?></strong>
    (<?php echo htmlspecialchars($libro["autor"]); ?>)?
</p>

<form method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <button class="btn btn-danger">Sí, eliminar</button>
    <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/index.php">Cancelar</a>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>