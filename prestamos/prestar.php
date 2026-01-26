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

    // 1) Comprobar que el libro existe y su estado actual
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

    // 2) Comprobar por BD (fuente de verdad): ¿ya hay un préstamo ACTIVO para este libro?
    // Esto evita incoherencias si libros.estado estuviera desincronizado.
    $sqlActivo = "SELECT COUNT(*)
                  FROM prestamos
                  WHERE libro_id = :libro_id AND estado = 'ACTIVO'";
    $stmt = $pdo->prepare($sqlActivo);
    $stmt->execute([":libro_id" => $libroId]);
    $hayActivo = ((int)$stmt->fetchColumn() > 0);

    if ($hayActivo) {
        // Aseguramos coherencia del campo estado (por si acaso)
        $stmt = $pdo->prepare("UPDATE libros SET estado = 'PRESTADO' WHERE id = :id");
        $stmt->execute([":id" => $libroId]);

        $pdo->commit();
        header("Location: " . APP_URL . "/index.php?error_prestamo=1");
        exit;
    }

    // 3) Comprobar estado del libro (si no está DISPONIBLE, no prestar)
    if ($libro["estado"] !== "DISPONIBLE") {
        $pdo->rollBack();
        header("Location: " . APP_URL . "/index.php?error_prestamo=1");
        exit;
    }

    // 4) Insertar préstamo ACTIVO
    $sqlPrestamo = "INSERT INTO prestamos (profesor_id, libro_id, fecha_inicio, estado)
                    VALUES (:profesor_id, :libro_id, NOW(), 'ACTIVO')";
    $stmt = $pdo->prepare($sqlPrestamo);
    $stmt->execute([
        ":profesor_id" => $profesorId,
        ":libro_id" => $libroId
    ]);

    // 5) Marcar libro como PRESTADO (coherencia)
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
