<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../vendor/autoload.php';

if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    die('Autoload não encontrado em: ' . __DIR__ . '/../../vendor/autoload.php');
}

// Receber dados do POST
$pontos = $_POST['pontos'] ?? 0;
$status = $_POST['status'] ?? '';
$recomendacao = $_POST['recomendacao'] ?? '';
$proximosPassos = $_POST['proximosPassos'] ?? [];
$promessa = $_POST['promessa'] ?? '';
$cliente = $_POST['cliente'] ?? '';
$beneficios = $_POST['beneficios'] ?? '';
$mecanismo = $_POST['mecanismo'] ?? '';

if (is_string($proximosPassos)) {
    $proximosPassos = @json_decode($proximosPassos, true) ?: [];
}

// Branding
$logo = '';
// Se quiser, coloque o caminho do logo aqui, ex: 'logo.png'

$html = '<html><head><style>
body { font-family: Arial, sans-serif; color: #222; }
.titulo { background: linear-gradient(90deg, #2563eb, #6366f1); color: #fff; padding: 24px; border-radius: 16px 16px 0 0; text-align: center; font-size: 2em; font-weight: bold; }
.secao { margin: 24px 0; padding: 16px; border-radius: 12px; background: #f3f4f6; }
.pontos { font-size: 2.5em; color: #2563eb; font-weight: bold; }
.status { font-size: 1.3em; margin: 12px 0; font-weight: bold; }
.recomendacao { color: #2563eb; font-size: 1.1em; margin-bottom: 12px; }
.passos { margin: 0 0 0 18px; }
.label { font-weight: bold; color: #6366f1; }
.resposta { margin-bottom: 8px; }
.footer { margin-top: 32px; text-align: center; color: #888; font-size: 0.9em; }
</style></head><body>';

$html .= '<div class="titulo">Checklist do Produto Lucrativo</div>';
if ($logo) {
    $html .= '<div style="text-align:center;margin:16px 0;"><img src="' . $logo . '" height="60"></div>';
}
$html .= '<div class="secao" style="text-align:center;">
    <div class="pontos">' . htmlspecialchars($pontos) . '/10</div>
    <div class="status">' . htmlspecialchars($status) . '</div>
</div>';
$html .= '<div class="secao">
    <div class="label">Recomendação do Especialista:</div>
    <div class="recomendacao">' . nl2br(htmlspecialchars($recomendacao)) . '</div>
</div>';
if (!empty($proximosPassos)) {
    $html .= '<div class="secao"><div class="label">Próximos Passos:</div><ul class="passos">';
    foreach ($proximosPassos as $passo) {
        $html .= '<li>' . htmlspecialchars($passo) . '</li>';
    }
    $html .= '</ul></div>';
}
$html .= '<div class="secao"><div class="label">Promessa Principal:</div><div class="resposta">' . nl2br(htmlspecialchars($promessa)) . '</div></div>';
$html .= '<div class="secao"><div class="label">Cliente Consciente:</div><div class="resposta">' . nl2br(htmlspecialchars($cliente)) . '</div></div>';
$html .= '<div class="secao"><div class="label">Benefícios:</div><div class="resposta">' . nl2br(htmlspecialchars($beneficios)) . '</div></div>';
$html .= '<div class="secao"><div class="label">Mecanismo Único:</div><div class="resposta">' . nl2br(htmlspecialchars($mecanismo)) . '</div></div>';
$html .= '<div class="footer">Checklist gerado em ' . date('d/m/Y H:i') . ' | checklist-produto-vencedor</div>';
$html .= '</body></html>';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_top' => 16,
    'margin_bottom' => 16,
    'margin_left' => 12,
    'margin_right' => 12,
]);
$mpdf->WriteHTML($html);

// Nome do arquivo
$filename = 'analise-produto-' . date('Ymd-His') . '.pdf';

// Download
$mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
exit; 