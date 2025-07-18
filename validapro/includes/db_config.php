<?php
/**
 * Configuração de Banco de Dados - ValidaPro
 * Sistema completamente independente
 */

// Configurações do Banco de Dados ValidaPro
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'paymen58_validapro');
define('DB_USER', 'paymen58');
define('DB_PASS', 'u4q7+B6ly)obP_gxN9sNe');

// Configurações de conexão PDO
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// Configurações de timeout e retry
define('DB_TIMEOUT', 30);
define('DB_RETRY_ATTEMPTS', 3);

// Função para criar conexão PDO
function createValidaProConnection() {
    try {
        $dsn = DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            PDO::ATTR_TIMEOUT => DB_TIMEOUT,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
        
    } catch (PDOException $e) {
        error_log("Erro na conexão com banco ValidaPro: " . $e->getMessage());
        throw new Exception("Erro na conexão com o banco de dados");
    }
}

// Função para verificar se a conexão está ativa
function isValidaProConnectionActive($pdo) {
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        return false;
    }
} 