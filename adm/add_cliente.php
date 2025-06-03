<?php
require 'protect.php';
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $whatsapp = filter_input(INPUT_POST, 'whatsapp', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $classificacao = filter_input(INPUT_POST, 'classificacao', FILTER_SANITIZE_STRING);

    try {
        $stmt = $pdo->prepare("INSERT INTO clientes (nome, email, whatsapp, status, classificacao, criado_em) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$nome, $email, $whatsapp, $status, $classificacao]);
        
        header('Location: clientes.php?msg=added');
        exit;
    } catch (PDOException $e) {
        header('Location: clientes.php?error=1');
        exit;
    }
} else {
    header('Location: clientes.php');
    exit;
} 