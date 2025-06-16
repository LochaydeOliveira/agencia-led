
<?php
require 'conexao.php';

$nome = "Cliente Teste";
$email = "teste@viviware.com.br";
$whatsapp = "85999999999";
$senha = password_hash("123456", PASSWORD_DEFAULT);
$status = "ativo";
$nivel = "cliente";

$sql = "INSERT INTO clientes (nome, email, whatsapp, senha, status, nivel_acesso) 
        VALUES (:nome, :email, :whatsapp, :senha, :status, :nivel)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":nome" => $nome,
    ":email" => $email,
    ":whatsapp" => $whatsapp,
    ":senha" => $senha,
    ":status" => $status,
    ":nivel" => $nivel,
]);

echo "Usuário cadastrado com sucesso!";
?>
