<?php
session_name('VALIDAPRO_TESTE'); // ESSENCIAL
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/auth.php';
logout();
?> 