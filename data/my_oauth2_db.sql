-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 10 月 09 日 14:53
-- 服务器版本: 5.1.73
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `my_oauth2_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `oauth_access_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`access_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('1592d39d6eade8a79f115406fe034e266e004f36', 'androidclient', '15919940006', '2015-09-29 20:15:57', 'all'),
('35a31be96649377669dff872f6585e386a5e9116', 'androidclient', '15919940006', '2015-09-30 07:31:57', 'all'),
('d41cc2afc473d7edc6d4e660e1d85aacaeb17294', 'androidclient', '15919940006', '2015-09-30 07:29:20', 'all'),
('f9c52448cfcdb2ba09ca45243d410b22ddced614', 'androidclient', '15919940006', '2015-09-30 07:24:49', 'all');

-- --------------------------------------------------------

--
-- 表的结构 `oauth_authorization_codes`
--

CREATE TABLE IF NOT EXISTS `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_clients`
--

CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) NOT NULL,
  `redirect_uri` varchar(2000) NOT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(100) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `oauth_clients`
--

INSERT INTO `oauth_clients` (`client_id`, `client_secret`, `redirect_uri`, `grant_types`, `scope`, `user_id`) VALUES
('androidclient', 'androidsecret', 'http://fake/', NULL, NULL, NULL),
('appleclient', 'applesecret', 'http://fake/', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `oauth_jwt`
--

CREATE TABLE IF NOT EXISTS `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_refresh_tokens`
--

CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `oauth_refresh_tokens`
--

INSERT INTO `oauth_refresh_tokens` (`refresh_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('1ae54efc6d0fea4f2cdf8297633d31f1c01027e8', 'androidclient', '15919940006', '2015-10-12 06:23:24', 'all'),
('268a4f26f93f20f2bcf59ef97c61869f0baeeb16', 'androidclient', '15919940006', '2015-10-12 06:23:55', 'all'),
('305f56a028829df609e41dd48a53fbb8542d2d16', 'androidclient', '15919940006', '2015-10-12 06:23:05', 'all'),
('447468894f7386170141485af4a3618a7564f515', 'androidclient', '15919940006', '2015-10-12 06:23:59', 'all'),
('626f8615edfcc3e080f8bbd087346c66a5077037', 'androidclient', '15919940006', '2015-10-12 06:18:11', 'all'),
('7894539767a828f569a6d2ea2153808b51fb1e7a', 'androidclient', '15919940006', '2015-10-12 06:24:09', 'all'),
('87f1574add682fa7f294c73ad423322ffb3bea89', 'androidclient', '15919940006', '2015-10-12 06:31:58', 'all'),
('a86648bafb6812e6f9d960213e79f836da97069a', 'androidclient', '15919940006', '2015-10-12 19:15:57', 'all'),
('b2c972ad4fecbf0d6a28fd72d698892a2eb7ff74', 'androidclient', '15919940006', '2015-10-12 06:29:20', 'all'),
('cbd61de5189dddda1c7ef393bf20f40a1a913e88', 'androidclient', '15919940006', '2015-10-12 06:23:57', 'all'),
('e7c403dd041c00ff593debf9ca5a2020e970fe09', 'androidclient', '15919940006', '2015-10-12 06:24:46', 'all');

-- --------------------------------------------------------

--
-- 表的结构 `oauth_scopes`
--

CREATE TABLE IF NOT EXISTS `oauth_scopes` (
  `scope` text,
  `is_default` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- 表的结构 `oauth_users`
--

CREATE TABLE IF NOT EXISTS `oauth_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
