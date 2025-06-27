<?php
require_once __DIR__ . '/../conexao.php';

// Filtros
$filtro_investimento = $_GET['investimento'] ?? '';
$filtro_data = $_GET['data'] ?? '';

$sql = "SELECT * FROM leads WHERE 1=1";
$params = [];
$types = '';

if ($filtro_investimento) {
    $sql .= " AND investimento = ?";
    $params[] = $filtro_investimento;
    $types .= 's';
}
if ($filtro_data) {
    $sql .= " AND DATE(data_envio) = ?";
    $params[] = $filtro_data;
    $types .= 's';
}
$sql .= " ORDER BY data_envio DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Cabeçalhos para download como Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="leads_mentoria_rubenio.xls"');
header('Pragma: no-cache');
header('Expires: 0');

echo "Data\tNome\tEmail\tWhatsApp\tInstagram\tMomento\tRenda\tInvestimento\tMotivo\tCompromisso 1\tCompromisso 2\n";
while ($lead = $result->fetch_assoc()) {
    echo date('d/m/Y H:i', strtotime($lead['data_envio'])) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['nome']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['email']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['whatsapp']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['instagram']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['momento']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['renda']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['investimento']) . "\t";
    echo str_replace(["\t", "\n", "\r"], ' ', $lead['motivo']) . "\t";
    echo ($lead['compromisso1'] ? 'Sim' : 'Não') . "\t";
    echo ($lead['compromisso2'] ? 'Sim' : 'Não') . "\n";
}
exit; 