<?php

$arrDomain = split('\.', $_SERVER['HTTP_HOST']);
$intCatId = Request::get("cid");

if ($intCatId != NAV_MYPUNCH_NOACCOUNT) {
	if (count($arrDomain) > 2) {
		$strDomain = $arrDomain[count($arrDomain) - 2] . "." . $arrDomain[count($arrDomain) - 1];
		$strSubName = $arrDomain[0];

		//*** Check if the account exists.
		$objAccount = Account::getByUri($strSubName);

		if (!is_object($objAccount)) {
			//*** Account does not exist.
			header("Location: " . Request::getURI("http") . "/?cid=" . NAV_MYPUNCH_NOACCOUNT);
			exit();
		}

		//*** Set the PunchId.
		$_CONF['app']['account'] 		= $objAccount;
		$_CONF['app']['pageTitle']		= sprintf($objLang->get('pageTitle'), $_CONF['app']['account']->getName(), APP_NAME, APP_VERSION);
	} else {
		/* The URI is in an invalid format.
		/* Redirect to the "account error" page. */

		header("Location: " . Request::getURI("http") . "/?cid=" . NAV_MYPUNCH_NOACCOUNT);
		exit();
	}
} else {
	$objAccount = new Account();

	$_CONF['app']['account'] 		= $objAccount;
	$_CONF['app']['pageTitle']		= $objLang->get('noAccountPageTitle');
}

?>