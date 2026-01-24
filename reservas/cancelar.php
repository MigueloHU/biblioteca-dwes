<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$perfil = $_SESSION["perfil"] ?? "PROFESOR";
$profesorId = (int)($_SESSION["usuario_id"] ?? 0);

if ($id <= 0) {
    header("Location: " . APP_URL . "/reservas/index.php");
    exit;
}

// POST -> cancelar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idPost = (int)($_POST["id"] ?? 0);

    if ($idPost <= 0) {
        header("Location: " . APP_URL . "/reservas/index.php");
        exit;
    }

    try {
        if ($perfil === "ADMIN") {
            $sql = "UPDATE reservas
                    SET estado = 'CANCELADO'
                    WHERE id = :id AND estado = 'EN_ESPERA'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $idPost]);
        } else {
            $sql = "UPDATE reservas
                    SET estado = 'CANCELADO'
                    WHERE id = :id AND profesor_id = :profesor_id AND estado = 'EN_ESPERA'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":id" => $idPost,
                ":profesor_id" => $profesorId
            ]);
        }

        header("Location: " . APP_URL . "/reservas/index.php?cancelada=1");
        exit;

    } catch (PDOException $e) {
        header("Location: " . APP_URL . "/reservas/index.php?error=1");
        exit;
    }
}

// GET -> mostrar confirmación
$sql = "SELECT r.id, r.estado, l.titulo, l.autor
        FROM reservas r
        JOIN libros l ON l.id = r.libro_id
        WHERE r.id = :id
        LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$reserva = $stmt->fetch();

if (!$reserva || $reserva["estado"] !== "EN_ESPERA") {
    header("Location: " . APP_URL . "/reservas/index.php");
    exit;
}

require_once __DIR__ . "/../includes/header.php";
?>

<h2 class="h4 mb-3 text-danger">Cancelar reserva</h2>

<div class="alert alert-warning">
  Confirma la cancelación de la reserva.
</div>

<p class="mb-3">
  <strong>Libro:</strong> <?php echo htmlspecialchars($reserva["titulo"]); ?>
  (<?php echo htmlspecialchars($reserva["autor"]); ?>)
</p>

<form method="post">
  <input type="hidden" name="id" value="<?php echo (int)$reserva["id"]; ?>">
  <button class="btn btn-danger">Sí, cancelar</button>
  <a class="btn btn-outline-secondary" href="<?php echo APP_URL; ?>/reservas/index.php">Volver</a>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>
