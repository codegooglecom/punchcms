<?php

/**************************************************************************
* PunchCMS Export2Html script v.0.2.0
* Exports an entire site to plain HTML files, including the used folders.
**************************************************************************/

require_once('includes/init.php');

$strBaseFolder 		= $_CONF['app']['basePath'];
$strZipFolder 		= $_CONF['app']['basePath'] . "../cache/";
$strZipName 		= $strZipFolder . "exportZip_" . rand() . ".zip";
$objZip 			= new dZip($strZipName, TRUE);
$blnDebug			= FALSE;

function copyr($source, $dest, $fileFilter = array()){
	global $objZip;

	//*** Simple copy for a file.
	if (is_file($source)) {
		if (count($fileFilter) == 0 || in_array(extension($source), $fileFilter)) {
			$objZip->addFile($source, $dest);
			logExport($source);
			return true;
		} else {
			return false;
		}
	}

	if (!is_dir($source)) {
		return false;
	}

	//*** Loop through the folder
	$dir = dir($source);
	while (false !== $entry = $dir->read()) {
		//*** Skip pointers
		if ($entry == "." || $entry == "..") {
			continue;
		}

		//*** Deep copy directories
		if ($dest !== "$source/$entry") {
			copyr("$source/$entry", "$dest/$entry", $fileFilter);
		}
	}

	logExport($source);

	//*** Clean up
	$dir->close();
	return true;
}

function extension($strPath) {
	$arrPath = pathinfo($strPath);

	return $arrPath['extension'];
}

function logExport($strLog) {
	global $blnDebug;

	if ($blnDebug) echo "(" . strftime("%Y-%m-%d %H:%M:%S") . ") exported: " . $strLog . "<br />";
}

function fixLinks($strInput) {
	$objCms = PCMS_Client::getInstance();
	$strOutput = $strInput;

	//*** Transform old skool ?eid= URLs to /eid/ URLs.
	$strPattern = "/href=\"(\/)*(\?eid=)([0-9]+)/ie";
	$arrMatches = array();
	if (preg_match_all($strPattern, $strOutput, $arrMatches) > 0) {
		for ($intCount = 0; $intCount < count($arrMatches[0]); $intCount++) {
			$strMatch = $arrMatches[0][$intCount];
			$objElement = $objCms->getElementById($arrMatches[3][$intCount]);
			if (is_object($objElement)) {
				$strOutput = str_ireplace("{$strMatch}", "href=\"" . $objElement->getLink(), $strOutput);
			}
		}
	}

	//*** Transform download.php?eid= URLs to the direct file URLs.
	preg_match_all("/href=\"download.php\?eid=([0-9]+)(&index=([0-9]+))*\"/", $strOutput, $arrMatches);
	if (count($arrMatches) > 1) {
		for ($intCount = 0; $intCount < count($arrMatches[1]); $intCount++) {
			$intFieldId = $arrMatches[1][$intCount];
			$intIndex = (!empty($arrMatches[3][$intCount])) ? $arrMatches[3][$intCount] : 0;
			$objElementField = $objCms->getFieldById($intFieldId);
			if (is_object($objElementField)) {
				$arrFiles = $objElementField->getValue();
				if (is_array($arrFiles) && count($arrFiles) > $intIndex) {
					$arrValue = $arrFiles[$intIndex];
					$strTarget = $objCms->getFilePath() . $arrValue['src'];

					$strOutput = str_replace($arrMatches[0][$intCount], "href=\"{$strTarget}\"", $strOutput);
				}
			}
		}
	}

	//*** Transform aliases to .html files.
	$strOutput = ereg_replace("href=\"(/[a-zA-Z0-9_/-]+)\"", "href=\"\\1.html\"", $strOutput);

	return $strOutput;
}

function exportIndexPage($objLanguage) {
	global $objZip;

	$strDirname = (!$objLanguage->default) ? "language/" . $objLanguage->getAbbr() : "";
	$strBasename = "index.html";

	$strFile = file_get_contents(Request::getRootURI() . "/language/" . $objLanguage->getAbbr());
	$objZip->addFile($strDirname . "/" . $strBasename, '', '', fixLinks($strFile));

	logExport($strDirname . "/" . $strBasename);
}

function exportPage($objPageElement, $objLanguage) {
	global $objZip;

	$objCms = PCMS_Client::getInstance();

	$strDirname = (!$objLanguage->default) ? dirname($objPageElement->getLink(TRUE, "", $objLanguage->getAbbr())) : dirname($objPageElement->getLink());
	$strBasename = basename($objPageElement->getLink());

	if ($strDirname == "/") $strDirname = "";

	$strFile = file_get_contents(Request::getRootURI() . $objPageElement->getLink());
	$objZip->addFile($strDirname . "/" . $strBasename . ".html", '', '', fixLinks($strFile));

	logExport($strDirname . "/" . $strBasename);
}

function exportSite() {
	global $strBaseFolder,
		$strZipFolder,
		$strZipName,
		$objZip;

	$objCms = PCMS_Client::getInstance();
	$objCms->setLanguage(ContentLanguage::getDefault());

	//*** Get a collection of Languages.
	$objLanguages = $objCms->getLanguages();
	foreach ($objLanguages as $objLanguage) {
		//*** Export the entry page.
		exportIndexPage($objLanguage);

		//*** Get the list of pages.
		$objElements = $objCms->getPageElements($objLanguage->getId());
		foreach ($objElements as $objElement) {
			exportPage($objElement, $objLanguage);
		}
	}

	//*** Export auxilary fodlers.
	$arrFileFilter = array("js", "css", "jpg", "gif", "png", "ico", "txt", "html");
	copyr($strBaseFolder . "css", "css", $arrFileFilter);
	copyr($strBaseFolder . "includes", "includes", $arrFileFilter);
	copyr($strBaseFolder . "images", "images", $arrFileFilter);
	copyr($strBaseFolder . "files", "files", $arrFileFilter);
	copyr($strBaseFolder . "libraries", "libraries", $arrFileFilter);
	copyr($strBaseFolder . "favicon.ico", "favicon.ico");
	copyr($strBaseFolder . "robots.txt", "robots.txt");

	//*** Close zip file.
    $objZip->save();

    //*** Return the zip file to the user.
	header("HTTP/1.1 200 OK");
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Type: application/octetstream; charset=utf-8');
	header("Content-Length: " . (string)(filesize($strZipName)));
	header('Content-Disposition: attachment; filename="' . $objCms->getAccount()->getUri() . '.zip"');
	header("Content-Transfer-Encoding: binary\n");

	readfile($strZipName);
	exit;
}

exportSite();

?>
