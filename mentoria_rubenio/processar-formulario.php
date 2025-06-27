<?php
require_once __DIR__ . '/../conexao.php';

// Função para limpar e proteger os dados
function limpar($dado) {
    return htmlspecialchars(trim($dado), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = limpar($_POST['nome'] ?? '');
    $email = limpar($_POST['email'] ?? '');
    $whatsapp = limpar($_POST['whatsapp'] ?? '');
    $instagram = limpar($_POST['instagram'] ?? '');
    $momento = limpar($_POST['momento'] ?? '');
    $renda = limpar($_POST['renda'] ?? '');
    $investimento = limpar($_POST['investimento'] ?? '');
    $motivo = limpar($_POST['motivo'] ?? '');
    $compromisso1 = isset($_POST['compromisso1']) ? 1 : 0;
    $compromisso2 = isset($_POST['compromisso2']) ? 1 : 0;

    // Validação básica
    if (!$nome || !$email || !$whatsapp || !$momento || !$renda || !$investimento || !$motivo || !$compromisso1 || !$compromisso2) {
        header('Location: index.html?erro=1');
        exit;
    }

    $sql = "INSERT INTO leads (nome, email, whatsapp, instagram, momento, renda, investimento, motivo, compromisso1, compromisso2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssii', $nome, $email, $whatsapp, $instagram, $momento, $renda, $investimento, $motivo, $compromisso1, $compromisso2);

    if ($stmt->execute()) {
        header('Location: index.html?sucesso=1');
        exit;
    } else {
        header('Location: index.html?erro=2');
        exit;
    }
}
else {
    header('Location: index.html');
    exit;
} 