<?php
// Logout ValidaPro - Versão 2.0
// Sistema simples e confiável

// Incluir sistema de autenticação
require_once 'includes/auth.php';

// Executar logout
logout();

// Se chegou até aqui, algo deu errado
// Redirecionar manualmente
if (!headers_sent()) {
    header('Location: login.php');
} else {
    echo '<script>window.location.href = "login.php";</script>';
}
exit();
?> 