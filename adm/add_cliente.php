<?php
require 'protect.php';
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = htmlspecialchars($_POST['nome'] ?? '', ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $whatsapp = htmlspecialchars($_POST['whatsapp'] ?? '', ENT_QUOTES, 'UTF-8');
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $status = htmlspecialchars($_POST['status'] ?? '', ENT_QUOTES, 'UTF-8');
    $classificacao = htmlspecialchars($_POST['classificacao'] ?? '', ENT_QUOTES, 'UTF-8');
    $listas = isset($_POST['listas']) ? $_POST['listas'] : [];

    try {
        $pdo->beginTransaction();

        // Inserir cliente
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, whatsapp, senha, status, classificacao, criado_em) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $whatsapp, $senha, $status, $classificacao]);
        $cliente_id = $pdo->lastInsertId();

        // Se o cliente for prata, inserir as listas selecionadas
        if ($classificacao === 'prata' && !empty($listas)) {
            $stmt = $pdo->prepare("INSERT INTO clientes_listas (cliente_id, lista_id) VALUES (?, ?)");
            foreach ($listas as $lista_id) {
                $stmt->execute([$cliente_id, $lista_id]);
            }
        }

        $pdo->commit();
        header('Location: clientes.php?msg=added');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: clientes.php?error=1');
        exit;
    }
} else {
    header('Location: clientes.php');
    exit;
}

// Buscar todas as listas para o select
$stmt = $pdo->query("SELECT id, nome FROM listas ORDER BY nome");
$listas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 