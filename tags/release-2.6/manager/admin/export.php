<?php require_once('includes/init.php'); ?>
<?php

$accountId		= Request::get("eid", 0);
$exportFiles	= Request::get("files", true);
$blnError 		= false;

$objAccount = Account::selectByPk($accountId);
$strZipFile = ImpEx::export($accountId, $exportFiles);

if (is_object($objAccount) && $strZipFile !== false) {
	header("HTTP/1.1 200 OK");
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header('Content-Type: application/octetstream; charset=utf-8');
	header("Content-Length: " . (string)(filesize($strZipFile)));
	header('Content-Disposition: attachment; filename="' . date("Y-m-d") . '_' . $objAccount->getUri() . '.zip"');
	header("Content-Transfer-Encoding: binary\n");

	readfile($strZipFile);
	exit;
} else {

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Downloader</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<h2>Sorry, File not found</h2>
<p>Unfortunatly we were unable to find the file you requested.</p>
<p>Please inform the administrator of this website to prevent future problems.</p>
</body>
</html>

<?php

}

?>