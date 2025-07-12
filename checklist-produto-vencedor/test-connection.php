<?php
// Teste de conexão com o banco de dados
$host = "localhost";
$db = "paymen58_checklist_produto_lucrativo";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

echo "<h2>Teste de Conexão com Banco de Dados</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color: green;'>✅ Conexão com banco de dados estabelecida com sucesso!</p>";
    
    // Verificar se as tabelas existem
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Tabelas encontradas:</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Verificar usuários
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $userCount = $stmt->fetch()['total'];
    echo "<p><strong>Total de usuários:</strong> $userCount</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Erro na conexão: " . $e->getMessage() . "</p>";
    
    // Tentar conectar sem especificar o banco para verificar se o usuário tem permissão
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
        echo "<p style='color: orange;'>⚠️ Usuário conecta ao MySQL, mas banco '$db' pode não existir</p>";
        
        // Listar bancos disponíveis
        $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Bancos disponíveis:</strong></p>";
        echo "<ul>";
        foreach ($databases as $database) {
            echo "<li>$database</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e2) {
        echo "<p style='color: red;'>❌ Erro ao conectar ao MySQL: " . $e2->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>← Voltar para o login</a></p>";
?> 