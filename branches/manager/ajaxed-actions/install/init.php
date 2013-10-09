<?php

/******************************
* Start session.
***/
session_save_path($_SERVER["DOCUMENT_ROOT"] . "/sessions");

$sid = session_id();
if (empty($sid)) {
	session_start();
}
$_CONF['app']['basePath'] = dirname(__FILE__) . "/../";

/******************************
* Set include paths.
***/
$_PATHS['includes'] 	= $_CONF['app']['basePath'] . 'includes/';
$_PATHS['libraries'] 	= $_CONF['app']['basePath'] . 'libraries/';
$_PATHS['pear']			= $_CONF['app']['basePath'] . 'pear/';

ini_set("include_path", $_PATHS['includes'] .
	PATH_SEPARATOR . $_PATHS['libraries'] .
	PATH_SEPARATOR . $_PATHS['pear']);

/******************************
* Load common methods.
***/
require_once('inc.common.php');

?>