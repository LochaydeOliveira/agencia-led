<?php
require_once 'includes/auth.php';

// Debug: registrar sessão antes do logout
file_put_contents('logout_debug.txt', date('Y-m-d H:i:s') . " - Antes do logout: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

logout();

// Debug: registrar sessão depois do logout (não será executado pois logout faz exit, mas deixo para referência)
// file_put_contents('logout_debug.txt', date('Y-m-d H:i:s') . " - Depois do logout: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
?> 