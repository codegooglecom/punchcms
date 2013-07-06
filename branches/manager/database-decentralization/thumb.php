<?php

require_once('includes/init.php');

//*** Only logged in users are allowed to make thumbs
require_once('includes/inc.login.php');

$src 			= request('src');
$strExtension 	= substr(strrchr($src, '.'), 1);
$strHash		= md5($src) . "." . $strExtension;

$arrExtensionWhiteList = array(
	"jpg", "jpeg", "png", "gif"
);

if (in_array($strExtension, $arrExtensionWhiteList)) {
	@file_put_contents($_PATHS['upload'] . $strHash, file_get_contents($src));
	ImageResizer::resize($_PATHS['upload'] . $strHash, 15, 11, RESIZE_DISTORT, 60, true, null, true);
} else {
	// Fallback image

}
