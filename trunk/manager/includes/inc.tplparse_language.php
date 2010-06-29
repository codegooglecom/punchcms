<?php

function parseLanguage($intLangId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CLEAN_POST,
			$_CONF,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("language.tpl.htm");

	switch ($strCommand) {
		case CMD_LIST:			
		case CMD_ADD:
		case CMD_EDIT:
			//*** Post the profile form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editLanguage") {
				//*** The element form has been posted.
				$blnError = FALSE;
				
				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_active"])) {
					$blnError = TRUE;
				}
				
				if (is_null($_CLEAN_POST["frm_name"])) {
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_apiname"])) {
					$blnError = TRUE;
				}
				
				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = TRUE;
				}

				if ($blnError === TRUE) {
					//*** Display global error.
					$objTpl->setVariable("FORM_ACTIVE_VALUE", ($_POST["frm_active"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_APINAME_VALUE", $_POST["frm_apiname"]);
					$objTpl->setVariable("ERROR_LANGUAGE_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the language.
					if ($strCommand == CMD_EDIT) {
						$objLanguage = ContentLanguage::selectByPK($intLangId);
					} else {
						$objLanguage = new ContentLanguage();
					}
					
					$objLanguage->setAccountId($_CONF['app']['account']->getId());
					$objLanguage->setActive(($_CLEAN_POST["frm_active"] == "on") ? 1 : 0);
					$objLanguage->setName($_CLEAN_POST["frm_name"]);
					$objLanguage->setAbbr($_CLEAN_POST["frm_apiname"]);
					$objLanguage->save();
				
					header("Location: " . Request::getURI() . "/?cid=" . NAV_PCMS_LANGUAGES);
					exit();
				}
			}			
			
			$objLangs = ContentLanguage::select();
			foreach ($objLangs as $objLanguage) {
				$objTpl->setCurrentBlock("multiview-item");
				$objTpl->setVariable("MULTIITEM_VALUE", $objLanguage->getId());
				$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:ContentLanguage.remove({$objLanguage->getId()});");
				$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
				$objTpl->setVariable("MULTIITEM_HREF", "?cid=" . NAV_PCMS_LANGUAGES . "&amp;eid={$objLanguage->getId()}&amp;cmd=" . CMD_EDIT);
				
				$strValue = htmlspecialchars($objLanguage->getName());
				$strShortValue = getShortValue($strValue, 50);
				$intSize = strlen($strValue);
				$objTpl->setVariable("MULTIITEM_NAME", ($intSize > 50) ? $strShortValue : $strValue);
				$objTpl->setVariable("MULTIITEM_TITLE", ($intSize > 50) ? $strValue : "");
				
				$objTpl->setVariable("MULTIITEM_ABBR", $objLanguage->getAbbr());
				if ($objLanguage->default > 0) {
					$strValue = $objLang->get("standardLanguage", "label");
				} else {
					$strValue = "<a href=\"javascript:;\" onclick=\"ContentLanguage.setDefault({$objLanguage->getId()})\" rel=\"internal\">" . $objLang->get("standardLanguage", "button") . "</a>";
				}
				$objTpl->setVariable("MULTIITEM_META", $strValue);
				if (!$objLanguage->getActive()) $objTpl->setVariable("MULTIITEM_ACTIVE", " class=\"inactive\"");
				$objTpl->parseCurrentBlock();
			}
			
			$objTpl->setVariable("LANGUAGES", $objLang->get("languages", "label"));
			$objTpl->setVariable("BUTTON_ADD", $objLang->get("languageAdd", "button"));
			$objTpl->setVariable("BUTTON_ADD_HREF", "ContentLanguage.prepareAdd()");
			
			//*** Form variables.
			if ($strCommand == CMD_EDIT) {
				$objLanguage = ContentLanguage::selectByPK($intLangId);
				$objTpl->setVariable("FORM_ACTIVE_VALUE", ($objLanguage->getActive()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objLanguage->getName());
				$objTpl->setVariable("FORM_APINAME_VALUE", $objLanguage->getAbbr());
				$objTpl->setVariable("FRM_HEADER", $objLang->get("editLanguage", "form"));
				$objTpl->setVariable("FRM_STYLE", "");
				$objTpl->setVariable("CMD", CMD_EDIT);
			} else {
				$objTpl->setVariable("FRM_HEADER", $objLang->get("addLanguage", "form"));
				$objTpl->setVariable("FRM_STYLE", " style=\"display:none\"");
				$objTpl->setVariable("CMD", CMD_ADD);
			}
			$objTpl->setVariable("FRM_LABEL_ACTIVE", $objLang->get("active", "form"));
			$objTpl->setVariable("FRM_LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("FRM_LABEL_ABBR", $objLang->get("shortName", "form"));
			$objTpl->setVariable("FRM_DESCR_ABBR", $objLang->get("shortName", "tip"));
			$objTpl->setVariable("FRM_LABEL_SAVE", $objLang->get("save", "button"));
			
			$objTpl->setVariable("CID", NAV_PCMS_LANGUAGES);
			$objTpl->setVariable("EID", $intLangId);
			$objTpl->parseCurrentBlock();

			$strReturn = $objTpl->get();
			
			break;

		case CMD_REMOVE:
			if (strpos($intLangId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrLanguages = explode(',', $intLangId);
				$objLanguages = ContentLanguage::selectByPK($arrLanguages);

				foreach ($objLanguages as $objLanguage) {
					$objLanguage->delete();
				}
			} else {
				//*** Single element submitted.
				$objLanguage = ContentLanguage::selectByPK($intLangId);
				$objLanguage->delete();
			}

			//*** Redirect the page.
			$strReturnTo = request('returnTo');
			if (empty($strReturnTo)) {
				header("Location: " . Request::getUri() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST);
				exit();
			} else {
				header("Location: " . Request::getURI() . $strReturnTo);
				exit();
			}

			break;

		case CMD_SET_DEFAULT:
			if ($intLangId > 0) {
				ContentLanguage::setDefault($intLangId);
			}

			//*** Redirect the page.
			$strReturnTo = request('returnTo');
			if (empty($strReturnTo)) {
				header("Location: " . Request::getUri() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST);
				exit();
			} else {
				header("Location: " . Request::getURI() . $strReturnTo);
				exit();
			}
			
			break;
	}

	return $strReturn;
}

?>