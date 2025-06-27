<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$usuario = 'paymen58';
$senha = 'u4q7+B6ly)obP_gxN9sNe';
$banco = 'paymen58_sistema_integrado_led';

echo "<h2>Teste de Conexão e Tabela</h2>";

// Teste de conexão
try {
    $conn = new mysqli($host, $usuario, $senha, $banco);
    if ($conn->connect_error) {
        die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
    }
    echo "<p style='color: green;'>✅ Conexão com banco de dados OK</p>";
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

// Verificar se a tabela existe
$result = $conn->query("SHOW TABLES LIKE 'leads'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ Tabela 'leads' existe</p>";
} else {
    echo "<p style='color: red;'>❌ Tabela 'leads' NÃO existe</p>";
    echo "<p>Criando tabela...</p>";
    
    $sql = "CREATE TABLE IF NOT EXISTS `leads` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `nome` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `whatsapp` varchar(20) NOT NULL,
      `instagram` varchar(100) DEFAULT NULL,
      `momento` text NOT NULL,
      `renda` varchar(100) NOT NULL,
      `investimento` varchar(100) NOT NULL,
      `motivo` text NOT NULL,
      `compromisso1` tinyint(1) NOT NULL DEFAULT 0,
      `compromisso2` tinyint(1) NOT NULL DEFAULT 0,
      `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `email` (`email`),
      KEY `data_cadastro` (`data_cadastro`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✅ Tabela 'leads' criada com sucesso</p>";
    } else {
        echo "<p style='color: red;'>❌ Erro ao criar tabela: " . $conn->error . "</p>";
    }
}

// Verificar estrutura da tabela
$result = $conn->query("DESCRIBE leads");
if ($result) {
    echo "<h3>Estrutura da tabela 'leads':</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Contar registros
$result = $conn->query("SELECT COUNT(*) as total FROM leads");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p><strong>Total de registros na tabela: " . $row['total'] . "</strong></p>";
}

// Testar consulta do painel
echo "<h3>Teste da consulta do painel:</h3>";
$sql = "SELECT * FROM leads ORDER BY data_cadastro DESC LIMIT 5";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Consulta executada com sucesso. Encontrados " . $result->num_rows . " registros.</p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Data</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $row['data_cadastro'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ Consulta executada, mas não há registros na tabela.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Erro na consulta: " . $conn->error . "</p>";
}

$conn->close();
echo "<p><a href='painel.php'>Ir para o Painel</a></p>";
?> 