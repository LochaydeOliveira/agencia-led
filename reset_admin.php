<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'conexao.php';

try {
    // Resetar senha do admin existente
    $nova_senha = 'admin123'; // Senha temporária
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE nivel = 'admin'");
    $stmt->execute([$senha_hash]);
    
    echo "Senha do admin existente foi resetada para: " . $nova_senha . "<br>";
    
    // Criar novo usuário admin
    $novo_admin = [
        'nome' => 'Novo Admin',
        'email' => 'novo.admin@empresa.com',
        'senha' => password_hash('admin456', PASSWORD_DEFAULT),
        'nivel' => 'admin',
        'status' => 'ativo'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $novo_admin['nome'],
        $novo_admin['email'],
        $novo_admin['senha'],
        $novo_admin['nivel'],
        $novo_admin['status']
    ]);
    
    echo "Novo usuário admin criado com sucesso!<br>";
    echo "Nome de usuário: " . $novo_admin['nome'] . "<br>";
    echo "Email: " . $novo_admin['email'] . "<br>";
    echo "Senha: admin456<br>";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?> 