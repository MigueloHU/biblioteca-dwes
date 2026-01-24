<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: " . APP_URL . "/prestamos/index.php");
    exit;
}

// Si POST -> devolver
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPost = (int)($_POST["id"] ?? 0);

    if ($idPost <= 0) {
        header("Location: " . APP_URL . "/prestamos/index.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1) Obtener el préstamo y el libro asociado
        $sqlP = "SELECT id, libro_id, estado
                 FROM prestamos
                 WHERE id = :id
                 LIMIT 1";
        $stmt = $pdo->prepare($sqlP);
        $stmt->execute([":id" => $idPost]);
        $prestamo = $stmt->fetch();

        if (!$prestamo) {
            $pdo->rollBack();
            header("Location: " . APP_URL . "/prestamos/index.php?error=1");
            exit;
        }

        if ($prestamo["estado"] !== "ACTIVO") {
            $pdo->rollBack();
            header("Location: " . APP_URL . "/prestamos/index.php?error=1");
            exit;
        }

        $libroId = (int)$prestamo["libro_id"];

        // 2) Marcar préstamo como DEVUELTO
        $sqlU = "UPDATE prestamos
                 SET estado = 'DEVUELTO',
                     fecha_fin = NOW()
                 WHERE id = :id";
        $stmt = $pdo->prepare($sqlU);
        $stmt->execute([":id" => $idPost]);

        // 3) Poner libro como DISPONIBLE
        $sqlL = "UPDATE libros
                 SET estado = 'DISPONIBLE'
                 WHERE id = :id";
        $stmt = $pdo->prepare($sqlL);
        $stmt->execute([":id" => $libroId]);

        $pdo->commit();

        header("Location: " . APP_URL . "/prestamos/index.php?devuelto=1");
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header("Location: " . APP_URL . "/prestamos/index.php?error=1");
        exit;
    }
}

// GET -> mostrar confirmación (cargar datos)
$sql = "SELECT p.id, p.estado, p.fecha_inicio,
               l.titulo, l.autor
        FROM prestamos p
        JOIN libros l ON l.id = p.libro_id
        WHERE p.id = :id
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$p = $stmt->fetch();

if (!$p || $p["estado"] !== "ACTIVO") {
    header("Location: " . APP_URL . "/prestamos/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3 text-danger">Devolver libro</h2>

<div class="alert alert-warning">
  Confirma la devolución del préstamo. Esta acción registrará la fecha de fin.
</div>

<p class="mb-1"><strong>Libro:</strong> <?php echo htmlspecialchars($p["titulo"]); ?> (<?php echo htmlspecialchars($p["autor"]); ?>)</p>
<p class="mb-3"><strong>Fecha inicio:</strong> <?php echo htmlspecialchars($p["fecha_inicio"]); ?></p>

<form method="post">
  <input type="hidden" name="id" value="<?php echo (int)$p["id"]; ?>">
  <button class="btn btn-danger">Sí, devolver</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/prestamos/index.php">Cancelar</a>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
