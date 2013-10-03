<?php

require_once('includes/init.php');

//*** Only logged in users are allowed to make AJAX requests
require_once('includes/inc.login.php');

$eId 		= Request::get('id', 0);
$type 		= Request::get('type', "elements");
$cmd 		= Request::get('cmd', "list");

if ($eId == "-1" || !is_numeric($eId)) {
    $eId = 0;
}
$strReturn 	= Tree::buildXmlTree($eId, $type, $cmd, $eId);

header('Content-type: text/xml');
echo $strReturn;

?>