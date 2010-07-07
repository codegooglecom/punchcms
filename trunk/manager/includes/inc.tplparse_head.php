<?php

function parseHeader($intCatId, $strCommand, $intElmntId) {
	global $_PATHS,
			$_CONF,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);

	switch ($intCatId) {
		case NAV_MYPUNCH_LOGIN:
		case NAV_MYPUNCH_NOACCOUNT:
			$objTpl->loadTemplatefile("header-login.tpl.htm");
			break;

		default:
			$objTpl->loadTemplatefile("header.tpl.htm");
			break;

	}

	$objTpl->setVariable("TITLE", htmlentities($_CONF['app']['pageTitle']));
	$objTpl->setVariable("GENERATOR", htmlentities(APP_NAME));
	$objTpl->setVariable("REVISION", htmlentities(APP_VERSION));
	
	switch ($intCatId) {
		case NAV_PCMS_ELEMENTS:
			$objTpl->touchBlock("tree");
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("tooltip");
			$objTpl->touchBlock("cms.elements");
			
			switch ($strCommand) {
				case CMD_ADD:
				case CMD_ADD_DYNAMIC:
				case CMD_EDIT:
					$objTpl->touchBlock("calendar");
					$objTpl->touchBlock("cms.languages");
					break;
			}
			break;
			
		case NAV_PCMS_TEMPLATES:
			$objTpl->touchBlock("tree");
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("tooltip");
			$objTpl->touchBlock("cms.templates");
			switch ($strCommand) {
				case CMD_ADD_STRUCTURE:
				case CMD_ADD_STRUCTURE_DETAIL:
					$objTpl->touchBlock("cms.aliases");
			}
			break;
			
		case NAV_PCMS_STORAGE:
			$objTpl->touchBlock("tree");
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("tooltip");
			$objTpl->touchBlock("cms.storage");
			break;
			
		case NAV_PCMS_ALIASES:
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("cms.aliases");
			break;
			
		case NAV_PCMS_FEEDS:
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("cms.feeds");
			break;
			
		case NAV_PCMS_LANGUAGES:
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("cms.languages");
			break;
			
		case NAV_MYPUNCH_USERS:
			$objTpl->touchBlock("tree");
			$objTpl->touchBlock("animation");
			$objTpl->touchBlock("cms.users");
			break;
	}
	
	if (AnnounceMessage::getMessages(FALSE)->count() > 0 && $objLiveUser->checkRight(MYPUNCH_ANNOUNCEMENTS_VIEW)) {
		$objTpl->touchBlock("lightbox");
	}
	
	$objLang = (isset($_SESSION["objLang"])) ? unserialize($_SESSION["objLang"]) : NULL;
	$strLang = (!is_null($objLang)) ? strtolower($objLang->get("abbr")) : "en";
	$objTpl->setVariable("DATEPICKER_LANG", $strLang);

	$strReturn = $objTpl->get();

	$strReturn .= parseScriptHeader($intCatId, $strCommand, $intElmntId);

	return $strReturn;
}

