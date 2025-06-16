-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 15/06/2025 às 23:13
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
-- Estrutura para tabela `arquivos_pdf`
--

CREATE TABLE `arquivos_pdf` (
  `id` int(11) NOT NULL,
  `product_id_ymp` bigint(20) NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `caminho` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `arquivos_pdf`
--

INSERT INTO `arquivos_pdf` (`id`, `product_id_ymp`, `nome`, `caminho`) VALUES
(4, 40621209, 'Fornecedores_Nicho_Decoração.pdf', '/files/Fornecedores_Nicho_Decoração.pdf'),
(5, 40685268, 'Fornecedores_Nicho_Bebê_Enxoval_Decoração_Pet.pdf', '/files/Fornecedores_Nicho_Bebê_Enxoval_Decoração_Pet.pdf'),
(6, 40688727, 'Fornecedores_Nicho_Brinquedos.pdf', '/files/Fornecedores_Nicho_Brinquedos.pdf');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ativo','inativo','suspenso') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `classificacao` enum('prata','ouro','diamante') COLLATE utf8mb4_unicode_ci DEFAULT 'prata'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `whatsapp`, `senha`, `status`, `criado_em`, `atualizado_em`, `classificacao`) VALUES
(23, 'Cliente Teste', 'fycelbrasil@gmail.com', '85999671024', '$2y$10$uE6yJhVGFvx1X7V9gdlUf.PENgbCfEgegWTiG2K.mQK4jWjBsJGqm', 'ativo', '2025-06-06 06:06:49', '2025-06-07 00:48:00', 'ouro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes_listas`
--

CREATE TABLE `clientes_listas` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `cliente` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lista_id` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ativo','bloqueado') COLLATE utf8_unicode_ci DEFAULT 'bloqueado',
  `nome_lista` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `clientes_listas`
--

INSERT INTO `clientes_listas` (`id`, `cliente_id`, `cliente`, `lista_id`, `criado_em`, `status`, `nome_lista`) VALUES
(15, 23, 'Cliente Teste', 2, '2025-06-06 06:06:51', 'ativo', 'Bebê, Casa e Banho');

-- --------------------------------------------------------

--
-- Estrutura para tabela `downloads`
--

CREATE TABLE `downloads` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `download_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `download_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `downloaded` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `download_tokens`
--

CREATE TABLE `download_tokens` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `downloaded` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `product_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `listas`
--

CREATE TABLE `listas` (
  `id` int(11) NOT NULL,
  `product_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci,
  `conteudo_html` text COLLATE utf8_unicode_ci NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `link_de_compra` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `listas`
--

INSERT INTO `listas` (`id`, `product_id`, `nome`, `descricao`, `conteudo_html`, `preco`, `link_de_compra`) VALUES
(1, '40621209', 'Decoração', 'Fornecedores de móveis e decoração para casa.', '<a href=\"https://moveistrovarelli.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Moveis Trovarelli</a><br>\r\n<a href=\"https://www.decormoveis.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Decor Moveis</a><br>\r\n<a href=\"https://www.gazinatacado.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Gazin Atacado</a><br>\r\n<a href=\"https://www.lenobre.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Le Nobre</a><br>\r\n<a href=\"https://www.printile.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Printile</a><br>\r\n<a href=\"https://www.souflorir.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Sou Florir</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/NEOYZECNBO'),
(2, '40830243', 'Bebê, Casa e Banho', 'Distribuidores e atacadistas para bebê, casa e banho.', '<a href=\"https://www.maisquedistribuidora.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Mais que Distribuidora</a>', 29.90, 'https://seguro.agencialed.com/r/8X638DBZ6E'),
(3, '40688727', 'Brinquedos', 'Catálogo de fornecedores de brinquedos nacionais.', '\r\n<a href=\"https://europio.catalogomobile.com.br/dashboard/products\" target=\"_blank\" class=\"btn btn-link-custom\">Europio</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/E4C8IV5R3L'),
(4, '40741683', 'Joias, Folheados e Acessórios', 'Joias, bijuterias, folheados e dropshipping de acessórios.', '\r\n<a href=\"https://www.luxjoias.com/dropshipping-revenda-i-47.html\" target=\"_blank\" class=\"btn btn-link-custom\">Lux Joias</a><br>\r\n<a href=\"https://dropse.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Dropse</a><br>\r\n<a href=\"https://www.florattajoias.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Floratta Joias</a><br>\r\n<a href=\"https://www.sobellavariedades.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Sobella Variedades</a><br>\r\n<a href=\"https://www.imagemfolheados.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Imagem Folheados</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/NEOYZECNBO'),
(5, '40855865', 'Calçados e Vestuário', 'Calçados, roupas e acessórios direto da fábrica.', '\r\n<a href=\"https://www.imperiodasrasteiras.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Império das Rasteiras</a><br>\r\n<a href=\"https://www.atacadobarato.com/\" target=\"_blank\" class=\"btn btn-link-custom\">Atacado Barato</a><br>\r\n<a href=\"https://www.revendadecalcados.com.br/painel_acesso.php\" target=\"_blank\" class=\"btn btn-link-custom\">Revenda de Calçados</a><br>\r\n<a href=\"https://www.parishoes.com.br/pagina/revenda.html\" target=\"_blank\" class=\"btn btn-link-custom\">Pari Shoes</a><br>\r\n<a href=\"https://rickshoes.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Rick Shoes</a><br>\r\n<a href=\"https://www.bmshopdrop.com.br/pagina/dropshipping-manual-do-lojista-reveendedor.html\" target=\"_blank\" class=\"btn btn-link-custom\">BM Shop Drop</a><br>\r\n<a href=\"https://cftdropshipping.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">CFT Dropshipping</a><br>\r\n<a href=\"https://www.francasapatos.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Franca Sapatos</a><br>\r\n<a href=\"https://www.dropaaqui.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Dropa Aqui</a><br>\r\n<a href=\"https://suafabrica.com.br/blogs/como-funciona/quais-as-vantagens-de-fazer-dropshipping\" target=\"_blank\" class=\"btn btn-link-custom\">Sua Fábrica</a><br>\r\n<a href=\"https://www.kaisan.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Kaisan</a><br>\r\n<a href=\"https://www.atacadaodaroupa.com/\" target=\"_blank\" class=\"btn btn-link-custom\">Atacadão da Roupa</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/TR7LMVIRHR'),
(6, '40855591', 'Relógios', 'Fornecedor especializado em relógios para revenda.', '\r\n<a href=\"https://www.relogiosnoatacado.com/m/dropshipping/\" target=\"_blank\" class=\"btn btn-link-custom\">Relógios no Atacado</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/RVNA9BFD2H'),
(7, '40860003', 'Perfumes', 'Perfumes e cosméticos para revenda online.', '\r\n<a href=\"https://www.bmshopdrop.com.br/home\" target=\"_blank\" class=\"btn btn-link-custom\">BM Shop Drop</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/JSKDRNDQJ0'),
(8, '40860589', 'Autopeças e Ferramentas', 'Distribuidores e fornecedores de automotivas, ferramentas e peças.', '\r\n<a href=\"https://www.laquila.com.br/seja-um-revendedor\" target=\"_blank\" class=\"btn btn-link-custom\">Laquila</a><br>\r\n<a href=\"https://www.gb.com.br/dropshipping/\" target=\"_blank\" class=\"btn btn-link-custom\">GB</a><br>\r\n<a href=\"https://www.shoppecas.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Shop Peças</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/2I5VKL2MJG'),
(9, '40858694', 'Informática, Eletrônicos e Sonorização', 'Fornecedores nacionais de eletrônicos e tecnologia.', '\r\n<a href=\"https://hayamax.com.br/dropshipping\" target=\"_blank\" class=\"btn btn-link-custom\">Hayamax</a><br>\r\n<a href=\"https://www.hayonik.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Hayonik</a>', 29.90, 'https://seguro.agencialed.com/r/L6H2XH3MPE'),
(10, '16494857', 'Diversas Categorias', 'Plataformas e fornecedores variados de diversos segmentos.', '\r\n<a href=\"https://fornecedornacional.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Fornecedor Nacional</a><br>\r\n<a href=\"https://atacaly.com/\" target=\"_blank\" class=\"btn btn-link-custom\">Atacaly</a><br>\r\n<a href=\"https://dinka.com.br/categoria-produto/fornecedor-dropshipping/todos-produtos/\" target=\"_blank\" class=\"btn btn-link-custom\">Dinka</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/NEOYZECNBO'),
(11, '38912776', 'Plataformas de Drop Nacional', 'Acesse plataformas prontas de dropshipping nacional.', '\r\n<a href=\"https://primodrop.online/\" target=\"_blank\" class=\"btn btn-link-custom\">Primodrop</a><br>\r\n<a href=\"https://www.updrop.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Updrop</a><br>\r\n<a href=\"https://updrop.online/\" target=\"_blank\" class=\"btn btn-link-custom\">Catálogo Updrop</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/NEOYZECNBO'),
(12, '40830037', 'Pets', 'Distribuidores e atacadistas para pet shops.', '\r\n<a href=\"https://www.dropet.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Dropet</a>\r\n<br>\r\n<a href=\"https://www.molinaspet.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Molinas Pet</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/K7M77AIF1S'),
(13, '40830204', 'Enxoval', 'Distribuidores e atacadistas para enxovais.', '<a href=\"https://linkme.bio/deccoralle\" target=\"_blank\" class=\"btn btn-link-custom\">Deccoralle Decor</a><br>\r\n<a href=\"https://www.lp.gugadistribuidoraibitinga.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Guga Distribuidora</a><br>\r\n<a href=\"https://www.gugadistribuidoraibitinga.com.br/\" target=\"_blank\" class=\"btn btn-link-custom\">Guga Distribuidora - Catálogo</a>\r\n', 29.90, 'https://seguro.agencialed.com/r/2PEWUIGUZF'),
(26, 'F001000', 'Moda Masculina', 'Lista completa de fornecedores especializados em Moda Masculina.', '<ul>\r\n  <li><strong>Dropa Aqui</strong><a href=\"https://www.dropaaqui.com.br/\" target=\"_blank\"><img class=\"globo\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\">\r\n</a>\r\n  </li>\r\n  <li><strong>Atacado Pima</strong><a href=\"https://www.instagram.com/atacadopima\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\">\r\n</a>\r\n  </li>\r\n  <li><strong>RP Imports</strong><a href=\"https://www.lojarpimports.com.br/\" target=\"_blank\"><img class=\"globo\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  <li><strong>Redentor</strong><a href=\"https://www.instagram.com/redentoratacados/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n</ul>', 19.90, 'https://compra.exemplo.com'),
(28, 'F002', 'Moda Feminina', 'Lista completa de fornecedores especializados em Moda Feminina.', '<ul>\r\n  <li>\r\n    <strong>Atacadão da Roupa</strong>\r\n    <a href=\"https://www.instagram.com/atacadaodaroupaoficial/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  <li>\r\n    <strong>Tem que ter senhorita</strong>\r\n    <a href=\"https://www.instagram.com/temquetersenhorita\" target=\"_blank\">\r\n        <img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\">\r\n    </a>\r\n  </li>\r\n  <li>\r\n    <strong>Oficina Girls</strong>\r\n    <a href=\"https://www.instagram.com/officinagirlsoficial\" target=\"_blank\">\r\n        <img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\">\r\n    </a>\r\n  </li>\r\n  <li>\r\n    <strong>La Belle Atacado</strong>\r\n    <a href=\"https://linktr.ee/lojalabelle\" target=\"_blank\">\r\n        <img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\">\r\n    </a>\r\n  </li>\r\n  <li>\r\n    <strong>Fiorella Fahion</strong>\r\n    <a href=\"https://www.instagram.com/fiorella.fashion\" target=\"_blank\">\r\n        <img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\">\r\n    </a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Mira Luxo</strong>\r\n    <a href=\"https://www.instagram.com/mirafashion\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://www.miraluxo.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/miraluxomodas\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Mia Store</strong>\r\n    <a href=\"https://www.instagram.com/miastoreatacado\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Le Vantage</strong>\r\n    <a href=\"https://linktr.ee/le_vantage\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/le_vantage/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>BS Moda</strong>\r\n    <a href=\"https://linkbio.co/60704006PHzDD\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/bsmodasatacadooficial/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Dona Flor</strong>\r\n    <a href=\"https://www.instagram.com/donaflorgyn\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://linktr.ee/donaflorgyn\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Morena Flor</strong>\r\n    <a href=\"https://www.instagram.com/morenaflor_bras/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://www.morenaflorbras.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>MB Atacados</strong>\r\n    <a href=\"https://www.instagram.com/mb_atacados/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://linktr.ee/mbatacados\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>Elite Modas 20</strong>\r\n    <a href=\"https://instabio.cc/20623BTQ2ew\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/elitemodas20\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>Elite Modas 20</strong>\r\n    <a href=\"https://www.instagram.com/elitemodas20\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>Pimenta Brava</strong>\r\n    <a href=\"https://www.instagram.com/pimentabrava_m/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>Luxo In foco</strong>\r\n    <a href=\"https://www.instagram.com/luxoinfoco/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n</li>\r\n\r\n  <li>\r\n    <strong>Milly Looks</strong>\r\n    <a href=\"https://linktr.ee/millylooks_atacado\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/millylooks_atacado\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Proesi Textil</strong>\r\n    <a href=\"https://www.proesitextil.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/proesi_textil/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Aquarella Tricot</strong>\r\n    <a href=\"https://www.aquarellatricot.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/aquarella_tricot/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n\r\n  <li>\r\n    <strong>Jessy</strong>\r\n    <a href=\"https://linkme.bio/Jessy.modafeminina\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/jessy.modafeminina/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Paula</strong>\r\n    <a href=\"https://www.instagram.com/paulamodas_10/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Roupas Baratas</strong>\r\n    <a href=\"https://www.roupasparaatacado.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Tão Feminina</strong>\r\n    <a href=\"https://www.taofeminino.app.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://linktr.ee/taofemininoofc\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/taofeminino.oficial/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>MN Modas</strong>\r\n    <a href=\"https://www.instagram.com/mn.moda\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Dress Cool</strong>\r\n    <a href=\"https://www.instagram.com/lojadresscool/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://beacons.ai/dresscool\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>MP Confecções</strong>\r\n    <a href=\"https://www.instagram.com/mp_confeccoes/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Delicada Moda Mãe e Filha</strong>\r\n    <a href=\"https://www.instagram.com/delicadaconfeccaoatacado\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Vestidos de Festas</strong>\r\n    <a href=\"https://linktr.ee/nayaracruzoficial\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.nayaracruz.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/vestidosnayaracruz/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Beloa (body)</strong>\r\n    <a href=\"https://linktr.ee/contatobeloa\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n    <a href=\"https://www.instagram.com/lojabeloa/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Super Vaidosa</strong>\r\n    <a href=\"https://www.instagram.com/supervaidosaatacado\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://www.supervaidosaatacado.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Red Blue</strong>\r\n    <a href=\"https://www.instagram.com/redblue.atacado/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://www.redblueatacado.com.br/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Camila Modas</strong>\r\n    <a href=\"https://www.instagram.com/camilamodasmc/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://linkbio.co/CamilaModasBras\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n  <li>\r\n    <strong>Clara Flor</strong>\r\n    <a href=\"https://www.instagram.com/claraflor_flor/\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/instagram.svg\" alt=\"icone instagram\"></a>\r\n    <a href=\"https://bio.site/claraflor\" target=\"_blank\"><img class=\"instagram\" src=\"assets-agencia-led/icones-svg/globo.svg\" alt=\"icone globo\"></a>\r\n  </li>\r\n  \r\n</ul>', 19.90, 'https://compra.exemplo.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detalhes` text COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `yampi_order_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `yampi_order_id`, `order_number`, `customer_email`, `customer_name`, `product_id`, `status`, `created_at`, `updated_at`) VALUES
(94, '137740549', '339833718449667', 'fycelbrasil@gmail.com', 'Cliente Teste', '40830243', 'paid', '2025-06-06 06:04:28', '2025-06-07 03:58:07'),
(95, '137808708', '339833470169515', 'lochaydeguerreiro2@gmail.com', 'Maleta Teste', '40741683', 'pending', '2025-06-07 03:54:51', '2025-06-07 03:58:16'),
(96, '137808824', '339833871848599', 'lochaydeguerreiro2@gmail.com', 'Maleta Teste', '40933079', 'Aguardando Pagamento', '2025-06-07 04:02:13', '2025-06-07 04:02:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao_senha`
--

CREATE TABLE `recuperacao_senha` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `recuperacao_senha`
--

INSERT INTO `recuperacao_senha` (`id`, `cliente_id`, `token`, `expira`, `usado`, `created_at`) VALUES
(1, 23, '953092d7ef06b2e008a6fff680ec806d1eb2b946dcb3881544e2ebe75904b74a', '2025-06-06 21:03:47', 0, '2025-06-06 23:03:47'),
(2, 23, 'c3107b8581b4bcdb735311e0c298f843cb38be6a087663c1df9a834550b9e342', '2025-06-06 21:05:36', 0, '2025-06-06 23:05:36'),
(3, 23, 'f4a0ac400390a70347101611105a5115d42c0699024e2da57be47cb8350980d4', '2025-06-06 21:05:42', 0, '2025-06-06 23:05:42'),
(4, 23, '5d8fdc5b06f70780b6370a1a05e86dc23c2315f37de5946b138c7ea9784911cc', '2025-06-06 21:05:49', 0, '2025-06-06 23:05:49'),
(5, 23, 'adbe8a9acea8318a3f16ca8726dcc46c87abbd6fb8360e195fc29b20712ae27f', '2025-06-06 21:06:09', 0, '2025-06-06 23:06:09'),
(6, 23, 'f49027820eb633b5826967c8823f67d3091fca68689ac0c724f03eab7ee36a33', '2025-06-06 21:16:48', 0, '2025-06-06 23:16:48'),
(7, 23, '594f75255a6ff90705e6f62d81bf2498f43a4655a997922663131708c691a7b6', '2025-06-06 21:17:24', 0, '2025-06-06 23:17:24'),
(8, 23, '1e6870d8475b413a3c0349c62ac08ac91365240451334697fe4fa70d0e1cba9a', '2025-06-06 21:46:15', 1, '2025-06-06 23:46:15'),
(9, 23, '93458400f7d9c537e34bbe66af25a146210ef3d719662a125e85e7f0ae522bc3', '2025-06-06 22:40:03', 1, '2025-06-07 00:40:03'),
(10, 23, 'c708d82dffa396adf1ac7629a0f67aa758bf42e4382e5b3445c3d4cce764ef2d', '2025-06-06 22:47:23', 1, '2025-06-07 00:47:23');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` enum('admin','operador') COLLATE utf8mb4_unicode_ci DEFAULT 'operador',
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  `token_recuperacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expira_token` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel`, `status`, `token_recuperacao`, `expira_token`, `criado_em`, `atualizado_em`) VALUES
(1, 'Gerente', 'admin@empresa.com', '$2y$10$N2X7zk.mIq0MMtRKr.lGLe67ts1WbSupmikSeBfxL6syU8iIiIg1C', 'admin', 'ativo', NULL, NULL, '2025-05-26 16:30:55', '2025-05-26 17:07:29');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arquivos_pdf`
--
ALTER TABLE `arquivos_pdf`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `clientes_listas`
--
ALTER TABLE `clientes_listas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cliente_id` (`cliente_id`,`lista_id`),
  ADD KEY `lista_id` (`lista_id`);

--
-- Índices de tabela `downloads`
--
ALTER TABLE `downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `download_token` (`download_token`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `download_tokens`
--
ALTER TABLE `download_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `listas`
--
ALTER TABLE `listas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `yampi_order_id` (`yampi_order_id`);

--
-- Índices de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expira` (`expira`);

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
-- AUTO_INCREMENT de tabela `arquivos_pdf`
--
ALTER TABLE `arquivos_pdf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `clientes_listas`
--
ALTER TABLE `clientes_listas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `downloads`
--
ALTER TABLE `downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `download_tokens`
--
ALTER TABLE `download_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de tabela `listas`
--
ALTER TABLE `listas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT de tabela `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clientes_listas`
--
ALTER TABLE `clientes_listas`
  ADD CONSTRAINT `clientes_listas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `clientes_listas_ibfk_2` FOREIGN KEY (`lista_id`) REFERENCES `listas` (`id`);

--
-- Restrições para tabelas `downloads`
--
ALTER TABLE `downloads`
  ADD CONSTRAINT `downloads_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Restrições para tabelas `download_tokens`
--
ALTER TABLE `download_tokens`
  ADD CONSTRAINT `download_tokens_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `recuperacao_senha`
--
ALTER TABLE `recuperacao_senha`
  ADD CONSTRAINT `recuperacao_senha_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
