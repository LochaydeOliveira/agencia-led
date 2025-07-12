<?php
// Teste completo do banco de dados e criação do usuário admin
$host = "localhost";
$db = "paymen58_checklist_produto_lucrativo";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

echo "<h2>Teste Completo - Banco de Dados Checklist</h2>";

try {
    // 1. Conectar sem especificar banco
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Conexão com MySQL estabelecida</p>";
    
    // 2. Verificar se o banco existe
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db'");
    $databaseExists = $stmt->fetch();
    
    if (!$databaseExists) {
        // Criar o banco
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: blue;'>📁 Banco de dados '$db' criado</p>";
    } else {
        echo "<p style='color: green;'>✅ Banco de dados '$db' já existe</p>";
    }
    
    // 3. Conectar ao banco específico
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Conectado ao banco '$db'</p>";
    
    // 4. Criar tabelas
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ Tabela 'users' criada/verificada</p>";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            promessa_principal TEXT,
            cliente_consciente TEXT,
            beneficios TEXT,
            mecanismo_unico TEXT,
            pontos INT DEFAULT 0,
            nota_final INT DEFAULT 0,
            mensagem VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ Tabela 'results' criada/verificada</p>";
    
    // 5. Verificar se o usuário admin existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@exemplo.com']);
    $userExists = $stmt->fetchColumn();
    
    if ($userExists == 0) {
        // Criar usuário admin
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute(['admin@exemplo.com', $hashed_password, 'Administrador']);
        echo "<p style='color: blue;'>👤 Usuário admin criado</p>";
    } else {
        // Atualizar senha do usuário admin
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, name = ? WHERE email = ?");
        $stmt->execute([$hashed_password, 'Administrador', 'admin@exemplo.com']);
        echo "<p style='color: green;'>✅ Usuário admin atualizado</p>";
    }
    
    // 6. Verificar usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $userCount = $stmt->fetch()['total'];
    echo "<p><strong>Total de usuários:</strong> $userCount</p>";
    
    // 7. Listar usuários
    $stmt = $pdo->query("SELECT id, email, name, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Usuários cadastrados:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Nome</th><th>Criado em</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td>" . $user['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Tudo configurado com sucesso!</h3>";
    echo "<p><strong>Credenciais de acesso:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@exemplo.com</li>";
    echo "<li><strong>Senha:</strong> 123456</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Ir para o login</a></p>";
echo "<p><a href='teste-simples.php'>← Teste simples</a></p>";
?> 