function parseScriptHeader($intCatId, $strCommand, $intElmntId) {
	global $_PATHS,
			$objLang,
			$objLiveUser;

	$strScript = "";
	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("formheader.tpl.htm");

	switch ($intCatId) {
		case NAV_MYPUNCH_LOGIN:
			//*** Don't use the FormObject. The login screen has it's own errorcheck.
			$strScript = "jQuery(function(){ jQuery('#handle').focus(); });";
			break;

		case NAV_PCMS_ELEMENTS:
			switch ($strCommand) {
				case CMD_LIST:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					break;

				case CMD_ADD:
				case CMD_EDIT:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					//*** Form fields.
					$strScript .= "var objForm = new ValidForm('elementForm');\n";
					$strScript .= "objForm.addElement('frm_active', LIBFRM_STRING);\n";
					$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
					$strScript .= "objForm.addElement('frm_apiname', LIBFRM_WORD);\n";
					$strScript .= "objForm.addElement('frm_template', LIBFRM_INTEGER);\n";

					//*** Element specific fields.
					//*** TODO!!!

					//*** Form errors.
					$strScript .= "var objAlerts = new Object();\n";
					$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_active'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("elementName", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_apiname'] = '" . $objLang->get("commonTypeWord", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_template'] = '" . $objLang->get("commonRequired", "formerror") . "';\n";
					$strScript .= "objForm.alerts = objAlerts;\n";

					//*** Element specific errors.
					//*** TODO!!!

					//*** Add form to the Forms object.
					$strScript .= "objValidForms.addForm(objForm);\n";

					break;

			}

			break;

		case NAV_PCMS_TEMPLATES:
			switch ($strCommand) {
				case CMD_LIST:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					break;

				case CMD_ADD:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					//*** Form fields.
					$strScript .= "var objForm = new ValidForm('templateForm');\n";
					$strScript .= "objForm.addElement('frm_ispage', LIBFRM_STRING);\n";
					$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
					$strScript .= "objForm.addElement('frm_apiname', LIBFRM_WORD);\n";
					$strScript .= "objForm.addElement('frm_description', LIBFRM_TEXT);\n";

					//*** Form errors.
					$strScript .= "var objAlerts = new Object();\n";
					$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_ispage'] = '" . $objLang->get("isPage", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("templateName", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_apiname'] = '" . $objLang->get("commonTypeWord", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_description'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
					$strScript .= "objForm.alerts = objAlerts;\n";

					//*** Add form to the Forms object.
					$strScript .= "objValidForms.addForm(objForm);\n";

					break;

				case CMD_ADD_FIELD:
				case CMD_EDIT_FIELD:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					//*** Form fields.
					$strScript .= "var objForm = new ValidForm('templateFieldForm');\n";
					$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
					$strScript .= "objForm.addElement('frm_apiname', LIBFRM_WORD);\n";
					$strScript .= "objForm.addElement('frm_description', LIBFRM_TEXT);\n";
					$strScript .= "objForm.addElement('frm_field_type', LIBFRM_STRING, true);\n";

					//*** Form errors.
					$strScript .= "var objAlerts = new Object();\n";
					$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("fieldName", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_apiname'] = '" . $objLang->get("commonTypeWord", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_description'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_field_type'] = '" . $objLang->get("fieldType", "formerror") . "';\n";
					$strScript .= "objForm.alerts = objAlerts;\n";

					//*** Add form to the Forms object.
					$strScript .= "objValidForms.addForm(objForm);\n";

					break;
			}
			break;

		case NAV_PCMS_FORMS:
			switch ($strCommand) {
				case CMD_LIST:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					break;

				case CMD_ADD:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					//*** Form fields.
					$strScript .= "var objForm = new ValidForm('formForm');\n";
					$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
					$strScript .= "objForm.addElement('frm_apiname', LIBFRM_WORD);\n";
					$strScript .= "objForm.addElement('frm_description', LIBFRM_TEXT);\n";

					//*** Form errors.
					$strScript .= "var objAlerts = new Object();\n";
					$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("formName", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_apiname'] = '" . $objLang->get("commonTypeWord", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_description'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
					$strScript .= "objForm.alerts = objAlerts;\n";

					//*** Add form to the Forms object.
					$strScript .= "objValidForms.addForm(objForm);\n";

					break;

				case CMD_ADD_FIELD:
				case CMD_EDIT_FIELD:
					//*** Form objects.
					$strScript .= "var objValidForms = new ValidForms();\n";

					//*** Form fields.
					$strScript .= "var objForm = new ValidForm('templateFieldForm');\n";
					$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
					$strScript .= "objForm.addElement('frm_apiname', LIBFRM_WORD);\n";
					$strScript .= "objForm.addElement('frm_description', LIBFRM_TEXT);\n";
					$strScript .= "objForm.addElement('frm_field_type', LIBFRM_STRING, true);\n";

					//*** Form errors.
					$strScript .= "var objAlerts = new Object();\n";
					$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("fieldName", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_apiname'] = '" . $objLang->get("commonTypeWord", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_description'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
					$strScript .= "objAlerts['frm_field_type'] = '" . $objLang->get("fieldType", "formerror") . "';\n";
					$strScript .= "objForm.alerts = objAlerts;\n";

					//*** Add form to the Forms object.
					$strScript .= "objValidForms.addForm(objForm);\n";

					break;
			}
			break;

		case NAV_MYPUNCH_PROFILE:
			//*** Form objects.
			$strScript .= "var objValidForms = new ValidForms();\n";

			//*** Form fields.
			$strScript .= "var objForm = new ValidForm('settingsProfileForm');\n";
			$strScript .= "objForm.addElement('frm_name', LIBFRM_STRING, true);\n";
			$strScript .= "objForm.addElement('frm_email', LIBFRM_EMAIL, true);\n";
			$strScript .= "objForm.addElement('frm_language', LIBFRM_WORD, true);\n";

			//*** Form errors.
			$strScript .= "var objAlerts = new Object();\n";
			$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_name'] = '" . $objLang->get("profileName", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_email'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_language'] = '" . $objLang->get("commonTypeText", "formerror") . "';\n";
			$strScript .= "objForm.alerts = objAlerts;\n";

			//*** Add form to the Forms object.
			$strScript .= "objValidForms.addForm(objForm);\n";

			//*** Form fields.
			$strScript .= "var objForm = new ValidForm('settingsPasswordForm');\n";
			$strScript .= "objForm.addElement('frm_currentpass', LIBFRM_PASSWORD, true);\n";
			$strScript .= "objForm.addElement('frm_newpass', LIBFRM_PASSWORD, true);\n";
			$strScript .= "objForm.addElement('frm_verifypass', LIBFRM_PASSWORD, true);\n";

			//*** Form errors.
			$strScript .= "var objAlerts = new Object();\n";
			$strScript .= "objAlerts.mainAlert = '" . $objLang->get("main", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_currentpass'] = '" . $objLang->get("commonTypePassword", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_newpass'] = '" . $objLang->get("commonTypePassword", "formerror") . "';\n";
			$strScript .= "objAlerts['frm_verifypass'] = '" . $objLang->get("commonTypePassword", "formerror") . "';\n";
			$strScript .= "objForm.alerts = objAlerts;\n";

			//*** Add form to the Forms object.
			$strScript .= "objValidForms.addForm(objForm);\n";

			break;

		default:
			$strScript .= "";
			break;
	}

	//*** Tree scripts.
	$strScript .= "\n";
	$intSelectedTab = 0;
	switch ($intCatId) {
		case NAV_PCMS_TEMPLATES:
			if ($strCommand == CMD_EDIT_FIELD) {
				$strScript .= Tree::treeRender("templatefields", $intElmntId);
			} else {
				$strScript .= Tree::treeRender("templates", $intElmntId);
			}
			break;

		case NAV_PCMS_ELEMENTS:
			$strScript .= Tree::treeRender("elements", $intElmntId);
			if ($strCommand == CMD_EDIT) $intSelectedTab = 1;
			break;

		case NAV_MYPUNCH_USERS:
			$strScript .= Tree::treeRender("users", $intElmntId);
			break;

		case NAV_PCMS_FORMS:
			$strScript .= Tree::treeRender("forms", $intElmntId);
			break;

		case NAV_PCMS_STORAGE:
			$strScript .= Tree::treeRender("storage", $intElmntId);
			break;
	}

	//*** Announcement script.
	$strScript .= "function loadAnnouncement() {";
	if (AnnounceMessage::getMessages(FALSE)->count() > 0 && $objLiveUser->checkRight(MYPUNCH_ANNOUNCEMENTS_VIEW)) {
		$strScript .= "objLightbox = new lightbox('index.php?cid=24');";
		$strScript .= "objLightbox.activate();";
	}
	$strScript .= "}";

	$objTpl->setVariable("SELECTED_TAB", $intSelectedTab);
	$objTpl->setVariable("SCRIPT", $strScript);
	
	return $objTpl->get();
}

function parseMenu($intCatId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CONF,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("menu.tpl.htm");

	//*** Parse the header links.
	$objTpl->setVariable("LOGGEDIN", $objLang->get("loggedInAs", "head"));
	$objTpl->setVariable("LOGOUT", $objLang->get("logout", "head"));
	$objTpl->setVariable("COMPANY_NAME", htmlentities($_CONF['app']['account']->getName()));
	$objTpl->setVariable("USER_NAME", $objLiveUser->getProperty('name'));

	if ($objLiveUser->checkRight(MYPUNCH_PROFILE_VIEW)) {
		$objTpl->setVariable("PROFILE_LINK", "href=\"?cid=" . NAV_MYPUNCH_PROFILE . "\"");
	}

	//*** Parse the main menu buttons.
	foreach ($_CONF['app']['msMypunch'] as $key => $value) {
		if (is_array($value)) {
			//*** Nested MyPunch menu items.
			foreach ($value as $subKey => $subValue) {
				if ($objLiveUser->checkRight($_CONF['app']['navRights'][$subValue]) == TRUE
						&& $_CONF['app']['account']->hasProduct(constant('PRODUCT_' . strtoupper($subKey))) == TRUE) {

					$objTpl->setCurrentBlock("mypunch.{$key}");
					$objTpl->setVariable("LABEL_MYPUNCH_" . strtoupper($key), $objLang->get($subKey, "menu"));
					$objTpl->setVariable("CID_MYPUNCH_" . strtoupper($key), $subValue);
					if ($intCatId == $subValue || in_array($intCatId, $_CONF['app']['ms' . ucfirst($subKey)])) {
						//*** Render product sub menu.
						foreach ($_CONF['app']['ms' . ucfirst($subKey)] as $productKey => $productValue) {
							if ($objLiveUser->checkRight($_CONF['app']['navRights'][$productValue]) == TRUE) {
								$objTpl->setVariable("LABEL_" . strtoupper($subKey) . "_" . strtoupper($productKey), $objLang->get($subKey . ucfirst($productKey), "menu"));
								$objTpl->setVariable("CID_" . strtoupper($subKey) . "_" . strtoupper($productKey), $productValue);

								//*** Activate sub button.
								if ($intCatId == $productValue) {
									$objTpl->setVariable("ACTIVE_" . strtoupper($subKey) . "_" . strtoupper($productKey), "class=\"active\"");
								}
							}
						}

						//*** Activate main button.
						$objTpl->setVariable("ACTIVE_MYPUNCH_" . strtoupper($key), "class=\"active\"");
					}
					$objTpl->parseCurrentBlock();
				}
			}
		} else {
			if ($objLiveUser->checkRight($_CONF['app']['navRights'][$value]) == TRUE) {
				//*** Plain MyPunch menu items.
				$objTpl->setVariable("LABEL_MYPUNCH_" . strtoupper($key), $objLang->get("mypunch" . ucfirst($key), "menu"));
				$objTpl->setVariable("CID_MYPUNCH_" . strtoupper($key), $value);

				//*** Activate menu item.
				if ($intCatId == $value) {
					$objTpl->setVariable("ACTIVE_MYPUNCH_" . strtoupper($key), "class=\"active\"");

					//*** Render MyPunch tab button.
					$objTpl->setVariable("LABEL_MYPUNCHSUB_" . strtoupper($key), $objLang->get("mypunch" . ucfirst($key), "menu"));
					$objTpl->setVariable("CID_MYPUNCHSUB_" . strtoupper($key), $value);
					$objTpl->setVariable("ACTIVE_MYPUNCHSUB_" . strtoupper($key), "class=\"active\"");
				}
			}
		}
	}

	return $objTpl->get();
}

function parseUndefined() {
	global $_PATHS,
			$objLang;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("404.tpl.htm");

	$objTpl->setVariable("HEADER", $objLang->get("undefinedHeader", "alert"));
	$objTpl->setVariable("BODY", $objLang->get("undefinedBody", "alert"));

	return $objTpl->get();
}

?>