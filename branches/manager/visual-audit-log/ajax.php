<?php

require_once('includes/init.php');

//*** Only logged in users are allowed to make AJAX requests
require_once('includes/inc.login.php');

$cmd 		= request('cmd');
$params     = request('params');
$strReturn 	= "";

header('Content-type: text/xml');
header("HTTP/1.0 500 Internal Server Error"); // Use this to test error handling in Javascript

$strReturn .= '<?xml version="1.0" encoding="utf-8" ?>';
$strReturn .= '<ajax-response>';

if (!empty($cmd)) {
	$strReturn .= '<response type="object" id="command">';
	$strReturn .= "<command value=\"{$cmd}\">";
	$strReturn .= call_user_func_array(explode('::', $cmd), explode(",", $params));
	$strReturn .= '</command>';
	$strReturn .= '</response>';
} else {
	$strReturn .= '<response type="object" id="error">';
	$strReturn .= '<error id="404">The command was invalid.</error>';
	$strReturn .= '</response>';
}

$strReturn .= '</ajax-response>';

echo $strReturn;
