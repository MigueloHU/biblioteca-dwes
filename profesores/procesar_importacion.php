<?php
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../includes/admin.php";
require_once __DIR__ . "/../config/conexion.php";

// Cargar autoload de Composer (PhpSpreadsheet)
require_once __DIR__ . "/../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . APP_URL . "/profesores/importar.php");
    exit;
}

if (!isset($_FILES["excel"]) || $_FILES["excel"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: " . APP_URL . "/profesores/importar.php?error=1");
    exit;
}

$tmpPath = $_FILES["excel"]["tmp_name"];
$nombreOriginal = $_FILES["excel"]["name"] ?? "";

$ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
if ($ext !== "xlsx") {
    header("Location: " . APP_URL . "/profesores/importar.php?error=1");
    exit;
}

try {
    $spreadsheet = IOFactory::load($tmpPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true); // columnas A,B,C...

    if (count($rows) < 2) {
        header("Location: " . APP_URL . "/profesores/importar.php?error=1");
        exit;
    }

    // --- Cabeceras (fila 1) ---
    // Esperamos nombres tipo: id, apellido1, apellido2, nombre, email, perfil, avatar, estado
    $header = $rows[1];
    $map = []; // "id" => "A", "apellido1" => "B", ...

    foreach ($header as $col => $name) {
        $k = strtolower(trim((string)$name));
        if ($k !== "") {
            $map[$k] = $col;
        }
    }

    $obligatorias = ["apellido1","apellido2","nombre","email","perfil","estado"];
    foreach ($obligatorias as $ob) {
        if (!isset($map[$ob])) {
            header("Location: " . APP_URL . "/profesores/importar.php?error=1");
            exit;
        }
    }

    // Columnas opcionales
    $colId = $map["id"] ?? null;
    $colAvatar = $map["avatar"] ?? null;

    $insertados = 0;
    $actualizados = 0;
    $saltados = 0;

    $pdo->beginTransaction();

    // Preparar queries
    $sqlExisteEmail = "SELECT id FROM profesores WHERE email = :email LIMIT 1";
    $stmtExiste = $pdo->prepare($sqlExisteEmail);

    $sqlInsert = "INSERT INTO profesores (apellido1, apellido2, nombre, email, password, perfil, avatar, estado)
                  VALUES (:apellido1, :apellido2, :nombre, :email, :password, :perfil, :avatar, :estado)";
    $stmtInsert = $pdo->prepare($sqlInsert);

    $sqlUpdate = "UPDATE profesores
                  SET apellido1 = :apellido1,
                      apellido2 = :apellido2,
                      nombre = :nombre,
                      perfil = :perfil,
                      avatar = :avatar,
                      estado = :estado
                  WHERE id = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);

    // Recorremos desde la fila 2
    for ($i = 2; $i <= count($rows); $i++) {
        $r = $rows[$i];

        $idExcel = $colId ? (int)trim((string)($r[$colId] ?? "")) : 0;

        // Nunca tocar el admin id=1
        if ($idExcel === 1) {
            $saltados++;
            continue;
        }

        $apellido1 = trim((string)($r[$map["apellido1"]] ?? ""));
        $apellido2 = trim((string)($r[$map["apellido2"]] ?? ""));
        $nombre    = trim((string)($r[$map["nombre"]] ?? ""));
        $email     = trim((string)($r[$map["email"]] ?? ""));
        $perfilRaw = strtoupper(trim((string)($r[$map["perfil"]] ?? "PROFESOR")));
        $estadoRaw = trim((string)($r[$map["estado"]] ?? "1"));
        $avatar    = $colAvatar ? trim((string)($r[$colAvatar] ?? "")) : "";

        // Validación mínima
        if ($apellido1 === "" || $apellido2 === "" || $nombre === "" || $email === "") {
            $saltados++;
            continue;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $saltados++;
            continue;
        }

        $perfil = ($perfilRaw === "ADMIN") ? "ADMIN" : "PROFESOR";
        $estado = ($estadoRaw === "0" || strtoupper($estadoRaw) === "INACTIVO") ? 0 : 1;

        // ¿Existe por email?
        $stmtExiste->execute([":email" => $email]);
        $existe = $stmtExiste->fetch();

        if ($existe) {
            $idBd = (int)$existe["id"];
            if ($idBd === 1) {
                $saltados++;
                continue;
            }

            $stmtUpdate->execute([
                ":apellido1" => $apellido1,
                ":apellido2" => $apellido2,
                ":nombre" => $nombre,
                ":perfil" => $perfil,
                ":avatar" => ($avatar !== "" ? $avatar : null),
                ":estado" => $estado,
                ":id" => $idBd
            ]);
            $actualizados++;
        } else {
            // Password por defecto para importados (puedes cambiarlo)
            $passwordDefecto = "profe";

            $stmtInsert->execute([
                ":apellido1" => $apellido1,
                ":apellido2" => $apellido2,
                ":nombre" => $nombre,
                ":email" => $email,
                ":password" => $passwordDefecto,
                ":perfil" => $perfil,
                ":avatar" => ($avatar !== "" ? $avatar : null),
                ":estado" => $estado
            ]);
            $insertados++;
        }
    }

    $pdo->commit();

    // Si quieres, puedes pasar resumen por GET, pero de momento redirigimos simple.
    header("Location: " . APP_URL . "/profesores/index.php?ok=1");
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: " . APP_URL . "/profesores/importar.php?error=1");
    exit;
}
