<?php
// Teste avançado para verificar se o formulário está funcionando
session_start();

echo "<h1>🔍 Teste Avançado do Formulário</h1>";

// Verificar se a sessão está funcionando
echo "<h2>📊 Status da Sessão:</h2>";
echo "<p>Sessão ativa: " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Sim" : "❌ Não") . "</p>";
echo "<p>ID da sessão: " . session_id() . "</p>";
echo "<p>Dados da sessão:</p>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Verificar se os arquivos necessários existem
echo "<h2>📁 Verificação de Arquivos:</h2>";
$arquivos = ['includes/db.php', 'includes/auth.php', 'resultado.php'];
foreach ($arquivos as $arquivo) {
    echo "<p>$arquivo: " . (file_exists($arquivo) ? "✅ Existe" : "❌ Não existe") . "</p>";
}

// Teste de conexão com banco
echo "<h2>🗄️ Teste de Banco de Dados:</h2>";
try {
    require_once 'includes/db.php';
    echo "<p>Conexão com banco: ✅ Funcionando</p>";
} catch (Exception $e) {
    echo "<p>Conexão com banco: ❌ Erro: " . $e->getMessage() . "</p>";
}

// Simular dados de teste
echo "<h2>🧪 Simulação de Envio:</h2>";
$_POST = [
    'csrf_token' => 'teste_token',
    'promessa_principal' => 'Teste de promessa',
    'cliente_consciente' => 'Cliente consciente teste',
    'beneficios' => 'Benefícios teste',
    'mecanismo_unico' => 'Mecanismo teste',
    'checklist' => ['vida_mais_facil', 'economiza_dinheiro', 'buscas_google']
];

echo "<h3>Dados Simulados:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h3>Cálculo de Pontos:</h3>";
$pontos = count($_POST['checklist']);
echo "Pontos: $pontos";

echo "<h3>Mensagem Baseada na Pontuação:</h3>";
if ($pontos >= 8) {
    echo "🏆 Produto com alto potencial!";
} elseif ($pontos >= 5) {
    echo "⭐ Produto razoável, com potencial";
} else {
    echo "📈 Produto fraco, repense a escolha";
}

echo "<h2>🔗 Links de Teste:</h2>";
echo "<p><a href='dashboard.php'>📋 Ir para Dashboard</a></p>";
echo "<p><a href='index.php'>🔐 Ir para Login</a></p>";

echo "<h2>💡 Próximos Passos:</h2>";
echo "<ol>";
echo "<li>Teste o formulário no dashboard</li>";
echo "<li>Verifique o console do navegador (F12)</li>";
echo "<li>Confirme se todos os campos obrigatórios estão preenchidos</li>";
echo "<li>Teste com diferentes combinações de checkboxes</li>";
echo "</ol>";

echo "<br><br>";
echo "<a href='dashboard.php' style='background: #3b82f6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Voltar ao Dashboard</a>";
?> 