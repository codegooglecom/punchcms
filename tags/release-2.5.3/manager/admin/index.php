<?php

//*** Initialize.
require_once('includes/init.php');

//*** Get global directives.
$eId 		= request("eid", 0);
$cId 		= request("cid", key($_CONF['app']['menu']));
$strOutput 	= "";

if (key_exists($cId, $_CONF['app']['menu'])
		&& count($_CONF['app']['menu'][$cId]) > 1
		&& is_array($_CONF['app']['menu'][$cId][1])) {
	$tmpCmd = key($_CONF['app']['menu'][$cId][1]);
} else {
	$tmpCmd = 0;
}
$cmd 		= request("cmd", $tmpCmd);

//*** Handle login directives.
require_once('includes/inc.login.php');

//*** Handle command requests.
switch ($cmd) {
	case CMD_REMOVE:
		switch ($cId) {
			case NAV_ACCOUNT:
				//*** Delete account.
				$objAccount = Account::selectByPk($eId);
				if (is_object($objAccount)) {
					$objAccount->delete();
				}

				$cmd = CMD_BROWSE;
				break;
		}
		break;
}

//*** Load the Template Parse methods.
require_once('includes/inc.tplparse.php');

$strOutput .= parseHeader($cId, $eId, $cmd);

if ($objLiveUser->isLoggedIn()) {
	if ($cmd == CMD_BROWSE) {
		$strOutput .= parseBrowse($cId, $eId, $cmd);
	} else {
		switch ($cId) {
			case NAV_ACCOUNT:
				$strOutput .= parseAccount($eId, $cmd);
				break;
				
			case NAV_TOOLS:
				$strOutput .= parseTools($eId, $cmd);
				break;
		}
	}
} else {
	switch ($cId) {
		case NAV_ADMIN:
			$strOutput .= parseAdmin($eId, $cmd);
			break;
			
		default:
			$strOutput .= parseLogin();
	}
}

$strOutput .= parseFooter($cId, $eId, $cmd);

echo $strOutput;

?>

