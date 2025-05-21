<?php

error_reporting(E_ALL);

ini_set('display_errors', 1);



require_once __DIR__ . '/config/config.php';

require_once __DIR__ . '/src/Database.php';



// Função para log personalizado

function app_log($message) {

    $date = date('Y-m-d H:i:s');

    $logMessage = "[$date] $message" . PHP_EOL;

    file_put_contents(LOG_FILE, $logMessage, FILE_APPEND);

}



// Verifica se o token foi fornecido

$token = $_GET['token'] ?? '';

if (empty($token)) {

    app_log("Tentativa de download sem token");

    die('Token não fornecido');

}



app_log("Tentativa de download com token: $token");



try {

    $db = Database::getInstance();

    $conn = $db->getConnection();

    

    // Verifica se o token é válido e não expirou

    $sql = "SELECT o.id, o.order_number, o.customer_name, dt.expires_at, dt.downloaded 

            FROM orders o 

            JOIN download_tokens dt ON o.id = dt.order_id 

            WHERE dt.token = ? AND dt.expires_at > NOW()";

    

    app_log("Verificando token no banco de dados");

    

    $stmt = $conn->prepare($sql);

    $stmt->execute([$token]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    

    if (!$result) {

        app_log("Token inválido ou expirado: $token");

        die('Token inválido ou expirado - é aqui');

    }

    

    app_log("Token válido encontrado para o pedido #" . $result['order_number']);

    

    // Verifica se o arquivo já foi baixado
/*
    if ($result['downloaded']) {

        app_log("Arquivo já foi baixado anteriormente para o pedido #" . $result['order_number']);

        die('Este arquivo já foi baixado anteriormente. Por favor, entre em contato conosco se precisar de uma nova cópia.');

    }
*/
    

    // Verifica se o arquivo existe

    $filePath = __DIR__ . '/files/Lista_Secreta_Fornecedores.pdf';

    if (!file_exists($filePath)) {

        app_log("Arquivo não encontrado: $filePath");

        die('Arquivo não encontrado');

    }

    

    app_log("Arquivo encontrado: $filePath");

    app_log("Tamanho do arquivo: " . filesize($filePath) . " bytes");

    

    // Limpa qualquer saída anterior

    if (ob_get_level()) {

        ob_end_clean();

    }

    

    // Define os headers para download

    header('Content-Type: application/pdf');

    header('Content-Disposition: attachment; filename="Lista_Secreta_Fornecedores.pdf"');

    header('Content-Length: ' . filesize($filePath));

    header('Cache-Control: private, no-transform, no-store, must-revalidate');

    header('Pragma: no-cache');

    header('Expires: 0');

    

    app_log("Headers definidos para download");

    

    // Marca o download como realizado ANTES de enviar o arquivo

    $sql = "UPDATE download_tokens SET downloaded = TRUE WHERE token = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$token]);

    

    app_log("Download marcado como realizado para o pedido #" . $result['order_number']);

    

    // Envia o arquivo

    app_log("Iniciando envio do arquivo");

    if (readfile($filePath) === false) {

        app_log("Erro ao ler o arquivo");

        die('Erro ao ler o arquivo');

    }

    

    app_log("Arquivo enviado com sucesso");

    exit;

    

} catch (Exception $e) {

    app_log("Erro ao processar download: " . $e->getMessage());

    app_log("Stack trace: " . $e->getTraceAsString());

    die('Erro ao processar download');

} 