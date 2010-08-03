<?php

require_once('includes/init.php');

set_time_limit(60 * 60 * 5); //*** 5 hours max execution time.

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

//*** Render Dynamic elements.
$objAccounts = Account::select();
foreach ($objAccounts as $objAccount) {
	$_CONF['app']['account'] = $objAccount;
	
	$objFeeds = Feed::selectActive();
	foreach ($objFeeds as $objFeed) {
		$objFeed->updateElements();
	}
}

echo "Schedule finished.";

?>