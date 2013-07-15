<?php

require_once('includes/init.php');

$eId 		= Request::get('id', 0);
$type 		= Request::get('type', "elements");
$cmd 		= Request::get('cmd', "list");

if ($eId == "-1") $eId = 0;
$strReturn 	= Tree::buildXmlTree($eId, $type, $cmd, $eId);

header('Content-type: text/xml');
echo $strReturn;

?>