-- Script para criar a tabela leads para o formulário da mentoria
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `instagram` varchar(100) DEFAULT NULL,
  `momento` text NOT NULL,
  `renda` varchar(100) NOT NULL,
  `investimento` varchar(100) NOT NULL,
  `motivo` text NOT NULL,
  `compromisso1` tinyint(1) NOT NULL DEFAULT 0,
  `compromisso2` tinyint(1) NOT NULL DEFAULT 0,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `data_cadastro` (`data_cadastro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 