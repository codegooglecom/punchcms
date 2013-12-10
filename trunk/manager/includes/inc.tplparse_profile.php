<?php

function parseProfile($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CLEAN_POST,
			$_CONF,
			$objLiveAdmin,
			$objLiveUser;

	//*** Retrieve the latest user info from the DB.
	$objLiveUser->updateProperty(true, false, $_CONF['app']['account']->getId());

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("profile.tpl.htm");

	$objTpl->setVariable("TITLE", $objLang->get("pageTitle"));

	//*** Render timezones.
	$objTimeZones = Timezone::select();
	//echo $objLiveUser->getProperty("time_zone_id");
	foreach ($objTimeZones as $objTimeZone) {
		$objTpl->setCurrentBlock("timezone-option");
		if ($objLiveUser->getProperty("time_zone_id") == $objTimeZone->getId()) {
			$objTpl->setVariable("TIMEZONE_SELECTED", " selected=\"selected\"");
		}
		$objTpl->setVariable("TIMEZONE_VALUE", $objTimeZone->getId());
		$objTpl->setVariable("TIMEZONE_TEXT", $objTimeZone->getLongName());
		$objTpl->parseCurrentBlock();
	}

	//*** Render languages.
	$objLanguages = $objLang->getLangs();
	foreach ($objLanguages as $objLanguage) {
		$objTpl->setCurrentBlock("language-option");
		if ($objLang->name == $objLanguage->name) {
			$objTpl->setVariable("LANGUAGE_SELECTED", " selected=\"selected\"");
		}
		$objTpl->setVariable("LANGUAGE_VALUE", $objLanguage->name);
		$objTpl->setVariable("LANGUAGE_TEXT", $objLanguage->language);
		$objTpl->parseCurrentBlock();
	}

	$objTpl->setCurrentBlock("profile");

	//*** Post the profile form if submitted.
	if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editProfile") {
		//*** The element form has been posted.
		$blnError = false;

		//*** Check sanitized input.
		if (is_null($_CLEAN_POST["frm_name"])) {
			$objTpl->setVariable("ERROR_NAME_ON", " error");
			$objTpl->setVariable("ERROR_NAME", $objLang->get("profileName", "formerror"));
			$blnError = true;
		}

		if (is_null($_CLEAN_POST["frm_email"])) {
			$objTpl->setVariable("ERROR_EMAIL_ON", " error");
			$objTpl->setVariable("ERROR_EMAIL", $objLang->get("commonTypeText", "formerror"));
			$blnError = true;
		}

		if (is_null($_CLEAN_POST["frm_language"])) {
			$objTpl->setVariable("ERROR_LANGUAGE_ON", " error");
			$objTpl->setVariable("ERROR_LANGUAGE", $objLang->get("commonTypeText", "formerror"));
			$blnError = true;
		}

		if (is_null($_CLEAN_POST["frm_timezone"])) {
			$objTpl->setVariable("ERROR_TIMEZONE_ON", " error");
			$objTpl->setVariable("ERROR_TIMEZONE", $objLang->get("commonTypeText", "formerror"));
			$blnError = true;
		}

		if (is_null($_CLEAN_POST["dispatch"])) {
			$blnError = true;
		}

		if ($blnError === true) {
			//*** Display global error.
			$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
			$objTpl->setVariable("FORM_EMAIL_VALUE", $_POST["frm_email"]);
			$objTpl->setVariable("ERROR_PROFILE_MAIN", $objLang->get("main", "formerror"));
		} else {
			//*** Save the user profile.
			$data = array(
						'name' => $_CLEAN_POST["frm_name"],
						'email' => $_CLEAN_POST["frm_email"],
						'time_zone_id' => $_CLEAN_POST["frm_timezone"]
					);
			$objLiveAdmin->updateUser($data, $objLiveUser->getProperty('perm_user_id'));

			//*** Save the language setting.
			$objLang->setLang($_CLEAN_POST["frm_language"]);
			$_SESSION['objLang'] = NULL;

			header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"]);
			exit();
		}
	}

	$objTpl->setVariable("PROFILE", $objLang->get("userprofile", "label"));
	$objTpl->setVariable("LABEL_USERNAME", $objLang->get("username", "form"));
	$objTpl->setVariable("FORM_USERNAME_VALUE", $objLiveUser->getProperty('handle'));
	$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
	$objTpl->setVariable("FORM_NAME_VALUE", $objLiveUser->getProperty('name'));
	$objTpl->setVariable("LABEL_EMAIL", $objLang->get("emailaddress", "form"));
	$objTpl->setVariable("FORM_EMAIL_VALUE", $objLiveUser->getProperty('email'));
	$objTpl->setVariable("LABEL_LANGUAGE", $objLang->get("language", "form"));
	$objTpl->setVariable("LABEL_TIMEZONE", $objLang->get("timezone", "form"));
	$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
	$objTpl->setVariable("CID", NAV_MYPUNCH_PROFILE);
	$objTpl->setVariable("CMD", $strCommand);
	$objTpl->setVariable("EID", $intElmntId);
	$objTpl->parseCurrentBlock();


	$objTpl->setCurrentBlock("password");

	//*** Post the password form if submitted.
	if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editPass") {
		//*** The element form has been posted.
		$blnError = false;

		//*** Check sanitized input.
		if (is_null($_CLEAN_POST["frm_currentpass"])) {
			$objTpl->setVariable("ERROR_CURRENTPASS_ON", " error");
			$objTpl->setVariable("ERROR_CURRENTPASS", $objLang->get("commonTypePassword", "formerror"));
			$blnError = true;
		}

		if (!is_null($_CLEAN_POST["frm_currentpass"])) {
			if (sha1($_CLEAN_POST["frm_currentpass"]) !== $objLiveUser->getProperty('passwd')) {
				$objTpl->setVariable("ERROR_CURRENTPASS_ON", " error");
				$objTpl->setVariable("ERROR_CURRENTPASS", $objLang->get("wrongPassword", "formerror"));
				$blnError = true;
			}
		}

		if (is_null($_CLEAN_POST["frm_newpass"])) {
			$objTpl->setVariable("ERROR_NEWPASS_ON", " error");
			$objTpl->setVariable("ERROR_NEWPASS", $objLang->get("commonTypePassword", "formerror"));
			$blnError = true;
		}

		if (!is_null($_CLEAN_POST["frm_newpass"]) && strlen($_CLEAN_POST["frm_newpass"]) < $_CONF['app']['minPassLength']) {
			$objTpl->setVariable("ERROR_NEWPASS_ON", " error");
			$objTpl->setVariable("ERROR_NEWPASS", $objLang->get("shortPassword", "formerror"));
			$blnError = true;
		}

		if (is_null($_CLEAN_POST["frm_verifypass"])) {
			$objTpl->setVariable("ERROR_VERIFYPASS_ON", " error");
			$objTpl->setVariable("ERROR_VERIFYPASS", $objLang->get("commonTypePassword", "formerror"));
			$blnError = true;
		}

		if (!is_null($_CLEAN_POST["frm_newpass"]) && !is_null($_CLEAN_POST["frm_verifypass"])) {
			if ($_CLEAN_POST["frm_newpass"] !== $_CLEAN_POST["frm_verifypass"]) {
				$objTpl->setVariable("ERROR_VERIFYPASS_ON", " error");
				$objTpl->setVariable("ERROR_VERIFYPASS", $objLang->get("passwordNotMatch", "formerror"));
				$blnError = true;
			}
		}

		if (is_null($_CLEAN_POST["dispatch"])) {
			$blnError = true;
		}

		if ($blnError === true) {
			//*** Display global error.
			$objTpl->setVariable("ERROR_PASSWORD_MAIN", $objLang->get("main", "formerror"));
		} else {
			//*** Save the password.
			$data = array('passwd' => $_CLEAN_POST["frm_newpass"]);
			$objLiveAdmin->updateUser($data, $objLiveUser->getProperty('perm_user_id'));

			header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"]);
			exit();
		}
	}

	$objTpl->setVariable("PASSWORD", $objLang->get("password", "label"));
	$objTpl->setVariable("LABEL_CURRENTPASS", $objLang->get("currentpassword", "form"));
	$objTpl->setVariable("LABEL_NEWPASS", $objLang->get("newpassword", "form"));
	$objTpl->setVariable("NEWPASS_NOTE", sprintf($objLang->get("newpasswordNote", "tip"), $_CONF['app']['minPassLength']));
	$objTpl->setVariable("LABEL_VERIFYPASS", $objLang->get("verifypassword", "form"));
	$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
	$objTpl->setVariable("CID", NAV_MYPUNCH_PROFILE);
	$objTpl->setVariable("CMD", $strCommand);
	$objTpl->setVariable("EID", $intElmntId);
	$objTpl->parseCurrentBlock();

	$strReturn = $objTpl->get();

	return $strReturn;
}

?>