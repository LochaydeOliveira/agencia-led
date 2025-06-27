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
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $whatsapp, $instagram, $momento, $renda, $investimento, $motivo, $compromisso1, $compromisso2]);

    if ($stmt->rowCount() > 0) {
   
        $to = 'contato@agencialed.com';
        $subject = 'Novo lead - Mentoria Rubênio Gabriel';
        $message = "<h2>Novo lead recebido</h2>"
            . "<b>Nome:</b> $nome<br>"
            . "<b>E-mail:</b> $email<br>"
            . "<b>WhatsApp:</b> $whatsapp<br>"
            . "<b>Instagram:</b> $instagram<br>"
            . "<b>Momento:</b> $momento<br>"
            . "<b>Renda:</b> $renda<br>"
            . "<b>Investimento:</b> $investimento<br>"
            . "<b>Motivo:</b> $motivo<br>"
            . "<b>Compromisso 1:</b> " . ($compromisso1 ? 'Sim' : 'Não') . "<br>"
            . "<b>Compromisso 2:</b> " . ($compromisso2 ? 'Sim' : 'Não') . "<br>"
            . "<br><small>Enviado em " . date('d/m/Y H:i') . ".</small>";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Mentoria Rubênio Gabriel <nao-responda@agencialed.com>\r\n";
        @mail($to, $subject, $message, $headers);


        try {
            require_once __DIR__ . '/../src/Mailer.php';
            $mailer = new Mailer();
            $mailer->sendCustomEmail(
                'lochaydeguerreiro@hotmail.com',
                'Notificação de novo lead - Mentoria Rubênio Gabriel',
                $message
            );
        } catch (Exception $e) {

            error_log('Erro ao enviar notificação para Lochayde: ' . $e->getMessage());
        }

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