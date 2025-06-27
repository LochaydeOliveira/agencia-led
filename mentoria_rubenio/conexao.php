<?php
$host = 'localhost';
$usuario = 'paymen58';
$senha = 'u4q7+B6ly)obP_gxN9sNe';
$banco = 'paymen58_sistema_integrado_led';

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4'); 