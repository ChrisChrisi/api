-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2017 at 01:35 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api`
--

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE `candidates` (
  `cn_id` int(11) NOT NULL,
  `cn_name` varchar(100) NOT NULL,
  `cn_jb_id` int(11) NOT NULL COMMENT 'key to table ''jobs''',
  `cn_created_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`cn_id`, `cn_name`, `cn_jb_id`, `cn_created_on`) VALUES
(2, 'Jane', 3, '2017-04-13 00:46:32'),
(5, 'Ivan', 3, '2017-04-13 02:06:01'),
(6, 'Georgi', 4, '2017-04-13 02:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `jb_id` int(11) NOT NULL,
  `jb_position` varchar(100) NOT NULL,
  `jb_description` text NOT NULL,
  `jb_created_on` datetime NOT NULL,
  `jb_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - active; 0 - not active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`jb_id`, `jb_position`, `jb_description`, `jb_created_on`, `jb_status`) VALUES
(1, '.Net', 'Looking for ... ', '2017-04-12 23:44:05', 1),
(3, 'Javascript', 'looking for web ninja', '2017-04-13 00:18:55', 1),
(4, 'nodejs', 'looking for ...', '2017-04-13 02:02:42', 1),
(5, 'php', 'looking for web ninja', '2017-04-13 02:02:44', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`cn_id`),
  ADD KEY `cn_jb_id` (`cn_jb_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`jb_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `cn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `jb_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`cn_jb_id`) REFERENCES `jobs` (`jb_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
