<?php

require_once __DIR__ . "/../config/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["usuario_id"])) {
    header("Location: " . APP_URL . "/login.php");
    exit;
}

if (!isset($_SESSION["perfil"]) || $_SESSION["perfil"] !== "ADMIN") {
    header("Location: " . APP_URL . "/index.php?sin_permiso=1");
    exit;
}
