<?php

function parseSetting($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CLEAN_POST,
			$_CONF,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("setting.tpl.htm");

	//*** Post the profile form if submitted.
	if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editSettings") {
		//*** The element form has been posted.
		$blnError = FALSE;

		//*** Check sanitized input.
		if (is_null($_CLEAN_POST["dispatch"])) {
			$blnError = TRUE;
		}

		if ($blnError === TRUE) {
			//*** Display global error.
			$objTpl->setVariable("ERROR_SETTINGS_MAIN", $objLang->get("main", "formerror"));
		} else {
			//*** Remove current settings.
			Setting::clearFields();

			//*** Save the settings.
			foreach ($_REQUEST as $key => $value) {
				if ($value != "" && substr($key, 0, 4) == "sfv_") {
					//*** Get the template Id from the request
					$intTemplateFieldId = substr($key, 4);

					//*** Is the Id really an Id?
					if (is_numeric($intTemplateFieldId)) {
						//*** Save the setting.
						if (!empty($value)) {							
							$objField = new Setting();
							$objField->setAccountId($_CONF['app']['account']->getId());
							$objField->setSettingId($intTemplateFieldId);
							$objField->setUsername($objLiveUser->getProperty('name'));
							
							$objSettingTemplate = SettingTemplate::selectByPk($intTemplateFieldId);
							switch ($objSettingTemplate->getType()) {
								case "text":
								case "number":
								case "password":
									$objField->setValue($value);
									break;
								case "checkbox":
									$objField->setValue(1);
									break;
							}
							
							$objField->save();
						}
					}
				}
			}
			
			//*** Move imported files to the remote server.
			ExImport::moveImportedFiles($_CONF['app']['account']);

			header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"]);
			exit();
		}
	}

	$objTpl->setVariable("SETTINGS", $objLang->get("settings", "label"));

	$objSections = SettingTemplate::select("SELECT DISTINCT section FROM pcms_setting_tpl ORDER BY sort");
	foreach ($objSections as $objSection) {
		//*** Fields.
		$strSql = sprintf("SELECT * FROM pcms_setting_tpl WHERE section = '%s' ORDER BY sort", $objSection->getSection());
		$objSettings = SettingTemplate::select($strSql);
		foreach ($objSettings as $objSetting) {
			$strValue = Setting::getValueByName($objSetting->getName());

			$objTpl->setCurrentBlock("setting.{$objSetting->getType()}");
			$objTpl->setVariable("FIELD_ID", "sfv_{$objSetting->getId()}");
			$objTpl->setVariable("FIELD_LABEL", $objLang->get($objSetting->getName(), "settingsLabel"));
			
			switch ($objSetting->getType()) {
				case "text":
				case "password":
					$objTpl->setVariable("FIELD_VALUE", $strValue);
					$objTpl->setVariable("FIELD_TYPE", $objSetting->getType());
					break;
				case "number":
					$objTpl->setVariable("FIELD_VALUE", $strValue);
					break;
				case "checkbox":
					$strValue = ($strValue) ? "checked=\"checked\"" : "";
					$objTpl->setVariable("FIELD_VALUE", $strValue);
					break;				
			}
			
			$objTpl->parseCurrentBlock();
		}
		
		$objTpl->setCurrentBlock("section");
		$objTpl->setVariable("SECTION", $objSetting->getSection());
		if ($objSections->key() != 0) $objTpl->setVariable("CLASS", " class=\"anchor\"");
		$objTpl->parseCurrentBlock();
		
		//*** Tabs.
		$objTpl->setCurrentBlock("section.tab");
		$objTpl->setVariable("SECTION", $objSection->getSection());
		$objTpl->setVariable("LABEL", $objLang->get("section_{$objSection->getSection()}", "settingsLabel"));
		if ($objSections->key() == 0) $objTpl->setVariable("CLASS", " class=\"on\"");
		$objTpl->parseCurrentBlock();
	}

	$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
	$objTpl->setVariable("CID", NAV_PCMS_SETTINGS);
	$objTpl->setVariable("CMD", $strCommand);
	$objTpl->setVariable("EID", $intElmntId);
	$objTpl->parseCurrentBlock();

	$strReturn = $objTpl->get();

	return $strReturn;
}

?>