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


    // Limpa os clientes
    $stmt = $conn->prepare("DELETE FROM clientes");
    $stmt->execute();
    $clientesDeleted = $stmt->rowCount();


    // Limpa os clientes_listas
    $stmt = $conn->prepare("DELETE FROM clientes_listas");
    $stmt->execute();
    $clientesListasDeleted = $stmt->rowCount(); 
 

    // Confirma a transação
    $conn->commit();

    

    echo "Limpeza concluída com sucesso!\n";

    echo "Registros removidos:\n";

    echo "- Tokens de download: $tokensDeleted\n";

    echo "- Logs: $logsDeleted\n";

    echo "- Pedidos: $ordersDeleted\n";

    

} catch (Exception $e) {

    // Em caso de erro, desfaz a transação

    if ($conn->inTransaction()) {

        $conn->rollBack();

    }

    echo "Erro durante a limpeza: " . $e->getMessage();

} 