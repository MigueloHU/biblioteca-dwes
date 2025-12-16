<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si NO hay sesión iniciada, vuelve al login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: /biblioteca/login.php");
    exit;
}
