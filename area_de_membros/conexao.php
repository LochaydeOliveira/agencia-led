<?php
$host = "192.185.222.27";
$db = "paymen58_area_de_membros";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>