<?php
session_start();
$_SESSION['teste'] = 'funciona';
echo 'Sessão: ' . session_id();
?>
