<?php

$strUsername 	= request('handle');
$strPassword 	= request('passwd');
$blnRemember 	= request('remember_me');
($blnRemember === "on") ? $blnRemember = TRUE : $blnRemember = FALSE;

//*** Check if we need to logout.
if ($objLiveUser->isLoggedIn() && $strCommand == CMD_LOGOUT) {
	$objLiveUser->logout();

	header("Location: " . Request::getURI());
	exit();
} else if (!$objLiveUser->isLoggedIn() || (!empty($strUsername) && $objLiveUser->getProperty('handle') != $strUsername)) {
	//*** Log in using LiveUser.
	if (empty($strUsername)) {
		$objLiveUser->login(null, null, true, false, $_CONF['app']['account']->getId());
	} else {
		if (!$objLiveUser->login($strUsername, $strPassword, $blnRemember, false, $_CONF['app']['account']->getId())) {
    		$objErrors = $objLiveUser->getErrors();
    		if (count($objErrors) > 0) {
    			foreach ($objErrors as $objError) {
    				echo $objError["message"] . "<br />";
    			}
    			die('User could not log in.');
    		}
		} else {
			//*** Clear old audit logs.
			AuditLog::cleanLog();
		
			header("Location: " . Request::getURI("http"));
			exit();
		}
	}
}

if (!$objLiveUser->isLoggedIn() && $intCatId != NAV_MYPUNCH_LOGIN && $intCatId != NAV_MYPUNCH_NOACCOUNT) {
	//*** Redirect to the login screen.
	if ($_CONF['app']['secureLogin']) {
		header("Location: " . Request::getURI("https") . "/?cid=" . NAV_MYPUNCH_LOGIN);
	} else {
		header("Location: " . Request::getURI("http") . "/?cid=" . NAV_MYPUNCH_LOGIN);
	}
	exit();
} else if ($objLiveUser->isLoggedIn() && $objLiveUser->getProperty('account_id') != $_CONF['app']['account']->getId()) {
	//*** Users from other accounts are not allowed.
	$objLiveUser->logout();
	header("Location: " . Request::getURI());
	exit();
} else if ($objLiveUser->isLoggedIn() && Request::getProtocol() == "https") {
	header("Location: " . Request::getURI("http"));
}

?>