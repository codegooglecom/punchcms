<?php

require_once('includes/init.php');

$cmd 		= request('cmd');
$strReturn 	= "";

header('Content-type: text/xml');
$strReturn .= '<?xml version="1.0" encoding="utf-8" ?>';
$strReturn .= '<ajax-response>';

if (!empty($cmd)) {
	$strReturn .= '<response type="object" id="command">';
	$strReturn .= "<command value=\"{$cmd}\">";
	$strReturn .= call_user_func(explode('::', $cmd));
	$strReturn .= '</command>';
	$strReturn .= '</response>';
} else {
	$strReturn .= '<response type="object" id="error">';
	$strReturn .= '<error id="404">The command was invalid.</error>';
	$strReturn .= '</response>';
}

$strReturn .= '</ajax-response>';

echo $strReturn;

?>