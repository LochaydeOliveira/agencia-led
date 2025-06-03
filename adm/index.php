<?php
require 'protect.php';
require '../conexao.php';

// Estatísticas gerais
$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$totalListas = $pdo->query("SELECT COUNT(*) FROM listas")->fetchColumn();
$totalPedidos = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Pedidos por status
$pedidosPorStatus = $pdo->query("
    SELECT status, COUNT(*) as total 
    FROM orders 
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// Pedidos dos últimos 7 dias
$pedidosPorDia = $pdo->query("
    SELECT DATE(created_at) as data, COUNT(*) as total 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY data
")->fetchAll(PDO::FETCH_ASSOC);

// Clientes por classificação
$clientesPorClassificacao = $pdo->query("
    SELECT classificacao, COUNT(*) as total 
    FROM clientes 
    GROUP BY classificacao
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets-admin/admin.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <div class="d-flex"> 
        <?php include 'partials/sidebar.php'; ?>    

        <main class="flex-grow-1 p-4">
            <h2 class="mb-4">Dashboard Administrativo</h2>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Clientes</h5>
                        <p class="fs-3 fw-bold text-primary"><?php echo $totalClientes; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Listas</h5>
                        <p class="fs-3 fw-bold text-success"><?php echo $totalListas; ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h5>Total de Pedidos</h5>
                        <p class="fs-3 fw-bold text-danger"><?php echo $totalPedidos; ?></p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Pedidos por Status</h5>
                            <canvas id="pedidosStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Pedidos dos Últimos 7 Dias</h5>
                            <canvas id="pedidosDiasChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Clientes por Classificação</h5>
                            <canvas id="clientesClassificacaoChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include 'partials/footer.php'; ?>

    <script>
        // Gráfico de Pedidos por Status
        new Chart(document.getElementById('pedidosStatusChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($pedidosPorStatus, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($pedidosPorStatus, 'total')); ?>,
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
            }
        });

        // Gráfico de Pedidos por Dia
        new Chart(document.getElementById('pedidosDiasChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($pedidosPorDia, 'data')); ?>,
                datasets: [{
                    label: 'Pedidos',
                    data: <?php echo json_encode(array_column($pedidosPorDia, 'total')); ?>,
                    borderColor: '#007bff',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Clientes por Classificação
        new Chart(document.getElementById('clientesClassificacaoChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($clientesPorClassificacao, 'classificacao')); ?>,
                datasets: [{
                    label: 'Clientes',
                    data: <?php echo json_encode(array_column($clientesPorClassificacao, 'total')); ?>,
                    backgroundColor: ['#6c757d', '#ffc107', '#17a2b8']
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
