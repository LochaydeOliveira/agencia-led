-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 10/07/2025 às 00:12
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `paymen58_my_training_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alimentacao`
--

CREATE TABLE `alimentacao` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `horario` time NOT NULL,
  `alimento` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `quantidade_gramas` decimal(6,2) NOT NULL,
  `calorias` decimal(6,2) DEFAULT NULL,
  `proteinas` decimal(5,2) DEFAULT NULL,
  `carboidratos` decimal(5,2) DEFAULT NULL,
  `gorduras` decimal(5,2) DEFAULT NULL,
  `observacoes` text COLLATE utf8_unicode_ci,
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `alimentacao`
--

INSERT INTO `alimentacao` (`id`, `usuario_id`, `horario`, `alimento`, `quantidade_gramas`, `calorias`, `proteinas`, `carboidratos`, `gorduras`, `observacoes`, `ativo`, `data_criacao`) VALUES
(1, 1, '07:00:00', 'Aveia com Whey', 100.00, 350.00, 25.00, 45.00, 8.00, NULL, 1, '2025-07-10 02:14:53'),
(2, 1, '10:00:00', 'Banana', 120.00, 105.00, 1.30, 27.00, 0.40, NULL, 1, '2025-07-10 02:14:53'),
(3, 1, '12:30:00', 'Arroz Integral', 150.00, 150.00, 3.00, 30.00, 1.00, NULL, 1, '2025-07-10 02:14:53'),
(4, 1, '12:30:00', 'Frango Grelhado', 200.00, 330.00, 62.00, 0.00, 7.00, NULL, 1, '2025-07-10 02:14:53'),
(5, 1, '12:30:00', 'Brócolis', 100.00, 34.00, 2.80, 7.00, 0.40, NULL, 1, '2025-07-10 02:14:53'),
(6, 1, '15:30:00', 'Iogurte Grego', 200.00, 130.00, 20.00, 8.00, 4.00, NULL, 1, '2025-07-10 02:14:53'),
(7, 1, '18:00:00', 'Batata Doce', 150.00, 135.00, 3.00, 30.00, 0.20, NULL, 1, '2025-07-10 02:14:53'),
(8, 1, '18:00:00', 'Atum', 150.00, 180.00, 35.00, 0.00, 4.00, NULL, 1, '2025-07-10 02:14:53'),
(9, 1, '21:00:00', 'Caseína', 30.00, 120.00, 24.00, 3.00, 1.00, NULL, 1, '2025-07-10 02:14:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `checklist`
--

CREATE TABLE `checklist` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `treino_feito` json DEFAULT NULL COMMENT 'JSON com treino_id => boolean',
  `refeicoes_realizadas` json DEFAULT NULL COMMENT 'JSON com refeicao_id => boolean',
  `peso_dia` decimal(5,2) DEFAULT NULL,
  `observacoes` text COLLATE utf8_unicode_ci,
  `sensacao_geral` enum('Ótima','Boa','Regular','Ruim') COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_usuario`
--

CREATE TABLE `configuracoes_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tema_escuro` tinyint(1) DEFAULT '0',
  `notificacoes_treino` tinyint(1) DEFAULT '1',
  `notificacoes_alimentacao` tinyint(1) DEFAULT '1',
  `horario_treino` time DEFAULT NULL,
  `horario_alimentacao` time DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes_usuario`
--

INSERT INTO `configuracoes_usuario` (`id`, `usuario_id`, `tema_escuro`, `notificacoes_treino`, `notificacoes_alimentacao`, `horario_treino`, `horario_alimentacao`, `data_criacao`, `data_atualizacao`) VALUES
(1, 1, 0, 1, 1, NULL, NULL, '2025-07-10 02:14:53', '2025-07-10 02:14:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso`
--

CREATE TABLE `progresso` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `braco_esquerdo` decimal(4,1) DEFAULT NULL,
  `braco_direito` decimal(4,1) DEFAULT NULL,
  `cintura` decimal(4,1) DEFAULT NULL,
  `quadril` decimal(4,1) DEFAULT NULL,
  `coxa_esquerda` decimal(4,1) DEFAULT NULL,
  `coxa_direita` decimal(4,1) DEFAULT NULL,
  `foto_frontal` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_lateral` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_posterior` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `observacoes` text COLLATE utf8_unicode_ci,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos`
--

CREATE TABLE `treinos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT '0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado',
  `exercicio` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `series` int(11) NOT NULL DEFAULT '3',
  `repeticoes` int(11) NOT NULL DEFAULT '12',
  `carga_sugerida` decimal(5,2) DEFAULT NULL COMMENT 'Peso em kg',
  `descanso` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tempo de descanso entre séries',
  `observacoes` text COLLATE utf8_unicode_ci,
  `ordem` int(11) NOT NULL DEFAULT '0',
  `ativo` tinyint(1) DEFAULT '1',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `treinos`
--

INSERT INTO `treinos` (`id`, `usuario_id`, `dia_semana`, `exercicio`, `series`, `repeticoes`, `carga_sugerida`, `descanso`, `observacoes`, `ordem`, `ativo`, `data_criacao`) VALUES
(1, 1, 1, 'Supino Reto', 4, 12, 60.00, '90s', NULL, 1, 1, '2025-07-10 02:14:53'),
(2, 1, 1, 'Supino Inclinado', 3, 12, 50.00, '90s', NULL, 2, 1, '2025-07-10 02:14:53'),
(3, 1, 1, 'Voador', 3, 15, 25.00, '60s', NULL, 3, 1, '2025-07-10 02:14:53'),
(4, 1, 1, 'Tríceps na Polia', 3, 15, 30.00, '60s', NULL, 4, 1, '2025-07-10 02:14:53'),
(5, 1, 3, 'Agachamento', 4, 10, 80.00, '120s', NULL, 1, 1, '2025-07-10 02:14:53'),
(6, 1, 3, 'Leg Press', 3, 12, 120.00, '90s', NULL, 2, 1, '2025-07-10 02:14:53'),
(7, 1, 3, 'Extensão de Pernas', 3, 15, 45.00, '60s', NULL, 3, 1, '2025-07-10 02:14:53'),
(8, 1, 3, 'Flexão de Pernas', 3, 15, 40.00, '60s', NULL, 4, 1, '2025-07-10 02:14:53'),
(9, 1, 5, 'Puxada na Frente', 4, 12, 50.00, '90s', NULL, 1, 1, '2025-07-10 02:14:53'),
(10, 1, 5, 'Remada Curvada', 3, 12, 45.00, '90s', NULL, 2, 1, '2025-07-10 02:14:53'),
(11, 1, 5, 'Rosca Direta', 3, 12, 20.00, '60s', NULL, 3, 1, '2025-07-10 02:14:53'),
(12, 1, 5, 'Rosca Martelo', 3, 12, 18.00, '60s', NULL, 4, 1, '2025-07-10 02:14:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `objetivo` text COLLATE utf8_unicode_ci,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_atualizacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `altura`, `peso`, `objetivo`, `data_criacao`, `data_atualizacao`) VALUES
(1, 'Usuário Teste', 'teste@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, '2025-07-10 02:14:53', '2025-07-10 02:14:53');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alimentacao`
--
ALTER TABLE `alimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alimentacao_usuario` (`usuario_id`);

--
-- Índices de tabela `checklist`
--
ALTER TABLE `checklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_usuario_data` (`usuario_id`,`data`),
  ADD KEY `idx_checklist_usuario_data` (`usuario_id`,`data`);

--
-- Índices de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `progresso`
--
ALTER TABLE `progresso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_progresso_usuario_data` (`usuario_id`,`data`);

--
-- Índices de tabela `treinos`
--
ALTER TABLE `treinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_treinos_usuario_dia` (`usuario_id`,`dia_semana`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alimentacao`
--
ALTER TABLE `alimentacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `checklist`
--
ALTER TABLE `checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `progresso`
--
ALTER TABLE `progresso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `treinos`
--
ALTER TABLE `treinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alimentacao`
--
ALTER TABLE `alimentacao`
  ADD CONSTRAINT `alimentacao_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `checklist`
--
ALTER TABLE `checklist`
  ADD CONSTRAINT `checklist_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `configuracoes_usuario`
--
ALTER TABLE `configuracoes_usuario`
  ADD CONSTRAINT `configuracoes_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `progresso`
--
ALTER TABLE `progresso`
  ADD CONSTRAINT `progresso_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `treinos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
