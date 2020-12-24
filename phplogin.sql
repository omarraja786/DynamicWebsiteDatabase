-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2020 at 02:35 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phplogin`
--

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_user` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `firstname`, `lastname`, `dob`, `created`, `last_updated`, `is_user`) VALUES
(1, 'Johnie', 'Smith', '1997-07-11', '2020-12-02 16:01:35', '2020-12-04 15:51:18', 1),
(2, 'Omar', 'Raja', '1994-09-15', '2020-12-02 17:21:15', '2020-12-02 17:50:26', 0),
(3, 'Steve', 'Lewis', '1964-02-03', '2020-12-02 17:21:32', '2020-12-02 17:50:30', 0),
(4, 'Paul', 'Wilkins', '1989-05-29', '2020-12-02 17:21:32', '2020-12-02 17:50:33', 0),
(5, 'John', 'Abraham', '1947-06-10', '2020-12-02 19:48:32', '2020-12-05 13:33:49', 0),
(6, 'Gerrard', 'Fraser', '1986-04-25', '2020-12-03 12:47:06', '2020-12-05 13:33:42', 0),
(7, 'Fleur', 'Lyons', '1985-07-30', '2020-12-03 12:47:06', '2020-12-04 16:35:24', 1),
(8, 'Javan', 'Grimes', '2002-09-01', '2020-12-03 12:47:06', '2020-12-05 12:38:41', 1),
(9, 'Sohail', 'Stewart', '1982-01-19', '2020-12-03 12:47:06', '2020-12-03 12:47:06', 0),
(10, 'Aahilo', 'Kearns', '2001-12-29', '2020-12-03 12:47:06', '2020-12-05 13:33:12', 0),
(11, 'Domas', 'Moran', '1985-04-11', '2020-12-03 12:47:06', '2020-12-04 15:57:25', 1),
(12, 'Benito', 'Mccarty', '1985-07-15', '2020-12-03 12:49:11', '2020-12-05 13:33:32', 0),
(13, 'Luke', 'Cooke', '1995-05-04', '2020-12-03 12:49:11', '2020-12-05 13:33:52', 0),
(14, 'Mateusz', 'Mcmillan', '1996-10-16', '2020-12-03 12:49:11', '2020-12-03 12:49:11', 0),
(15, 'Taliah', 'Maldonado', '1986-04-25', '2020-12-03 12:49:11', '2020-12-03 12:49:11', 0),
(16, 'Talia', 'Clements', '1986-07-14', '2020-12-03 12:49:11', '2020-12-03 12:49:11', 0),
(42, 'test', 'test', '2000-01-01', '2020-12-03 19:08:21', '2020-12-05 13:34:41', 0),
(43, 'Omar', 'Raja', '1997-11-01', '2020-12-04 13:46:41', '2020-12-05 13:34:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `id`, `username`, `password`, `status`) VALUES
(1, 1, 'admin', '$2y$10$HfnOLgmncXY3TfdRwju/HO5ltp3a.BPzPNJtZbW/fcY2JuHs7r9jC', 1),
(3, 11, 'test', 'test', 1),
(6, 7, 'fleuracc', 'fleur', 1),
(8, 8, 'javan', 'fgdfgfg', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_updated` (`last_updated`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `id` (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id`) REFERENCES `staff` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
