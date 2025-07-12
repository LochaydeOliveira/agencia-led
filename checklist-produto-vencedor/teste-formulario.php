<?php
// Teste simples para verificar se o formulário está funcionando
session_start();

// Simular dados de teste
$_POST = [
    'promessa_principal' => 'Teste de promessa',
    'cliente_consciente' => 'Cliente consciente teste',
    'beneficios' => 'Benefícios teste',
    'mecanismo_unico' => 'Mecanismo teste',
    'checklist' => ['vida_mais_facil', 'economiza_dinheiro', 'buscas_google']
];

echo "<h1>Teste do Formulário</h1>";
echo "<h2>Dados Recebidos:</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Pontos Calculados:</h2>";
$pontos = count($_POST['checklist']);
echo "Pontos: $pontos";

echo "<h2>Mensagem:</h2>";
if ($pontos >= 8) {
    echo "Produto com alto potencial!";
} elseif ($pontos >= 5) {
    echo "Produto razoável, com potencial";
} else {
    echo "Produto fraco, repense a escolha";
}

echo "<br><br>";
echo "<a href='dashboard.php'>Voltar ao Dashboard</a>";
?> 