<?php
// config/log.php

function registrar_log(PDO $pdo, string $tipo, string $tabla, ?int $registroId, int $profesorId, ?string $descripcion = null): void
{
    $tipo = strtoupper(trim($tipo));
    $tabla = trim($tabla);
    $registroId = $registroId ?? null;
    $descripcion = $descripcion !== null ? trim($descripcion) : null;

    $stmt = $pdo->prepare("CALL sp_log_insertar(:tipo, :tabla, :registro_id, :profesor_id, :descripcion)");
    $stmt->execute([
        ":tipo" => $tipo,
        ":tabla" => $tabla,
        ":registro_id" => $registroId,
        ":profesor_id" => $profesorId,
        ":descripcion" => $descripcion
    ]);

    // Importante con CALL: cerrar cursor para poder hacer más queries después
    $stmt->closeCursor();
}
