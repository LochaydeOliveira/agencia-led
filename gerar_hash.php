<?php
$senha = '154719';
$hash = password_hash($senha, PASSWORD_DEFAULT);
echo "Senha: " . $senha . "\n";
echo "Hash: " . $hash . "\n";

// Verificar se o hash está correto
if (password_verify($senha, $hash)) {
    echo "Hash verificado com sucesso!\n";
} else {
    echo "Erro na verificação do hash!\n";
}
?>
