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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Leads - Mentoria Rubênio Gabriel</title>
    <link rel="stylesheet" href="admin-assets/bootstrap.min.css">
    <link rel="stylesheet" href="admin-assets/style.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Painel de Leads - Mentoria Rubênio Gabriel</h2>
    <form class="row g-3 mb-4" method="get">
        <div class="col-md-4">
            <label for="investimento" class="form-label">Filtrar por Investimento</label>
            <select class="form-select" id="investimento" name="investimento">
                <option value="">Todos</option>
                <option>Não posso investir no momento</option>
                <option>Até R$1.000</option>
                <option>De R$1.100 a R$1.500</option>
                <option>De R$1.501 a R$3.000</option>
                <option>De R$3.001 a R$5.000</option>
                <option>De R$5.001 a R$15.000</option>
                <option>De R$16.000 a R$25.000</option>
                <option>Acima de R$26.000</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="data" class="form-label">Filtrar por Data</label>
            <input type="date" class="form-control" id="data" name="data" value="<?php echo htmlspecialchars($filtro_data); ?>">
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-3 align-self-end">
            <a href="exportar_excel.php?investimento=<?php echo urlencode($filtro_investimento); ?>&data=<?php echo urlencode($filtro_data); ?>" class="btn btn-success w-100">Exportar para Excel</a>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Data</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Instagram</th>
                    <th>Momento</th>
                    <th>Renda</th>
                    <th>Investimento</th>
                    <th>Motivo</th>
                    <th>Compromissos</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($lead = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($lead['data_envio'])); ?></td>
                    <td><?php echo htmlspecialchars($lead['nome']); ?></td>
                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                    <td>
                        <?php 
                        $wpp = preg_replace('/\D/', '', $lead['whatsapp']);
                        if (strlen($wpp) >= 10) {
                            echo '<a href="https://wa.me/55'.$wpp.'" target="_blank" class="btn btn-success btn-sm">WhatsApp</a> ';
                        }
                        echo htmlspecialchars($lead['whatsapp']);
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($lead['instagram']); ?></td>
                    <td><?php echo htmlspecialchars($lead['momento']); ?></td>
                    <td><?php echo htmlspecialchars($lead['renda']); ?></td>
                    <td><?php echo htmlspecialchars($lead['investimento']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($lead['motivo'])); ?></td>
                    <td>
                        <?php echo $lead['compromisso1'] ? '✔️' : '❌'; ?> <br>
                        <?php echo $lead['compromisso2'] ? '✔️' : '❌'; ?>
                    </td>
                    <td>
                        <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" class="btn btn-outline-primary btn-sm">E-mail</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html> 