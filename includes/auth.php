<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void
{
    if (!isset($_SESSION["usuario"])) {
        header("Location: /login.php");
        exit;
    }
}

function isAdmin(): bool
{
    return isset($_SESSION["usuario"]) && $_SESSION["usuario"]["perfil"] === "ADMIN";
}
