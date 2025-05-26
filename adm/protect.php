<?php
session_start();
if (!isset($_SESSION['usuario']) || ($_SESSION['nivel'] !== 'admin' && $_SESSION['nivel'] !== 'operador')) {
    header("Location: ../login_usuarios.php");
    exit;
}
?>
