<?php
require 'conexao.php';

try {
    // Lê o conteúdo do arquivo SQL
    $sql = file_get_contents('sql/recuperacao_senha.sql');
    
    // Executa o SQL
    $pdo->exec($sql);
    
    echo "Tabela 'recuperacao_senha' criada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao criar tabela: " . $e->getMessage();
}
?> 