<?php

function parseForms($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$_CONF,
			$_CLEAN_POST,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);

	switch ($strCommand) {
		case CMD_LIST:
			$objTpl->loadTemplatefile("multiview.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsForms", "menu"));

			$objForm = Form::selectByPK($intElmntId);

			if (empty($intElmntId)) {
				$strFormName = "WEBROOT";
			} else {
				if (is_object($objForm)) {
					$strFormName = $objForm->getName();
				} else {
					$strFormName = "";
				}
			}

			if (is_object($objForm)) {
				$objFields = $objForm->getFields();

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
						$strMeta = "Aangepast door " . $objField->getUsername() . ", " . Date::fromMysql($objLang->get("datefmt"), $objField->getModified());

						$objTpl->setCurrentBlock("multiview-item");
						$objTpl->setVariable("BUTTON_DUPLICATE", $objLang->get("duplicate", "button"));
						$objTpl->setVariable("BUTTON_DUPLICATE_HREF", "javascript:PTemplateField.duplicate({$objField->getId()});");
						$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
						$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:PTemplateField.remove({$objField->getId()});");

						$objTpl->setVariable("MULTIITEM_VALUE", $objField->getId());
						$objTpl->setVariable("MULTIITEM_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$objField->getId()}&amp;cmd=" . CMD_EDIT_FIELD);
						$objTpl->setVariable("MULTIITEM_NAME", $objField->getName());
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
						$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId&amp;pos=$previousPos");
						$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
						$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId&amp;pos=$nextPos");

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-top");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId&amp;pos=$position\"");
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
			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId\"");

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

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_FORMS . "&amp;eid=$intElmntId");
			$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
			$objTpl->setVariable("LIST_ACTION_ONCHANGE", "PTemplateField.multiDo(this, this[this.selectedIndex].value)");
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
			$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:PTemplateField.multiSelect()");

			if (is_object($objForm)) {
				$objTpl->setVariable("BUTTON_EDIT", $objLang->get("edit", "button"));
				$objTpl->setVariable("BUTTON_EDIT_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_EDIT);
				$objTpl->setVariable("BUTTON_NEWFIELD", $objLang->get("newField", "button"));
				$objTpl->setVariable("BUTTON_NEWFIELD_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_FIELD);
				$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("removeForm", "button"));
				$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:Form.remove({$intElmntId});");
			} else {
				$objTpl->setVariable("BUTTON_NEWSUBJECT", $objLang->get("newForm", "button"));
				$objTpl->setVariable("BUTTON_NEWSUBJECT_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD);
			}

			$objTpl->setVariable("LABEL_SUBJECT", $objLang->get("fieldsFor", "label") . " ");
			$objTpl->setVariable("SUBJECT_NAME", $strFormName);

			$objTpl->parseCurrentBlock();

			break;

		case CMD_REMOVE:
			$objForm = Form::selectByPK($intElmntId);

			$intParent = $objForm->getParentId();
			$objForm->delete();

			header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
			exit();

			break;

		case CMD_REMOVE_FIELD:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrFields = explode(',', $intElmntId);
				$objFields = TemplateField::selectByPK($arrFields);

				$intParent = $objFields->current()->getFormId();

				foreach ($objFields as $objField) {
					$objField->delete();
				}
			} else {
				//*** Single element submitted.
				$objField = TemplateField::selectByPK($intElmntId);

				$intParent = $objField->getFormId();
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

				$intParent = $objFields->current()->getFormId();

				foreach ($objFields as $objField) {
					$objField->setUsername($objLiveUser->getProperty("name"));
					$objField->duplicate($objLang->get("copyOf", "label"));
				}
			} else {
				//*** Single element submitted.
				$objField = TemplateField::selectByPK($intElmntId);

				$intParent = $objField->getFormId();
				$objField->setUsername($objLiveUser->getProperty("name"));
				$objField->duplicate($objLang->get("copyOf", "label"));
			}

			header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
			exit();

			break;

		case CMD_ADD:
		case CMD_EDIT:
			$objTpl->loadTemplatefile("forms.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsForms", "menu"));

			//*** Post the template form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addForm") {
				//*** The template form has been posted.
				$blnError = FALSE;

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_name"])) {
					$objTpl->setVariable("ERROR_NAME_ON", " error");
					$objTpl->setVariable("ERROR_NAME", $objLang->get("formName", "formerror"));
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
					$objTpl->setVariable("FORM_NAME", "formForm");
					$objTpl->setVariable("FORM_ISPAGE_VALUE", (isset($_POST["frm_ispage"]) && $_POST["frm_ispage"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_APINAME_VALUE", $_POST["frm_apiname"]);
					$objTpl->setVariable("FORM_NOTES_VALUE", $_POST["frm_description"]);
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the form.
					if ($strCommand == CMD_EDIT) {
						$objForm = Form::selectByPK($intElmntId);
					} else {
						$objForm = new Form();
						$objForm->setAccountId($_CONF['app']['account']->getId());
					}

					$objForm->setName($_CLEAN_POST["frm_name"]);
					$objForm->setApiName($_CLEAN_POST["frm_apiname"]);
					$objForm->setDescription($_CLEAN_POST["frm_description"]);
					$objForm->save();

					header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objForm->getId());
					exit();
				}
			} else {
				$objTpl->setVariable("FORM_NAME", "formForm");
			}

			//*** Parse the form.
			$objForm = Form::selectByPK($intElmntId);

			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("formDetails", "label"));
			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("formadd");

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT) {
				$objTpl->setVariable("FORM_ISPAGE_VALUE", ($objForm->getIsPage()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_ISCONTAINER_VALUE", ($objForm->getIsContainer()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objForm->getName());
				$objTpl->setVariable("FORM_APINAME_VALUE", $objForm->getApiname());
				$objTpl->setVariable("FORM_NOTES_VALUE", $objForm->getDescription());
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$objForm->getParentId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$objForm->getParentId()}&amp;cmd=" . CMD_LIST);
			} else {
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}

			$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("requiredFields", "form"));
			$objTpl->setVariable("LABEL_PAGECONTAINER", $objLang->get("pageContainer", "form"));
			$objTpl->setVariable("LABEL_CONTAINER", $objLang->get("container", "form"));
			$objTpl->setVariable("LABEL_FORMNAME", $objLang->get("formName", "form"));
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("APINAME_DESCRIPTION", $objLang->get("apiNameShort", "tip"));
			$objTpl->setVariable("APINAME_NOTE", $objLang->get("apiNameNote", "tip"));
			$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("singleview");
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_FORMS);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

			break;

		case CMD_ADD_FIELD:
		case CMD_EDIT_FIELD:
			$objTpl->loadTemplatefile("templatefield.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsForms", "menu"));

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
						$objField->setFormId($_POST["eid"]);
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
						if ($value != "" && substr($key, 0, 4) == "tfv_") {
							$objValue = new TemplateFieldValue();
							$objValue->setName($key);
							$objValue->setValue($value);
							$objValue->setFieldId($objField->getId());
							$objValue->save();
						}
					}

					header("Location: " . Request::getURI() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objField->getFormId());
					exit();
				}
			} else {
				$objTpl->setVariable("FORM_NAME", "templateFieldForm");
			}

			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("templateFieldDetails", "label"));
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
					$objTpl->setVariable("FIELDTYPE_TEXT", $objType->getName());
					$objTpl->parseCurrentBlock();
				}
			}

			$objTpl->setCurrentBlock("templatefieldadd");
			$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("requiredFields", "form"));
			$objTpl->setVariable("LABEL_REQUIREDFIELD", $objLang->get("requiredField", "form"));
			$objTpl->setVariable("LABEL_FIELDNAME", $objLang->get("fieldName", "form"));
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("APINAME_DESCRIPTION", $objLang->get("apiNameShort", "tip"));
			$objTpl->setVariable("APINAME_NOTE", $objLang->get("apiNameNote", "tip"));
			$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			$objTpl->setVariable("LABEL_FIELDTYPE", $objLang->get("fieldType", "form"));
			$objTpl->setVariable("LABEL_FIELDTYPE_OPTIONS", $objLang->get("typeOptions", "label"));
			$objTpl->setVariable("TFV_LIST_NOTES", $objLang->get("templateListType", "tip"));
			$objTpl->setVariable("TFV_FORMAT_NOTES", $objLang->get("templateDateType", "tip"));
			$objTpl->setVariable("TFV_QUALITY_NOTES", $objLang->get("templateImageType", "tip"));
			$objTpl->setVariable("TFV_EXTENSION_NOTES", $objLang->get("templateFileType", "tip"));
			
			//*** Render image scale pulldown.
			$arrValues = array(1,2,3,4,5);
			$arrLabels = array("Resize exact, crop","Resize exact, distort","Resize with boundary, crop","Resize with boundary, distort","Resize with boundary, exact");
			$strValue = "";
			foreach ($arrValues as $key => $value) {
				$strValue .= "<option value=\"$arrValues[$key]\">{$arrLabels[$key]}</option>\n";
			}
			$objTpl->setVariable("TFV_IMAGE_SCALE", $strValue);

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT_FIELD) {
				$objTpl->setVariable("FORM_REQUIRED_VALUE", ($objField->getRequired()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objField->getName());
				$objTpl->setVariable("FORM_APINAME_VALUE", $objField->getApiname());
				$objTpl->setVariable("FORM_NOTES_VALUE", $objField->getDescription());

				//*** Insert values for the field type.
				$objFieldValues = $objField->getValues();
				if (is_object($objFieldValues)) {
					foreach ($objFieldValues as $objFieldValue) {
						if (strtoupper($objFieldValue->getName()) == "TFV_IMAGE_SCALE") {
							if ($objField->getTypeId() == FIELD_TYPE_IMAGE) {
								$strValue = "";
								foreach ($arrValues as $key => $value) {
									$selected = ($value == $objFieldValue->getValue()) ? " selected=\"selected\"" : "";
									$strValue .= "<option value=\"$arrValues[$key]\"{$selected}>{$arrLabels[$key]}</option>\n";
								}
								$objTpl->setVariable(strtoupper($objFieldValue->getName()), $strValue);
							}
						} else {
							$strValue = $objFieldValue->getValue();
							$objTpl->setVariable(strtoupper($objFieldValue->getName()), $strValue);
						}
					}
				}
			}
			
			$objTpl->parseCurrentBlock();

			$objTpl->setCurrentBlock("singleview");
			if ($strCommand == CMD_EDIT_FIELD) {
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$objField->getFormId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$objField->getFormId()}&amp;cmd=" . CMD_LIST);
			} else {
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_FORMS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_FORMS);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);
			$objTpl->parseCurrentBlock();

			break;
	}

	return $objTpl->get();
}

?>