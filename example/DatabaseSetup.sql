-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 25, 2024 at 06:21 PM
-- Server version: 10.3.39-MariaDB
-- PHP Version: 8.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `[Name of your database]`
--

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(10) UNSIGNED NOT NULL,
  `description` varchar(190) DEFAULT 'description',
  `classification` varchar(4) NOT NULL DEFAULT 'RGLR',
  `parent_id` int(10) UNSIGNED DEFAULT 0,
  `name` varchar(45) DEFAULT 'name',
  `lastname` varchar(45) DEFAULT 'lastname',
  `email` varchar(45) DEFAULT 'name@domain.com',
  `role` varchar(5) NOT NULL DEFAULT 'U' COMMENT 'USER (U) or ADMIN (A)',
  `password` varchar(256) DEFAULT 'password',
  `ownpwd` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 0,
  `subscriptionuntil` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Table structure for table `mail_queue`
--

CREATE TABLE mail_queue (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    description varchar(45) DEFAULT 'description',
    classification varchar(4) NOT NULL DEFAULT 'RGLR',
    parent_id int(10) UNSIGNED DEFAULT 0,
    subject VARCHAR(255) NOT NULL,
    body MEDIUMTEXT NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    bcc VARCHAR(1000) DEFAULT NULL,
    status ENUM('pending', 'sending', 'sent', 'failed') NOT NULL DEFAULT 'pending',
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at DATETIME NULL,
    sent_at DATETIME NULL,
    error TEXT NULL,

    PRIMARY KEY (id),
    INDEX idx_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
