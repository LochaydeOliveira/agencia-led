<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Usar o arquivo de conexão correto
require_once __DIR__ . '/conexao.php';

// Converter PDO para mysqli para compatibilidade
try {
    $host = "localhost";
    $usuario = "paymen58";
    $senha = "u4q7+B6ly)obP_gxN9sNe";
    $banco = "paymen58_sistema_integrado_led";
    
    $conn = new mysqli($host, $usuario, $senha, $banco);
    if ($conn->connect_error) {
        die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Leads - Mentoria Rubênio Gabriel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin-assets/style.css">
    <style>
        .table {
            font-size: 0.85rem;
        }
        .table th {
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
            vertical-align: middle;
            background-color: #f8f9fa;
        }
        .table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            vertical-align: middle;
        }
        .table td.text-wrap {
            white-space: normal;
            word-wrap: break-word;
        }
        .container-fluid {
            max-width: 100%;
            padding: 0 15px;
        }
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .filters-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <h2 class="mb-4">Painel de Leads - Mentoria Rubênio Gabriel</h2>
    
    <div class="filters-section">
        <form class="row g-3" method="get">
            <div class="col-md-4">
                <label for="investimento" class="form-label">Filtrar por Investimento</label>
                <select class="form-select form-select-sm" id="investimento" name="investimento">
                    <option value="">Todos</option>
                    <option value="Não posso investir no momento" <?php echo $filtro_investimento === 'Não posso investir no momento' ? 'selected' : ''; ?>>Não posso investir no momento</option>
                    <option value="Até R$1.000" <?php echo $filtro_investimento === 'Até R$1.000' ? 'selected' : ''; ?>>Até R$1.000</option>
                    <option value="De R$1.100 a R$1.500" <?php echo $filtro_investimento === 'De R$1.100 a R$1.500' ? 'selected' : ''; ?>>De R$1.100 a R$1.500</option>
                    <option value="De R$1.501 a R$3.000" <?php echo $filtro_investimento === 'De R$1.501 a R$3.000' ? 'selected' : ''; ?>>De R$1.501 a R$3.000</option>
                    <option value="De R$3.001 a R$5.000" <?php echo $filtro_investimento === 'De R$3.001 a R$5.000' ? 'selected' : ''; ?>>De R$3.001 a R$5.000</option>
                    <option value="De R$5.001 a R$15.000" <?php echo $filtro_investimento === 'De R$5.001 a R$15.000' ? 'selected' : ''; ?>>De R$5.001 a R$15.000</option>
                    <option value="De R$16.000 a R$25.000" <?php echo $filtro_investimento === 'De R$16.000 a R$25.000' ? 'selected' : ''; ?>>De R$16.000 a R$25.000</option>
                    <option value="Acima de R$26.000" <?php echo $filtro_investimento === 'Acima de R$26.000' ? 'selected' : ''; ?>>Acima de R$26.000</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="data" class="form-label">Filtrar por Data</label>
                <input type="date" class="form-control form-control-sm" id="data" name="data" value="<?php echo htmlspecialchars($filtro_data); ?>">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filtrar</button>
            </div>
            <div class="col-md-3 align-self-end">
                <a href="exportar_excel.php?investimento=<?php echo urlencode($filtro_investimento); ?>&data=<?php echo urlencode($filtro_data); ?>" class="btn btn-success btn-sm w-100">Exportar para Excel</a>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="min-width: 120px;">Data/Hora</th>
                    <th style="min-width: 150px;">Nome</th>
                    <th style="min-width: 200px;">Email</th>
                    <th style="min-width: 140px;">WhatsApp</th>
                    <th style="min-width: 120px;">Instagram</th>
                    <th style="min-width: 250px;">Momento</th>
                    <th style="min-width: 150px;">Renda</th>
                    <th style="min-width: 180px;">Investimento</th>
                    <th style="min-width: 300px;" class="text-wrap">Motivo</th>
                    <th style="min-width: 100px;">Compromissos</th>
                    <th style="min-width: 120px;">Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if ($result && $result->num_rows > 0):
                while ($lead = $result->fetch_assoc()): 
            ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($lead['data_envio'])); ?></td>
                    <td title="<?php echo htmlspecialchars($lead['nome']); ?>"><?php echo htmlspecialchars($lead['nome']); ?></td>
                    <td title="<?php echo htmlspecialchars($lead['email']); ?>"><?php echo htmlspecialchars($lead['email']); ?></td>
                    <td>
                        <?php 
                        $wpp = preg_replace('/\D/', '', $lead['whatsapp']);
                        if (strlen($wpp) >= 10) {
                            echo '<a href="https://wa.me/55'.$wpp.'" target="_blank" class="btn btn-success btn-sm">WhatsApp</a> ';
                        }
                        echo htmlspecialchars($lead['whatsapp']);
                        ?>
                    </td>
                    <td title="<?php echo htmlspecialchars($lead['instagram']); ?>"><?php echo htmlspecialchars($lead['instagram']); ?></td>
                    <td title="<?php echo htmlspecialchars($lead['momento']); ?>"><?php echo htmlspecialchars($lead['momento']); ?></td>
                    <td title="<?php echo htmlspecialchars($lead['renda']); ?>"><?php echo htmlspecialchars($lead['renda']); ?></td>
                    <td title="<?php echo htmlspecialchars($lead['investimento']); ?>"><?php echo htmlspecialchars($lead['investimento']); ?></td>
                    <td class="text-wrap" title="<?php echo htmlspecialchars($lead['motivo']); ?>"><?php echo nl2br(htmlspecialchars($lead['motivo'])); ?></td>
                    <td class="text-center">
                        <div class="d-flex flex-column gap-1">
                            <span class="<?php echo $lead['compromisso1'] ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $lead['compromisso1'] ? '✔️' : '❌'; ?>
                            </span>
                            <span class="<?php echo $lead['compromisso2'] ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $lead['compromisso2'] ? '✔️' : '❌'; ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column gap-1">
                            <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" class="btn btn-outline-primary btn-sm">E-mail</a>
                            <?php if ($lead['instagram']): ?>
                            <a href="https://instagram.com/<?php echo str_replace('@', '', htmlspecialchars($lead['instagram'])); ?>" target="_blank" class="btn btn-outline-danger btn-sm">Instagram</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php 
                endwhile; 
            else:
            ?>
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        <p>Nenhum lead encontrado.</p>
                        <p><a href="teste_painel.php" class="btn btn-outline-primary btn-sm">Testar Conexão</a></p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html> 