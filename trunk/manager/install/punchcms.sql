-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 18, 2008 at 04:04 PM
-- Server version: 5.0.38
-- PHP Version: 5.2.1
-- 
-- Database: `punchcms`
-- Version: `2.3.0
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_applications`
-- 

CREATE TABLE `punch_liveuser_applications` (
  `application_id` int(11) default '0',
  `application_define_name` char(32) default NULL,
  `account_id` int(11) NOT NULL default '0',
  UNIQUE KEY `application_id` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_applications`
-- 

INSERT INTO `punch_liveuser_applications` (`application_id`, `application_define_name`, `account_id`) VALUES 
(1, 'PunchCMS', 0),
(2, 'MyPunch', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_applications_seq`
-- 

CREATE TABLE `punch_liveuser_applications_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

-- 
-- Dumping data for table `punch_liveuser_applications_seq`
-- 

INSERT INTO `punch_liveuser_applications_seq` (`sequence`) VALUES 
(77);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_area_admin_areas`
-- 

CREATE TABLE `punch_liveuser_area_admin_areas` (
  `area_id` int(11) default '0',
  `perm_user_id` int(11) default '0',
  UNIQUE KEY `id_id` (`area_id`,`perm_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_area_admin_areas`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_areas`
-- 

CREATE TABLE `punch_liveuser_areas` (
  `area_id` int(11) default '0',
  `application_id` int(11) default '0',
  `account_id` int(11) NOT NULL default '0',
  `area_define_name` char(32) default NULL,
  UNIQUE KEY `area_id` (`area_id`),
  UNIQUE KEY `area_define_name` (`application_id`,`area_define_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_areas`
-- 

INSERT INTO `punch_liveuser_areas` (`area_id`, `application_id`, `account_id`, `area_define_name`) VALUES 
(1, 1, 0, 'Elements'),
(2, 1, 0, 'Templates'),
(3, 1, 0, 'Settings'),
(6, 1, 0, 'Search'),
(12, 1, 0, 'Help'),
(13, 2, 0, 'Users'),
(14, 2, 0, 'Profile'),
(15, 2, 0, 'Account'),
(20, 2, 0, 'PunchCMS'),
(24, 2, 0, 'Announcements'),
(25, 1, 0, 'Languages'),
(31, 1, 0, 'Forms'),
(32, 1, 0, 'Storage'),
(33, 1, 0, 'Aliases');

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_areas_seq`
-- 

CREATE TABLE `punch_liveuser_areas_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=186 ;

-- 
-- Dumping data for table `punch_liveuser_areas_seq`
-- 

INSERT INTO `punch_liveuser_areas_seq` (`sequence`) VALUES 
(185);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_group_subgroups`
-- 

CREATE TABLE `punch_liveuser_group_subgroups` (
  `group_id` int(11) default '0',
  `subgroup_id` int(11) default '0',
  UNIQUE KEY `id_id` (`group_id`,`subgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_group_subgroups`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_grouprights`
-- 

CREATE TABLE `punch_liveuser_grouprights` (
  `group_id` int(11) default '0',
  `right_id` int(11) default '0',
  `right_level` int(11) default NULL,
  `account_id` int(11) NOT NULL default '0',
  UNIQUE KEY `id_id` (`group_id`,`right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_grouprights`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_groups`
-- 

CREATE TABLE `punch_liveuser_groups` (
  `group_id` int(11) default '0',
  `group_type` int(11) default NULL,
  `group_define_name` char(32) default NULL,
  `isactive` tinyint(1) default NULL,
  `account_id` int(11) NOT NULL default '0',
  `owner_user_id` int(11) default NULL,
  `owner_group_id` int(11) default NULL,
  UNIQUE KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_groups`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_groups_seq`
-- 

CREATE TABLE `punch_liveuser_groups_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=241 ;

-- 
-- Dumping data for table `punch_liveuser_groups_seq`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_groupusers`
-- 

CREATE TABLE `punch_liveuser_groupusers` (
  `perm_user_id` int(11) default '0',
  `group_id` int(11) default '0',
  UNIQUE KEY `id_id` (`perm_user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_groupusers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_perm_users`
-- 

CREATE TABLE `punch_liveuser_perm_users` (
  `perm_user_id` int(11) default '0',
  `auth_user_id` char(32) default NULL,
  `auth_container_name` char(32) default NULL,
  `perm_type` int(11) default NULL,
  UNIQUE KEY `perm_user_id` (`perm_user_id`),
  UNIQUE KEY `auth_id_id` (`auth_user_id`,`auth_container_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_perm_users`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_perm_users_seq`
-- 

CREATE TABLE `punch_liveuser_perm_users_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1079 ;

-- 
-- Dumping data for table `punch_liveuser_perm_users_seq`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_right_implied`
-- 

CREATE TABLE `punch_liveuser_right_implied` (
  `right_id` int(11) default '0',
  `implied_right_id` int(11) default '0',
  UNIQUE KEY `id_id` (`right_id`,`implied_right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_right_implied`
-- 

INSERT INTO `punch_liveuser_right_implied` (`right_id`, `implied_right_id`) VALUES 
(180, 1),
(180, 182),
(180, 183),
(180, 184),
(181, 1),
(181, 182),
(181, 183),
(181, 184),
(182, 1),
(182, 183),
(182, 184),
(183, 1),
(183, 184),
(184, 1),
(185, 1),
(185, 180),
(185, 181),
(185, 182),
(185, 183),
(185, 184);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_rights`
-- 

CREATE TABLE `punch_liveuser_rights` (
  `right_id` int(11) default '0',
  `area_id` int(11) default '0',
  `right_define_name` char(32) default NULL,
  `account_id` int(11) NOT NULL default '0',
  `has_implied` tinyint(1) default NULL,
  UNIQUE KEY `right_id` (`right_id`),
  UNIQUE KEY `right_define_name` (`area_id`,`right_define_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_rights`
-- 

INSERT INTO `punch_liveuser_rights` (`right_id`, `area_id`, `right_define_name`, `account_id`, `has_implied`) VALUES 
(1, 1, 'View', 0, 0),
(2, 2, 'View', 0, 1),
(3, 3, 'View', 0, 1),
(13, 12, 'View', 0, 1),
(14, 15, 'View', 0, NULL),
(15, 14, 'View', 0, NULL),
(16, 13, 'View', 0, NULL),
(21, 20, 'View', 0, NULL),
(26, 24, 'View', 0, NULL),
(27, 25, 'View', 0, NULL),
(29, 31, 'View', 0, NULL),
(30, 32, 'View', 0, NULL),
(9, 6, 'View', 0, 1),
(31, 33, 'View', 0, NULL),
(180, 1, 'ChangePermissions', 0, 1),
(181, 1, 'Create', 0, 1),
(182, 1, 'Write', 0, 1),
(183, 1, 'Read', 0, 1),
(184, 1, 'Browse', 0, 1),
(185, 1, 'FullControl', 0, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_rights_seq`
-- 

CREATE TABLE `punch_liveuser_rights_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=190 ;

-- 
-- Dumping data for table `punch_liveuser_rights_seq`
-- 

INSERT INTO `punch_liveuser_rights_seq` (`sequence`) VALUES 
(189);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_translations`
-- 

CREATE TABLE `punch_liveuser_translations` (
  `translation_id` int(11) NOT NULL default '0',
  `section_id` int(11) NOT NULL default '0',
  `section_type` int(11) NOT NULL default '0',
  `language_id` char(32) NOT NULL default '',
  `name` char(32) default NULL,
  `description` char(255) default NULL,
  UNIQUE KEY `translation_id` (`translation_id`),
  UNIQUE KEY `language_id` (`section_id`,`section_type`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_translations`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_translations_seq`
-- 

CREATE TABLE `punch_liveuser_translations_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `punch_liveuser_translations_seq`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_userrights`
-- 

CREATE TABLE `punch_liveuser_userrights` (
  `perm_user_id` int(11) default '0',
  `right_id` int(11) default '0',
  `right_level` int(11) default NULL,
  `account_id` int(11) NOT NULL default '0',
  UNIQUE KEY `id_id` (`perm_user_id`,`right_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_userrights`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_users`
-- 

CREATE TABLE `punch_liveuser_users` (
  `authuserid` char(32) default NULL,
  `handle` char(32) default NULL,
  `passwd` char(42) default NULL,
  `name` char(250) default NULL,
  `email` char(250) default NULL,
  `time_zone_id` int(11) NOT NULL default '1',
  `owner_user_id` int(11) default NULL,
  `owner_group_id` int(11) default NULL,
  `account_id` int(11) NOT NULL default '0',
  `lastlogin` datetime default NULL,
  `isactive` tinyint(1) default NULL,
  UNIQUE KEY `authuserid` (`authuserid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_users`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_liveuser_users_seq`
-- 

CREATE TABLE `punch_liveuser_users_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_liveuser_users_seq`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_alias`
-- 

CREATE TABLE `pcms_alias` (
  `id` bigint(20) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL,
  `alias` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  `active` int(11) NOT NULL,
  `sort` bigint(20) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_alias`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_announce_message`
-- 

CREATE TABLE `pcms_announce_message` (
  `id` int(11) NOT NULL auto_increment,
  `language` varchar(100) NOT NULL,
  `header` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_announce_message`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_announce_user`
-- 

CREATE TABLE `pcms_announce_user` (
  `id` bigint(20) NOT NULL auto_increment,
  `messageId` int(11) NOT NULL default '0',
  `permUserId` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `messageId` (`messageId`),
  KEY `permUserId` (`permUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_announce_user`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_audit_log`
-- 

CREATE TABLE `pcms_audit_log` (
  `id` bigint(20) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `typeId` bigint(20) NOT NULL,
  `typeName` varchar(255) NOT NULL,
  `userId` int(11) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `action` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL default '1',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_audit_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element`
-- 

CREATE TABLE `pcms_element` (
  `id` bigint(20) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `nameCount` int(11) NOT NULL default '0',
  `apiName` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `typeId` int(11) NOT NULL default '2',
  `templateId` int(11) NOT NULL default '0',
  `isPage` tinyint(4) NOT NULL default '0',
  `parentId` bigint(20) NOT NULL default '0',
  `userId` bigint(20) NOT NULL default '0',
  `groupId` bigint(20) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `templateId` (`templateId`),
  KEY `parentId` (`parentId`),
  KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_field`
-- 

CREATE TABLE `pcms_element_field` (
  `id` bigint(20) NOT NULL auto_increment,
  `elementId` bigint(20) NOT NULL default '0',
  `templateFieldId` bigint(20) NOT NULL default '0',
  `fieldTypeId` bigint(20) NOT NULL default '0',
  `originalName` varchar(255) NOT NULL default '',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `elementId` (`elementId`),
  KEY `templateFieldId` (`templateFieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_field`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_field_bigtext`
-- 

CREATE TABLE `pcms_element_field_bigtext` (
  `id` bigint(20) NOT NULL auto_increment,
  `value` longtext NOT NULL,
  `fieldId` bigint(20) NOT NULL default '0',
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_field_bigtext`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_field_date`
-- 

CREATE TABLE `pcms_element_field_date` (
  `id` bigint(20) NOT NULL auto_increment,
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  `fieldId` bigint(20) NOT NULL default '0',
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_field_date`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_field_number`
-- 

CREATE TABLE `pcms_element_field_number` (
  `id` bigint(20) NOT NULL auto_increment,
  `value` float NOT NULL default '0',
  `fieldId` bigint(20) NOT NULL default '0',
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_field_number`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_field_text`
-- 

CREATE TABLE `pcms_element_field_text` (
  `id` bigint(20) NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `fieldId` bigint(20) NOT NULL default '0',
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_field_text`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_language`
-- 

CREATE TABLE `pcms_element_language` (
  `id` bigint(20) NOT NULL auto_increment,
  `elementId` bigint(20) NOT NULL,
  `languageId` int(11) NOT NULL,
  `cascade` tinyint(4) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  `sort` bigint(20) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_language`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_permission`
-- 

CREATE TABLE `pcms_element_permission` (
  `id` bigint(20) NOT NULL auto_increment,
  `elementId` bigint(20) NOT NULL default '0',
  `userId` int(11) NOT NULL default '0',
  `groupId` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_permission`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_schedule`
-- 

CREATE TABLE `pcms_element_schedule` (
  `id` bigint(20) NOT NULL auto_increment,
  `elementId` bigint(20) NOT NULL,
  `startDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `endDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `startActive` tinyint(4) NOT NULL default '1',
  `endActive` tinyint(4) NOT NULL default '1',
  `monday` tinyint(4) NOT NULL default '1',
  `tuesday` tinyint(4) NOT NULL default '1',
  `wednesday` tinyint(4) NOT NULL default '1',
  `thursday` tinyint(4) NOT NULL default '1',
  `friday` tinyint(4) NOT NULL default '1',
  `saturday` tinyint(4) NOT NULL default '1',
  `sunday` tinyint(4) NOT NULL default '1',
  `startTime` time NOT NULL default '00:00:00',
  `endTime` time NOT NULL default '00:00:00',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_element_schedule`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_element_type`
-- 

CREATE TABLE `pcms_element_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `pcms_element_type`
-- 

INSERT INTO `pcms_element_type` (`id`, `name`, `sort`, `created`, `modified`) VALUES 
(1, 'folder', 0, '2006-06-22 22:19:53', '2006-06-22 22:19:53'),
(2, 'element', 0, '2006-06-22 22:19:53', '2006-06-22 22:19:53'),
(3, 'element container', 0, '2006-06-22 22:19:53', '2006-06-22 22:19:53');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_form`
-- 

CREATE TABLE `pcms_form` (
  `id` int(11) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `apiName` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `active` tinyint(4) NOT NULL default '1',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_form`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_language`
-- 

CREATE TABLE `pcms_language` (
  `id` int(11) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `abbr` varchar(100) NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  `default` tinyint(4) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `username` varchar(250) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_language`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_search_index`
-- 

CREATE TABLE `pcms_search_index` (
  `id` bigint(20) NOT NULL auto_increment,
  `elementId` bigint(20) NOT NULL default '0',
  `word` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `elementId` (`elementId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_search_index`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_setting`
-- 

CREATE TABLE `pcms_setting` (
  `id` int(11) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL default '0',
  `settingId` int(11) NOT NULL default '0',
  `value` varchar(250) NOT NULL default '',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `accountId` (`accountId`),
  KEY `settingId` (`settingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_setting`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_setting_tpl`
-- 

CREATE TABLE `pcms_setting_tpl` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(250) NOT NULL default '',
  `value` varchar(250) NOT NULL default '',
  `section` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `pcms_setting_tpl`
-- 

INSERT INTO `pcms_setting_tpl` (`id`, `name`, `value`, `section`, `type`, `sort`, `created`, `modified`) VALUES 
(1, 'ftp_server', 'localhost', 'ftp', 'text', 1, '2006-06-23 12:22:08', '2006-06-23 12:22:08'),
(2, 'ftp_username', '', 'ftp', 'text', 2, '2006-06-23 12:22:08', '2006-06-23 12:22:08'),
(3, 'ftp_password', '', 'ftp', 'password', 3, '2006-06-23 12:22:08', '2006-06-23 12:22:08'),
(4, 'ftp_remote_folder', 'httpdocs/files/', 'ftp', 'text', 4, '2006-06-23 12:22:08', '2006-06-23 12:22:08'),
(5, 'file_upload_extensions', '.doc .xls .pdf .zip .exe', 'files', 'text', 100, '2006-06-23 12:22:08', '2006-06-23 12:22:08'),
(6, 'image_upload_extensions', '.jpg .gif .png', 'files', 'text', 101, '2007-03-05 23:13:26', '2007-03-05 23:13:26'),
(7, 'caching_enable', '0', 'caching', 'checkbox', 200, '2007-11-09 17:14:11', '2007-11-09 17:14:11'),
(8, 'caching_timeout', '1440', 'caching', 'text', 203, '2007-11-09 17:14:11', '2007-11-09 17:14:11'),
(10, 'caching_folder', '../cache/', 'caching', 'text', 201, '2007-11-09 17:58:10', '2007-11-09 17:58:10'),
(11, 'file_folder', '/files/', 'files', 'text', 102, '2007-11-09 18:05:01', '2007-11-09 18:05:01'),
(12, 'audit_enable', '0', 'audit', 'checkbox', 400, '2007-11-14 17:53:35', '2007-11-14 17:53:35'),
(13, 'audit_rotation', '7', 'audit', 'number', 401, '2007-11-14 18:00:53', '2007-11-14 18:00:53'),
(14, 'file_download', '/download.php?eid=', 'files', 'text', 203, '2007-11-16 11:20:06', '2007-11-16 11:20:06'),
(15, 'caching_ftp_folder', 'cache', 'caching', 'text', 202, '2007-11-28 16:33:51', '2007-11-28 16:33:51'),
(16, 'elmnt_active_state', '0', 'elements', 'checkbox', 51, '2008-06-09 08:55:08', '2008-06-09 08:55:08'),
(17, 'web_server', '', 'files', 'text', 99, '2008-06-09 08:55:08', '2008-06-09 08:55:08');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_storage_data`
-- 

CREATE TABLE `pcms_storage_data` (
  `id` bigint(20) NOT NULL auto_increment,
  `itemId` bigint(20) NOT NULL,
  `originalName` varchar(255) NOT NULL,
  `localName` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `itemId` (`itemId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_storage_data`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_storage_item`
-- 

CREATE TABLE `pcms_storage_item` (
  `id` bigint(20) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL,
  `parentId` bigint(20) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(250) NOT NULL,
  `typeId` varchar(128) NOT NULL,
  `username` varchar(250) NOT NULL,
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_storage_item`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_storage_type`
-- 

CREATE TABLE `pcms_storage_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `pcms_storage_type`
-- 

INSERT INTO `pcms_storage_type` (`id`, `name`, `sort`, `created`, `modified`) VALUES 
(1, 'folder', 0, '2008-09-17 13:42:16', '2008-09-17 13:42:16'),
(2, 'file', 0, '2008-09-17 13:42:16', '2008-09-17 13:42:16');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_structure`
-- 

CREATE TABLE `pcms_structure` (
  `id` int(11) NOT NULL auto_increment,
  `fileName` varchar(255) NOT NULL,
  `section` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `pcms_structure`
-- 

INSERT INTO `pcms_structure` (`id`, `fileName`, `section`, `sort`, `created`, `modified`) VALUES 
(1, 'complete_layout_nl', 'template', 1, '2007-11-16 15:13:25', '2007-11-16 15:13:25'),
(2, 'contactform_nl', 'template', 3, '2007-11-28 10:56:36', '2007-11-28 10:56:36'),
(3, 'complete_layout_en', 'template', 2, '2008-01-08 11:24:03', '2008-01-08 11:24:03'),
(4, 'validform_nl', 'template', 4, '2008-01-08 11:24:03', '2008-01-08 11:24:03');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_structure_meta`
-- 

CREATE TABLE `pcms_structure_meta` (
  `id` int(11) NOT NULL auto_increment,
  `structureId` int(11) NOT NULL,
  `language` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `pcms_structure_meta`
-- 

INSERT INTO `pcms_structure_meta` (`id`, `structureId`, `language`, `name`, `description`, `sort`, `created`, `modified`) VALUES 
(1, 1, 'english', 'Complete website Dutch', 'Structure for a complete website, with Dutch template and element names. Including Dutch and English languages.', 0, '2007-11-16 15:26:29', '2007-11-16 15:26:29'),
(2, 1, 'nederlands', 'Complete website Nederlands', 'Structuur voor een complete website met Nederlandse sjablonen en elementen, in de talen Engels en Nederlands.', 0, '2007-11-16 15:26:29', '2007-11-16 15:26:29'),
(3, 2, 'english', 'Contact form Dutch', 'Structure for a standard contact form, with Dutch template and element names. Including Dutch and English languages.', 0, '2007-11-28 10:59:57', '2007-11-28 10:59:57'),
(4, 2, 'nederlands', 'Contactformulier Nederlands', 'Structuur voor een standaard contactformulier met Nederlandse sjablonen en elementen, in de talen Engels en Nederlands.', 0, '2007-11-28 10:59:57', '2007-11-28 10:59:57'),
(5, 3, 'english', 'Complete website English', 'Structure for a complete website, with English template and element names. Including Dutch and English languages.', 0, '2008-01-08 11:30:58', '2008-01-08 11:30:58'),
(6, 3, 'nederlands', 'Complete website Engels', 'Structuur voor een complete website met Engelse sjablonen en elementen, in de talen Engels en Nederlands.', 0, '2008-01-08 11:31:33', '2008-01-08 11:31:33'),
(7, 4, 'nederlands', 'ValidForm sjabloon', 'Sjabloon voor ValidForm elementen', 0, '2008-01-08 11:31:33', '2008-01-08 11:31:33');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_template`
-- 

CREATE TABLE `pcms_template` (
  `id` int(11) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `apiName` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `parentId` int(11) NOT NULL default '0',
  `isPage` tinyint(4) NOT NULL default '0',
  `isContainer` tinyint(4) NOT NULL default '0',
  `forceCreation` tinyint(4) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `parentId` (`parentId`),
  KEY `accountId` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_template`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_template_field`
-- 

CREATE TABLE `pcms_template_field` (
  `id` bigint(20) NOT NULL auto_increment,
  `templateId` int(11) NOT NULL default '0',
  `formId` int(11) NOT NULL default '0',
  `typeId` int(11) NOT NULL default '0',
  `required` tinyint(4) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `apiName` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `username` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `templateId` (`templateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_template_field`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_template_field_type`
-- 

CREATE TABLE `pcms_template_field_type` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `input` varchar(255) NOT NULL default '',
  `element` varchar(150) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- 
-- Dumping data for table `pcms_template_field_type`
-- 

INSERT INTO `pcms_template_field_type` (`id`, `name`, `input`, `element`, `sort`, `created`, `modified`) VALUES 
(1, 'Date', 'date', 'date', 4, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(2, 'Small text', 'text', 'text', 1, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(3, 'Large text', 'textarea', 'bigtext', 2, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(4, 'File', 'file', 'bigtext', 6, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(5, 'Number', 'number', 'number', 3, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(6, 'Select list (multi)', 'select', 'bigtext', 7, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(7, 'Image', 'file', 'bigtext', 5, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(8, 'User', 'select', 'text', 12, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(9, 'Deep link', 'link', 'text', 13, '2006-06-22 22:24:30', '2006-06-22 22:24:30'),
(10, 'Boolean', 'checkbox', 'text', 14, '2006-10-24 09:21:01', '2006-10-24 09:21:01'),
(11, 'Select list (single)', 'select', 'bigtext', 8, '2007-05-14 13:46:40', '2007-05-14 13:46:40'),
(12, 'Check list (multi)', 'checkbox', 'bigtext', 9, '2007-05-14 13:50:03', '2007-05-14 13:50:03'),
(13, 'Check list (single)', 'radio', 'bigtext', 10, '2007-05-14 13:50:03', '2007-05-14 13:50:03'),
(14, 'Simple text', 'textarea', 'bigtext', 11, '2008-05-26 10:04:45', '2008-05-26 10:04:45');

-- --------------------------------------------------------

-- 
-- Table structure for table `pcms_template_field_value`
-- 

CREATE TABLE `pcms_template_field_value` (
  `id` int(11) NOT NULL auto_increment,
  `fieldId` bigint(20) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `fieldId` (`fieldId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pcms_template_field_value`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_account`
-- 

CREATE TABLE `punch_account` (
  `id` int(11) NOT NULL auto_increment,
  `punchId` varchar(64) NOT NULL,
  `name` varchar(250) NOT NULL default '',
  `uri` varchar(250) NOT NULL default '',
  `timeZoneId` int(11) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `timeZoneId` (`timeZoneId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_account`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_account_product`
-- 

CREATE TABLE `punch_account_product` (
  `id` int(11) NOT NULL auto_increment,
  `accountId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `accountId` (`accountId`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `punch_account_product`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `punch_product`
-- 

CREATE TABLE `punch_product` (
  `id` int(11) NOT NULL auto_increment,
  `parentId` int(11) NOT NULL default '0',
  `name` varchar(150) NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `punch_product`
-- 

INSERT INTO `punch_product` (`id`, `parentId`, `name`, `active`, `sort`) VALUES 
(1, 0, 'PunchCMS', 1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `punch_timezone`
-- 

CREATE TABLE `punch_timezone` (
  `id` int(11) NOT NULL auto_increment,
  `shortName` varchar(250) NOT NULL default '',
  `longName` varchar(250) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=143 ;

-- 
-- Dumping data for table `punch_timezone`
-- 

INSERT INTO `punch_timezone` (`id`, `shortName`, `longName`, `sort`, `created`, `modified`) VALUES 
(1, 'Hawaii', '(GMT-10:00) Hawaii', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Alaska', '(GMT-09:00) Alaska', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Pacific Time (US &amp; Canada)', '(GMT-08:00) Pacific Time (US &amp; Canada)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Arizona', '(GMT-07:00) Arizona', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'Mountain Time (US &amp; Canada)', '(GMT-07:00) Mountain Time (US &amp; Canada)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'Central Time (US &amp; Canada)', '(GMT-06:00) Central Time (US &amp; Canada)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'Eastern Time (US &amp; Canada)', '(GMT-05:00) Eastern Time (US &amp; Canada)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'Indiana (East)', '(GMT-05:00) Indiana (East)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'International Date Line West', '(GMT-12:00) International Date Line West', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'Midway Island', '(GMT-11:00) Midway Island', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'Samoa', '(GMT-11:00) Samoa', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'Tijuana', '(GMT-08:00) Tijuana', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'Chihuahua', '(GMT-07:00) Chihuahua', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'La Paz', '(GMT-07:00) La Paz', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'Mazatlan', '(GMT-07:00) Mazatlan', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'Central America', '(GMT-06:00) Central America', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'Guadalajara', '(GMT-06:00) Guadalajara', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'Mexico City', '(GMT-06:00) Mexico City', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'Monterrey', '(GMT-06:00) Monterrey', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'Saskatchewan', '(GMT-06:00) Saskatchewan', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 'Bogota', '(GMT-05:00) Bogota', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 'Lima', '(GMT-05:00) Lima', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 'Quito', '(GMT-05:00) Quito', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 'Atlantic Time (Canada)', '(GMT-04:00) Atlantic Time (Canada)', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 'Caracas', '(GMT-04:00) Caracas', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 'La Paz', '(GMT-04:00) La Paz', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 'Santiago', '(GMT-04:00) Santiago', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 'Newfoundland', '(GMT-03:30) Newfoundland', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 'Brasilia', '(GMT-03:00) Brasilia', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'Buenos Aires', '(GMT-03:00) Buenos Aires', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 'Georgetown', '(GMT-03:00) Georgetown', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'Greenland', '(GMT-03:00) Greenland', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 'Mid-Atlantic', '(GMT-02:00) Mid-Atlantic', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 'Azores', '(GMT-01:00) Azores', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 'Cape Verde Is.', '(GMT-01:00) Cape Verde Is.', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 'Casablanca', '(GMT) Casablanca', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 'Dublin', '(GMT) Dublin', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 'Edinburgh', '(GMT) Edinburgh', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 'Lisbon', '(GMT) Lisbon', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 'London', '(GMT) London', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 'Monrovia', '(GMT) Monrovia', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 'Amsterdam', '(GMT+01:00) Amsterdam', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 'Belgrade', '(GMT+01:00) Belgrade', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 'Berlin', '(GMT+01:00) Berlin', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 'Bern', '(GMT+01:00) Bern', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 'Bratislava', '(GMT+01:00) Bratislava', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 'Brussels', '(GMT+01:00) Brussels', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, 'Budapest', '(GMT+01:00) Budapest', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 'Copenhagen', '(GMT+01:00) Copenhagen', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 'Ljubljana', '(GMT+01:00) Ljubljana', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 'Madrid', '(GMT+01:00) Madrid', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, 'Paris', '(GMT+01:00) Paris', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, 'Prague', '(GMT+01:00) Prague', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, 'Rome', '(GMT+01:00) Rome', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 'Sarajevo', '(GMT+01:00) Sarajevo', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 'Skopje', '(GMT+01:00) Skopje', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 'Stockholm', '(GMT+01:00) Stockholm', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 'Vienna', '(GMT+01:00) Vienna', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 'Warsaw', '(GMT+01:00) Warsaw', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 'West Central Africa', '(GMT+01:00) West Central Africa', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 'Zagreb', '(GMT+01:00) Zagreb', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 'Athens', '(GMT+02:00) Athens', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 'Bucharest', '(GMT+02:00) Bucharest', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 'Cairo', '(GMT+02:00) Cairo', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 'Harare', '(GMT+02:00) Harare', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 'Helsinki', '(GMT+02:00) Helsinki', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 'Istanbul', '(GMT+02:00) Istanbul', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 'Jerusalem', '(GMT+02:00) Jerusalem', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, 'Kyev', '(GMT+02:00) Kyev', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 'Minsk', '(GMT+02:00) Minsk', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 'Pretoria', '(GMT+02:00) Pretoria', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 'Riga', '(GMT+02:00) Riga', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 'Sofia', '(GMT+02:00) Sofia', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 'Tallinn', '(GMT+02:00) Tallinn', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, 'Vilnius', '(GMT+02:00) Vilnius', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, 'Baghdad', '(GMT+03:00) Baghdad', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 'Kuwait', '(GMT+03:00) Kuwait', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 'Moscow', '(GMT+03:00) Moscow', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 'Nairobi', '(GMT+03:00) Nairobi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 'Riyadh', '(GMT+03:00) Riyadh', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 'St. Petersburg', '(GMT+03:00) St. Petersburg', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 'Volgograd', '(GMT+03:00) Volgograd', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 'Tehran', '(GMT+03:30) Tehran', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 'Abu Dhabi', '(GMT+04:00) Abu Dhabi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 'Baku', '(GMT+04:00) Baku', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 'Muscat', '(GMT+04:00) Muscat', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 'Tbilisi', '(GMT+04:00) Tbilisi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 'Yerevan', '(GMT+04:00) Yerevan', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 'Kabul', '(GMT+04:30) Kabul', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 'Ekaterinburg', '(GMT+05:00) Ekaterinburg', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 'Islamabad', '(GMT+05:00) Islamabad', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 'Karachi', '(GMT+05:00) Karachi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 'Tashkent', '(GMT+05:00) Tashkent', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 'Chennai', '(GMT+05:30) Chennai', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(95, 'Kolkata', '(GMT+05:30) Kolkata', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(96, 'Mumbai', '(GMT+05:30) Mumbai', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(97, 'New Delhi', '(GMT+05:30) New Delhi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(98, 'Kathmandu', '(GMT+05:45) Kathmandu', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(99, 'Almaty', '(GMT+06:00) Almaty', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(100, 'Astana', '(GMT+06:00) Astana', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(101, 'Dhaka', '(GMT+06:00) Dhaka', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, 'Novosibirsk', '(GMT+06:00) Novosibirsk', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, 'Sri Jayawardenepura', '(GMT+06:00) Sri Jayawardenepura', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, 'Rangoon', '(GMT+06:30) Rangoon', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, 'Bangkok', '(GMT+07:00) Bangkok', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, 'Hanoi', '(GMT+07:00) Hanoi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, 'Jakarta', '(GMT+07:00) Jakarta', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, 'Krasnoyarsk', '(GMT+07:00) Krasnoyarsk', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, 'Beijing', '(GMT+08:00) Beijing', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, 'Chongqing', '(GMT+08:00) Chongqing', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, 'Hong Kong', '(GMT+08:00) Hong Kong', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, 'Irkutsk', '(GMT+08:00) Irkutsk', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, 'Kuala Lumpur', '(GMT+08:00) Kuala Lumpur', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, 'Perth', '(GMT+08:00) Perth', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(115, 'Singapore', '(GMT+08:00) Singapore', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(116, 'Taipei', '(GMT+08:00) Taipei', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(117, 'Ulaan Bataar', '(GMT+08:00) Ulaan Bataar', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(118, 'Urumqi', '(GMT+08:00) Urumqi', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(119, 'Osaka', '(GMT+09:00) Osaka', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(120, 'Sapporo', '(GMT+09:00) Sapporo', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(121, 'Seoul', '(GMT+09:00) Seoul', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(122, 'Tokyo', '(GMT+09:00) Tokyo', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(123, 'Yakutsk', '(GMT+09:00) Yakutsk', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(124, 'Adelaide', '(GMT+09:30) Adelaide', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(125, 'Darwin', '(GMT+09:30) Darwin', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(126, 'Brisbane', '(GMT+10:00) Brisbane', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(127, 'Canberra', '(GMT+10:00) Canberra', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, 'Guam', '(GMT+10:00) Guam', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, 'Hobart', '(GMT+10:00) Hobart', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, 'Melbourne', '(GMT+10:00) Melbourne', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, 'Port Moresby', '(GMT+10:00) Port Moresby', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, 'Sydney', '(GMT+10:00) Sydney', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 'Vladivostok', '(GMT+10:00) Vladivostok', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, 'Magadan', '(GMT+11:00) Magadan', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(135, 'New Caledonia', '(GMT+11:00) New Caledonia', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(136, 'Solomon Is.', '(GMT+11:00) Solomon Is.', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(137, 'Auckland', '(GMT+12:00) Auckland', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(138, 'Fiji', '(GMT+12:00) Fiji', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(139, 'Kamchatka', '(GMT+12:00) Kamchatka', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(140, 'Marshall Is.', '(GMT+12:00) Marshall Is.', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(141, 'Wellington', '(GMT+12:00) Wellington', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(142, 'Nuku''alofa', '(GMT+13:00) Nuku''alofa', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `pcms_announce_user`
-- 
ALTER TABLE `pcms_announce_user`
  ADD CONSTRAINT `pcms_announce_user_ibfk_3` FOREIGN KEY (`messageId`) REFERENCES `pcms_announce_message` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element`
-- 
ALTER TABLE `pcms_element`
  ADD CONSTRAINT `pcms_element_ibfk_3` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_field`
-- 
ALTER TABLE `pcms_element_field`
  ADD CONSTRAINT `pcms_element_field_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `pcms_element` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pcms_element_field_ibfk_2` FOREIGN KEY (`templateFieldId`) REFERENCES `pcms_template_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_field_bigtext`
-- 
ALTER TABLE `pcms_element_field_bigtext`
  ADD CONSTRAINT `pcms_element_field_bigtext_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `pcms_element_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_field_date`
-- 
ALTER TABLE `pcms_element_field_date`
  ADD CONSTRAINT `pcms_element_field_date_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `pcms_element_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_field_number`
-- 
ALTER TABLE `pcms_element_field_number`
  ADD CONSTRAINT `pcms_element_field_number_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `pcms_element_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_field_text`
-- 
ALTER TABLE `pcms_element_field_text`
  ADD CONSTRAINT `pcms_element_field_text_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `pcms_element_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_language`
-- 
ALTER TABLE `pcms_element_language`
  ADD CONSTRAINT `pcms_element_language_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `pcms_element` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_permission`
-- 
ALTER TABLE `pcms_element_permission`
  ADD CONSTRAINT `pcms_element_permission_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `pcms_element` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_element_schedule`
-- 
ALTER TABLE `pcms_element_schedule`
  ADD CONSTRAINT `pcms_element_schedule_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `pcms_element` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_form`
-- 
ALTER TABLE `pcms_form`
  ADD CONSTRAINT `pcms_form_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_language`
-- 
ALTER TABLE `pcms_language`
  ADD CONSTRAINT `pcms_language_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_search_index`
-- 
ALTER TABLE `pcms_search_index`
  ADD CONSTRAINT `pcms_search_index_ibfk_1` FOREIGN KEY (`elementId`) REFERENCES `pcms_element` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_setting`
-- 
ALTER TABLE `pcms_setting`
  ADD CONSTRAINT `pcms_setting_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pcms_setting_ibfk_2` FOREIGN KEY (`settingId`) REFERENCES `pcms_setting_tpl` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_template`
-- 
ALTER TABLE `pcms_template`
  ADD CONSTRAINT `pcms_template_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_template_field`
-- 
ALTER TABLE `pcms_template_field`
  ADD CONSTRAINT `pcms_template_field_ibfk_1` FOREIGN KEY (`templateId`) REFERENCES `pcms_template` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_template_field_value`
-- 
ALTER TABLE `pcms_template_field_value`
  ADD CONSTRAINT `pcms_template_field_value_ibfk_1` FOREIGN KEY (`fieldId`) REFERENCES `pcms_template_field` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `punch_account`
-- 
ALTER TABLE `punch_account`
  ADD CONSTRAINT `punch_account_ibfk_1` FOREIGN KEY (`timeZoneId`) REFERENCES `punch_timezone` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `punch_account_product`
-- 
ALTER TABLE `punch_account_product`
  ADD CONSTRAINT `punch_account_product_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `punch_account_product_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `punch_product` (`id`) ON DELETE CASCADE;
  
-- 
-- Constraints for table `pcms_storage_data`
-- 
ALTER TABLE `pcms_storage_data`
  ADD CONSTRAINT `pcms_storage_data_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `pcms_storage_item` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `pcms_storage_item`
-- 
ALTER TABLE `pcms_storage_item`
  ADD CONSTRAINT `pcms_storage_item_ibfk_1` FOREIGN KEY (`accountId`) REFERENCES `punch_account` (`id`) ON DELETE CASCADE;