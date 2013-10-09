<?php

//$start = microtime(TRUE);

require_once('includes/init.php');

//*** Global Variables.
$intCatId 	= request('cid');
$intElmntId = request('eid', 0);
$strCommand = request('cmd', CMD_LIST);
$strOutput  = "";

//*** Take care of any login requests.
require_once('includes/inc.login.php');

//*** Find the first possible category Id if none given.
if (empty($intCatId)
		|| !isset($_CONF['app']['navRights'][$intCatId])
		|| !$objLiveUser->checkRight($_CONF['app']['navRights'][$intCatId])) {

	foreach ($_CONF['app']['navRights'] as $key => $value) {
		if ($objLiveUser->checkRight($value) == TRUE) {
			$intCatId = $key;
			break;
		}
	}
}

//*** Check if the category is a top level product id.
if ($productKey = array_search($intCatId, $_CONF['app']['msMypunch']['product'])) {
	foreach($_CONF['app']['ms' . ucfirst($productKey)] as $key => $value) {
		if ($objLiveUser->checkRight($_CONF['app']['navRights'][$value]) == TRUE) {
			$intCatId = $value;
			break;
		}
	}
}

//*** Handle data manipulation request.
switch ($strCommand) {
	case CMD_SORT:
		switch ($intCatId) {
			case NAV_PCMS_ELEMENTS:
				Elements::sortChildren($intElmntId);
				$strCommand = CMD_LIST;
				break;

			case NAV_PCMS_TEMPLATES:
				Templates::sortChildren($intElmntId);
				$strCommand = CMD_LIST;
				break;

			case NAV_PCMS_LANGUAGES:
				ContentLanguage::sort($intElmntId);
				$strCommand = CMD_LIST;
				break;

			case NAV_PCMS_STORAGE:
				StorageItems::sortChildren($intElmntId);
				$strCommand = CMD_LIST;
				break;
		}
		break;
}

//*** Load the Template Parse methods.
require_once('includes/inc.tplparse_head.php');
require_once('includes/inc.tplparse_foot.php');

//*** Parse the HTML Header.
$strOutput .= parseHeader($intCatId, $strCommand, $intElmntId);

//*** Route to the correct HTML Body Parser.
switch ($intCatId) {
	case NAV_MYPUNCH_LOGIN:
		if ($_CONF['app']['secureLogin']) {
			header("Location: " . Request::getURI("https") . "/?cid=" . NAV_MYPUNCH_LOGIN);
			exit;
		} else {
			require_once('inc.tplparse_login.php');
			$strOutput .= parseLogin($intElmntId, $strCommand);
		}
		break;

	case NAV_MYPUNCH_NOACCOUNT:
		require_once('includes/inc.tplparse_noaccount.php');
		$strOutput .= parsePage($intElmntId, $strCommand);
		break;

	case NAV_MYPUNCH_USERS:
		require_once('includes/inc.tplparse_user.php');
		if ($intElmntId == 0) $intElmntId = NAV_MYPUNCH_USERS_USER;
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseUsers($intElmntId, $strCommand);
		break;

	case NAV_MYPUNCH_PROFILE:
		require_once('includes/inc.tplparse_profile.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseProfile($intElmntId, $strCommand);
		break;

	case NAV_MYPUNCH_ANNOUNCEMENTS:
		require_once('includes/inc.tplparse_announcments.php');
		$strOutput .= parseAnnouncment();
		break;

	case NAV_PCMS_TEMPLATES:
		require_once('includes/inc.tplparse_template.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseTemplates($intElmntId, $strCommand);
		break;

	case NAV_PCMS_FORMS:
		require_once('includes/inc.tplparse_form.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseForms($intElmntId, $strCommand);
		break;

	case NAV_PCMS_STORAGE:
		require_once('includes/inc.tplparse_storage.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseFiles($intElmntId, $strCommand);
		break;

	case NAV_PCMS_ALIASES:
		require_once('includes/inc.tplparse_alias.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseAlias($intElmntId, $strCommand);
		break;

	case NAV_PCMS_FEEDS:
		require_once('includes/inc.tplparse_feeds.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseFeeds($intElmntId, $strCommand);
		break;

	case NAV_PCMS_SETTINGS:
		require_once('includes/inc.tplparse_setting.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseSetting($intElmntId, $strCommand);
		break;

	case NAV_PCMS_LANGUAGES:
		require_once('includes/inc.tplparse_language.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseLanguage($intElmntId, $strCommand);
		break;

	case NAV_PCMS_HELP:
		require_once('includes/inc.tplparse_help.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseHelp($intElmntId, $strCommand);
		break;

	case NAV_PCMS_SEARCH:
		require_once('includes/inc.tplparse_search.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseSearch($intElmntId, $strCommand);
		break;

	case NAV_PCMS_ELEMENTS:
		require_once('includes/inc.tplparse_element.php');
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parsePages($intElmntId, $strCommand);
		break;

	default:
		$strOutput .= parseMenu($intCatId, $strCommand);
		$strOutput .= parseUndefined($intCatId, $strCommand);

}

//*** Parse the HTML Footer.
$strOutput .= parseFooter();

echo $strOutput;

//echo "<br /><br />Execution time: " . (microtime(TRUE) - $start);

?>

