<?php
require 'protect.php';
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_STRING);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $preco = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $link_de_compra = filter_input(INPUT_POST, 'link_de_compra', FILTER_SANITIZE_URL);
    $conteudo_html = ''; // Campo obrigatório, mas será preenchido posteriormente

    try {
        $stmt = $pdo->prepare("INSERT INTO listas (product_id, nome, descricao, conteudo_html, preco, link_de_compra) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$product_id, $nome, $descricao, $conteudo_html, $preco, $link_de_compra]);
        
        header('Location: listas.php?msg=added');
        exit;
    } catch (PDOException $e) {
        header('Location: listas.php?error=1');
        exit;
    }
} else {
    header('Location: listas.php');
    exit;
} 