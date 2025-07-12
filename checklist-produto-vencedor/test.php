<?php
// Arquivo de teste para diagnosticar problemas
echo "<h1>🔍 Teste de Diagnóstico - Checklist do Produto</h1>";

// 1. Teste de versão do PHP
echo "<h2>1. Versão do PHP</h2>";
echo "Versão atual: " . phpversion() . "<br>";
echo "Status: " . (version_compare(PHP_VERSION, '7.4.0') >= 0 ? "✅ OK" : "❌ Versão muito antiga") . "<br><br>";

// 2. Teste de extensões necessárias
echo "<h2>2. Extensões PHP</h2>";
$extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($extensions as $ext) {
    echo "$ext: " . (extension_loaded($ext) ? "✅ Carregada" : "❌ Não carregada") . "<br>";
}
echo "<br>";

// 3. Teste de conexão com banco de dados
echo "<h2>3. Teste de Conexão com Banco de Dados</h2>";
try {
    $host = "localhost";
    $db = "paymen58_checklist_produto_lucrativo";
    $user = "paymen58";
    $pass = "u4q7+B6ly)obP_gxN9sNe";
    
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexão com banco de dados estabelecida com sucesso!<br>";
    
    // Testar se as tabelas existem
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tabelas encontradas: " . implode(', ', $tables) . "<br>";
    
    if (in_array('users', $tables)) {
        echo "✅ Tabela 'users' existe<br>";
        
        // Contar usuários
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "Número de usuários: $userCount<br>";
    } else {
        echo "❌ Tabela 'users' não existe<br>";
    }
    
    if (in_array('results', $tables)) {
        echo "✅ Tabela 'results' existe<br>";
    } else {
        echo "❌ Tabela 'results' não existe<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Erro na conexão com banco: " . $e->getMessage() . "<br>";
}
echo "<br>";

// 4. Teste de permissões de arquivo
echo "<h2>4. Permissões de Arquivo</h2>";
$files = [
    'index.php' => 'Arquivo principal',
    'includes/db.php' => 'Conexão com banco',
    'includes/auth.php' => 'Autenticação',
    'dashboard.php' => 'Dashboard',
    'resultado.php' => 'Resultado'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description ($file) - Existe<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;Permissões: " . substr(sprintf('%o', fileperms($file)), -4) . "<br>";
    } else {
        echo "❌ $description ($file) - Não encontrado<br>";
    }
}
echo "<br>";

// 5. Teste de sessões
echo "<h2>5. Teste de Sessões</h2>";
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Sessões funcionando<br>";
} else {
    echo "❌ Problema com sessões<br>";
}
echo "<br>";

// 6. Informações do servidor
echo "<h2>6. Informações do Servidor</h2>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __FILE__ . "<br>";
echo "Current Directory: " . getcwd() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<br>";

// 7. Teste de redirecionamento
echo "<h2>7. Links de Teste</h2>";
echo "<a href='index.php' style='color: blue; text-decoration: underline;'>🔗 Testar index.php</a><br>";
echo "<a href='dashboard.php' style='color: blue; text-decoration: underline;'>🔗 Testar dashboard.php</a><br>";
echo "<a href='admin/add_user.php' style='color: blue; text-decoration: underline;'>🔗 Testar admin/add_user.php</a><br>";
echo "<br>";

// 8. Criar usuário de teste se necessário
echo "<h2>8. Criar Usuário de Teste</h2>";
if (isset($pdo) && !in_array('users', $tables)) {
    try {
        // Criar tabela users
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Criar tabela results
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
        
        // Inserir usuário padrão
        $hashed_password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
        $stmt->execute(['admin@exemplo.com', $hashed_password, 'Administrador']);
        
        echo "✅ Tabelas criadas e usuário padrão inserido!<br>";
        echo "Email: admin@exemplo.com<br>";
        echo "Senha: 123456<br>";
        
    } catch (PDOException $e) {
        echo "❌ Erro ao criar tabelas: " . $e->getMessage() . "<br>";
    }
} else {
    echo "ℹ️ Tabelas já existem ou conexão não disponível<br>";
}

echo "<br><hr>";
echo "<p><strong>Se todos os testes passaram, tente acessar:</strong></p>";
echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Acessar Checklist do Produto</a>";
?> 