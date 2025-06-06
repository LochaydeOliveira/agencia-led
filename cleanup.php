<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/src/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Inicia a transação
    $conn->beginTransaction();

    // Limpa os tokens de download
    $stmt = $conn->prepare("DELETE FROM download_tokens");
    $stmt->execute();
    $tokensDeleted = $stmt->rowCount();

    // Limpa os logs
    $stmt = $conn->prepare("DELETE FROM usuarios");
    $stmt->execute();
    $logsDeleted = $stmt->rowCount();

    // Limpa os pedidos
    $stmt = $conn->prepare("DELETE FROM orders");
    $stmt->execute();
    $ordersDeleted = $stmt->rowCount();

    // LIMPAR clientes_listas ANTES de clientes
    $stmt = $conn->prepare("DELETE FROM clientes_listas");
    $stmt->execute();
    $clientesListasDeleted = $stmt->rowCount();

    // Agora pode limpar os clientes
    $stmt = $conn->prepare("DELETE FROM clientes");
    $stmt->execute();
    $clientesDeleted = $stmt->rowCount();

    // Confirma a transação
    $conn->commit();

    echo "Limpeza concluída com sucesso!\n";
    echo "Registros removidos:\n";
    echo "- Tokens de download: $tokensDeleted\n";
    echo "- Logs: $logsDeleted\n";
    echo "- Pedidos: $ordersDeleted\n";
    echo "- Clientes das listas: $clientesListasDeleted\n";
    echo "- Clientes: $clientesDeleted\n";

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo "Erro durante a limpeza: " . $e->getMessage();
}
