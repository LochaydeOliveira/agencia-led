<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>
<h2>Bem-vindo à área de membros!</h2>
<p>Você está logado com sucesso.</p>
<a href='logout.php'>Sair</a>