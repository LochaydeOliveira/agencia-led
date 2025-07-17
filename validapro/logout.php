<?php
// logout.php

require_once 'includes/auth.php';

// Executar a função de logout segura
logout();

// Redirecionar para a página de login
//header('Location: login.php');
//exit();
?>