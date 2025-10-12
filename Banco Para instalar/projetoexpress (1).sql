-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/10/2025 às 22:05
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `projetoexpress`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `texto` text NOT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `comentarios`
--

INSERT INTO `comentarios` (`id`, `item_id`, `usuario_id`, `texto`, `criado_em`, `atualizado_em`) VALUES
(2, 3, 3, 'gogo', '2025-10-12 17:04:20', '2025-10-12 17:04:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `entradas`
--

CREATE TABLE `entradas` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_entrada` datetime DEFAULT current_timestamp(),
  `responsavel_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `entradas`
--

INSERT INTO `entradas` (`id`, `item_id`, `quantidade`, `data_entrada`, `responsavel_id`, `observacoes`) VALUES
(1, 2, 80, '2025-10-11 16:03:32', 3, ''),
(2, 3, 80, '2025-10-11 16:12:57', 3, ''),
(3, 6, 10, '2025-10-11 16:44:54', 3, ''),
(4, 9, 100, '2025-10-11 20:27:12', 3, ''),
(5, 10, 130, '2025-10-11 20:32:24', 4, '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `responsavel_id` int(11) DEFAULT NULL,
  `data_movimentacao` datetime DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `historico`
--

INSERT INTO `historico` (`id`, `tipo`, `item_id`, `quantidade`, `responsavel_id`, `data_movimentacao`, `observacoes`) VALUES
(1, 'entrada', 2, 80, 3, '2025-10-11 16:03:32', ''),
(2, 'entrada', 3, 80, 3, '2025-10-11 16:12:57', ''),
(3, 'entrada', 6, 10, 3, '2025-10-11 16:44:54', ''),
(4, 'entrada', 9, 100, 3, '2025-10-11 20:27:12', ''),
(5, 'entrada', 10, 130, 4, '2025-10-11 20:32:24', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens`
--

CREATE TABLE `itens` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `nome` varchar(150) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `quantidade` int(11) DEFAULT 0,
  `localizacao` enum('Setor A','Setor B','Setor C') DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `criado_por` int(11) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens`
--

INSERT INTO `itens` (`id`, `codigo`, `nome`, `categoria`, `quantidade`, `localizacao`, `observacoes`, `criado_por`, `criado_em`, `atualizado_em`) VALUES
(2, '001', 'Cadeira japonesa', 'Cadeira', 84, 'Setor A', '', 3, '2025-10-11 16:03:32', '2025-10-11 20:26:37'),
(3, '002', 'Mesa Japonesa', 'Mesa', 80, 'Setor C', '', 3, '2025-10-11 16:12:57', '2025-10-11 16:12:57'),
(6, '003', 'Mesa Japonesa Grande', 'Mesa', 10, 'Setor B', '', 3, '2025-10-11 16:44:54', '2025-10-11 16:44:54'),
(9, '005', 'Craft Tabel', 'Bancada', 100, 'Setor C', '', 3, '2025-10-11 20:27:12', '2025-10-11 20:27:12'),
(10, '006', 'BackDeck', 'Bancada', 130, 'Setor A', '', 4, '2025-10-11 20:32:24', '2025-10-11 20:32:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `saidas`
--

CREATE TABLE `saidas` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `data_saida` datetime DEFAULT current_timestamp(),
  `destino` varchar(150) DEFAULT NULL,
  `responsavel_id` int(11) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','funcionario') DEFAULT 'funcionario',
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`, `atualizado_em`) VALUES
(3, 'murilo', 'koba@gmail.com', '$2y$10$GdSH58A1NPl5Jc3cOODMxeJrWW3otAGgOd3cuKILrWMcdGpDqOcIW', 'admin', '2025-10-11 14:30:34', '2025-10-11 14:30:34'),
(4, 'Cachorro', 'kobas@gmail.com', '$2y$10$feMuAKW/Q0ErQV4WdGwjIuNJC0G2uL7kMJ4241raB8RgCVQmBLWzC', 'funcionario', '2025-10-11 20:29:32', '2025-10-11 20:29:32');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_item_id` (`item_id`);

--
-- Índices de tabela `entradas`
--
ALTER TABLE `entradas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

--
-- Índices de tabela `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `criado_por` (`criado_por`);

--
-- Índices de tabela `saidas`
--
ALTER TABLE `saidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `responsavel_id` (`responsavel_id`);

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
-- AUTO_INCREMENT de tabela `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `entradas`
--
ALTER TABLE `entradas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `itens`
--
ALTER TABLE `itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `saidas`
--
ALTER TABLE `saidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `itens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `entradas`
--
ALTER TABLE `entradas`
  ADD CONSTRAINT `entradas_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `itens` (`id`),
  ADD CONSTRAINT `entradas_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `itens` (`id`),
  ADD CONSTRAINT `historico_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `itens`
--
ALTER TABLE `itens`
  ADD CONSTRAINT `itens_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `saidas`
--
ALTER TABLE `saidas`
  ADD CONSTRAINT `saidas_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `itens` (`id`),
  ADD CONSTRAINT `saidas_ibfk_2` FOREIGN KEY (`responsavel_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
