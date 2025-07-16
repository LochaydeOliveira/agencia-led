<?php
session_start();
setcookie(session_name(), '', time() - 3600, '/');
echo "Cookie de sessão removido.";
?> 