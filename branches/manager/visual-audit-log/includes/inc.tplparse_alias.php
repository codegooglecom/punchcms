<?php

function parseAlias($intAliasId, $strCommand) {
	global $_PATHS,
			$_CLEAN_POST,
			$_CONF,
			$objLang,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("alias.tpl.htm");

	$blnError = FALSE;

	switch ($strCommand) {
		case CMD_LIST:
		case CMD_ADD:
		case CMD_EDIT:
			//*** Post the profile form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editAlias") {
				//*** The element form has been posted.

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_active"])) {
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_alias"])) {
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_language"])) {
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_element"])) {
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = TRUE;
				}

				if ($blnError === TRUE) {
					//*** Display global error.
					$objTpl->setVariable("FORM_ACTIVE_VALUE", ($_POST["frm_active"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_ALIAS_VALUE", $_POST["frm_alias"]);
					$objTpl->setVariable("ERROR_ALIAS_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the alias.
					if ($strCommand == CMD_EDIT) {
						$objAlias = Alias::selectByPK($intAliasId);
					} else {
						$objAlias = new Alias();
					}

					$objAlias->setAccountId($_CONF['app']['account']->getId());
					$objAlias->setActive(($_POST["frm_active"] == "on") ? 1 : 0);
					$objAlias->setLanguageId((empty($_CLEAN_POST["frm_language"])) ? 0 : $_CLEAN_POST["frm_language"]);
					$objAlias->setAlias($_CLEAN_POST["frm_alias"]);
					$objAlias->setUrl($_CLEAN_POST["frm_element"]);
					$objAlias->save();

					header("Location: " . Request::getURI() . "/?cid=" . NAV_PCMS_ALIASES);
					exit();
				}
			}

			//*** Initiate child element loop.
			$objAliases = Alias::selectSorted();
			$totalCount = 0;
			$listCount = 0;
			$intPosition = request("pos");
			$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
			$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];

			//*** Find total count.
			foreach ($objAliases as $objAlias) {
				$strAlias = $objAlias->getAlias();
				if (!empty($strAlias)) {
					$totalCount++;
				}
			}

			$objAliases->seek($intPosition);
			$objLanguages = ContentLanguage::select();

			foreach ($objAliases as $objAlias) {
				$strAlias = $objAlias->getAlias();
				if (!empty($strAlias)) {
					$strUrl = $objAlias->getUrl();
					if (is_numeric($strUrl)) {
						$objElement = Element::selectByPk($strUrl);
						if (is_object($objElement)) {
							$strUrlHref = "?eid={$strUrl}&amp;cmd=" . CMD_EDIT . "&amp;cid=" . NAV_PCMS_ELEMENTS;
							$strUrl = Element::recursivePath($strUrl);
						} else {
							$strUrlHref = "?cid=" . NAV_PCMS_ALIASES;
							$strUrl = "<b>" . $objLang->get("aliasUnavailable", "label") . "</b>";
						}
					}

					$objTpl->setCurrentBlock("multiview-item");
					$objTpl->setVariable("MULTIITEM_VALUE", $objAlias->getId());
					$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:Alias.remove({$objAlias->getId()});");
					$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
					$objTpl->setVariable("MULTIITEM_HREF", "?cid=" . NAV_PCMS_ALIASES . "&amp;eid={$objAlias->getId()}&amp;cmd=" . CMD_EDIT);
					$objTpl->setVariable("MULTIITEM_TYPE_CLASS", "alias");
					$objTpl->setVariable("MULTIITEM_ALIAS", $objAlias->getAlias());
					$objTpl->setVariable("MULTIITEM_POINTS_TO", $objLang->get("pointsTo", "label"));
					$objTpl->setVariable("MULTIITEM_URL", $strUrl);
					$objTpl->setVariable("MULTIITEM_URL_HREF", $strUrlHref);
					if ($objLanguages->count() > 1) {
						if ($objAlias->getLanguageId() > 0) {
							$strLanguage = ContentLanguage::selectByPK($objAlias->getLanguageId())->getName();
							$objTpl->setVariable("MULTIITEM_LANGUAGE", sprintf($objLang->get("forLanguage", "label"), $strLanguage));
						} else {
							$objTpl->setVariable("MULTIITEM_LANGUAGE", $objLang->get("forAllLanguages", "label"));
						}
					} else {
						$objTpl->setVariable("MULTIITEM_LANGUAGE", "");
					}
					if (!$objAlias->getActive()) $objTpl->setVariable("MULTIITEM_ACTIVE", " class=\"inactive\"");
					$objTpl->parseCurrentBlock();

					$listCount++;
					if ($listCount >= $_SESSION["listCount"]) break;
				}
			}

			//*** Render page navigation.
			$pageCount = ceil($totalCount / $_SESSION["listCount"]);
			if ($pageCount > 0) {
				$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
				$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
				$nextPos = (($intPosition + $_SESSION["listCount"]) < $totalCount) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

				$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
				$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
				$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_ALIASES . "&amp;pos=$previousPos");
				$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
				$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_ALIASES . "&amp;pos=$nextPos");

				//*** Top page navigation.
				for ($intCount = 0; $intCount < $pageCount; $intCount++) {
					$objTpl->setCurrentBlock("multiview-pagenavitem-top");
					$position = $intCount * $_SESSION["listCount"];
					if ($intCount != $intPosition / $_SESSION["listCount"]) {
						$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_ALIASES . "&amp;pos=$position\"");
					}
					$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
					$objTpl->parseCurrentBlock();
				}

				//*** Bottom page navigation.
				for ($intCount = 0; $intCount < $pageCount; $intCount++) {
					$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
					$position = $intCount * $_SESSION["listCount"];
					if ($intCount != $intPosition / $_SESSION["listCount"]) {
						$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_ALIASES . "&amp;pos=$position\"");
					}
					$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Render list action pulldown.
			$arrActions[$objLang->get("choose", "button")] = 0;
			$arrActions[$objLang->get("delete", "button")] = "delete";
			foreach ($arrActions as $key => $value) {
				$objTpl->setCurrentBlock("multiview-listactionitem");
				$objTpl->setVariable("LIST_ACTION_TEXT", $key);
				$objTpl->setVariable("LIST_ACTION_VALUE", $value);
				$objTpl->parseCurrentBlock();
			}

			$objTpl->setCurrentBlock("multiview");

			$objTpl->setVariable("ACTIONS_OPEN", $objLang->get("pcmsOpenActionsMenu", "menu"));
			$objTpl->setVariable("ACTIONS_CLOSE", $objLang->get("pcmsCloseActionsMenu", "menu"));

			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_ALIASES . "\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_ALIASES . "\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_ALIASES . "\"");

			switch ($_SESSION["listCount"]) {
				case 10:
					$objTpl->setVariable("LIST_LENGTH_HREF_10", "");
					break;

				case 25:
					$objTpl->setVariable("LIST_LENGTH_HREF_25", "");
					break;

				case 100:
					$objTpl->setVariable("LIST_LENGTH_HREF_100", "");
					break;
			}

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_ALIASES);
			$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
			$objTpl->setVariable("LIST_ACTION_ONCHANGE", "Alias.multiDo(this, this[this.selectedIndex].value)");
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
			$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:Alias.multiSelect()");
			$objTpl->parseCurrentBlock();

			//*** Form variables.

			$intActiveLanguage = 0;
			if ($strCommand == CMD_EDIT) {
				$objAlias = Alias::selectByPK($intAliasId);
				$intActiveLanguage = $objAlias->getLanguageId();

				$objTpl->setVariable("FORM_ACTIVE_VALUE", ($objAlias->getActive()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_ALIAS_VALUE", $objAlias->getAlias());
				$objTpl->setVariable("FORM_URL_VALUE", $objAlias->getUrl());
				$objTpl->setVariable("FRM_HEADER", $objLang->get("editAlias", "form"));
				$objTpl->setVariable("FRM_STYLE", "");
				$objTpl->setVariable("CMD", CMD_EDIT);

				$objTpl->touchBlock("alias.edit");
			} else {
				$objTpl->setVariable("FORM_ACTIVE_VALUE", "checked=\"checked\"");
				$objTpl->setVariable("FRM_HEADER", $objLang->get("addAlias", "form"));
				if (!$blnError) $objTpl->setVariable("FRM_STYLE", " style=\"display:none\"");
				$objTpl->setVariable("CMD", CMD_ADD);

				$objTpl->touchBlock("alias.add");
			}

			//*** Languages.
			$objLanguages = ContentLanguage::select();
			foreach ($objLanguages as $objLanguage) {
				$objTpl->setCurrentBlock("language.item");
				$objTpl->setVariable("ID", $objLanguage->getId());
				$objTpl->setVariable("LABEL", $objLanguage->getName());
				$objTpl->setVariable("SELECTED", ($intActiveLanguage == $objLanguage->getId()) ? " selected=\"selected\"" : "");
				$objTpl->parseCurrentBlock();
			}

			$objTpl->setVariable("ALIASES", $objLang->get("aliases", "label"));
			$objTpl->setVariable("BUTTON_ADD", $objLang->get("aliasAdd", "button"));

			$objTpl->setVariable("FRM_LABEL_ACTIVE", $objLang->get("active", "form"));
			$objTpl->setVariable("FRM_LABEL_ALIAS", $objLang->get("alias", "form"));
			$objTpl->setVariable("FRM_DESCR_ALIAS", $objLang->get("alias", "tip"));
			$objTpl->setVariable("FRM_LABEL_LANGUAGE", $objLang->get("language", "form"));
			$objTpl->setVariable("FRM_DESCR_LANGUAGE", $objLang->get("language", "tip"));
			$objTpl->setVariable("FRM_LABEL_ALL_LANGUAGES", $objLang->get("allLanguages", "form"));
			$objTpl->setVariable("FRM_LABEL_URL", $objLang->get("element", "form"));
			$objTpl->setVariable("FRM_LABEL_SAVE", $objLang->get("save", "button"));

			$objTpl->setVariable("CID", NAV_PCMS_ALIASES);
			$objTpl->setVariable("EID", $intAliasId);
			$objTpl->parseCurrentBlock();

			$strReturn = $objTpl->get();

			break;

		case CMD_REMOVE:
			if (strpos($intAliasId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrAliases = explode(',', $intAliasId);
				$objAliases = Alias::selectByPK($arrAliases);

				foreach ($objAliases as $objAlias) {
					$objAlias->delete();
				}
			} else {
				//*** Single element submitted.
				$objAlias = Alias::selectByPK($intAliasId);
				$objAlias->delete();
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