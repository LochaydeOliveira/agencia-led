<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Database.php';

// Verifica se o token foi fornecido
if (!isset($_GET['token'])) {
    die('Token não fornecido');
}

$token = $_GET['token'];
$db = Database::getInstance()->getConnection();

try {
    // Verifica se o token é válido e não expirou
    $stmt = $db->prepare("
        SELECT d.*, o.order_number, o.customer_name 
        FROM downloads d 
        JOIN orders o ON d.order_id = o.id 
        WHERE d.download_token = ? 
        AND d.expires_at > NOW() 
        AND d.downloaded = 0
    ");
    $stmt->execute([$token]);
    $download = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$download) {
        die('Link inválido ou expirado');
    }

    // Marca o download como realizado
    $stmt = $db->prepare("UPDATE downloads SET downloaded = 1 WHERE id = ?");
    $stmt->execute([$download['id']]);

    // Registra o log
    $stmt = $db->prepare("INSERT INTO logs (order_id, event_type, event_data) VALUES (?, 'download.completed', ?)");
    $stmt->execute([$download['order_id'], json_encode(['token' => $token])]);

    // Define os headers para download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Lista_Secreta_Fornecedores.pdf"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Lê e envia o arquivo
    $filePath = __DIR__ . '/../files/Lista_Secreta_Fornecedores.pdf';
    if (file_exists($filePath)) {
        readfile($filePath);
    } else {
        die('Arquivo não encontrado');
    }
} catch (PDOException $e) {
    error_log("Erro ao processar download: " . $e->getMessage());
    die('Erro ao processar download');
} 