-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 29/06/2025 às 14:46
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
-- Banco de dados: `paymen58_sistema_integrado_led`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `whatsapp` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `instagram` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `momento` text COLLATE utf8_unicode_ci,
  `renda` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `investimento` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motivo` text COLLATE utf8_unicode_ci,
  `compromisso1` tinyint(1) DEFAULT NULL,
  `compromisso2` tinyint(1) DEFAULT NULL,
  `data_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `naoquerofornecer` tinyint(1) NOT NULL DEFAULT '0',
  `naousoredesociais` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `leads`
--

INSERT INTO `leads` (`id`, `nome`, `email`, `whatsapp`, `instagram`, `momento`, `renda`, `investimento`, `motivo`, `compromisso1`, `compromisso2`, `data_envio`, `naoquerofornecer`, `naousoredesociais`) VALUES
(9, 'João Paulo', 'jpauloo@gmail.com', '85921447153', '', 'Estou pronto para começar um negócio de verdade, com acompanhamento.', 'De R$2.001 a R$5.000', 'De R$1.501 a R$3.000', 'Apenas Teste', 1, 1, '2025-06-27 14:54:21', 0, 0),
(10, 'Maria Jane', 'maria@hmail.com', '85921447153', 'maria_jane', 'Estou pronto para começar um negócio de verdade, com acompanhamento.', 'De R$5.001 a R$10.000', 'De R$1.501 a R$3.000', 'Apenas teste', 1, 1, '2025-06-27 19:42:56', 0, 0),
(11, 'Joana M.', 'joa@gmail.com', '85921447153', 'joa_teste', 'Estou pronto para começar um negócio de verdade, com acompanhamento.', 'De R$5.001 a R$10.000', 'De R$5.001 a R$15.000', 'Apenas um teste', 1, 1, '2025-06-27 19:53:54', 0, 0),
(12, 'Pedro Junior', 'pedroj@gmail.com', '85921447153', 'pedro', 'Estou pronto para começar um negócio de verdade, com acompanhamento.', 'De R$5.001 a R$10.000', 'De R$5.001 a R$15.000', 'Apenas teste', 1, 1, '2025-06-27 20:00:28', 0, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
