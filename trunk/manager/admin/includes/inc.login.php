<?php

$strUsername 	= request('handle');
$strPassword 	= request('passwd');
$blnRemember 	= request('remember_me');
($blnRemember === "on") ? $blnRemember = true : $blnRemember = false;

//*** Check if we need to logout.
if ($objLiveUser->isLoggedIn() && $cmd == CMD_LOGOUT) {
	$objLiveUser->logout();
} else if (!$objLiveUser->isLoggedIn() || (!empty($strUsername) && $objLiveUser->getProperty('handle') != $strUsername)) {
	//*** Log in using LiveUser.
	if (empty($strUsername)) {
		$objLiveUser->login(null, null, true);
	} else {
		if (!$objLiveUser->login($strUsername, $strPassword, $blnRemember)) {
    		$objErrors = $objLiveUser->getErrors();
    		if (count($objErrors) > 0) {
    			foreach ($objErrors as $objError) {
    				echo $objError["message"] . "<br />";
    			}
    			die('User could not log in.');
    		}
		}
	}
}

if (!$objLiveUser->isLoggedIn()) {
	//*** Check if there are any users available.
	$filters = array('container' => 'auth', 'filters' => array('account_id' => array(0)));
	$objUsers = $objLiveAdmin->getUsers($filters);

	if (count($objUsers) <= 0 && $cId != NAV_ADMIN && $cmd != CMD_ADD) {
		//*** Redirect to the admin creation screen.
		header("Location: " . Request::getURI() . "/?cid=" . NAV_ADMIN . "&cmd=" . CMD_ADD);
		exit();
	}
} else if ($objLiveUser->getProperty('account_id') !== 0) {
	//*** Only Super Admins allowed.
	$objLiveUser->logout();
}

?>