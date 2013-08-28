-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 09, 2010 at 10:17 AM
-- Server version: 5.1.32
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `punchcms`
--

-- --------------------------------------------------------

--
-- Table structure for table `pcms_element_feed`
--

CREATE TABLE IF NOT EXISTS `pcms_element_feed` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `elementId` bigint(20) NOT NULL DEFAULT '0',
  `feedId` bigint(20) NOT NULL DEFAULT '0',
  `feedPath` varchar(250) NOT NULL,
  `maxItems` int(11) NOT NULL DEFAULT '0',
  `sortBy` varchar(250) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `pcms_element_field_feed`
--

CREATE TABLE IF NOT EXISTS `pcms_element_field_feed` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `elementId` bigint(20) NOT NULL,
  `templateFieldId` bigint(20) NOT NULL,
  `feedPath` varchar(250) NOT NULL,
  `xpath` varchar(255) NOT NULL,
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `pcms_feed`
--

CREATE TABLE IF NOT EXISTS `pcms_feed` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `accountId` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `feed` varchar(250) NOT NULL,
  `basepath` varchar(250) NOT NULL,
  `refresh` bigint(20) NOT NULL DEFAULT '3600',
  `lastUpdate` datetime NOT NULL,
  `active` int(11) NOT NULL,
  `sort` bigint(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
