<?php
// auth/comprobar_login.php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit;
}

$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if ($email === "" || $password === "") {
    header("Location: ../login.php?error=1");
    exit;
}

require_once __DIR__ . "/../config/conexion.php";

/*
  OJO: si tu tabla NO usa estos nombres de columnas, habrá que ajustarlo.
  Por ahora asumo: profesores(id, email, password, nombre)
*/
$sql = "SELECT id, email, password, nombre
        FROM profesores
        WHERE email = :email
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":email" => $email]);
$prof = $stmt->fetch();

if (!$prof) {
    header("Location: ../login.php?error=1");
    exit;
}

$passBD = $prof["password"];

// Si la contraseña está hasheada, usa password_verify.
// Si confirmamos que NO es hash, comparamos en texto plano.
$info = password_get_info($passBD);
$ok = false;

if ($info["algo"] !== 0) {
    // Es un hash válido de password_hash
    $ok = password_verify($password, $passBD);
} else {
    // Texto plano
    $ok = ($password === $passBD);
}

if ($ok) {
    $_SESSION["usuario_id"] = $prof["id"];
    $_SESSION["email"] = $prof["email"];
    $_SESSION["nombre"] = $prof["nombre"];

    header("Location: ../index.php");
    exit;
}

header("Location: ../login.php?error=1");
exit;
