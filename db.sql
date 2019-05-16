-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 19-05-17 00:05
-- 서버 버전: 10.1.26-MariaDB
-- PHP 버전: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `todo_list`
--

-- --------------------------------------------------------

--
-- 테이블 구조 `notification`
--

CREATE TABLE `notification` (
  `nt_no` int(11) NOT NULL,
  `nt_type` enum('insert','impending','dead','done','edit','star','hello') COLLATE utf8mb4_bin NOT NULL,
  `td_no` int(21) DEFAULT NULL,
  `nt_registered_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 테이블의 덤프 데이터 `notification`
--

INSERT INTO `notification` (`nt_no`, `nt_type`, `td_no`, `nt_registered_at`) VALUES
(1, 'hello', NULL, '2019-05-16 00:00:00');

-- --------------------------------------------------------

--
-- 테이블 구조 `todo`
--

CREATE TABLE `todo` (
  `td_no` int(21) NOT NULL,
  `td_subject` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `td_content` text COLLATE utf8mb4_bin NOT NULL,
  `td_deadline` date DEFAULT NULL,
  `td_star` enum('0','1','2') COLLATE utf8mb4_bin NOT NULL,
  `td_is_done` tinyint(1) NOT NULL,
  `td_registered_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`nt_no`),
  ADD KEY `td_no` (`td_no`);

--
-- 테이블의 인덱스 `todo`
--
ALTER TABLE `todo`
  ADD PRIMARY KEY (`td_no`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `notification`
--
ALTER TABLE `notification`
  MODIFY `nt_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 테이블의 AUTO_INCREMENT `todo`
--
ALTER TABLE `todo`
  MODIFY `td_no` int(21) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`td_no`) REFERENCES `todo` (`td_no`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
