-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 05-Fev-2017 às 15:15
-- Versão do servidor: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `short-url`
--
CREATE DATABASE IF NOT EXISTS `short-url` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `short-url`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_urls`
--

DROP TABLE IF EXISTS `tb_urls`;
CREATE TABLE IF NOT EXISTS `tb_urls` (
`id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL COMMENT 'Url original',
  `hash` varchar(10) NOT NULL COMMENT 'Hash da url encurtada',
  `hits` int(11) NOT NULL DEFAULT '0' COMMENT 'Contator de hits da url',
  `user_id` varchar(50) NOT NULL COMMENT 'User id',
  `dt_insert` datetime NOT NULL COMMENT 'Data de inserção do registro',
  `ip_insert` varchar(30) NOT NULL COMMENT 'IP de origem do registro',
  `dt_update` datetime DEFAULT NULL COMMENT 'Data de atualização do registro',
  `ip_update` varchar(30) DEFAULT NULL COMMENT 'IP de origem da atualização',
  `st_record` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: Active | 2: Inactive'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tb_users`
--

DROP TABLE IF EXISTS `tb_users`;
CREATE TABLE IF NOT EXISTS `tb_users` (
  `id` varchar(50) NOT NULL,
  `dt_insert` datetime NOT NULL COMMENT 'Data de inserção do registro',
  `ip_insert` varchar(30) NOT NULL COMMENT 'IP de origem do registro',
  `dt_update` datetime DEFAULT NULL COMMENT 'Data de atualização do registro',
  `ip_update` varchar(30) DEFAULT NULL COMMENT 'IP de origem da atualização',
  `st_record` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: Active | 2: Inactive'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_urls`
--
ALTER TABLE `tb_urls`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_urls`
--
ALTER TABLE `tb_urls`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

GRANT USAGE ON *.* TO 'short-url'@'%' IDENTIFIED BY PASSWORD '*207D9198511C55CE4D8C7D2D6ED9F0A337519CE4';
GRANT USAGE ON *.* TO 'short-url'@'localhost' IDENTIFIED BY PASSWORD '*207D9198511C55CE4D8C7D2D6ED9F0A337519CE4';

GRANT ALL PRIVILEGES ON `short-url`.* TO 'short-url'@'%';
GRANT ALL PRIVILEGES ON `short-url`.* TO 'short-url'@'localhost';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
