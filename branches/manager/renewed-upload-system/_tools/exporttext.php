<?php

/**************************************************************************
* PunchCMS ExportText script v.0.1.0
* Exports all text from a website.
**************************************************************************/

require_once('includes/init.php');

$strBaseFolder 		= $GLOBALS['_CONF']['app']['basePath'];
$strExportFolder 	= $GLOBALS['_CONF']['app']['basePath'] . "cache/";
$strExportName 		= $strExportFolder . "exporttext_" . rand() . ".txt";

function writeToExport($strBody) {
	global $strExportName;
	
	file_put_contents($strExportName, $strBody . "\n", FILE_APPEND);
}

function exportPage($objElement) {	
	$objFields = $objElement->getFields();
	foreach ($objFields as $objField) {
		switch ($objField->getTypeId()) {
			case FIELD_TYPE_LARGETEXT:
			case FIELD_TYPE_SIMPLETEXT:
			case FIELD_TYPE_CHECK_LIST_MULTI:
			case FIELD_TYPE_CHECK_LIST_SINGLE:
			case FIELD_TYPE_SELECT_LIST_MULTI:
			case FIELD_TYPE_SELECT_LIST_SINGLE:
			case FIELD_TYPE_SMALLTEXT:
				writeToExport(strip_tags($objField->getValue()));
				
				break;
		}
	}	
	
	$objElements = $objElement->getElements();
	foreach ($objElements as $objElement) {
		exportPage($objElement);
	}
}

function exportSite() {
	global $strExportName;

	$objCms = PCMS_Client::getInstance();
	$objCms->setLanguage(ContentLanguage::getDefault());

	//*** Get the list of pages.
	$objElements = $objCms->getElementByTemplate("Pages")->getElements();
	foreach ($objElements as $objElement) {
		exportPage($objElement);
	}

	//*** Return the zip file to the user.
	header("HTTP/1.1 200 OK");
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Type: text; charset=utf-8');
	header("Content-Length: " . (string)(filesize($strExportName)));
	header('Content-Disposition: attachment; filename="' . $objCms->getAccount()->getUri() . '.txt"');

	readfile($strExportName);
	exit;
}

exportSite();

?>