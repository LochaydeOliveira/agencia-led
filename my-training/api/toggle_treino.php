<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Ler dados JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['treino_id']) || !isset($input['data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit();
}

$treino_id = (int)$input['treino_id'];
$data = $input['data'];

try {
    // Verificar se o treino existe e pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM treinos WHERE id = ? AND usuario_id = ? AND ativo = 1");
    $stmt->execute([$treino_id, $user_id]);
    
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Treino não encontrado']);
        exit();
    }
    
    // Buscar checklist do dia
    $stmt = $pdo->prepare("SELECT * FROM checklist WHERE usuario_id = ? AND data = ?");
    $stmt->execute([$user_id, $data]);
    $checklist = $stmt->fetch();
    
    if ($checklist) {
        // Atualizar checklist existente
        $treinos_feitos = json_decode($checklist['treino_feito'], true) ?: [];
        
        if (isset($treinos_feitos[$treino_id])) {
            unset($treinos_feitos[$treino_id]); // Desmarcar
        } else {
            $treinos_feitos[$treino_id] = true; // Marcar
        }
        
        $stmt = $pdo->prepare("UPDATE checklist SET treino_feito = ?, data_atualizacao = NOW() WHERE id = ?");
        $stmt->execute([json_encode($treinos_feitos), $checklist['id']]);
    } else {
        // Criar novo checklist
        $treinos_feitos = [$treino_id => true];
        
        $stmt = $pdo->prepare("INSERT INTO checklist (usuario_id, data, treino_feito) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $data, json_encode($treinos_feitos)]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Treino atualizado com sucesso']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?> 