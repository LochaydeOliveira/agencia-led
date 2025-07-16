<?php
file_put_contents('logout_debug.txt', date('Y-m-d H:i:s') . " - Logout chamado\n", FILE_APPEND);
require_once 'includes/auth.php';
logout();
?> 