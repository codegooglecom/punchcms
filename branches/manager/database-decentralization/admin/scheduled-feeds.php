<?php

require_once('includes/init.php');

set_time_limit(60 * 60 * 5); //*** 5 hours max execution time.

//*** Render Dynamic elements.
$objAccounts = Account::select();
foreach ($objAccounts as $objAccount) {
	$_CONF['app']['account'] = $objAccount;
	
	$objFeeds = Feed::selectActive();
	foreach ($objFeeds as $objFeed) {
		$objFeed->updateElements();
	}
}

echo "Scheduled feed sync finished.";

?>