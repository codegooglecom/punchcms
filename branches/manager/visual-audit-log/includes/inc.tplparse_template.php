<?php

function parseTemplates($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CONF,
			$_CLEAN_POST,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);

	switch ($strCommand) {
		case CMD_LIST:
			$objTpl->loadTemplatefile("multiview.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsTemplates", "menu"));

			$objTemplate = Template::selectByPK($intElmntId);

			if (empty($intElmntId)) {
				$strTemplateName = "Website";
			} else {
				if (is_object($objTemplate)) {
					$strTemplateName = $objTemplate->getName();
				} else {
					$strTemplateName = "";
				}
			}

			if (is_object($objTemplate)) {
				$objFields = $objTemplate->getFields();

				if (is_object($objFields)) {
					//*** Initiate field loop.
					$listCount = 0;
					$intPosition = request("pos");
					$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
					$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];
					$objFields->seek($intPosition);

					//*** Loop through the fields.
					foreach ($objFields as $objField) {
						$objFieldType = TemplateFieldType::selectByPK($objField->getTypeId());
						$strMeta = $objLang->get("editedBy", "label") . " " . $objField->getUsername() . ", " . Date::fromMysql($objLang->get("datefmt"), $objField->getModified());

						$objTpl->setCurrentBlock("multiview-item");
						$objTpl->setVariable("BUTTON_DUPLICATE", $objLang->get("duplicate", "button"));
						$objTpl->setVariable("BUTTON_DUPLICATE_HREF", "javascript:PTemplateField.duplicate({$objField->getId()});");
						$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
						$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:PTemplateField.remove({$objField->getId()});");

						$objTpl->setVariable("MULTIITEM_VALUE", $objField->getId());
						$objTpl->setVariable("MULTIITEM_HREF", "href=\"?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$objField->getId()}&amp;cmd=" . CMD_EDIT_FIELD . "\"");

						$strValue = htmlspecialchars($objField->getName());
						$strShortValue = getShortValue($strValue, 50);
						$intSize = strlen($strValue);
						$objTpl->setVariable("MULTIITEM_NAME", ($intSize > 50) ? $strShortValue : $strValue);
						$objTpl->setVariable("MULTIITEM_TITLE", ($intSize > 50) ? $strValue : "");

						$objTpl->setVariable("MULTIITEM_TYPE", ", " . $objFieldType->getName());
						$objTpl->setVariable("MULTIITEM_TYPE_CLASS", "field");
						$objTpl->setVariable("MULTIITEM_META", $strMeta);
						$objTpl->parseCurrentBlock();

						$listCount++;
						if ($listCount >= $_SESSION["listCount"]) break;
					}

					//*** Render page navigation.
					$pageCount = ceil($objFields->count() / $_SESSION["listCount"]);
					if ($pageCount > 0) {
						$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
						$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
						$nextPos = (($intPosition + $_SESSION["listCount"]) < $objFields->count()) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

						$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
						$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
						$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId&amp;pos=$previousPos");
						$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
						$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId&amp;pos=$nextPos");

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-top");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Render list action pulldown.
			$arrActions[$objLang->get("choose", "button")] = 0;
			$arrActions[$objLang->get("delete", "button")] = "delete";
			$arrActions[$objLang->get("duplicate", "button")] = "duplicate";
			foreach ($arrActions as $key => $value) {
				$objTpl->setCurrentBlock("multiview-listactionitem");
				$objTpl->setVariable("LIST_ACTION_TEXT", $key);
				$objTpl->setVariable("LIST_ACTION_VALUE", $value);
				$objTpl->parseCurrentBlock();
			}

			//*** Render the rest of the page.
			$objTpl->setCurrentBlock("multiview");

			$objTpl->setVariable("ACTIONS_OPEN", $objLang->get("pcmsOpenActionsMenu", "menu"));
			$objTpl->setVariable("ACTIONS_CLOSE", $objLang->get("pcmsCloseActionsMenu", "menu"));

			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId\"");

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

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_TEMPLATES . "&amp;eid=$intElmntId");
			$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
			$objTpl->setVariable("LIST_ACTION_ONCHANGE", "PTemplateField.multiDo(this, this[this.selectedIndex].value)");
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
			$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:PTemplateField.multiSelect()");
			$objTpl->setVariable("BUTTON_NEWSUBJECT", $objLang->get("newTemplate", "button"));
			$objTpl->setVariable("BUTTON_NEWSUBJECT_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD);
			$objTpl->setVariable("BUTTON_NEWSTRUCTURE", $objLang->get("newStructure", "button"));
			$objTpl->setVariable("BUTTON_NEWSTRUCTURE_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_STRUCTURE);

			if (is_object($objTemplate)) {
				$objTpl->setVariable("BUTTON_NEWFIELD", $objLang->get("newField", "button"));
				$objTpl->setVariable("BUTTON_NEWFIELD_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_FIELD);
				$objTpl->setVariable("BUTTON_EDIT", $objLang->get("edit", "button"));
				$objTpl->setVariable("BUTTON_EDIT_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_EDIT);
				$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("removeTemplate", "button"));
				$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:PTemplate.remove({$intElmntId});");
				$objTpl->setVariable("BUTTON_MAIN_DUPLICATE", $objLang->get("duplicateTemplate", "button"));
				$objTpl->setVariable("BUTTON_MAIN_DUPLICATE_HREF", "javascript:PTemplate.duplicate({$intElmntId});");
				$objTpl->setVariable("BUTTON_EXPORT_TEMPLATE",  $objLang->get("export", "button"));
				$objTpl->setVariable("BUTTON_EXPORT_TEMPLATE_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_EXPORT_TEMPLATE);
				$objTpl->setVariable("BUTTON_IMPORT_TEMPLATE",  $objLang->get("import", "button"));
				$objTpl->setVariable("BUTTON_IMPORT_TEMPLATE_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_IMPORT_TEMPLATE);
			}

			$objTpl->setVariable("LABEL_SUBJECT", $objLang->get("fieldsFor", "label") . " ");
			$objTpl->setVariable("SUBJECT_NAME", $strTemplateName);

			$objTpl->parseCurrentBlock();

			break;

		case CMD_REMOVE:
			$objTemplate = Template::selectByPK($intElmntId);

			$intParent = $objTemplate->getParentId();
			$objTemplate->delete();

			header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
			exit();

			break;

		case CMD_DUPLICATE:
			$objTemplate = Template::selectByPK($intElmntId);

			$intParent = $objTemplate->getParentId();
			$objTemplate->setUsername($objLiveUser->getProperty("name"));
			$objDuplicate = $objTemplate->duplicate($objLang->get("copyOf", "label"));

			//*** Redirect the page.
			$strReturnTo = request('returnTo');
			if (empty($strReturnTo)) {
				header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
				exit();
			} else {
				header("Location: " . Request::getURI() . $strReturnTo);
				exit();
			}

			break;

		case CMD_REMOVE_FIELD:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrFields = explode(',', $intElmntId);
				$objFields = TemplateField::selectByPK($arrFields);

				$intParent = $objFields->current()->getTemplateId();

				foreach ($objFields as $objField) {
					$objField->delete();
				}
			} else {
				//*** Single element submitted.
				$objField = TemplateField::selectByPK($intElmntId);

				$intParent = $objField->getTemplateId();
				$objField->delete();
			}

			header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
			exit();

			break;

		case CMD_DUPLICATE_FIELD:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrFields = explode(',', $intElmntId);
				$objFields = TemplateField::selectByPK($arrFields);

				$intParent = $objFields->current()->getTemplateId();

				foreach ($objFields as $objField) {
					$objField->setUsername($objLiveUser->getProperty("name"));
					$objField->duplicate($objLang->get("copyOf", "label"));
				}
			} else {
				//*** Single element submitted.
				$objField = TemplateField::selectByPK($intElmntId);

				$intParent = $objField->getTemplateId();
				$objField->setUsername($objLiveUser->getProperty("name"));
				$objField->duplicate($objLang->get("copyOf", "label"));
			}

			header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
			exit();

			break;

		case CMD_ADD:
		case CMD_EDIT:
			$objTpl->loadTemplatefile("template.tpl.htm");

			//*** Check if the rootfolder has been submitted.
			if ($strCommand == CMD_EDIT && $intElmntId == 0) {
				//*** Redirect to list mode.
				header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intElmntId);
				exit();
			}

			//*** Post the template form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addTemplate") {
				//*** The template form has been posted.
				$blnError = FALSE;

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_ispage"])) {
					$objTpl->setVariable("ERROR_ISPAGE_ON", " error");
					$objTpl->setVariable("ERROR_ISPAGE", $objLang->get("isPage", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_iscontainer"])) {
					$objTpl->setVariable("ERROR_ISCONTAINER_ON", " error");
					$objTpl->setVariable("ERROR_ISCONTAINER", $objLang->get("isContainer", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_forcecreation"])) {
					$objTpl->setVariable("ERROR_FORCECREATION_ON", " error");
					$objTpl->setVariable("ERROR_FORCECREATION", $objLang->get("forceCreation", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_name"])) {
					$objTpl->setVariable("ERROR_NAME_ON", " error");
					$objTpl->setVariable("ERROR_NAME", $objLang->get("templateName", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_apiname"])) {
					$objTpl->setVariable("ERROR_APINAME_ON", " error");
					$objTpl->setVariable("ERROR_APINAME", $objLang->get("commonTypeWord", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_description"])) {
					$objTpl->setVariable("ERROR_NOTES_ON", " error");
					$objTpl->setVariable("ERROR_NOTES", $objLang->get("commonTypeText", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = TRUE;
				}

				if ($blnError === TRUE) {
					//*** Display global error.
					$objTpl->setVariable("FORM_NAME", "templateForm");
					$objTpl->setVariable("FORM_ISPAGE_VALUE", (isset($_POST["frm_ispage"]) && $_POST["frm_ispage"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_APINAME_VALUE", $_POST["frm_apiname"]);
					$objTpl->setVariable("FORM_NOTES_VALUE", $_POST["frm_description"]);
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the template.
					if ($strCommand == CMD_EDIT) {
						$objTemplate = Template::selectByPK($intElmntId);
					} else {
						$objTemplate = new Template();
						$objTemplate->setParentId($_POST["eid"]);
						$objTemplate->setAccountId($_CONF['app']['account']->getId());
					}

					$objTemplate->setIsPage((empty($_CLEAN_POST["frm_ispage"])) ? 0 : 1);
					$objTemplate->setIsContainer((empty($_CLEAN_POST["frm_iscontainer"])) ? 0 : 1);
					$objTemplate->setForceCreation((empty($_CLEAN_POST["frm_forcecreation"])) ? 0 : 1);
					$objTemplate->setName($_CLEAN_POST["frm_name"]);
					$objTemplate->setApiName($_CLEAN_POST["frm_apiname"]);
					$objTemplate->setDescription($_CLEAN_POST["frm_description"]);
					$objTemplate->save();

					header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objTemplate->getId());
					exit();
				}
			} else {
				$objTpl->setVariable("FORM_NAME", "templateForm");
			}

			//*** Parse the template.
			$objTemplate = Template::selectByPK($intElmntId);

			//*** Set section title.
			if ($strCommand == CMD_EDIT) {
				$objTpl->setVariable("MAINTITLE", $objLang->get("templateDetailsFor", "label"));
				$objTpl->setVariable("MAINSUB", $objTemplate->getName());
			} else {
				$objTpl->setVariable("MAINTITLE", $objLang->get("templateDetails", "label"));
			}

			//*** Set tab title.
			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("details", "label"));
			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("templateadd");

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT) {
				$objTpl->setVariable("FORM_ISPAGE_VALUE", ($objTemplate->getIsPage()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_ISCONTAINER_VALUE", ($objTemplate->getIsContainer()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_FORCECREATION_VALUE", ($objTemplate->getForceCreation()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objTemplate->getName());
				$objTpl->setVariable("FORM_APINAME_VALUE", $objTemplate->getApiname());
				$objTpl->setVariable("FORM_NOTES_VALUE", $objTemplate->getDescription());
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$objTemplate->getParentId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$objTemplate->getParentId()}&amp;cmd=" . CMD_LIST);
			} else {
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}

			$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("requiredFields", "form"));
			$objTpl->setVariable("LABEL_PAGECONTAINER", $objLang->get("pageContainer", "form"));
			$objTpl->setVariable("LABEL_ISCONTAINER", $objLang->get("container", "form"));
			$objTpl->setVariable("ISCONTAINER_NOTE", $objLang->get("containerNote", "tip"));
			$objTpl->setVariable("LABEL_FORCECREATION", $objLang->get("forceCreation", "form"));
			$objTpl->setVariable("FORCECREATION_NOTE", $objLang->get("forceCreationNote", "tip"));
			$objTpl->setVariable("LABEL_TEMPLATENAME", $objLang->get("templateName", "form"));
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("APINAME_NOTE", $objLang->get("apiNameNote", "tip"));
			$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("singleview");
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_TEMPLATES);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

			break;

		case CMD_ADD_FIELD:
		case CMD_EDIT_FIELD:
			$objTpl->loadTemplatefile("templatefield.tpl.htm");

			//*** Post the templateField form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addTemplateField") {
				//*** The template form has been posted.
				$blnError = FALSE;

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_required"])) {
					$objTpl->setVariable("ERROR_REQUIRED_ON", " error");
					$objTpl->setVariable("ERROR_REQUIRED", $objLang->get("commonTypeText", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_name"])) {
					$objTpl->setVariable("ERROR_NAME_ON", " error");
					$objTpl->setVariable("ERROR_NAME", $objLang->get("fieldName", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_apiname"])) {
					$objTpl->setVariable("ERROR_APINAME_ON", " error");
					$objTpl->setVariable("ERROR_APINAME", $objLang->get("commonTypeWord", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_description"])) {
					$objTpl->setVariable("ERROR_NOTES_ON", " error");
					$objTpl->setVariable("ERROR_NOTES", $objLang->get("commonTypeText", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["frm_field_type"])) {
					$objTpl->setVariable("ERROR_FIELDTYPE_ON", " error");
					$objTpl->setVariable("ERROR_FIELDTYPE", $objLang->get("fieldType", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = TRUE;
				}

				if ($blnError === TRUE) {
					//*** Display global error.
					$objTpl->setVariable("FORM_NAME", "templateFieldForm");
					$objTpl->setVariable("FORM_REQUIRED_VALUE", (isset($_POST["frm_required"]) && $_POST["frm_required"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_APINAME_VALUE", $_POST["frm_apiname"]);
					$objTpl->setVariable("FORM_NOTES_VALUE", $_POST["frm_description"]);
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the template.
					if ($strCommand == CMD_EDIT_FIELD) {
						$objField = TemplateField::selectByPK($intElmntId);
					} else {
						$objField = new TemplateField();
						$objField->setTemplateId($_POST["eid"]);
					}
					$objField->setRequired((empty($_CLEAN_POST["frm_required"])) ? 0 : 1);
					$objField->setName($_CLEAN_POST["frm_name"]);
					$objField->setApiName($_CLEAN_POST["frm_apiname"]);
					$objField->setDescription($_CLEAN_POST["frm_description"]);
					$objField->setTypeId($_CLEAN_POST["frm_field_type"]);
					$objField->setUsername($objLiveUser->getProperty("name"));
					$objField->save();

					$objField->clearValues();
					//*** Add type values to the field.
					foreach ($_REQUEST as $key => $value) {
						if (is_array($value)) {
							$intCount = 0;
							foreach ($value as $subKey => $subValue) {
								$objValue = new TemplateFieldValue();
								$objValue->setName($key . "_" . $intCount);
								$objValue->setValue($subValue);
								$objValue->setFieldId($objField->getId());
								$objValue->save();
								$intCount++;
							}
						} else {
							if ($value != "" && substr($key, 0, 4) == "tfv_") {
								$objValue = new TemplateFieldValue();
								$objValue->setName($key);
								$objValue->setValue($value);
								$objValue->setFieldId($objField->getId());
								$objValue->save();
							}
						}
					}

					header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objField->getTemplateId());
					exit();
				}
			} else {
				$objTpl->setVariable("FORM_NAME", "templateFieldForm");
			}

			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("details", "label"));
			$objTpl->parseCurrentBlock();

			$typeValue = 0;
			if ($strCommand == CMD_EDIT_FIELD) {
				$objField = TemplateField::selectByPK($intElmntId);
				$typeValue = $objField->getTypeId();
			}
			$objTypes = TemplateFieldTypes::getTypes();

			if (is_object($objTypes)) {
				foreach ($objTypes as $objType) {
					$objTpl->setCurrentBlock("list_fieldtype");
					if ($typeValue == $objType->getId()) {
						$objTpl->setVariable("FIELDTYPE_SELECTED", "selected=\"selected\"");
					}
					$objTpl->setVariable("FIELDTYPE_VALUE", $objType->getId());
					$objTpl->setVariable("FIELDTYPE_TEXT", xhtmlsave($objType->getName()));
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Set section title.
			if ($strCommand == CMD_EDIT_FIELD) {
				$objTpl->setVariable("MAINTITLE", $objLang->get("templateFieldDetailsFor", "label"));
				$objTpl->setVariable("MAINSUB", $objField->getName());
			} else {
				$objTpl->setVariable("MAINTITLE", $objLang->get("templateFieldDetails", "label"));
			}

			//*** Image crop settings.
			$arrValues = array(1,2,3,4);
			$arrLabels = array("Resize cropped","Resize fit cropped","Resize distorted","Resize to fit");

			$arrSettings = array();
			$arrImageSettings = array();

			if ($strCommand == CMD_EDIT_FIELD) {
				$objFieldValues = $objField->getValues();
				if (is_object($objFieldValues)) {
					foreach ($objFieldValues as $objFieldValue) {
						switch (strtoupper($objFieldValue->getName())) {
							case "TFV_BOOLEAN_DEFAULT":
								if ($objFieldValue->getValue()) {
									$arrSettings[$objFieldValue->getName()] = "checked=\"checked\"";
								}

								break;
							default:
								$arrKey = explode("_", $objFieldValue->getName());
								$intIndex = array_pop($arrKey);
								if (is_numeric($intIndex)) {
									$strValue = $objFieldValue->getValue();
									$arrImageSettings[$intIndex][implode("_", $arrKey)] = xhtmlsave($strValue);
								} else {
									$strValue = $objFieldValue->getValue();
									$arrSettings[$objFieldValue->getName()] = xhtmlsave($strValue);
								}
						}
					}

					if (count($arrImageSettings) > 0) {
						//*** Image settings.
						$arrImageSettings = array_reverse($arrImageSettings);
						foreach ($arrImageSettings as $key => $objValue) {
							$objTpl->setCurrentBlock("image.settings");
							foreach ($objValue as $setting => $value) {
								switch (strtoupper($setting)) {
									case "TFV_IMAGE_SCALE":
										$strValue = "";
										foreach ($arrValues as $settingKey => $settingValue) {
											$selected = ($settingValue == $value) ? " selected=\"selected\"" : "";
											$strValue .= "<option value=\"$arrValues[$settingKey]\"{$selected}>{$arrLabels[$settingKey]}</option>\n";
										}
										$objTpl->setVariable(strtoupper($setting), $strValue);

										break;
									case "TFV_IMAGE_GRAYSCALE":
										if ($value) {
											$objTpl->setVariable(strtoupper($setting), "checked=\"checked\"");
										}

										break;
									default:
										$objTpl->setVariable(strtoupper($setting), xhtmlsave($value));
								}
							}

							if (count($arrImageSettings) == 1) {
								$objTpl->setVariable("API_STYLE", "display:none");
							}

							if ($key == 0) {
								$objTpl->setVariable("REMOVE_STYLE", "display:none");
							}

							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			$objTpl->setCurrentBlock("templatefieldadd");
			$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("requiredFields", "form"));
			$objTpl->setVariable("LABEL_REQUIREDFIELD", $objLang->get("requiredField", "form"));
			$objTpl->setVariable("LABEL_FIELDNAME", $objLang->get("fieldName", "form"));
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("APINAME_NOTE", $objLang->get("apiNameNote", "tip"));
			$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			$objTpl->setVariable("LABEL_FIELDTYPE", $objLang->get("fieldType", "form"));
			$objTpl->setVariable("LABEL_FIELDTYPE_OPTIONS", $objLang->get("typeOptions", "label"));
			$objTpl->setVariable("TFV_LIST_NOTES", $objLang->get("templateListType", "tip"));
			$objTpl->setVariable("TFV_FORMAT_NOTES", $objLang->get("templateDateType", "tip"));
			$objTpl->setVariable("TFV_QUALITY_NOTES", $objLang->get("templateImageType", "tip"));
			$objTpl->setVariable("TFV_EXTENSION_NOTES", $objLang->get("templateFileType", "tip"));

			//*** Render image scale pulldown.
			if (count($arrImageSettings) == 0) {
				$strValue = "";
				foreach ($arrValues as $key => $value) {
					$strValue .= "<option value=\"$arrValues[$key]\">{$arrLabels[$key]}</option>\n";
				}
				$objTpl->setVariable("TFV_IMAGE_SCALE", $strValue);
				$objTpl->setVariable("API_STYLE", "display:none");
				$objTpl->setVariable("REMOVE_STYLE", "display:none");
			}

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT_FIELD) {
				$objTpl->setVariable("FORM_REQUIRED_VALUE", ($objField->getRequired()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objField->getName());
				$objTpl->setVariable("FORM_APINAME_VALUE", $objField->getApiname());
				$objTpl->setVariable("FORM_NOTES_VALUE", $objField->getDescription());

				//*** Insert values for the field type.
				if (count($arrSettings) > 0) {
					foreach ($arrSettings as $name => $value) {
						switch (strtoupper($name)) {
							case "TFV_BOOLEAN_DEFAULT":
								if ($value) {
									$objTpl->setVariable(strtoupper($name), "checked=\"checked\"");
								}

								break;
							case "TFV_IMAGE_SCALE":
								//*** Skip. Already set.

								break;
							default:
								$objTpl->setVariable(strtoupper($name), xhtmlsave($value));
						}
					}
				}
			}

			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("singleview");
			if ($strCommand == CMD_EDIT_FIELD) {
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$objField->getTemplateId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$objField->getTemplateId()}&amp;cmd=" . CMD_LIST);
			} else {
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_TEMPLATES);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

			break;

		case CMD_ADD_STRUCTURE:
		case CMD_ADD_STRUCTURE_DETAIL:
			$objTpl->loadTemplatefile("structure.tpl.htm");

			$blnRenderSelects = FALSE;

			//*** Post the structure form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addStructure") {
				//*** The structure form has been posted.
				$blnError = FALSE;

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_structure"])) {
					$objTpl->setVariable("ERROR_STRUCTURE_ON", " error");
					$objTpl->setVariable("ERROR_STRUCTURE", $objLang->get("structure", "formerror"));
					$blnError = TRUE;
				}

				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = TRUE;
				}

				if ($blnError === TRUE) {
					//*** Display global error.
					$objTpl->setVariable("FORM_NAME", "structureForm");
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Import the structure.
					if (Structure::hasSelect($_CLEAN_POST["frm_structure"]) && $strCommand == CMD_ADD_STRUCTURE) {
						$blnRenderSelects = TRUE;
					} else {
						Structure::addById($_CLEAN_POST["frm_structure"], $intElmntId);

						header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $intElmntId);
						exit();
					}
				}
			} else {
				$objTpl->setVariable("FORM_NAME", "structureForm");
			}

			//*** Parse the template.
			$objTemplate = Template::selectByPK($intElmntId);

			//*** Set section title.
			$objTpl->setVariable("MAINTITLE", $objLang->get("structureAdd", "label"));

			//*** Set tab title.
			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("structureDetails", "label"));
			$objTpl->parseCurrentBlock();

			if (!$blnRenderSelects) {
				$objElements = Structure::selectBySection("template");
				foreach ($objElements as $objElement) {
					$objTpl->setCurrentBlock("structure.item");
					$objTpl->setVariable("VALUE", $objElement->getId());
					$objTpl->setVariable("LABEL", $objElement->getName());
					$objTpl->parseCurrentBlock();
				}
				foreach ($objElements as $objElement) {
					$objTpl->setCurrentBlock("structure.description");
					$objTpl->setVariable("VALUE", $objElement->getId());
					$objTpl->setVariable("BODY", $objElement->getDescription());
					if ($objElements->key() > 0) $objTpl->setVariable("HIDE", "display:none");
					$objTpl->parseCurrentBlock();
				}

				$objTpl->setCurrentBlock("structureadd.description");
				$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("structureAdd", "tip"));
				$objTpl->parseCurrentBlock();

				$objTpl->setCurrentBlock("structureadd");
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("LABEL_STRUCTURENAME", $objLang->get("structureName", "form"));
				$objTpl->parseCurrentBlock();
			} else {
				$objSelects = Structure::getSelectsById($_CLEAN_POST["frm_structure"]);
				foreach ($objSelects as $objSelect) {
					switch ($objSelect->getType()) {
						case "language":
							$objContentLangs = ContentLanguage::select();
							foreach ($objContentLangs as $objContentLang) {
								$objTpl->setCurrentBlock("select.language.item");
								$objTpl->setVariable("LABEL", $objContentLang->getName());
								$objTpl->setVariable("VALUE", $objContentLang->getId());
								$objTpl->parseCurrentBlock();
							}

							$objTpl->setCurrentBlock("select.language");
							$objTpl->setVariable("LABEL", $objLang->get("sSelectLanguage", "form"));
							$objTpl->setVariable("DESCRIPTION", $objSelect->getDescription());
							$objTpl->setVariable("SELECT_NAME", "frm_select_" . $objSelect->getId());
							$objTpl->parseCurrentBlock();
							break;
						case "element":
							$objTpl->setCurrentBlock("select.element");
							$objTpl->setVariable("DESCRIPTION", $objSelect->getDescription());
							$objTpl->setVariable("SELECT_NAME", "frm_select_" . $objSelect->getId());
							$objTpl->setVariable("FORM_NAME", "detailsForm");
							$objTpl->parseCurrentBlock();
							break;
					}
				}

				$objTpl->setCurrentBlock("structureadd.description");
				$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("structureSelects", "tip"));
				$objTpl->parseCurrentBlock();

				$objTpl->setCurrentBlock("structureselects");
				$objTpl->setVariable("FRM_STRUCURE", $_CLEAN_POST["frm_structure"]);
				$objTpl->parseCurrentBlock();

				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}

			$objTpl->setCurrentBlock("singleview");
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("insert", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_TEMPLATES);
			$objTpl->setVariable("CMD", (!$blnRenderSelects) ? CMD_ADD_STRUCTURE : CMD_ADD_STRUCTURE_DETAIL);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

			if ($blnRenderSelects) {
				$objTpl->setVariable("FORM_NAME", "detailsForm");
			}

			break;

        case CMD_EXPORT_TEMPLATE:
			$objTpl->loadTemplatefile("export.tpl.htm");

            //*** Parse the template.
			$objTemplate = Template::selectByPK($intElmntId);

			//*** Set section title.
			$objTpl->setVariable("MAINTITLE", $objLang->get("export", "label"));

			//*** Set tab title.
			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("exportOptions", "label"));
			$objTpl->parseCurrentBlock();

            $objTpl->setVariable("FORM_NAME", "exportForm");

            //*** Handle request & create export
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['export_type']))
            {
                //*** The template form has been posted.
                $arrTemplateFilters = array();
                foreach($_POST['tmpl'] as $id => $val)
                {
                    $arrTemplateFilters[] = intval($id);
                }
                $exportElements = ($_POST['export_type'] == 'templates_elements') ? true : false;
                $strZipFile = ImpEx::exportFrom(NULL, $objTemplate->getId(), NULL, $arrTemplateFilters , $_CONF['app']['account']->getId(), $exportElements);

                //*** Return XML.
                header("HTTP/1.1 200 OK");
                header("Pragma: public");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                header('Content-Type: application/octetstream; charset=utf-8');
                header("Content-Length: " . (string)(filesize($strZipFile)));
                header('Content-Disposition: attachment; filename="' . date("Y-m-d") . '_exportTemplates.zip"');
                header("Content-Transfer-Encoding: binary\n");

                readfile($strZipFile);
                unlink($strZipFile);
                exit;
			}

            //*** Create template checkboxes
            $objTpl->setVariable("FORM_CHECKBOXES", createTemplateTree($objTemplate));

            $objTpl->setVariable("EXPORT", $objLang->get("export", "label"));
            $objTpl->setVariable("EXPORT_TEMPLATES_ELEMENTS", $objLang->get("templatesElements", "label"));
            $objTpl->setVariable("EXPORT_TEMPLATES", $objLang->get("templates", "label"));
			$objTpl->setVariable("SELECT_ITEMS", $objLang->get("selectTemplates", "label"));

            //*** Set form buttons
			$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
            $objTpl->setCurrentBlock("singleview");
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("export", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_TEMPLATES);
			$objTpl->setVariable("CMD", CMD_EXPORT_TEMPLATE);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();


            break;

        case CMD_IMPORT_TEMPLATE:
			$objTpl->loadTemplatefile("import.tpl.htm");

            //*** Parse the template.
			$objTemplate = Template::selectByPK($intElmntId);

			//*** Set section title.
			$objTpl->setVariable("MAINTITLE", $objLang->get("import", "label"));

			//*** Set tab title.
			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("importOptions", "label"));
			$objTpl->parseCurrentBlock();

            //*** Handle request & do import
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES["file"]["name"] ))
            {
                if ($_FILES["file"]["error"] > 0)
                {
                    $objTpl->setVariable('ERROR_MAIN','Error: '. $_FILES["file"]["error"]);
                }
                else if(end(explode(".", $_FILES["file"]["name"])) !== 'zip')
                {
                    $objTpl->setVariable('ERROR_MAIN','Error: Only *.ZIP files allowed');
                }
                else
                {
                    $importElements = ($_POST['import_type'] === 'templates_elements') ? true : false;
                    if(!ImpEx::importIn($_FILES["file"]["tmp_name"],NULL,$objTemplate->getId(),$_CONF['app']['account']->getId(),true,$importElements,false))
                    {
                        $objTpl->setVariable('ERROR_MAIN','Templates and/or fields of templates in file do not match the destination templates');
                    }
                }
            }

            $objTpl->setVariable("IMPORT", $objLang->get("import", "label"));
            $objTpl->setVariable("IMPORT_TEMPLATES_ELEMENTS", $objLang->get("templatesElements", "label"));
            $objTpl->setVariable("IMPORT_TEMPLATES", $objLang->get("templates", "label"));
            $objTpl->setVariable("IMPORT_ELEMENTS", $objLang->get("elements", "label"));
            $objTpl->setVariable('CUR_LOCATION',$objTemplate->getName());
			$objTpl->setVariable("IMPORT_FILE", $objLang->get("importFile", "label"));
			$objTpl->setVariable("IMPORT_FILE_TIP", $objLang->get("importFile", "tip"));

            //*** Set form buttons
			$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_TEMPLATES . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
            $objTpl->setCurrentBlock("singleview");
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("import", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_TEMPLATES);
			$objTpl->setVariable("CMD", CMD_IMPORT_TEMPLATE);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

            break;
	}

	return $objTpl->get();
}

?>