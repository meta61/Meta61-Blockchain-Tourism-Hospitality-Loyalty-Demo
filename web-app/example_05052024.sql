-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.44 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for example
DROP DATABASE IF EXISTS `example`;
CREATE DATABASE IF NOT EXISTS `example` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `example`;

-- Dumping structure for table example.api_logs
DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE IF NOT EXISTS `api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) DEFAULT NULL,
  `api_url` varchar(255) DEFAULT NULL,
  `http_status` varchar(255) DEFAULT NULL,
  `request_data` text,
  `response_data` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table example.api_logs: ~5 rows (approximately)
DELETE FROM `api_logs`;
/*!40000 ALTER TABLE `api_logs` DISABLE KEYS */;
INSERT INTO `api_logs` (`id`, `user_id`, `api_url`, `http_status`, `request_data`, `response_data`, `created_at`) VALUES
	(1, '2', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'Array', '{"success":true,"message":"meta enrolled Successfully","token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MTUyMDE5MzUsInVzZXJuYW1lIjoibWV0YSIsIm9yZ05hbWUiOiJPcmcxIiwiaWF0IjoxNzE1MTY1OTM1fQ.4eE7A1YfMHABQPQDHcj6CtO4UM0m6AGtpH-dTIOOu18"}', '2024-05-08 10:58:53'),
	(2, '2', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'Array', '{"success":true,"message":"meta enrolled Successfully","token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MTUyMDMzMDgsInVzZXJuYW1lIjoibWV0YSIsIm9yZ05hbWUiOiJPcmcxIiwiaWF0IjoxNzE1MTY3MzA4fQ.x1kHXmaDo_fHqVs5-FEKFQbH_ArvzSjQ8ZuvJ8R1m-E"}', '2024-05-08 11:21:46'),
	(3, '2', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'meta Org1 Array', '{"success":true,"message":"meta enrolled Successfully","token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MTUyMDQwMDksInVzZXJuYW1lIjoibWV0YSIsIm9yZ05hbWUiOiJPcmcxIiwiaWF0IjoxNzE1MTY4MDA5fQ.6GFXXDFKQ-3J8ibO7mz3wc1NKAFtdun8_HvWduhRUn8"}', '2024-05-08 11:33:28'),
	(4, '2', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'meta Org1 Array', '{"success":true,"message":"meta enrolled Successfully","token":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE3MTUyMDQxODYsInVzZXJuYW1lIjoibWV0YSIsIm9yZ05hbWUiOiJPcmcxIiwiaWF0IjoxNzE1MTY4MTg2fQ.w8oafjqARS2RmZIJ5cH7LsJicSCKBruk3T9v-RcTok4"}', '2024-05-08 11:36:25'),
	(5, '3', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'jbl Org1 Array', '200', '2024-05-09 09:22:26'),
	(6, '3', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'jbl Org1 Array', '200', '2024-05-09 10:33:20'),
	(7, '3', 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000/users', '0', 'jbl Org1 Array', '200', '2024-05-09 10:33:41');
/*!40000 ALTER TABLE `api_logs` ENABLE KEYS */;

-- Dumping structure for table example.customers
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table example.customers: ~1 rows (approximately)
DELETE FROM `customers`;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` (`id`, `name`, `description`) VALUES
	(1, 'Customer1', 'Customer1');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;

-- Dumping structure for table example.hotels
DROP TABLE IF EXISTS `hotels`;
CREATE TABLE IF NOT EXISTS `hotels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table example.hotels: ~1 rows (approximately)
DELETE FROM `hotels`;
/*!40000 ALTER TABLE `hotels` DISABLE KEYS */;
INSERT INTO `hotels` (`id`, `name`, `description`) VALUES
	(1, 'hotels1', 'hotels1');
/*!40000 ALTER TABLE `hotels` ENABLE KEYS */;

-- Dumping structure for table example.redeems
DROP TABLE IF EXISTS `redeems`;
CREATE TABLE IF NOT EXISTS `redeems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer` varchar(255) DEFAULT NULL,
  `amount` float DEFAULT '0',
  `hotel` varchar(255) DEFAULT NULL,
  `taxi` varchar(255) DEFAULT NULL,
  `reward` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `description` text,
  `created` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table example.redeems: ~0 rows (approximately)
DELETE FROM `redeems`;
/*!40000 ALTER TABLE `redeems` DISABLE KEYS */;
INSERT INTO `redeems` (`id`, `customer`, `amount`, `hotel`, `taxi`, `reward`, `user`, `description`, `created`) VALUES
	(1, 'customer1', 50, NULL, NULL, NULL, NULL, 'test', NULL),
	(2, 'Customer', 25, 'Hotel', 'Taxi', 'Reward', 'jbl', 'Description:', '2024-05-09 22:10:33');
/*!40000 ALTER TABLE `redeems` ENABLE KEYS */;

-- Dumping structure for table example.rewards
DROP TABLE IF EXISTS `rewards`;
CREATE TABLE IF NOT EXISTS `rewards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `amount` float DEFAULT '0',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- Dumping data for table example.rewards: ~3 rows (approximately)
DELETE FROM `rewards`;
/*!40000 ALTER TABLE `rewards` DISABLE KEYS */;
INSERT INTO `rewards` (`id`, `name`, `amount`, `description`) VALUES
	(2, 'Gold', 500, 'Gold'),
	(3, 'Platinum', 1000, 'Platinum'),
	(18, 'Silver', 250, 'Silver');
/*!40000 ALTER TABLE `rewards` ENABLE KEYS */;

-- Dumping structure for table example.taxis
DROP TABLE IF EXISTS `taxis`;
CREATE TABLE IF NOT EXISTS `taxis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table example.taxis: ~1 rows (approximately)
DELETE FROM `taxis`;
/*!40000 ALTER TABLE `taxis` DISABLE KEYS */;
INSERT INTO `taxis` (`id`, `name`, `description`) VALUES
	(2, 'taxis', 'taxis');
/*!40000 ALTER TABLE `taxis` ENABLE KEYS */;

-- Dumping structure for table example.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table example.users: ~3 rows (approximately)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
	(1, 'admin', '$2y$10$HaDkGK4GvJQjXrVdGFdpHOWOWFMXpiVozmGOwKlwRp6AsBUCaJRI6', '2024-04-28 02:52:36'),
	(2, 'meta', '$2y$10$o4.VKrUPEDyh45m5Vnw2fe2SdAwfC23wE9AqoYgrXqn0wagtADT1O', '2024-05-07 09:32:34'),
	(3, 'jbl', '$2y$10$BkGcXgAf/bcrm6QxQZUxHOaX8Q5.7K/lNpnLjpWi4xRL/t1vp4Txq', '2024-05-09 09:22:19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
