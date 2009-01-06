#!/usr/bin/php-cgi -q
<?php

require_once('includes/init.php');

//*** Clear stale files from the files folder.
$intNow = time();
$arrFiles = scandir($_PATHS['upload']);
foreach ($arrFiles as $strFile) {
	if ($strFile != "." && $strFile != "..") {
		$arrStat = stat($_PATHS['upload'] . $strFile);
		if ($intNow - $arrStat["mtime"] > 180) {
			@unlink($_PATHS['upload'] . $strFile);
		}
	}
}

//*** Make backups.
$objAccounts = Account::select();
foreach ($objAccounts as $objAccount) {
	$objAccount->makeBackup($_CONF['app']['maxBackups']);
}

?>