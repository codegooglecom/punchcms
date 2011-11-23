<?php
		
$_CONF['db']['host'] 				= "!!DB_SERVER!!";
$_CONF['db']['dbName'] 				= "!!DB_NAME!!";
$_CONF['db']['username'] 			= "!!DB_USERNAME!!";
$_CONF['db']['password'] 			= "!!DB_PASSWORD!!";
$_CONF['db']['dsn'] 				= "mysql://{$_CONF['db']['username']}:{$_CONF['db']['password']}@{$_CONF['db']['host']}/{$_CONF['db']['dbName']}?charset=utf8";
	
$_CONF['comm']['mailFrom']			= "!!MAIL_ADMIN!!";


/* DO NOT MODIFY THE FOLLOWING SETTINGS IF YOU ARE NOT SURE WHAT YOU ARE DOING */

$_CONF['app']['basePath'] 			= dirname(__FILE__) . "/";
$_CONF['app']['baseUri']			= "/";
$_CONF['app']['langPath'] 			= $_CONF['app']['basePath'] . "languages/";
$_CONF['app']['uploadPath'] 		= $_CONF['app']['basePath'] . "files/";
$_CONF['app']['defaultLang']		= "nederlands-utf-8";
$_CONF['app']['universalDate']		= "%d %B %Y %H:%M:%S";
$_CONF['app']['minPassLength']		= 7;
$_CONF['app']['maxPassLength']		= 15;
$_CONF['app']['listLength']			= 10;
$_CONF['app']['secureLogin']		= FALSE;
$_CONF['app']['singleInstance']		= !!CMS_TYPE!!;
$_CONF['app']['maxBackups']			= 3;
$_CONF['app']['timezone'] 			= 'America/La_Paz';
$_CONF['app']['msMypunch']			= array (
											"product" => array (
												"pcms" => NAV_MYPUNCH_PCMS,
											),
											"users" => NAV_MYPUNCH_USERS,
											"profile" => NAV_MYPUNCH_PROFILE,
										);
$_CONF['app']['msPcms']				= array (
											"elements" => NAV_PCMS_ELEMENTS,
											"templates" => NAV_PCMS_TEMPLATES,
											"storage" => NAV_PCMS_STORAGE,
											"aliases" => NAV_PCMS_ALIASES,
											"feeds" => NAV_PCMS_FEEDS,
											"languages" => NAV_PCMS_LANGUAGES,
											"settings" => NAV_PCMS_SETTINGS,
											"search" => NAV_PCMS_SEARCH,
											"help" => NAV_PCMS_HELP,
										);
$_CONF['app']['msPprojects']		= array (
											"projects" => NAV_PPROJECTS_PROJECTS,
											"contacts" => NAV_PPROJECTS_CONTACTS,
											"calendar" => NAV_PPROJECTS_CALENDAR,
											"help" => NAV_PPROJECTS_HELP,
										);

?>