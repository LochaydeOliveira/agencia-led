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

if (!isset($input['refeicao_id']) || !isset($input['data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit();
}

$refeicao_id = (int)$input['refeicao_id'];
$data = $input['data'];

try {
    // Verificar se a refeição existe e pertence ao usuário
    $stmt = $pdo->prepare("SELECT id FROM alimentacao WHERE id = ? AND usuario_id = ? AND ativo = 1");
    $stmt->execute([$refeicao_id, $user_id]);
    
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Refeição não encontrada']);
        exit();
    }
    
    // Buscar checklist do dia
    $stmt = $pdo->prepare("SELECT * FROM checklist WHERE usuario_id = ? AND data = ?");
    $stmt->execute([$user_id, $data]);
    $checklist = $stmt->fetch();
    
    if ($checklist) {
        // Atualizar checklist existente
        $refeicoes_realizadas = json_decode($checklist['refeicoes_realizadas'], true) ?: [];
        
        if (isset($refeicoes_realizadas[$refeicao_id])) {
            unset($refeicoes_realizadas[$refeicao_id]); // Desmarcar
        } else {
            $refeicoes_realizadas[$refeicao_id] = true; // Marcar
        }
        
        $stmt = $pdo->prepare("UPDATE checklist SET refeicoes_realizadas = ?, data_atualizacao = NOW() WHERE id = ?");
        $stmt->execute([json_encode($refeicoes_realizadas), $checklist['id']]);
    } else {
        // Criar novo checklist
        $refeicoes_realizadas = [$refeicao_id => true];
        
        $stmt = $pdo->prepare("INSERT INTO checklist (usuario_id, data, refeicoes_realizadas) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $data, json_encode($refeicoes_realizadas)]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Refeição atualizada com sucesso']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?> 