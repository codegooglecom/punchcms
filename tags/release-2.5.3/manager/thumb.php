<?php

require_once('includes/init.php');

$src 			= request('src');
$strExtension 	= substr(strrchr($src, '.'), 1);
$strHash		= md5($src) . "." . $strExtension;

@file_put_contents($_PATHS['upload'] . $strHash, file_get_contents($src));
ImageResizer::resize($_PATHS['upload'] . $strHash, 15, 11, RESIZE_DISTORT, 60, TRUE, NULL, TRUE);

?>