<?php

$listCount = request("list");

if (!empty($listCount) && is_numeric($listCount)) {
	$_SESSION["listCount"] = $listCount;
} else if (empty($_SESSION["listCount"])) {
	$_SESSION["listCount"] = $_CONF['app']['listLength'];
}

?>