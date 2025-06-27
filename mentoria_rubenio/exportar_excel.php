<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão direta para evitar problemas
$host = "localhost";
$db = "paymen58_sistema_integrado_led";
$user = "paymen58";
$pass = "u4q7+B6ly)obP_gxN9sNe";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Filtros
$filtro_investimento = $_GET['investimento'] ?? '';
$filtro_data = $_GET['data'] ?? '';

$sql = "SELECT * FROM leads WHERE 1=1";
$params = [];

if ($filtro_investimento) {
    $sql .= " AND investimento = ?";
    $params[] = $filtro_investimento;
}
if ($filtro_data) {
    $sql .= " AND DATE(data_envio) = ?";
    $params[] = $filtro_data;
}
$sql .= " ORDER BY data_envio DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

// Cabeçalhos para download como Excel
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="leads_mentoria_rubenio_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// BOM para UTF-8
echo "\xEF\xBB\xBF";

// Cabeçalhos da tabela
echo "Data/Hora\tNome\tEmail\tWhatsApp\tInstagram\tMomento\tRenda\tInvestimento\tMotivo\tCompromisso 1\tCompromisso 2\n";

// Dados
foreach ($result as $lead) {
    $data = date('d/m/Y H:i', strtotime($lead['data_envio']));
    $nome = str_replace(["\t", "\n", "\r"], ' ', $lead['nome']);
    $email = str_replace(["\t", "\n", "\r"], ' ', $lead['email']);
    $whatsapp = str_replace(["\t", "\n", "\r"], ' ', $lead['whatsapp']);
    $instagram = str_replace(["\t", "\n", "\r"], ' ', $lead['instagram'] ?? '');
    $momento = str_replace(["\t", "\n", "\r"], ' ', $lead['momento'] ?? '');
    $renda = str_replace(["\t", "\n", "\r"], ' ', $lead['renda'] ?? '');
    $investimento = str_replace(["\t", "\n", "\r"], ' ', $lead['investimento'] ?? '');
    $motivo = str_replace(["\t", "\n", "\r"], ' ', $lead['motivo'] ?? '');
    $compromisso1 = $lead['compromisso1'] ? 'Sim' : 'Não';
    $compromisso2 = $lead['compromisso2'] ? 'Sim' : 'Não';
    
    echo "$data\t$nome\t$email\t$whatsapp\t$instagram\t$momento\t$renda\t$investimento\t$motivo\t$compromisso1\t$compromisso2\n";
}

exit; 