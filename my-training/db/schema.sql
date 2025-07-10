ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS paymen58_my_training_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paymen58_my_training_db;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    altura DECIMAL(3,2) NULL,
    peso DECIMAL(5,2) NULL,
    objetivo TEXT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de treinos
CREATE TABLE IF NOT EXISTS treinos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    dia_semana TINYINT NOT NULL COMMENT '0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado',
    exercicio VARCHAR(200) NOT NULL,
    series INT NOT NULL DEFAULT 3,
    repeticoes INT NOT NULL DEFAULT 12,
    carga_sugerida DECIMAL(5,2) NULL COMMENT 'Peso em kg',
    descanso VARCHAR(50) NULL COMMENT 'Tempo de descanso entre séries',
    observacoes TEXT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de alimentação
CREATE TABLE IF NOT EXISTS alimentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    horario TIME NOT NULL,
    alimento VARCHAR(200) NOT NULL,
    quantidade_gramas DECIMAL(6,2) NOT NULL,
    calorias DECIMAL(6,2) NULL,
    proteinas DECIMAL(5,2) NULL,
    carboidratos DECIMAL(5,2) NULL,
    gorduras DECIMAL(5,2) NULL,
    observacoes TEXT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de checklist diário
CREATE TABLE IF NOT EXISTS checklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data DATE NOT NULL,
    treino_feito JSON NULL COMMENT 'JSON com treino_id => boolean',
    refeicoes_realizadas JSON NULL COMMENT 'JSON com refeicao_id => boolean',
    peso_dia DECIMAL(5,2) NULL,
    observacoes TEXT NULL,
    sensacao_geral ENUM('Ótima', 'Boa', 'Regular', 'Ruim') NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_data (usuario_id, data)
);

-- Tabela de progresso (fotos e medidas)
CREATE TABLE IF NOT EXISTS progresso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data DATE NOT NULL,
    peso DECIMAL(5,2) NULL,
    altura DECIMAL(3,2) NULL,
    braco_esquerdo DECIMAL(4,1) NULL,
    braco_direito DECIMAL(4,1) NULL,
    cintura DECIMAL(4,1) NULL,
    quadril DECIMAL(4,1) NULL,
    coxa_esquerda DECIMAL(4,1) NULL,
    coxa_direita DECIMAL(4,1) NULL,
    foto_frontal VARCHAR(255) NULL,
    foto_lateral VARCHAR(255) NULL,
    foto_posterior VARCHAR(255) NULL,
    observacoes TEXT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de configurações do usuário
CREATE TABLE IF NOT EXISTS configuracoes_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL UNIQUE,
    tema_escuro BOOLEAN DEFAULT FALSE,
    notificacoes_treino BOOLEAN DEFAULT TRUE,
    notificacoes_alimentacao BOOLEAN DEFAULT TRUE,
    horario_treino TIME NULL,
    horario_alimentacao TIME NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Índices para melhor performance
CREATE INDEX idx_treinos_usuario_dia ON treinos(usuario_id, dia_semana);
CREATE INDEX idx_alimentacao_usuario ON alimentacao(usuario_id);
CREATE INDEX idx_checklist_usuario_data ON checklist(usuario_id, data);
CREATE INDEX idx_progresso_usuario_data ON progresso(usuario_id, data);

-- Inserir usuário de teste (senha: 123456)
INSERT INTO usuarios (nome, email, senha_hash) VALUES 
('Usuário Teste', 'teste@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir alguns treinos de exemplo
INSERT INTO treinos (usuario_id, dia_semana, exercicio, series, repeticoes, carga_sugerida, descanso, ordem) VALUES
(1, 1, 'Supino Reto', 4, 12, 60.0, '90s', 1),
(1, 1, 'Supino Inclinado', 3, 12, 50.0, '90s', 2),
(1, 1, 'Voador', 3, 15, 25.0, '60s', 3),
(1, 1, 'Tríceps na Polia', 3, 15, 30.0, '60s', 4),
(1, 3, 'Agachamento', 4, 10, 80.0, '120s', 1),
(1, 3, 'Leg Press', 3, 12, 120.0, '90s', 2),
(1, 3, 'Extensão de Pernas', 3, 15, 45.0, '60s', 3),
(1, 3, 'Flexão de Pernas', 3, 15, 40.0, '60s', 4),
(1, 5, 'Puxada na Frente', 4, 12, 50.0, '90s', 1),
(1, 5, 'Remada Curvada', 3, 12, 45.0, '90s', 2),
(1, 5, 'Rosca Direta', 3, 12, 20.0, '60s', 3),
(1, 5, 'Rosca Martelo', 3, 12, 18.0, '60s', 4);

-- Inserir algumas refeições de exemplo
INSERT INTO alimentacao (usuario_id, horario, alimento, quantidade_gramas, calorias, proteinas, carboidratos, gorduras) VALUES
(1, '07:00:00', 'Aveia com Whey', 100.0, 350.0, 25.0, 45.0, 8.0),
(1, '10:00:00', 'Banana', 120.0, 105.0, 1.3, 27.0, 0.4),
(1, '12:30:00', 'Arroz Integral', 150.0, 150.0, 3.0, 30.0, 1.0),
(1, '12:30:00', 'Frango Grelhado', 200.0, 330.0, 62.0, 0.0, 7.0),
(1, '12:30:00', 'Brócolis', 100.0, 34.0, 2.8, 7.0, 0.4),
(1, '15:30:00', 'Iogurte Grego', 200.0, 130.0, 20.0, 8.0, 4.0),
(1, '18:00:00', 'Batata Doce', 150.0, 135.0, 3.0, 30.0, 0.2),
(1, '18:00:00', 'Atum', 150.0, 180.0, 35.0, 0.0, 4.0),
(1, '21:00:00', 'Caseína', 30.0, 120.0, 24.0, 3.0, 1.0);

-- Inserir configurações padrão
INSERT INTO configuracoes_usuario (usuario_id, tema_escuro, notificacoes_treino, notificacoes_alimentacao) VALUES
(1, FALSE, TRUE, TRUE); 