-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Tempo de geração: 15-Nov-2021 às 10:46
-- Versão do servidor: 5.7.34
-- versão do PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `votacao2`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `respostas`
--

CREATE TABLE `respostas` (
  `id_votacao` int(11) NOT NULL,
  `id_resposta` int(11) NOT NULL,
  `texto` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `respostas`
--

INSERT INTO `respostas` (`id_votacao`, `id_resposta`, `texto`) VALUES
(1, 1, 'sim'),
(1, 2, 'nao'),
(1, 3, 'talvez');

-- --------------------------------------------------------

--
-- Estrutura da tabela `respostas_resultado`
--

CREATE TABLE `respostas_resultado` (
  `id_votacao` int(11) NOT NULL,
  `id_resposta` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `time_stamp` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `respostas_resultado`
--

INSERT INTO `respostas_resultado` (`id_votacao`, `id_resposta`, `username`, `time_stamp`) VALUES
(1, 3, 'waza', '15-11-2021');

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `username` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `nivel_utilizador` int(11) NOT NULL,
  `temp` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`username`, `nome`, `password`, `nivel_utilizador`, `temp`) VALUES
('waza', 'goncalo antunes', '$2y$10$lQ/UwVBYaexLwx3IhgYyhunMiD5MkJJ7bhjmGXVzAedYcoDZuzHu.', 1, '');

-- --------------------------------------------------------

--
-- Estrutura da tabela `votacoes`
--

CREATE TABLE `votacoes` (
  `id_votacao` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `votacoes`
--

INSERT INTO `votacoes` (`id_votacao`, `username`, `titulo`, `descricao`) VALUES
(1, 'waza', 'Gostas de fiambre?', '');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `respostas`
--
ALTER TABLE `respostas`
  ADD PRIMARY KEY (`id_votacao`,`id_resposta`);

--
-- Índices para tabela `respostas_resultado`
--
ALTER TABLE `respostas_resultado`
  ADD PRIMARY KEY (`id_votacao`,`id_resposta`,`username`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- Índices para tabela `votacoes`
--
ALTER TABLE `votacoes`
  ADD PRIMARY KEY (`id_votacao`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
