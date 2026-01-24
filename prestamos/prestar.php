<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/conexion.php";

$libroId = isset($_GET["libro_id"]) ? (int)$_GET["libro_id"] : 0;

if ($libroId <= 0) {
    header("Location: " . APP_URL . "/index.php");
    exit;
}

$profesorId = (int)($_SESSION["usuario_id"] ?? 0);
if ($profesorId <= 0) {
    header("Location: " . APP_URL . "/login.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1) Comprobar estado del libro
    $sqlLibro = "SELECT id, titulo, estado
                FROM libros
                WHERE id = :id
                LIMIT 1";
    $stmt = $pdo->prepare($sqlLibro);
    $stmt->execute([":id" => $libroId]);
    $libro = $stmt->fetch();

    if (!$libro) {
        $pdo->rollBack();
        header("Location: " . APP_URL . "/index.php");
        exit;
    }

    if ($libro["estado"] !== "DISPONIBLE") {
        $pdo->rollBack();
        header("Location: " . APP_URL . "/index.php?error_prestamo=1");
        exit;
    }

    // 2) Insertar préstamo
    $sqlPrestamo = "INSERT INTO prestamos (profesor_id, libro_id, fecha_inicio, estado)
                    VALUES (:profesor_id, :libro_id, NOW(), 'ACTIVO')";
    $stmt = $pdo->prepare($sqlPrestamo);
    $stmt->execute([
        ":profesor_id" => $profesorId,
        ":libro_id" => $libroId
    ]);

    // 3) Marcar libro como PRESTADO
    $sqlUpdateLibro = "UPDATE libros SET estado = 'PRESTADO' WHERE id = :id";
    $stmt = $pdo->prepare($sqlUpdateLibro);
    $stmt->execute([":id" => $libroId]);

    $pdo->commit();

    header("Location: " . APP_URL . "/prestamos/index.php?ok=1");
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: " . APP_URL . "/prestamos/index.php?error=1");
    exit;
}
