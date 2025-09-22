-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/09/2025 às 03:53
-- Versão do servidor: 10.4.32-MariaDB
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
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `laboratorio_id` int(11) NOT NULL,
  `dia` date NOT NULL,
  `turno` enum('manhã','tarde','noite') NOT NULL,
  `motivo` text DEFAULT NULL,
  `status` enum('pendente','aprovado','rejeitado') DEFAULT 'pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `nome`, `matricula`, `email`, `telefone`, `laboratorio_id`, `dia`, `turno`, `motivo`, `status`, `criado_em`) VALUES
(9, 'xxxxxxxxxxx', 'xxxxxxxxxxxxxx', 'carlosandrebr.6@gmail.com', 'xxxxxxxxxxxxxxxxxx', 8, '2025-09-21', 'noite', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'aprovado', '2025-09-22 00:09:50'),
(10, 'xxxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'mv16082005@gmail.com', 'xxxxxxxxxxxxxxxxxxxx', 6, '2025-09-21', 'noite', 'xxxxxxxxxxxxxxxxx', 'aprovado', '2025-09-22 00:20:04'),
(11, 'xxxxxxxxxx', 'xxxxxxxxxxxx', 'abas@gmail.com', 'xxxxxxxxxxxxxxxxx', 7, '2025-09-21', 'noite', 'xxxxxxxxxxxxxxxxxxxxx', 'aprovado', '2025-09-22 00:25:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas`
--

CREATE TABLE `aulas` (
  `id` int(11) NOT NULL,
  `disciplina` varchar(100) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `laboratorio_id` int(11) DEFAULT NULL,
  `turno` varchar(50) NOT NULL,
  `dia_semana` varchar(50) NOT NULL,
  `semestre` varchar(10) DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `status` enum('ativa','arquivada') NOT NULL DEFAULT 'ativa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `aulas`
--

INSERT INTO `aulas` (`id`, `disciplina`, `professor_id`, `laboratorio_id`, `turno`, `dia_semana`, `semestre`, `data_inicio`, `data_fim`, `status`) VALUES
(4, 'SSIN0047 - VISÃO COMPUTACIONAL', 11, 7, 'tarde', 'segunda', '2025.2', '2025-08-18', '2025-12-17', 'ativa'),
(5, 'SBCC0048 - TÓPICOS AVANÇADOS EM CIÊNCIA DE DADOS', 10, 8, 'manhã', 'terça', '2025.2', '2025-08-18', '2025-12-17', 'ativa'),
(6, 'PC010037 - PROGRAMAÇÃO PARA WEB', 9, 8, 'noite', 'terça', '2025.2', '2025-08-18', '2025-12-17', 'ativa'),
(7, 'PC010007 - LABORATÓRIO DE PROGRAMAÇÃO', 7, 8, 'tarde', 'terça', '2025.2', '2025-08-18', '2025-12-17', 'ativa'),
(8, 'SBCC0024 - ARQUITETURA E DESEMPENHO DE BANCO DE DADOS', 8, 6, 'manhã', 'quinta', '2025.2', '2025-08-18', '2025-12-17', 'ativa'),
(9, 'TESTE', 10, 7, 'manhã', 'segunda', '2025.2', '2025-08-08', '2025-12-17', 'ativa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `id` int(11) NOT NULL,
  `aula_id` int(11) DEFAULT NULL,
  `dia_semana` varchar(20) DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_fim` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `capacidade` int(11) DEFAULT NULL,
  `numero` varchar(255) NOT NULL,
  `projetor` tinyint(1) NOT NULL DEFAULT 0,
  `localizacao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nome`, `capacidade`, `numero`, `projetor`, `localizacao`) VALUES
(6, 'LABINOVA', 25, '221', 0, 'BMT I 2º piso'),
(7, 'LAB', 35, '219', 0, 'BMT I 2º piso'),
(8, 'LABORATORIO DE ALGORITMOS', 35, '37', 0, 'CENTRO DE BSI');

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriculas_autorizadas`
--

CREATE TABLE `matriculas_autorizadas` (
  `id` int(11) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `usada` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `matriculas_autorizadas`
--

INSERT INTO `matriculas_autorizadas` (`id`, `matricula`, `usada`) VALUES
(4, '2023006760', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `monitores`
--

CREATE TABLE `monitores` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `turno` varchar(50) NOT NULL,
  `laboratorio_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `monitores`
--

INSERT INTO `monitores` (`id`, `nome`, `matricula`, `turno`, `laboratorio_id`, `email`, `telefone`) VALUES
(1, 'Fernando', '20230202', 'manhã', 1, 'fernando@email.com', NULL),
(2, 'valter', '23465', 'tarde', 2, 'valter@email.com', NULL),
(6, 'CARLOS ANDRE BARROSO RABELO', '', 'tarde', 6, 'carlosandrebr.6@gmail.com', '93 9194-3879'),
(7, 'ENDRIO AGASSI BERNARDES DE ALMEIDA', '', 'tarde', 7, 'endrio.ufopa@gmail.com', '93 8104-9871'),
(8, 'RAYSSA DE SOUSA SIMOES', '', 'manhã', 6, 'rayssasimoes27@gmail.com', '93 9237-9203'),
(9, 'ANA CAROLINE MONTEIRO VIEIRA PINTO', '', 'tarde', 8, 'anacvieira1415@gmail.com', '93 9124-9533'),
(10, 'JONAS ALVES DA SILVA FILHO', '', 'noite', 6, 'jonasalvezmararu@gmail.com', '93 8423-0299'),
(11, 'JOSE WALTER LOBATO MOURAO JUNIOR', '', 'noite', 7, 'josecastro00k1@gmail.com', '93 8416-8910'),
(12, 'JOAO PAULO SANTOS BEMBE', '', 'noite', 8, 'jpbembe@gmail.com', '93 000000000'),
(13, 'ISAAC CORREA DE OLIVEIRA', '', 'manhã', 8, 'correa.isaac250@gmail.com', '93 9231-5016'),
(14, 'FERNANDO ABREU DE SOUSA', '', 'manhã', 7, 'fernando.compsci@gmail.com', '93 8815-9444');

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `matricula` varchar(255) NOT NULL,
  `area_conhecimento` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `nome`, `email`, `telefone`, `matricula`, `area_conhecimento`, `status`) VALUES
(6, 'MARCELINO SILVA DA SILVA', 'marcelino.ss@ufopa.edu.br', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo'),
(7, 'GUILHERME AUGUSTO BARROS CONDE', 'guilherme.conde@ufopa.edu.br', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo'),
(8, 'SOCORRO VANIA LOURENCO ALVES', 'socorro.alves@ufopa.edu.br', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo'),
(9, 'RENNAN JOSE MAIA DA SILVA', 'rennanmaia@gmail.com', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo'),
(10, 'BRUNO ALMEIDA DA SILVA', 'brunostm@gmail.com', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo'),
(11, 'MARCIO JOSE MOUTINHO DA PONTE', 'mjmponte@gmail.com', 'xxxxxxxxxxxxxxx', 'xxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxx', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'comum',
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password_hash`, `role`, `email`) VALUES
(1, 'admin', '$2y$10$AzSHCHUn24XJoEPRGKeBquJntpNR9o998JNeZ5tBLsfL1Pr8P.6p6', 'admin', 'admin@email.com'),
(2, 'servidor', '$2y$10$AzSHCHUn24XJoEPRGKeBquJntpNR9o998JNeZ5tBLsfL1Pr8P.6p6', 'servidor', 'servidor@email.com');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_agendamento_laboratorio` (`laboratorio_id`);

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
-- Índices de tabela `matriculas_autorizadas`
--
ALTER TABLE `matriculas_autorizadas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricula` (`matricula`);

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
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `matriculas_autorizadas`
--
ALTER TABLE `matriculas_autorizadas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `monitores`
--
ALTER TABLE `monitores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `fk_agendamento_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `aulas`
--
ALTER TABLE `aulas`
  ADD CONSTRAINT `aulas_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `aulas_ibfk_2` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
