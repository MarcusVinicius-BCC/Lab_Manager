-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/09/2025 às 00:21
-- Versão do servidor: 8.0.42
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lab_manager`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas`
--

CREATE TABLE `aulas` (
  `id` int NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `professor_id` int DEFAULT NULL,
  `laboratorio_id` int DEFAULT NULL,
  `turno` varchar(50) NOT NULL,
  `dia_semana` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `aulas`
--

INSERT INTO `aulas` (`id`, `disciplina`, `professor_id`, `laboratorio_id`, `turno`, `dia_semana`) VALUES
(1, 'arquitetura', 2, 1, 'manhã', 'segunda');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `id` int NOT NULL,
  `aula_id` int DEFAULT NULL,
  `dia_semana` varchar(20) DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `capacidade` int DEFAULT NULL,
  `numero` varchar(255) NOT NULL,
  `projetor` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nome`, `capacidade`, `numero`, `projetor`) VALUES
(1, 'Labinova', 35, '219', 1),
(3, 'Laboratório de programação', 35, '37', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `monitores`
--

CREATE TABLE `monitores` (
  `id` int NOT NULL,
  `nome` varchar(255) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `turno` varchar(50) NOT NULL,
  `laboratorio_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `monitores`
--

INSERT INTO `monitores` (`id`, `nome`, `matricula`, `turno`, `laboratorio_id`) VALUES
(1, 'Fernando', '20230202', 'manhã', 1),
(2, 'valter', '23465', 'tarde', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `disciplina` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `nome`, `email`, `telefone`, `disciplina`) VALUES
(2, 'Cássio', NULL, NULL, 'arquitetura');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'comum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `role`) VALUES
(1, 'admin', '$2y$10$AzSHCHUn24XJoEPRGKeBquJntpNR9o998JNeZ5tBLsfL1Pr8P.6p6', 'admin');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professor_id` (`professor_id`),
  ADD KEY `laboratorio_id` (`laboratorio_id`);

--
-- Índices de tabela `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aula_id` (`aula_id`);

--
-- Índices de tabela `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `monitores`
--
ALTER TABLE `monitores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `monitores`
--
ALTER TABLE `monitores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aulas`
--
ALTER TABLE `aulas`
  ADD CONSTRAINT `aulas_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`),
  ADD CONSTRAINT `aulas_ibfk_2` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`);

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
