-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 18/07/2025 às 16:03
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `paymen58_validapro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `user_id`, `token`, `expira`, `usado`, `created_at`) VALUES
(1, 2, 'f20db46229384c6713be2f2f0377450e6b2829c6169b630dc4f60c4a9ae22e69', '2025-07-18 16:47:26', 0, '2025-07-18 18:47:26'),
(2, 2, '76b78013eb3ef4c4fa22fc1ad6b2d35cfca949de0537a5be9cb01cba954300ae', '2025-07-18 16:47:37', 0, '2025-07-18 18:47:37'),
(3, 2, 'f8e2d3edae98d6af8d89cd044158f0979426bcbe645a5ff7ac02995385a321a1', '2025-07-18 16:48:15', 0, '2025-07-18 18:48:15'),
(4, 2, 'f912fba2e3349a5000797a4f01e6444ce62e9c4bbea14b78ae9b39908d539c80', '2025-07-18 17:00:11', 0, '2025-07-18 19:00:11'),
(5, 2, 'bc9222bd8893dc434e97c9d18e13d3894284dc663f9bc32bb61e63eaeade8e84', '2025-07-18 17:00:22', 0, '2025-07-18 19:00:22'),
(6, 2, '1f102c062c5c8429159743b03cc69212504b0ea4bededcf432e0a5f44f0fcc8c', '2025-07-18 17:00:27', 0, '2025-07-18 19:00:27'),
(7, 2, '9331818ccec5dd18933eb411b76a51920377177e90486791f8276e1e7ac1badf', '2025-07-18 17:00:32', 0, '2025-07-18 19:00:32'),
(8, 2, '7e38dcdc8192227ef7d639160997378dbf128bed1b06027c85f557abf824e942', '2025-07-18 17:00:36', 0, '2025-07-18 19:00:36'),
(9, 2, '0ee2d5e24a893f1c59bdc59774150e8cb892190a4558e797474ce17b3669cfcd', '2025-07-18 17:01:00', 0, '2025-07-18 19:01:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `promessa_principal` text COLLATE utf8mb4_unicode_ci,
  `cliente_consciente` text COLLATE utf8mb4_unicode_ci,
  `beneficios` text COLLATE utf8mb4_unicode_ci,
  `mecanismo_unico` text COLLATE utf8mb4_unicode_ci,
  `pontos` int(11) DEFAULT '0',
  `nota_final` int(11) DEFAULT '0',
  `mensagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Cliente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expira` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `usuario`, `created_at`, `active`, `last_login`, `reset_token`, `reset_token_expira`) VALUES
(2, 'lochaydeguerreiro@hotmail.com', '$2y$10$.n51mlhZXL.2fZ6oHbuL7eGmiA/deuKbOvIpdmeJ9KVRJhMeFIie.', 'Lochayde', 'Administrador', '2025-07-17 10:11:59', 1, '2025-07-18 11:48:45', '1f9b6a11f3fa0c45ac99bbcdbbd632eb95a7caed00c5c7f808b4571ffe324304', '2025-07-18 12:50:56'),
(3, 'admin@exemplo.com', '$2y$10$uR1Pho3bWV1azB3FdlYa7uWM925bGwe.PYvtMj52FRnqdnETbX8My', 'Administrador', 'Cliente', '2025-07-17 22:29:59', 1, '2025-07-17 23:55:47', NULL, NULL),
(4, 'admin@validapro.com', '$2y$10$lJzY6RlNxY5BJsmNtA6zS.2KK17J7O7SIfWebsCNhg3uKjRIJwe.i', 'Administrador ValidaPro', 'admin', '2025-07-18 00:27:04', 1, '2025-07-17 22:24:02', NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expira` (`expira`);

--
-- Índices de tabela `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD CONSTRAINT `recuperacao_senha_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
