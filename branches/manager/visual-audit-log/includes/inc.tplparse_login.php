<?php

function parseLogin($intElmntId, $strCommand) {
	global $_PATHS,
			$_CONF,
			$objLang,
			$objLiveAdmin;

	$strUser = request('handle');

	//*** Set user language.
	$objLang->setLang(request('ilanguage'));
	unset($_SESSION['objLang']);

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("login.tpl.htm");

	switch ($strCommand) {
		case CMD_PASSREMIND:
			if (!empty($strUser)) {
				//*** Mail a new password to the user.
				if (User::remindPassword($strUser) === TRUE) {
					//*** Mail was send successfully.
					$objTpl->setCurrentBlock("info");
					$objTpl->setVariable("TEXT", $objLang->get("infoReminderSend", "login"));
					$objTpl->parseCurrentBlock();
				} else {
					//*** An error occured.
					$objTpl->setCurrentBlock("error");
					$objTpl->setVariable("TEXT", $objLang->get("errorReminderSend", "login"));
					$objTpl->parseCurrentBlock();

					$objTpl->setVariable("BACK_LINK", Request::getRootUri());
					$objTpl->setVariable("BACK_BUTTON", $objLang->get("labelButtonCancel", "login"));
					$objTpl->setVariable("USERNAME", $objLang->get("labelUser", "login"));
					$objTpl->setVariable("BUTTON", $objLang->get("labelButtonReminder", "login"));
				}
			} else {
				$objTpl->setCurrentBlock("info");
				$objTpl->setVariable("TEXT", $objLang->get("infoReminder", "login"));
				$objTpl->parseCurrentBlock();

				$objTpl->setVariable("BACK_LINK", Request::getURI());
				$objTpl->setVariable("BACK_BUTTON", $objLang->get("labelButtonCancel", "login"));
				$objTpl->setVariable("USERNAME", $objLang->get("labelUser", "login"));
				$objTpl->setVariable("BUTTON", $objLang->get("labelButtonReminder", "login"));
			}

			break;

		default:
			if (!empty($strUser)) {
				//*** The login form was submitted, but the login failed.
				$objTpl->setCurrentBlock("error");
				$objTpl->setVariable("TEXT", $objLang->get("errorMain", "login"));
				$objTpl->parseCurrentBlock();
			}

			$objTpl->setVariable("USERNAME", $objLang->get("labelUser", "login"));
			$objTpl->setVariable("PASSWORD", $objLang->get("labelPassword", "login"));
			$objTpl->setVariable("LANGUAGE", $objLang->get("labelLanguage", "login"));
			$objTpl->setVariable("FORGOT_PASS", $objLang->get("labelForgotPassword", "login"));
			$objTpl->setVariable("REMEMBER_ME", $objLang->get("labelRememberMe", "login"));
			$objTpl->setVariable("BUTTON", $objLang->get("labelButtonLogin", "login"));
			$objTpl->setVariable("USERNAME_VALUE", $strUser);

			$objLanguages = $objLang->getLangs();
			foreach ($objLanguages as $objLanguage) {
				$objTpl->setCurrentBlock("language_item");
				if ($objLang->name == $objLanguage->name) {
					$objTpl->setVariable("LANGUAGE_SELECTED", " selected=\"selected\"");
				}
				$objTpl->setVariable("LANGUAGE_VALUE", $objLanguage->name);
				$objTpl->setVariable("LANGUAGE_TEXT", $objLanguage->language);
				$objTpl->parseCurrentBlock();
			}
	}
	
	if (is_object($_CONF['app']['account'])) {
		$objTpl->setVariable("CLIENT_NAME", $_CONF['app']['account']->getName());
	} else {
		$objTpl->setVariable("CLIENT_NAME", $objLang->get("invalidAccount", "login"));
	}
	$objTpl->setVariable("POWERED_BY", $objLang->get("poweredBy", "label"));
	$objTpl->setVariable("CID", NAV_MYPUNCH_LOGIN);
	$objTpl->setVariable("CMD", $strCommand);

	return $objTpl->get();
}

?>