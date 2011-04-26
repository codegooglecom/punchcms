<?php

function parsePages($intElmntId, $strCommand) {
	global 	$objLang,
			$_CLEAN_POST,
			$objLiveUser,
			$_CONF,
			$_PATHS,
			$DBAConn,
			$objMultiUpload;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
		
	$blnUiError = Request::get('err', 0);

	switch ($strCommand) {
		case CMD_LIST:
			$objTpl->loadTemplatefile("multiview.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsElements", "menu"));

			$objElement = Element::selectByPK($intElmntId);

			if (empty($intElmntId)) {
				$strElmntName = "Website";
			} else {
				if (is_object($objElement)) {
					$strElmntName = $objElement->getName();
				} else {
					$strElmntName = "";
				}
			}

			if (is_object($objElement) || empty($intElmntId)) {
				if (empty($intElmntId)) {
					$objElements = Elements::getFromParent(0, FALSE);
				} else {
					$objElements = $objElement->getElements(FALSE);
				}

				if (is_object($objElements)) {
					//*** Initiate child element loop.
					$listCount = 0;
					$intPosition = request("pos");
					$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
					$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];
					$objElements->seek($intPosition);

					//*** Loop through the elements.
					foreach ($objElements as $objSubElement) {
						//if (Permissions::hasElementPermission(SPINCMS_ELEMENTS_READ, $objSubElement)) {
							$objTemplate = Template::selectByPK($objSubElement->getTemplateId(), array('name'));
							$strMeta = $objLang->get("editedBy", "label") . " " . $objSubElement->getUsername() . ", " . Date::fromMysql($objLang->get("datefmt"), $objSubElement->getModified());

							$objTpl->setCurrentBlock("multiview-item");
							if ($objSubElement->getTypeId() != ELM_TYPE_LOCKED) {
								$objTpl->setVariable("BUTTON_DUPLICATE", $objLang->get("duplicate", "button"));
								$objTpl->setVariable("BUTTON_DUPLICATE_HREF", "javascript:PElement.duplicate({$objSubElement->getId()});");
								$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
								$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:PElement.remove({$objSubElement->getId()});");
							}

							$objTpl->setVariable("MULTIITEM_VALUE", $objSubElement->getId());
							//if (Permissions::hasElementPermission(SPINCMS_ELEMENTS_WRITE, $objSubElement)) {
								$objTpl->setVariable("MULTIITEM_HREF", "href=\"?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$objSubElement->getId()}&amp;cmd=" . CMD_EDIT . "\"");
							//} else {
							//	$objTpl->setVariable("MULTIITEM_HREF", "");
							//}
							if ($objSubElement->getActive() < 1) $objTpl->setVariable("MULTIITEM_ACTIVE", " class=\"inactive\"");
							
							$strValue = htmlspecialchars($objSubElement->getName());
							$strShortValue = getShortValue($strValue, 50);
							$intSize = strlen($strValue);
							$objTpl->setVariable("MULTIITEM_NAME", ($intSize > 50) ? $strShortValue : $strValue);
							$objTpl->setVariable("MULTIITEM_TITLE", ($intSize > 50) ? $strValue : "");
							

							$strTypeClass = "";
							if ($objSubElement->getTypeId() == ELM_TYPE_FOLDER) {
								$strTypeClass = "folder";
							} else {
								$objChildElements = $objSubElement->getElements();
								if (is_object($objChildElements) && $objChildElements->count() > 0) {
									switch ($objSubElement->getTypeId()) {
										case ELM_TYPE_DYNAMIC:
											$strTypeClass = "widget-dynamic";
											break;
										case ELM_TYPE_LOCKED:
											$strTypeClass = "widget-locked";
											break;
										default:
											$strTypeClass = "widget";												
									}
								} else {
									switch ($objSubElement->getTypeId()) {
										case ELM_TYPE_DYNAMIC:
											$strTypeClass = "element-dynamic";
											break;
										case ELM_TYPE_LOCKED:
											$strTypeClass = "element-locked";
											break;
										default:
											$strTypeClass = "element";													
									}									
								}
							}
							$objTpl->setVariable("MULTIITEM_TYPE_CLASS", $strTypeClass);

							if (is_object($objTemplate)) {
								$objTpl->setVariable("MULTIITEM_TYPE", ", " . $objTemplate->getName());
							}

							$objTpl->setVariable("MULTIITEM_META", $strMeta);
							$objTpl->parseCurrentBlock();

							$listCount++;
							if ($listCount >= $_SESSION["listCount"]) break;
						//}
					}

					//*** Render page navigation.
					$pageCount = ceil($objElements->count() / $_SESSION["listCount"]);
					if ($pageCount > 0) {
						$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
						$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
						$nextPos = (($intPosition + $_SESSION["listCount"]) < $objElements->count()) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

						$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
						$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
						$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId&amp;pos=$previousPos");
						$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
						$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId&amp;pos=$nextPos");

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-top");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}

						//*** Bottom page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Render list action pulldown.
			if (!is_object($objElement) || $objElement->getTypeId() != ELM_TYPE_LOCKED) {
				$arrActions[$objLang->get("choose", "button")] = 0;
				$arrActions[$objLang->get("delete", "button") . "&nbsp;&nbsp;"] = "delete";
				$arrActions[$objLang->get("duplicate", "button") . "&nbsp;&nbsp;"] = "duplicate";
				$arrActions[$objLang->get("activate", "button") . "&nbsp;&nbsp;"] = "activate";
				$arrActions[$objLang->get("deactivate", "button") . "&nbsp;&nbsp;"] = "deactivate";
				foreach ($arrActions as $key => $value) {
					$objTpl->setCurrentBlock("multiview-listactionitem");
					$objTpl->setVariable("LIST_ACTION_TEXT", $key);
					$objTpl->setVariable("LIST_ACTION_VALUE", $value);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Render the rest of the page.
			$objTpl->setCurrentBlock("multiview");
			
			$objTpl->setVariable("ACTIONS_OPEN", $objLang->get("pcmsOpenActionsMenu", "menu"));
			$objTpl->setVariable("ACTIONS_CLOSE", $objLang->get("pcmsCloseActionsMenu", "menu"));
			
			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId\"");

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

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_ELEMENTS . "&amp;eid=$intElmntId");
			if (!is_object($objElement) || $objElement->getTypeId() != ELM_TYPE_LOCKED) {
				$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
				$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
				$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:PElement.multiSelect()");
				$objTpl->setVariable("LIST_ACTION_ONCHANGE", "PElement.multiDo(this, this[this.selectedIndex].value)");
			}
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			
			if (!isset($objElement) || ($objElement->getTypeId() != ELM_TYPE_DYNAMIC && $objElement->getTypeId() != ELM_TYPE_LOCKED)) {
				$objTpl->setVariable("BUTTON_NEWSUBJECT", $objLang->get("newElement", "button"));
	
				$objDefaultLang = ContentLanguage::getDefault();
				if (!is_object($objDefaultLang)) {
					$objTpl->setVariable("BUTTON_NEWSUBJECT_HREF", "javascript:alert('" . $objLang->get("elementBeforeLanguage", "alert") . "')");
				} else {
					$objTpl->setVariable("BUTTON_NEWSUBJECT_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD);
				}
				
				$objTpl->setVariable("BUTTON_NEWFOLDER", $objLang->get("newFolder", "button"));
				$objTpl->setVariable("BUTTON_NEWFOLDER_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_FOLDER);
			}
			
			if (!isset($objElement) || $objElement->getTypeId() != ELM_TYPE_LOCKED) {
				$objTpl->setVariable("BUTTON_NEWDYNAMIC", $objLang->get("newDynamic", "button"));
				$objTpl->setVariable("BUTTON_NEWDYNAMIC_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_DYNAMIC);
	
				if ($intElmntId > 0) {
					$objElement = Element::selectByPK($intElmntId);
					$objTpl->setVariable("BUTTON_EDIT", $objLang->get("edit", "button"));
					$objTpl->setVariable("BUTTON_EDIT_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_EDIT);
				}
			}

			$objTpl->setVariable("LABEL_SUBJECT", $objLang->get("elementsIn", "label") . " ");
			$objTpl->setVariable("SUBJECT_NAME", $strElmntName);
			$objTpl->setVariable("EID", $intElmntId);

			$objTpl->parseCurrentBlock();

			break;

		case CMD_REMOVE:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrElements = explode(',', $intElmntId);
				$objElements = Element::selectByPK($arrElements);

				$intParent = $objElements->current()->getParentId();

				foreach ($objElements as $objElement) {
					$objElement->delete();
				}
			} else {
				//*** Single element submitted.
				$objElement = Element::selectByPK($intElmntId);

				$intParent = $objElement->getParentId();
				$objElement->delete();
			}

			//*** Redirect the page.
			$strReturnTo = request('returnTo');
			if (empty($strReturnTo)) {
				header("Location: " . Request::getUri() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intParent);
				exit();
			} else {
				header("Location: " . Request::getURI() . $strReturnTo);
				exit();
			}

			break;

		case CMD_DUPLICATE:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrElements = explode(',', $intElmntId);
				$objElements = Element::selectByPK($arrElements);

				$intParent = $objElements->current()->getParentId();

				foreach ($objElements as $objElement) {
					$objElement->setUsername($objLiveUser->getProperty("name"));
					$objDuplicate = $objElement->duplicate($objLang->get("copyOf", "label"));

					//*** Update the search index.
					$objSearch = new Search();
					$objSearch->updateIndex($objDuplicate->getId());
				}
			} else {
				//*** Single element submitted.
				$objElement = Element::selectByPK($intElmntId);
				$intParent = $objElement->getParentId();

				$objElement->setUsername($objLiveUser->getProperty("name"));
				$objDuplicate = $objElement->duplicate($objLang->get("copyOf", "label"));

				//*** Update the search index.
				$objSearch = new Search();
				$objSearch->updateIndex($objDuplicate->getId());
			}

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

		case CMD_ACTIVATE:
		case CMD_DEACTIVATE:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrElements = explode(',', $intElmntId);
				$objElements = Element::selectByPK($arrElements);

				$intParent = $objElements->current()->getParentId();

				foreach ($objElements as $objElement) {
					if ($strCommand == CMD_ACTIVATE) {
						$objElement->setActive(1);
					} else {
						$objElement->setActive(0);
					}
					$objElement->save();
				}
			} else {
				//*** Single element submitted.
				$objElement = Element::selectByPK($intElmntId);
				$intParent = $objElement->getParentId();
				
				if ($strCommand == CMD_ACTIVATE) {
					$objElement->setActive(1);
				} else {
					$objElement->setActive(0);
				}
				$objElement->save();
			}

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

		case CMD_ADD:
		case CMD_EDIT:
		case CMD_ADD_FOLDER:
		case CMD_ADD_DYNAMIC:

			$objTpl->loadTemplatefile("elementfields.tpl.htm");
			$blnError = FALSE;
			$blnIsFolder = FALSE;
			$blnIsDynamic = FALSE;

			//*** Check the element type (element or folder)
			if ($strCommand == CMD_EDIT) {
				$objElement = Element::selectByPK($intElmntId);
				if (is_object($objElement) && $objElement->getTypeId() == ELM_TYPE_FOLDER) {
					$blnIsFolder = TRUE;
				} else if (is_object($objElement) && $objElement->getTypeId() == ELM_TYPE_DYNAMIC) {
					$blnIsDynamic = TRUE;
				}
			} else if ($strCommand == CMD_ADD_FOLDER) {
				$blnIsFolder = TRUE;
			} else if ($strCommand == CMD_ADD_DYNAMIC) {
				$blnIsDynamic = TRUE;
			}

			//*** Check if the rootfolder has been submitted.
			if ($strCommand == CMD_EDIT && $intElmntId == 0) {
				//*** Redirect to list mode.
				header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intElmntId);
				exit();
			}

			//*** Check if an invalid element has been submitted.
			if ($strCommand == CMD_EDIT && !is_object($objElement)) {
				//*** Redirect to list mode.
				header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=0");
				exit();
			}
			
			//*** Set section title.
			if ($blnIsFolder) {
				if ($strCommand == CMD_EDIT) {
					$objTpl->setVariable("MAINTITLE", $objLang->get("folderDetailsFor", "label"));
					$objTpl->setVariable("MAINSUB", $objElement->getName());
				} else {
					$objTpl->setVariable("MAINTITLE", $objLang->get("folderDetails", "label"));
				}
			} else if ($blnIsDynamic) {
				if ($strCommand == CMD_EDIT) {
					$objTpl->setVariable("MAINTITLE", $objLang->get("dynamicDetailsFor", "label"));
					$objTpl->setVariable("MAINSUB", $objElement->getName());
				} else {
					$objTpl->setVariable("MAINTITLE", $objLang->get("dynamicDetails", "label"));
				}
			} else {
				if ($strCommand == CMD_EDIT) {
					$objTpl->setVariable("MAINTITLE", $objLang->get("pageDetailsFor", "label"));
					$objTpl->setVariable("MAINSUB", $objElement->getName());
				} else {
					$objTpl->setVariable("MAINTITLE", $objLang->get("pageDetails", "label"));
				}
			}		

			//*** Post the element form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addElement") {
				//*** The element form has been posted.

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_active"])) {
					$objTpl->setVariable("ERROR_ACTIVE_ON", " error");
					$objTpl->setVariable("ERROR_ACTIVE", $objLang->get("active", "formerror"));
					$blnError = TRUE;
				}

				if ($strCommand == CMD_ADD_FOLDER || $blnIsFolder) {
					if (is_null($_CLEAN_POST["frm_ispage"])) {
						$objTpl->setVariable("ERROR_ISPAGE_ON", " error");
						$objTpl->setVariable("ERROR_ISPAGE", $objLang->get("isPage", "formerror"));
						$blnError = TRUE;
					}
				}

				if ($strCommand == CMD_ADD_DYNAMIC || $blnIsDynamic) {
					if (is_null($_CLEAN_POST["frm_feed"])) {
						$objTpl->setVariable("ERROR_FEED_ON", " error");
						$objTpl->setVariable("ERROR_FEED", $objLang->get("feed", "formerror"));
						$blnError = TRUE;
					}
					
					if (is_null($_CLEAN_POST["frm_feedpath"])) {
						$objTpl->setVariable("ERROR_FEEDPATH_ON", " error");
						$objTpl->setVariable("ERROR_FEEDPATH", $objLang->get("feedPath", "formerror"));
						$blnError = TRUE;
					}
					
					if (is_null($_CLEAN_POST["frm_maxitems"])) {
						$objTpl->setVariable("ERROR_MAXITEMS_ON", " error");
						$objTpl->setVariable("ERROR_MAXITEMS", $objLang->get("maxItems", "formerror"));
						$blnError = TRUE;
					}
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

				/*
				if (is_null($_CLEAN_POST["frm_alias"])) {
					$objTpl->setVariable("ERROR_ALIAS_ON", " error");
					$objTpl->setVariable("ERROR_ALIAS", $objLang->get("commonTypeWord", "formerror"));
					$blnError = TRUE;
				}
				*/

				if (is_null($_CLEAN_POST["frm_template"]) && !$blnIsFolder) {
					$objTpl->setVariable("ERROR_TEMPLATE_ON", " error");
					$objTpl->setVariable("ERROR_TEMPLATE", $objLang->get("commonTypeText", "formerror"));
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

				//*** Check element specific fields.
				//*** TODO!!

				if ($blnError === TRUE) {
					//*** Display global error.
					if ($blnIsFolder) {
						$objTpl->setVariable("FORM_ISPAGE_VALUE", (isset($_POST["frm_ispage"]) && $_POST["frm_ispage"] == "on") ? "checked=\"checked\"" : "");
					}
					$objTpl->setVariable("FORM_ACTIVE_VALUE", (isset($_POST["frm_active"]) && $_POST["frm_active"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_APINAME_VALUE", $_POST["frm_apiname"]);
					//$objTpl->setVariable("FORM_ALIAS_VALUE", $_POST["frm_alias"]);
					
					if ($blnIsDynamic) {
						$objTpl->setVariable("FORM_MAXITEMS_VALUE", $_POST["frm_maxitems"]);
					}
					
					$objTpl->setVariable("FORM_NOTES_VALUE", $_POST["frm_description"]);
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));

					//*** Display element specific errors.
					//*** TODO!!
				} else {
					//*** Input is valid. Save the element.
					if ($strCommand == CMD_EDIT) {
						$objElement = Element::selectByPK($intElmntId);
						$objParent = Element::selectByPK($objElement->getParentId());
					} else {
						$objParent = Element::selectByPK($_POST["eid"]);
						$objPermissions = new ElementPermission();

						if (is_object($objParent)) {
							$objPermissions->setUserId($objParent->getPermissions()->getUserId());
							$objPermissions->setGroupId($objParent->getPermissions()->getGroupId());
						}
						
						$objElement = new Element();
						$objElement->setParentId($_POST["eid"]);
						$objElement->setAccountId($_CONF['app']['account']->getId());
						$objElement->setPermissions($objPermissions);
					}
					$objElement->setActive((empty($_CLEAN_POST["frm_active"])) ? 0 : 1);
					$objElement->setIsPage((empty($_CLEAN_POST["frm_ispage"])) ? 0 : 1);
					$objElement->setName($_CLEAN_POST["frm_name"]);
					$objElement->setApiName($_CLEAN_POST["frm_apiname"]);
					$objElement->setDescription($_CLEAN_POST["frm_description"]);
					$objElement->setUsername($objLiveUser->getProperty("name"));
					
					//*** Get remote settings.
					$strServer = Setting::getValueByName('ftp_server');
					$strUsername = Setting::getValueByName('ftp_username');
					$strPassword = Setting::getValueByName('ftp_password');
					$strRemoteFolder = Setting::getValueByName('ftp_remote_folder');

					if ($blnIsFolder) {
						$objElement->setTypeId(ELM_TYPE_FOLDER);
					} else if ($blnIsDynamic) {
						$objElement->setTypeId(ELM_TYPE_DYNAMIC);
						$objElement->setTemplateId($_CLEAN_POST["frm_template"]);
					} else {
						$objElement->setTypeId(ELM_TYPE_ELEMENT);
						$objElement->setTemplateId($_CLEAN_POST["frm_template"]);
					}

					$objElement->save();
					
					if ($blnIsDynamic) {
						$intFeedId = $_CLEAN_POST["frm_feed"];
						if (empty($intFeedId)) $intFeedId = $objParent->getFeed()->getFeedId();
						
						$objElementFeed = new ElementFeed();
						$objElementFeed->setFeedId($intFeedId);
						$objElementFeed->setFeedPath($_CLEAN_POST["frm_feedpath"]);
						$objElementFeed->setMaxItems($_CLEAN_POST["frm_maxitems"]);
						if ($_CLEAN_POST["frm_dynamic_alias_check"]) {
							$objElementFeed->setAliasField($_CLEAN_POST["frm_dynamic_alias"]);
						} else {
							$objElementFeed->setAliasField("");
						}
												
						$objElement->setFeed($objElementFeed);
					}
										
					//*** Handle the Alias value.
					//$objElement->setAlias($_CLEAN_POST["frm_alias"]);
					
					//*** Handle the publish values.
					$objElement->clearSchedule();
					$objSchedule = new ElementSchedule();
					if (!empty($_CLEAN_POST["publish_start"])) {
						$strDate = $_CLEAN_POST["publish_start_date"];
						if (empty($strDate)) $strDate = strftime($_CONF['app']['universalDate']);
						$strDate = Date::convertDate($strDate, $_CONF['app']['universalDate'], "%d %B %Y");
						$strHour = (empty($_CLEAN_POST["publish_start_hour"])) ? "00" : $_CLEAN_POST["publish_start_hour"];
						$strMinute = (empty($_CLEAN_POST["publish_start_minute"])) ? "00" : $_CLEAN_POST["publish_start_minute"];
						$strDate = $strDate . " " . $strHour . ":" . $strMinute . ":00";

						$objSchedule->setStartActive(1);
						$objSchedule->setStartDate(Date::toMysql($strDate));							
					} else {
						//*** If not set we set the date to 0. This is nessecary for the client side library,
						$objSchedule->setStartActive(0);
						$objSchedule->setStartDate(APP_DEFAULT_STARTDATE);
					}

					if (!empty($_CLEAN_POST["publish_end"])) {
						$strDate = $_CLEAN_POST["publish_end_date"];
						if (empty($strDate)) $strDate = strftime($_CONF['app']['universalDate']);
						$strDate = Date::convertDate($strDate, $_CONF['app']['universalDate'], "%d %B %Y");
						$strHour = (empty($_CLEAN_POST["publish_end_hour"])) ? "00" : $_CLEAN_POST["publish_end_hour"];
						$strMinute = (empty($_CLEAN_POST["publish_end_minute"])) ? "00" : $_CLEAN_POST["publish_end_minute"];
						$strDate = $strDate . " " . $strHour . ":" . $strMinute . ":00";

						$objSchedule->setEndActive(1);
						$objSchedule->setEndDate(Date::toMysql($strDate));							
					} else {
						//*** If not set we set the date in the far future. This is nessecary for the client side library,
						$objSchedule->setEndActive(0);
						$objSchedule->setEndDate(APP_DEFAULT_ENDDATE);
					}
					$objElement->setSchedule($objSchedule);
					
					//*** Handle the meta values.
					if ($objElement->isPage()) {
						$objElement->clearMeta();
						$objElement->clearAliases();
						$arrFields = array("title", "keywords", "description");
						$objContentLangs = ContentLanguage::select();
						foreach ($objContentLangs as $objContentLanguage) {
							//*** Insert the value by language.
							foreach ($arrFields as $value) {
								$objMeta = new ElementMeta();
								$arrCascades = explode(",", request("frm_meta_{$value}_cascades"));
								$blnCascade = (in_array($objContentLanguage->getId(), $arrCascades)) ? 1 : 0;
								$objMeta->setName($value);
								$objMeta->setValue(request("frm_meta_{$value}_{$objContentLanguage->getId()}"));
								$objMeta->setLanguageId($objContentLanguage->getId());
								$objMeta->setCascade($blnCascade);
								$objElement->setMeta($objMeta);
							}
							
							$objAlias = new Alias();
							$arrCascades = explode(",", request("frm_meta_alias_cascades"));
							$blnCascade = (in_array($objContentLanguage->getId(), $arrCascades)) ? 1 : 0;
							$objAlias->setAlias(request("frm_meta_alias_{$objContentLanguage->getId()}"));
							$objAlias->setLanguageId($objContentLanguage->getId());
							$objAlias->setCascade($blnCascade);
							$objElement->setAlias($objAlias);
						}
					}

					//*** Handle element values.
					if (!$blnIsFolder) {
						//*** Cache and clear values.
						$objCachedFields = $objElement->getFields(TRUE);
						$objElement->clearFields();
						$objElement->clearLanguages();

						//*** Insert the active flag by language.
						$arrActives = explode(",", request("language_actives"));
						$objContentLangs = ContentLanguage::select();
						foreach ($objContentLangs as $objContentLanguage) {
							$blnActive = (in_array($objContentLanguage->getId(), $arrActives)) ? TRUE : FALSE;
							$objElement->setLanguageActive($objContentLanguage->getId(), $blnActive);
						}
						if ($strCommand == CMD_ADD) $objElement->setLanguageActive(ContentLanguage::getDefault()->getId(), TRUE);
						
						//*** Cache to handsome array.
						$arrFieldCache = array();
						foreach ($objCachedFields as $objCacheField) {
							foreach ($objContentLangs as $objContentLanguage) {
								if ($objCacheField->getTypeId() == FIELD_TYPE_FILE || $objCacheField->getTypeId() == FIELD_TYPE_IMAGE) {
									$arrFieldCache[$objCacheField->getTemplateFieldId()][$objContentLanguage->getId()] = $objCacheField->value[$objContentLanguage->getId()]->getValue();
								}
							}
						}
												
						foreach ($_REQUEST as $key => $value) {
							//*** Template Fields.
							if (substr($key, 0, 4) == "efv_") {
								//*** Get the template Id from the request
								$intTemplateFieldId = substr($key, 4);

								//*** Is the Id really an Id?
								if (is_numeric($intTemplateFieldId)) {
									$objTemplateField = TemplateField::selectByPK($intTemplateFieldId);
									$objField = new ElementField();
									$objField->setElementId($objElement->getId());
									$objField->setTemplateFieldId($intTemplateFieldId);
									$objField->save();

									//*** Get the cascade value for the currentfield.
									$arrCascades = explode(",", request("efv_{$intTemplateFieldId}_cascades"));

									//*** Loop through the languages to insert the value by language.
									$objContentLangs = ContentLanguage::select();
									foreach ($objContentLangs as $objContentLanguage) {
										//*** Insert the value by language.
										(in_array($objContentLanguage->getId(), $arrCascades)) ? $blnCascade = TRUE : $blnCascade = FALSE;
										$strValue = request("efv_{$intTemplateFieldId}_{$objContentLanguage->getId()}");
										
										//*** Check for certain type requirements.
										switch ($objTemplateField->getTypeId()) {
											case FIELD_TYPE_FILE:
											case FIELD_TYPE_IMAGE:
												$cacheFileValue = "";
												$arrCurrent = (is_array($strValue)) ? $strValue : array();
												foreach ($arrCurrent as $value) {
													if (!empty($value)) {
														$arrFile = explode(":", $value);
														if (count($arrFile) > 1 && !empty($arrFile[1])) {
															$cacheFileValue .= $value . "\n";
	
															//*** Remove file from cache.
															if (isset($arrFieldCache[$intTemplateFieldId]) && isset($arrFieldCache[$intTemplateFieldId][$objContentLanguage->getId()])) {
																$arrFieldCache[$intTemplateFieldId][$objContentLanguage->getId()] = str_replace($value, "", $arrFieldCache[$intTemplateFieldId][$objContentLanguage->getId()]);
															}
														}
													}
												}

												//*** Multifile SWFUpload
												foreach ($arrCurrent as $value) {
													if (!empty($value)) {
														$arrFile = explode(":", $value);
														if (count($arrFile) > 1 && empty($arrFile[1])) {
															//*** Any image manipulation?
															$strLocalValue = ImageField::filename2LocalName($arrFile[0]);
															
															$objImageField = new ImageField($intTemplateFieldId);
															$arrSettings = $objImageField->getSettings();
															if (count($arrSettings) > 1) {
																foreach ($arrSettings as $key => $arrSetting) {																	
																	$strFileName = FileIO::add2Base($strLocalValue, $arrSetting['key']);
																	if (copy($_PATHS['upload'] . $arrFile[0], $_PATHS['upload'] . $strFileName)) {							
																		if ($objTemplateField->getTypeId() == FIELD_TYPE_IMAGE && (
																				!empty($arrSetting['width']) ||
																				!empty($arrSetting['height']))) {										
																			
																			//*** Resize the image.
																			$intQuality = (empty($arrSetting['quality'])) ? 75 : $arrSetting['quality'];
																			ImageResizer::resize(
																				$_PATHS['upload'] . $strFileName, 
																				$arrSetting['width'],
																				$arrSetting['height'],
																				$arrSetting['scale'],
																				$intQuality,
																				TRUE,
																				NULL,
																				FALSE,
																				$arrSetting['grayscale']);		
																		}
																	
																		//*** Move file to remote server.
																		$objUpload = new SingleUpload();																		
																		if (!$objUpload->moveToFTP($strFileName, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
																			Log::handleError("File could not be moved to remote server. " . $objUpload->errorMessage());
																		}
																	}						
																}			
											
																//*** Move original file.
																if (rename($_PATHS['upload'] . $arrFile[0], $_PATHS['upload'] . $strLocalValue)) {	
																	$objUpload = new SingleUpload();																	
																	if (!$objUpload->moveToFTP($strLocalValue, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
																		Log::handleError("File could not be moved to remote server. " . $objUpload->errorMessage());
																	}
																}
																
																//*** Unlink original file.
																@unlink($_PATHS['upload'] . $arrFile[0]);
															} else {																
																if ($objTemplateField->getTypeId() == FIELD_TYPE_IMAGE && (
																		!empty($arrSettings[0]['width']) ||
																		!empty($arrSettings[0]['height']))) {
																							
																	$strFileName = FileIO::add2Base($strLocalValue, $arrSettings[0]['key']);
																	
																	//*** Resize the image.
																	if (rename($_PATHS['upload'] . $arrFile[0], $_PATHS['upload'] . $strFileName)) {	
																		$intQuality = (empty($arrSettings[0]['quality'])) ? 75 : $arrSettings[0]['quality'];
																		ImageResizer::resize(
																			$_PATHS['upload'] . $strFileName, 
																			$arrSettings[0]['width'],
																			$arrSettings[0]['height'],
																			$arrSettings[0]['scale'],
																			$intQuality,
																			TRUE,
																			NULL,
																			FALSE,
																			$arrSettings[0]['grayscale']);				
																
																		//*** Move file to remote server.
																		$objUpload = new SingleUpload();
																		if (!$objUpload->moveToFTP($strFileName, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
																			Log::handleError("File could not be moved to remote server.");
																		}
																	}																
																}
																
																//*** Move original file.
																if (file_exists($_PATHS['upload'] . $arrFile[0]) && rename($_PATHS['upload'] . $arrFile[0], $_PATHS['upload'] . $strLocalValue)) {	
																	//*** Move file to remote server.
																	$objUpload = new SingleUpload();
																	if (!$objUpload->moveToFTP($strLocalValue, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
																		Log::handleError("File could not be moved to remote server.");
																	}
																}
																
																//*** Unlink original file.
																@unlink($_PATHS['upload'] . $arrFile[0]);																
															}
																
															//*** Set file value.
															$cacheFileValue .= $arrFile[0] . ":" . $strLocalValue . "\n";
														}
													}
												}
																								
												//*** Check newly uploaded files.
												$strFiles = "efv_{$intTemplateFieldId}_{$objContentLanguage->getId()}_new";
												$fileValue = $cacheFileValue;
												if (isset($_FILES[$strFiles])) {
													if ($objTemplateField->getTypeId() == FIELD_TYPE_FILE) {
														$objValue = $objTemplateField->getValueByName("tfv_file_extension");
														$strExtensions = (is_object($objValue)) ? $objValue->getValue() : "";
														if (!empty($strExtensions)) {
															$strExtensions = str_replace("%s", Setting::getValueByName('file_upload_extensions'), $strExtensions);
															$objMultiUpload->setExtensions(explode(" ", strtolower($strExtensions)));
														} else {
															$objMultiUpload->setExtensions(explode(" ", strtolower(Setting::getValueByName('file_upload_extensions'))));
														}
													} else {
														$objMultiUpload->setExtensions(explode(" ", strtolower(Setting::getValueByName('image_upload_extensions'))));
													}
													$objMultiUpload->setTempNames($_FILES[$strFiles]['tmp_name']);
													$objMultiUpload->setOriginalNames($_FILES[$strFiles]['name']);
													$objMultiUpload->setErrors($_FILES[$strFiles]['error']);
													$objMultiUpload->uploadFiles();

													if ($objMultiUpload->getTotalFiles() == $objMultiUpload->getSuccessFiles()) {
														//*** Everything is cool.
														$localValues = $objMultiUpload->getLocalNames();

														//*** Any image manipulation?
														$blnResize = FALSE;
														$objImageField = new ImageField($intTemplateFieldId);
														$arrSettings = $objImageField->getSettings();
																												
														if ($objTemplateField->getTypeId() == FIELD_TYPE_IMAGE && (
																!empty($arrSettings[0]['width']) ||
																!empty($arrSettings[0]['height']))) {
																
															$blnResize = TRUE;
														}

														foreach ($objMultiUpload->getOriginalNames() as $subkey => $subvalue) {
															if (!empty($subvalue)) {
																$fileValue .= $subvalue . ":" . $localValues[$subkey] . "\n";
																
																//*** Resize the image.
																$intQuality = (empty($arrSettings[0]['quality'])) ? 75 : $arrSettings[0]['quality'];
																if ($blnResize) ImageResizer::resize(
																		$_PATHS['upload'] . $localValues[$subkey], 
																		$arrSettings[0]['width'],
																		$arrSettings[0]['height'],
																		$arrSettings[0]['scale'],
																		$intQuality,
																		TRUE,
																		NULL,
																		FALSE,
																		$arrSettings[0]['grayscale']);
															}
														}

														//*** Move file to remote server.
														if (!$objMultiUpload->moveToFTP($strServer, $strUsername, $strPassword, $strRemoteFolder)) {
															$strMessage = $objLang->get("moveToFTP", "alert");
															$fileValue = $cacheFileValue;
														}
													} else {
														$strMessage = $objMultiUpload->errorMessage() . "<br />";
														$strMessage .= "Files: " . $objMultiUpload->getTotalFiles() . " and Success: " . $objMultiUpload->getSuccessFiles();
													}
												}
												$strValue = $fileValue;
												break;
											
											case FIELD_TYPE_BOOLEAN:
												if ($strValue == "1") $strValue = "true";
												if (empty($strValue)) $strValue = "false";
												break;
										}
																				
										$objValue = $objField->getNewValueObject();
										$objValue->setValue($strValue);
										$objValue->setLanguageId($objContentLanguage->getId());
										$objValue->setCascade(($blnCascade) ? 1 : 0);

										$objField->setValueObject($objValue);
									}
								}
							}
							
							//*** Feed Fields.
							if (substr($key, 0, 4) == "tpf_") {
								//*** Get the template Id from the request
								$intTemplateFieldId = substr($key, 4);

								//*** Is the Id really an Id?
								if (is_numeric($intTemplateFieldId)) {
									//*** Get the cascade value for the currentfield.
									$arrCascades = explode(",", request("efv_{$intTemplateFieldId}_cascades"));

									//*** Loop through the languages to insert the value by language.
									$objContentLangs = ContentLanguage::select();
									foreach ($objContentLangs as $objContentLanguage) {
										//*** Insert the value by language.
										(in_array($objContentLanguage->getId(), $arrCascades)) ? $blnCascade = TRUE : $blnCascade = FALSE;
										$strValue = request("tpf_{$intTemplateFieldId}_{$objContentLanguage->getId()}");
										
										$objFeedField = new ElementFieldFeed();
										$objFeedField->setElementId($objElement->getId());
										$objFeedField->setTemplateFieldId($intTemplateFieldId);
										$objFeedField->setFeedPath(str_replace("----", "/", $strValue));
										$objFeedField->setXpath(str_replace("----", "/", $strValue));
										$objFeedField->setLanguageId($objContentLanguage->getId());
										$objFeedField->setCascade(($blnCascade) ? 1 : 0);
										$objFeedField->save();
									}
								}
							}
						}
												
						//*** Remove deleted files.
						$objFtp = new FTP($strServer);
						$objFtp->login($strUsername, $strPassword);
						$objFtp->pasv(TRUE);
						foreach ($arrFieldCache as $intTemplateFieldId => $arrLanguage) {
							foreach ($arrLanguage as $strValue) {
								$arrValues = explode("\n", $strValue);
								foreach ($arrValues as $value) {
									if (!empty($value)) {
										//*** Find file name.
										$arrFile = explode(":", $value);
										if (count($arrFile) > 1 && count($arrFile) < 3) {
											//*** Check if the file is used by other elements.
											if (!ElementField::fileHasDuplicates($value)) {
												//*** Remove file.
												$strFile = $strRemoteFolder . $arrFile[1];
												$objFtp->delete($strFile);
												
												//*** Resized variations?
												$objImageField = new ImageField($intTemplateFieldId);
												$arrSettings = $objImageField->getSettings();
												foreach ($arrSettings as $key => $arrSetting) {
													if (!empty($arrSetting['width']) ||	!empty($arrSetting['height'])) {
														//*** Remove file.
														$strFile = $strRemoteFolder . FileIO::add2Base($arrFile[1], $arrSetting['key']);
														$objFtp->delete($strFile);
													}		
												}		
											}										
										}
									}
								}
							}
						}

						//*** Update the search index.
						$objSearch = new Search();
						$objSearch->updateIndex($objElement->getId());
						
						//*** Clear cache if caching enabled.
						$objElement->clearCache($objFtp);
						$objElement->clearZeroCache($objFtp);
					} else {
						//*** Activate all languages for the folder type.
						$objContentLangs = ContentLanguage::select();
						foreach ($objContentLangs as $objContentLanguage) {
							$objElement->setLanguageActive($objContentLanguage->getId(), TRUE);
						}
					}
										
					//*** Redirect the page.
					if (empty($strMessage)) {
						header("Location: " . Request::getUri() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objElement->getParentId());
						exit();
					} else {
						$_SESSION['uiError'] = $strMessage;
						header("Location: " . Request::getUri() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_EDIT . "&eid=" . $objElement->getId() . "&err=1");
						exit();
					}
				}
			}

			//*** Parse the page.
			$objElement = Element::selectByPK($intElmntId);
			
			//*** Errors.
			if ($blnUiError) {
				$objTpl->setCurrentBlock("error-main");
				$objTpl->setVariable("ERROR_MAIN", $_SESSION['uiError']);
				$objTpl->parseCurrentBlock();
			}

			//*** Render the template pulldown.
			if ($blnIsFolder) {
				$objTpl->setCurrentBlock("headertitel_simple");
				$objTpl->setVariable("HEADER_TITLE", $objLang->get("details", "label"));
				$objTpl->parseCurrentBlock();

				$objTemplates = NULL;
			} else {
				$objTpl->setCurrentBlock("headertitel_simple");
				$objTpl->setVariable("HEADER_TITLE", $objLang->get("details", "label"));
				$objTpl->parseCurrentBlock();

				if (is_object($objElement)) {
					if ($strCommand == CMD_EDIT) {
						$objTemplate = Template::selectByPK($objElement->getTemplateId());
						$objTemplates = new DBA__Collection();
						$objTemplates->addObject($objTemplate);
					} else {
						$objTemplates = $objElement->getSubTemplates();
					}
				} else {
					$strSql = sprintf("SELECT * FROM pcms_template WHERE parentId = '0' AND accountId = '%s'", $_CONF['app']['account']->getId());
					$objTemplates = Template::select($strSql);
				}
			}

			if (is_object($objTemplates)) {
				foreach ($objTemplates as $objTemplate) {
					$objTpl->setCurrentBlock("list_template");
					$objTpl->setVariable("TEMPLATELIST_VALUE", $objTemplate->getId());
					$objTpl->setVariable("TEMPLATELIST_TEXT", $objTemplate->getName());
					$objTpl->parseCurrentBlock();
				}

				//*** Render fields if there is only one template.
				if ($objTemplates->count() == 1 || $strCommand == CMD_EDIT) {
					$strLanguageBlock = ($blnIsDynamic) ? "feed.list_language" : "list_language";					
					
					$intDefaultLanguage = ContentLanguage::getDefault()->getId();
					$intSelectLanguage = $intDefaultLanguage;

					$objContentLangs = ContentLanguage::select();
					foreach ($objContentLangs as $objContentLanguage) {
						$objTpl->setCurrentBlock($strLanguageBlock);
						$objTpl->setVariable("LANGUAGELIST_VALUE", $objContentLanguage->getId());

						if ($intDefaultLanguage == $objContentLanguage->getId()) {
							$objTpl->setVariable("LANGUAGELIST_TEXT", $objContentLanguage->getName() . " (" . $objLang->get("default", "label") . ")");
						} else {
							$objTpl->setVariable("LANGUAGELIST_TEXT", $objContentLanguage->getName());
						}

						if ($intSelectLanguage == $objContentLanguage->getId()) $objTpl->setVariable("LANGUAGELIST_SELECTED", " selected=\"selected\"");

						$objTpl->parseCurrentBlock();
					}
					
					$objTemplates->rewind();
					$objFields = $objTemplates->current()->getFields();

					$objTpl->setVariable("LABEL_ELEMENT_FIELDS", $objLang->get("elementFields", "label"));
					
					$strFields = "";

					if (!$blnIsDynamic) {
						foreach ($objFields as $objField) {
							$objFieldTpl = new HTML_Template_ITX($_PATHS['templates']);
							$objFieldTpl->loadTemplatefile("elementfield.tpl.htm");
	
							//*** Get the field value from the element.
							$strValue = "";
							if (is_object($objElement)) {
								$strValue = $objElement->getValueByTemplateField($objField->getId());
							}
							$strDescription = $objField->getDescription();
	
							//*** Get the field type object.
							$objType = TemplateFieldType::selectByPK($objField->getTypeId());
	
							$intMaxFileCount = null;
							
							switch ($objField->getTypeId()) {
								case FIELD_TYPE_DATE:
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.date', 'elementfield_date.tpl.htm');
	
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.{$objType->getInput()}.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
										
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId(), TRUE);
											$strValue = Date::fromMysql($_CONF['app']['universalDate'], $strValue);
										} else {
											$strValue = "";
										}
										
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", htmlspecialchars($strValue));
										$objFieldTpl->parseCurrentBlock();
									}
	
									$objValue = $objField->getValueByName("tfv_field_format");
									$strFormatValue = (is_object($objValue)) ? $objValue->getValue() : "";
	
									$objFieldTpl->setCurrentBlock("field.date");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_DATE_FORMAT", $strFormatValue);
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_LARGETEXT:
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.textarea', 'elementfield_textarea.tpl.htm');
	
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.{$objType->getInput()}.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId());
										} else {
											$strValue = "";
										}
																			
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", str_replace("$", "&#36;", htmlspecialchars($strValue)));
	
										$objFieldTpl->parseCurrentBlock();
									}
	
									//*** Parse the special FCKeditor oncomplete section.
									$objTpl->setCurrentBlock("field_{$objType->getInput()}_oncomplete_value");
									$objTpl->setVariable("ELEMENT_FIELD_ID", "efv_{$objField->getId()}");
										
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objTpl->setVariable("ELEMENT_FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objTpl->parseCurrentBlock();
									
									$oFCKeditor = new FCKeditor("efv_{$objField->getId()}");
									$oFCKeditor->BasePath = 'libraries/fckeditor/';
									$oFCKeditor->Config['DefaultLanguage'] = $objLang->get("abbr");
									$oFCKeditor->Width = "490";
	
									//*** Calculate and set the textarea height.
									$minHeight = 165;
									$maxHeight = 400;
									$intHeight = $minHeight;
									$objValue = $objField->getValueByName("tfv_field_max_characters");
									$strMaxChar = (is_object($objValue)) ? $objValue->getValue() : "";
									if (!empty($strMaxChar) && is_numeric($strMaxChar)) {
										$intHeight = (($strMaxChar - 500) * 0.05) + $minHeight;
										if ($intHeight < $minHeight) $intHeight = $minHeight;
										if ($intHeight > $maxHeight) $intHeight = $maxHeight;
									}
									$oFCKeditor->Height = "{$intHeight}";
	
									$objFieldTpl->setCurrentBlock("field.textarea");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									$objFieldTpl->setVariable("FIELD_TEXTAREA", $oFCKeditor->CreateHtml());
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_SELECT_LIST_SINGLE:
								case FIELD_TYPE_SELECT_LIST_MULTI:
									if ($objField->getTypeId() == FIELD_TYPE_SELECT_LIST_SINGLE) {
										$objDefaultValue = $objField->getValueByName("tfv_list_default");
										$objValue = $objField->getValueByName("tfv_list_value");
										$strFieldClass = "select-one";
										$strMultiple = "";
									} else {
										$objDefaultValue = $objField->getValueByName("tfv_multilist_default");
										$objValue = $objField->getValueByName("tfv_multilist_value");
										$strFieldClass = "select-multiple";
										$strMultiple = "multiple=\"multiple\"";
									}
									
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.select', 'elementfield_selectlist.tpl.htm');
									
									$strTemplValue = (is_object($objDefaultValue)) ? $objDefaultValue->getValue() : "";
										
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.select.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										//*** Determine the selected value for the list.
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId());
										} else {
											$strValue = NULL;
										}
										
										if (!empty($strValue) || !is_null($strValue)) {
											//*** Do Nothing.
										} elseif (!empty($strTemplValue)) {
											$strValue = $strTemplValue;
										}
										$arrDefaultValue = explode("\n", $strValue);
										$arrValue = array();
										foreach ($arrDefaultValue as $value) {
											$value = trim($value);
											if (!empty($value)) array_push($arrValue, $value);
										}
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", implode(",", $arrValue));
	
										$objFieldTpl->parseCurrentBlock();
									}
	
									//*** Render options for the list.
									$strListValue = (is_object($objValue)) ? $objValue->getValue() : "";
									$arrValues = explode("\n", $strListValue);
	
									foreach ($arrValues as $value) {
										if (!empty($value)) {
											//*** Determine if we have a label.
											$arrValue = explode(":", $value);
											if (count($arrValue) > 1) {
												$optionLabel = trim($arrValue[0]);
												$optionValue = trim($arrValue[1]);
											} else {
												$optionLabel = trim($value);
												$optionValue = trim($value);
											}
	
											$objFieldTpl->setCurrentBlock("field.select.option");
											$objFieldTpl->setVariable("FIELD_VALUE", $optionValue);
											$objFieldTpl->setVariable("FIELD_TEXT", xhtmlsave($optionLabel));
											$objFieldTpl->parseCurrentBlock();
										}
									}
	
									$objFieldTpl->setCurrentBlock("field.select");
									$objFieldTpl->setVariable("FIELD_SELECT_SIZE", 1);
									$objFieldTpl->setVariable("FIELD_CLASS", $strFieldClass);
									$objFieldTpl->setVariable("FIELD_MULTIPLE", $strMultiple);
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_CHECK_LIST_SINGLE:
								case FIELD_TYPE_CHECK_LIST_MULTI:
									if ($objField->getTypeId() == FIELD_TYPE_CHECK_LIST_SINGLE) {
										$objDefaultValue = $objField->getValueByName("tfv_list_default");
										$objValue = $objField->getValueByName("tfv_list_value");
										$strType = "radio";
									} else {
										$objDefaultValue = $objField->getValueByName("tfv_multilist_default");
										$objValue = $objField->getValueByName("tfv_multilist_value");
										$strType = "checkbox";
									}
									
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.check', 'elementfield_checklist.tpl.htm');
									
									$strTemplValue = (is_object($objDefaultValue)) ? $objDefaultValue->getValue() : "";
										
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.check.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										//*** Determine the selected value for the list.
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId());
										} else {
											$strValue = NULL;
										}
										
										if (!empty($strValue) || !is_null($strValue)) {
											//*** Do Nothing.
										} elseif (!empty($strTemplValue)) {
											$strValue = $strTemplValue;
										}
										$arrDefaultValue = explode("\n", $strValue);
										$arrValue = array();
										foreach ($arrDefaultValue as $value) {
											$value = trim($value);
											if (!empty($value)) array_push($arrValue, $value);
										}
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", implode(",", $arrValue));
	
										$objFieldTpl->parseCurrentBlock();
									}
	
									//*** Render options for the list.
									$strListValue = (is_object($objValue)) ? $objValue->getValue() : "";
									$arrValues = explode("\n", $strListValue);
									$intCount = 0;
	
									foreach ($arrValues as $value) {
										if (!empty($value)) {
											//*** Determine if we have a label.
											$arrValue = explode(":", $value);
											if (count($arrValue) > 1) {
												$optionLabel = trim($arrValue[0]);
												$optionValue = trim($arrValue[1]);
											} else {
												$optionLabel = trim($value);
												$optionValue = trim($value);
											}
	
											$objFieldTpl->setCurrentBlock("field.check.item");
											$objFieldTpl->setVariable("SUBFIELD_TYPE", $strType);
											$objFieldTpl->setVariable("SUBFIELD_VALUE", $optionValue);
											$objFieldTpl->setVariable("SUBFIELD_TEXT", $optionLabel);
											$objFieldTpl->setVariable("SUBFIELD_ID", "efv_{$objField->getId()}_sub_$intCount");
											$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
											$objFieldTpl->parseCurrentBlock();
											
											
											$intCount++;
										}
									}
									$objFieldTpl->setCurrentBlock("field.list");
									$objFieldTpl->setVariable("SUBFIELD_TYPE", $strType);
									$objFieldTpl->parseCurrentBlock();
	
									$objFieldTpl->setCurrentBlock("field.check");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_IMAGE:
									$objValue = $objField->getValueByName('tfv_image_count');
									$intMaxFileCount = (is_object($objValue)) ? $objValue->getValue() : 10000;
									$strCurrentTitle = $objLang->get("imagesCurrent", "label");
									$strNewTitle = $objLang->get("imagesNew", "label");
									$strThumbPath = Setting::getValueByName("web_server") . Setting::getValueByName("file_folder");
									$strUploadPath = Request::getURI() . $_CONF['app']['baseUri'] . "files/";
									
								case FIELD_TYPE_FILE:
									if (!isset($intMaxFileCount)) {
										$objValue = $objField->getValueByName('tfv_file_count');
										$intMaxFileCount = (is_object($objValue)) ? $objValue->getValue() : 10000;
										$strCurrentTitle = $objLang->get("filesCurrent", "label");
										$strNewTitle = $objLang->get("filesNew", "label");
										$strThumbPath = Setting::getValueByName("web_server") . Setting::getValueByName("file_folder");
										$strUploadPath = Request::getURI() . $_CONF['app']['baseUri'] . "files/";
									}
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
									}
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.file', 'elementfield_file.tpl.htm');
	
									foreach ($objContentLangs as $objContentLanguage) {
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId(), TRUE);
										} else {
											$strValue = "";
										}
										
										$intFileCount = 0;
										if (!empty($strValue)) {
											$arrValues = explode("\n", $strValue);
	
											foreach ($arrValues as $value) {
												if (!empty($value)) {
													$arrValue = explode(":", $value);
													if (count($arrValue) > 1) {
														$strValue = $arrValue[1];
														$strLabel = $arrValue[0];
	
														//*** Media library item?
														if (count($arrValue) > 2) {
															$strValue = $arrValue[1] . ":" . $arrValue[2];
														}
													} else {
														$strValue = $arrValue[0];
														$strLabel = $arrValue[0];
													}
	
													$intFileCount++;
													
													$objFieldTpl->setCurrentBlock("field.file.edit");
													$objFieldTpl->setVariable("FIELD_LANGUAGE_ID_COUNT", "efv_{$objField->getId()}_{$objContentLanguage->getId()}_{$intFileCount}");
													$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
													$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", "{$strLabel}:{$strValue}");
													$objFieldTpl->parseCurrentBlock();
												}
											}
										}			
										
										
										$objFieldTpl->setCurrentBlock("field.file.value");						
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_CURRENT_FILES", $intFileCount);
										
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ALTTEXT_VALUE", "");
										$objFieldTpl->parseCurrentBlock();
									}
	
									$intFileCount = 0;
									if (!empty($strValue)) {
										$arrValues = explode("\n", $strValue);
							
										foreach ($arrValues as $value) {
											if (!empty($value)) {
												$arrValue = explode(":", $value);
												if (count($arrValue) > 1) {
													$strValue = $arrValue[1];
													$strLabel = $arrValue[0];
												} else {
													$strValue = $arrValue[0];
													$strLabel = $arrValue[0];
												}
	
												if ($objField->getTypeId() == FIELD_TYPE_IMAGE) {
													$objFieldTpl->setCurrentBlock("thumbnail");
													$objFieldTpl->setVariable("FIELD_ORIGINAL_VALUE", $strLabel);
													$objFieldTpl->setVariable("FIELD_VALUE", $strValue);
													$objFieldTpl->parseCurrentBlock();
												}
												$objFieldTpl->setCurrentBlock("field.{$objType->getInput()}.edit");
												$objFieldTpl->setVariable("FIELD_FILE_ID", "efv_{$objField->getId()}");
												$objFieldTpl->setVariable("FIELD_ORIGINAL_VALUE", $strLabel);
												$objFieldTpl->setVariable("FIELD_VALUE", $strValue);
												$objFieldTpl->parseCurrentBlock();
												
												$intFileCount++;
											}
										}
									}								
	
									//*** Parse the rest of the block.
									$objFieldTpl->setCurrentBlock("field.file.select-type.library");
									$objFieldTpl->setVariable("LABEL_LIBRARY", $objLang->get("pcmsInlineStorage", "menu"));
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									$objFieldTpl->parseCurrentBlock();
									
									$objFieldTpl->setCurrentBlock("field.file.select-type.upload");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									$objFieldTpl->parseCurrentBlock();
									
									$objFieldTpl->setCurrentBlock("field.file");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									$objFieldTpl->setVariable("FIELD_BROWSE_NAME", $objLang->get("browseImage", "label"));
									//$objFieldTpl->setVariable("FIELD_ALT_NAME", $objLang->get("altImage", "label"));
									$objFieldTpl->setVariable("FIELD_CURRENT_FILES", $intFileCount);
									$objFieldTpl->setVariable("FIELD_MAX_FILES", $intMaxFileCount);
									$objFieldTpl->setVariable("FIELD_THUMB_PATH", $strThumbPath);
									$objFieldTpl->setVariable("FIELD_UPLOAD_PATH", $strUploadPath);
									$objFieldTpl->setVariable("FIELD_MAX_CHAR", 60);
									$objFieldTpl->setVariable("STORAGE_ITEMS", StorageItems::getFolderListHTML());
									$objFieldTpl->setVariable("LABEL_CHOOSE_FOLDER", $objLang->get("chooseFolder", "label"));
									$objFieldTpl->setVariable("FIELD_HEADER_CURRENT", $strCurrentTitle);
									$objFieldTpl->setVariable("FIELD_HEADER_NEW", $strNewTitle);
									$objFieldTpl->setVariable("FIELD_LABEL_REMOVE", $objLang->get("delete", "button"));
									$objFieldTpl->setVariable("FIELD_LABEL_CANCEL", strtolower($objLang->get("cancel", "button")));
									$objFieldTpl->setVariable("FIELD_LABEL_ALT", $objLang->get("alttag", "button"));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									if (is_object($objElementField)) {
										$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
									}
									
									if ($objField->getTypeId() == FIELD_TYPE_FILE) {
										$objValue = $objField->getValueByName("tfv_file_extension");
										$strExtensions = (is_object($objValue)) ? $objValue->getValue() : "";
										if (!empty($strExtensions)) {
											$strExtensions = str_replace("%s", Setting::getValueByName('file_upload_extensions'), $strExtensions);
										} else {
											$strExtensions = strtolower(Setting::getValueByName('file_upload_extensions'));
										}
									} else {
										$strExtensions = strtolower(Setting::getValueByName('image_upload_extensions'));
									}
									$objFieldTpl->setVariable("FIELD_FILE_TYPE", "*" . implode("; *", explode(" ", $strExtensions)));
									
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_SMALLTEXT:
								case FIELD_TYPE_NUMBER:
								case FIELD_TYPE_LINK:
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.text', 'elementfield_text.tpl.htm');
									
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.text.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										if (is_object($objElement)) {
											$strValue = htmlspecialchars($objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId()));
										} else {
											$strValue = "";
										}
	
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
	
										$objFieldTpl->parseCurrentBlock();
									}
	
									$objFieldTpl->setCurrentBlock("field.text");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_SIMPLETEXT:
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.simpletext', 'elementfield_simpletext.tpl.htm');
									
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.simpletext.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										if (is_object($objElement)) {
											$strValue = htmlspecialchars($objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId()));
										} else {
											$strValue = "";
										}
	
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
	
										$objFieldTpl->parseCurrentBlock();
									}
									
									//*** Calculate and set the textarea height.
									$minHeight = 115;
									$maxHeight = 400;
									$intHeight = $minHeight;
									$objValue = $objField->getValueByName("tfv_field_max_characters");
									$strMaxChar = (is_object($objValue)) ? $objValue->getValue() : "";
									if (!empty($strMaxChar) && is_numeric($strMaxChar)) {
										$intHeight = (($strMaxChar - 500) * 0.05) + $minHeight;
										if ($intHeight < $minHeight) $intHeight = $minHeight;
										if ($intHeight > $maxHeight) $intHeight = $maxHeight;
									}
	
									$objFieldTpl->setCurrentBlock("field.simpletext");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									$objFieldTpl->setVariable("FIELD_HEIGHT", "{$intHeight}px");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();
									break;
	
								case FIELD_TYPE_USER:
									$strFieldClass = "select-one";
									
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.select', 'elementfield_selectlist.tpl.htm');
																		
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.select.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										//*** Determine the selected value for the list.
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId());
										} else {
											$strValue = "";
										}
										
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
	
										$objFieldTpl->parseCurrentBlock();
									}
	
									//*** Render options for the list.
									global $objLiveAdmin;
									$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
									$objUsers = $objLiveAdmin->getUsers($filters);
									if (is_array($objUsers)) {
										foreach ($objUsers as $objUser) {
											$objFieldTpl->setCurrentBlock("field.select.option");
											$objFieldTpl->setVariable("FIELD_VALUE", $objUser["perm_user_id"]);
											$objFieldTpl->setVariable("FIELD_TEXT", xhtmlsave($objUser["handle"]));
											$objFieldTpl->parseCurrentBlock();
										}
									}
	
									$objFieldTpl->setCurrentBlock("field.select");
									$objFieldTpl->setVariable("FIELD_SELECT_SIZE", 1);
									$objFieldTpl->setVariable("FIELD_CLASS", $strFieldClass);
									$objFieldTpl->setVariable("FIELD_MULTIPLE", "");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();								
									break;
	
								case FIELD_TYPE_BOOLEAN:
									$objDefaultValue = $objField->getValueByName("tfv_boolean_default");
									$strTemplValue = (is_object($objDefaultValue)) ? $objDefaultValue->getValue() : "";
										
									$objFieldTpl->addBlockfile('ELEMENT_FIELD', 'field.checkbox', 'elementfield_checkbox.tpl.htm');
	
									foreach ($objContentLangs as $objContentLanguage) {
										$objFieldTpl->setCurrentBlock("field.checkbox.value");
										$objFieldTpl->setVariable("FIELD_LANGUAGE_ID", "efv_{$objField->getId()}_{$objContentLanguage->getId()}");
	
										if (is_object($objElement)) {
											$strValue = $objElement->getValueByTemplateField($objField->getId(), $objContentLanguage->getId());
										} else {
											$strValue = NULL;
										}
																			
										if (!empty($strValue) || !is_null($strValue)) {
											//*** Do Nothing.
										} elseif (!empty($strTemplValue)) {
											$strValue = $strTemplValue;
										}
										
										$objFieldTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
	
										$objFieldTpl->parseCurrentBlock();
									}
									
									$objFieldTpl->setCurrentBlock("field.checkbox");
									$objFieldTpl->setVariable("FIELD_ID", "efv_{$objField->getId()}");
									if ($objField->getRequired()) $objFieldTpl->setVariable("FIELD_REQUIRED", "* ");
									$objFieldTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
									$objFieldTpl->setVariable("FIELD_VALUE", $strValue);
									if (!empty($strDescription)) $objFieldTpl->setVariable("FIELD_DESCRIPTION", $objField->getDescription());
									
									if (is_object($objElement)) {
										$objElementField = $objElement->getFieldByTemplateField($objField->getId());
										if (is_object($objElementField)) {
											$objFieldTpl->setVariable("FIELD_CASCADES", implode(",", $objElementField->getCascades()));
										}
									}
									
									$objFieldTpl->parseCurrentBlock();
									break;
							}
	
							$strFields .= $objFieldTpl->get();
						}
					}

					if (!empty($strFields)) $objTpl->setVariable("ELEMENT_FIELDS", $strFields);
					if (!$blnIsDynamic) {
						$objTpl->setVariable("LABEL_LANGUAGE", $objLang->get("language", "form"));
						$objTpl->setVariable("ACTIVE_LANGUAGE", $intDefaultLanguage);
						$objTpl->setVariable("DEFAULT_LANGUAGE", $intDefaultLanguage);
					} else {
						$objTpl->setCurrentBlock("feedlanguage");
						$objTpl->setVariable("LABEL_LANGUAGE", $objLang->get("language", "form"));
						$objTpl->setVariable("ACTIVE_LANGUAGE", $intDefaultLanguage);
						$objTpl->setVariable("DEFAULT_LANGUAGE", $intDefaultLanguage);
						$objTpl->parseCurrentBlock();
					}
					
					
					//*** Meta tab.
					if (is_object($objElement) && $objElement->isPage()) {
						$objTpl->setCurrentBlock("meta-title");
						$objTpl->setVariable("HEADER", $objLang->get("meta", "label"));
						$objTpl->parseCurrentBlock();
						$objTpl->setCurrentBlock("description-meta");
						$objTpl->setVariable("LABEL", $objLang->get("metaInfo", "form"));
						$objTpl->parseCurrentBlock();
	
						//*** Meta specific labels
						$objTpl->setVariable("LABEL_META_ALIAS", $objLang->get("alias", "form"));
						$objTpl->setVariable("LABEL_META_TITLE", $objLang->get("metaTitle", "label"));
						$objTpl->setVariable("LABEL_META_KEYWORDS", $objLang->get("metaKeywords", "label"));
						$objTpl->setVariable("LABEL_META_DESCRIPTION", $objLang->get("metaDescription", "label"));
						$objTpl->setVariable("META_KEYWORDS_NOTE", $objLang->get("metaKeywords", "tip"));
						$objTpl->setVariable("META_DESCRIPTION_NOTE", $objLang->get("metaDescription", "tip"));	
						$objTpl->setVariable("META_ALIAS_NOTE", $objLang->get("alias", "tip"));	
						$objTpl->setVariable("ACTIVE_META_LANGUAGE", $intSelectLanguage);
						$objTpl->setVariable("DEFAULT_META_LANGUAGE", $intDefaultLanguage);			
						$objTpl->setVariable("LABEL_META_LANGUAGE", $objLang->get("language", "form"));
	
						//*** Meta languages					
						$objContentLangs = ContentLanguage::select();
						foreach ($objContentLangs as $objContentLanguage) {
							$objTpl->setCurrentBlock("list_meta-language");
							$objTpl->setVariable("LANGUAGELIST_VALUE", $objContentLanguage->getId());
							if ($intDefaultLanguage == $objContentLanguage->getId()) {
								$objTpl->setVariable("LANGUAGELIST_TEXT", $objContentLanguage->getName() . " (" . $objLang->get("default", "label") . ")");
							} else {
								$objTpl->setVariable("LANGUAGELIST_TEXT", $objContentLanguage->getName());
							}
							if ($intSelectLanguage == $objContentLanguage->getId()) $objTpl->setVariable("LANGUAGELIST_SELECTED", " selected=\"selected\"");
							$objTpl->parseCurrentBlock();
						}
						
						//*** Meta language values.
						foreach ($objContentLangs as $objContentLanguage) {
							$strValue = $objElement->getAlias($objContentLanguage->getId());
							$objTpl->setCurrentBlock("field.meta_alias.value");
							$objTpl->setVariable("FIELD_ALIAS_ID", "frm_meta_alias_{$objContentLanguage->getId()}");
							$objTpl->setVariable("FIELD_ALIAS_VALUE", $strValue);
							$objTpl->parseCurrentBlock();
							
							$objMeta = (is_object($objElement)) ? $objElement->getMeta($objContentLanguage->getId()) : NULL;
														
							$strValue = (is_object($objMeta)) ? $objMeta->getValueByValue("name", "title") : "";
							$objTpl->setCurrentBlock("field.meta_title.value");
							$objTpl->setVariable("FIELD_LANGUAGE_ID", "frm_meta_title_{$objContentLanguage->getId()}");
							$objTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
							$objTpl->parseCurrentBlock();
							
							$strValue = (is_object($objMeta)) ? $objMeta->getValueByValue("name", "keywords") : "";
							$objTpl->setCurrentBlock("field.meta_keywords.value");
							$objTpl->setVariable("FIELD_LANGUAGE_ID", "frm_meta_keywords_{$objContentLanguage->getId()}");
							$objTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
							$objTpl->parseCurrentBlock();
							
							$strValue = (is_object($objMeta)) ? $objMeta->getValueByValue("name", "description") : "";
							$objTpl->setCurrentBlock("field.meta_description.value");
							$objTpl->setVariable("FIELD_LANGUAGE_ID", "frm_meta_description_{$objContentLanguage->getId()}");
							$objTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
							$objTpl->parseCurrentBlock();
						}
						
						//*** Meta language cascades.
						$objTpl->setVariable("META_ALIAS_CASCADES", implode(",", Alias::getCascades($objElement->getId())));
						$objTpl->setVariable("META_TITLE_CASCADES", implode(",", ElementMeta::getCascades($objElement->getId(), "title")));
						$objTpl->setVariable("META_KEYWORDS_CASCADES", implode(",", ElementMeta::getCascades($objElement->getId(), "keywords")));
						$objTpl->setVariable("META_DESCRIPTION_CASCADES", implode(",", ElementMeta::getCascades($objElement->getId(), "description")));
					}
				}
											
				//*** Feeds if dynamic.
				if ($blnIsDynamic) {			
					if ($strCommand == CMD_EDIT) {
						$objElementFeed = $objElement->getFeed();
						
						$objFeed = Feed::selectByPK($objElementFeed->getFeedId());
						$objFeeds = new DBA__Collection();
						$objFeeds->addObject($objFeed);
						
						$objParent = Element::selectByPK($objElement->getParentId());
					} else {	
						$objFeeds = Feed::select();
					
						$objParent = Element::selectByPK($intElmntId);
					}

					if (isset($objParent) && $objParent->getTypeId() == ELM_TYPE_DYNAMIC) {
						$objNodes = $objParent->getFeed()->getStructuredNodes();
						
						$objTpl->setCurrentBlock("list_feedpath");
						$objTpl->setVariable("VALUE", "");
						$objTpl->setVariable("TEXT", "Basepath");
						$objTpl->parseCurrentBlock();
						
						$objTpl->setCurrentBlock("list_feedpath");
						$objTpl->setVariable("VALUE", "");
						$objTpl->setVariable("TEXT", "-------------");
						$objTpl->parseCurrentBlock();
						
						if (count($objNodes) > 0) {
							foreach ($objNodes as $objSubElement) {
								$objTpl->setCurrentBlock("list_feedpath");
								$objTpl->setVariable("VALUE", $objSubElement->getName());
								$objTpl->setVariable("TEXT", $objSubElement->getName());
								$objTpl->parseCurrentBlock();
							}
						}
					} else {
						if (is_object($objFeeds)) {
							foreach ($objFeeds as $objFeed) {
								$objTpl->setCurrentBlock("list_feed");
								$objTpl->setVariable("FEEDLIST_VALUE", $objFeed->getId());
								$objTpl->setVariable("FEEDLIST_TEXT", $objFeed->getName());
								$objTpl->parseCurrentBlock();
							}
						}
					}
										
					if ($strCommand == CMD_EDIT) {								
						$blnDynamicAlias = false;		
						$objFeedFields = $objElementFeed->getStructuredNodes();	
						foreach ($objFeedFields as $objFeedField) {
							$objTpl->setCurrentBlock("list_feed_field");
							$objTpl->setVariable("FEEDLIST_VALUE", $objFeedField->getName());
							$objTpl->setVariable("FEEDLIST_TEXT", $objFeedField->getName());
							if ($objElementFeed->getAliasField() == $objFeedField->getName()) {
								$objTpl->setVariable("FEEDLIST_SELECTED", "selected=\"selected\"");
								$blnDynamicAlias = true;
							}
							$objTpl->parseCurrentBlock();
						}
						
						if ($blnDynamicAlias) {
							$objTpl->setVariable("FORM_DYNAMIC_ALIAS_VALUE", "checked=\"checked\"");
						}
						
						$objTpl->setVariable("FORM_MAXITEMS_VALUE", $objElementFeed->getMaxItems());
						
						//*** Template fields.
						foreach ($objFields as $objField) {								
							foreach ($objContentLangs as $objContentLanguage) {
								$objTpl->setCurrentBlock("feed.field.value");
								$objTpl->setVariable("FIELD_LANGUAGE_ID", "tpf_{$objField->getId()}_{$objContentLanguage->getId()}");

								if (is_object($objElement)) {
									$strValue = htmlspecialchars($objElement->getFeedValueByTemplateField($objField->getId(), $objContentLanguage->getId()));
								} else {
									$strValue = "";
								}

								$objTpl->setVariable("FIELD_LANGUAGE_VALUE", $strValue);
								$objTpl->parseCurrentBlock();
							}

							$objTpl->setCurrentBlock("feed.field");
							$objTpl->setVariable("FIELD_ID", "tpf_{$objField->getId()}");
							$objTpl->setVariable("FIELD_NAME", html_entity_decode($objField->getName()));
							
							if (is_object($objElement)) {
								$objFeedField = $objElement->getFeedFieldByTemplateField($objField->getId());
								if (is_object($objFeedField)) {
									$objTpl->setVariable("FIELD_CASCADES", implode(",", $objFeedField->getCascades()));
								}
							}
								
							$objTpl->parseCurrentBlock();
						}
						
						//*** Feed fields.
						$objFeedFields = $objElementFeed->getStructuredNodes();
						$strFields = renderRecursiveFeedFields($objFeedFields);
						$objTpl->setCurrentBlock("feed.tag");
						$objTpl->setVariable("FEEDFIELDS", $strFields);
						$objTpl->parseCurrentBlock();
					}
				}
			}

			//*** Render the element form.
			$objTpl->setCurrentBlock("description-details");
			$objTpl->setVariable("LABEL", $objLang->get("requiredFields", "form"));
			$objTpl->parseCurrentBlock();
			
			$objTpl->setVariable("LABEL_ACTIVE", $objLang->get("active", "form"));
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			//$objTpl->setVariable("LABEL_ALIAS", $objLang->get("alias", "form"));
			$objTpl->setVariable("APINAME_NOTE", $objLang->get("apiNameNote", "tip"));
			//$objTpl->setVariable("ALIAS_NOTE", $objLang->get("alias", "tip"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));
			if (isset($objElement) && $objElement->getTypeId() == ELM_TYPE_LOCKED) {
				$objTpl->setVariable("DISABLED_SAVE", "disabled=\"disabled\"");
			}

			if ($blnIsFolder) {
				$objTpl->setVariable("LABEL_ELEMENTNAME", $objLang->get("folderName", "form"));
				$objTpl->setVariable("LABEL_ISPAGE", $objLang->get("pageContainer", "form"));
				if ($blnError === FALSE && is_object($objElement)) {
					$objTpl->setVariable("FORM_ISPAGE_VALUE", ($objElement->getIsPage()) ? "checked=\"checked\"" : "");
				}
			} else {			
				$objTpl->setVariable("LABEL_ELEMENTNAME", $objLang->get("elementName", "form"));
				$objTpl->setVariable("LABEL_TEMPLATENAME", $objLang->get("template", "form"));
				
				if ($blnIsDynamic) {
					if (isset($objParent) && $objParent->getTypeId() == ELM_TYPE_DYNAMIC) {
						$objTpl->setVariable("LABEL_FEEDPATH", $objLang->get("basepath", "form"));
					} else {
						$objTpl->setVariable("LABEL_FEEDNAME", $objLang->get("feed", "form"));	
					}					
					$objTpl->setVariable("LABEL_MAXITEMS", $objLang->get("maxItems", "form"));
				}
			}
			
			//*** Predefine schedule variables.
			$intStartHour = 8;
			$intStartMinute = 0;
			$intEndHour = 17;
			$intEndMinute = 0;

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT) {
				if ($blnError === FALSE) {
					$objTpl->setVariable("FORM_ACTIVE_VALUE", ($objElement->getActive()) ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", str_replace("\"", "&quot;", $objElement->getName()));
					$objTpl->setVariable("FORM_APINAME_VALUE", $objElement->getApiname());
					//$objTpl->setVariable("FORM_ALIAS_VALUE", $objElement->getAlias());
					$objTpl->setVariable("FORM_NOTES_VALUE", $objElement->getDescription());
				}
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$objElement->getParentId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$objElement->getParentId()}&amp;cmd=" . CMD_LIST);
				if (!$blnIsFolder && $objElement->getTypeId() != ELM_TYPE_DYNAMIC) {					
					$objTpl->setVariable("ACTIVES_LANGUAGE", implode(",", $objElement->getLanguageActives()));
				}
				
				//*** Publish specific values.
				$objSchedule = $objElement->getSchedule();
				
				if ($objSchedule->getStartActive()) {
					$strValue = Date::fromMysql("%d %B %Y", $objSchedule->getStartDate());
					$objTpl->setVariable("START_DATE_DISPLAY", (empty($strValue)) ? "&nbsp;" : $strValue);

					$objTpl->setVariable("START_DATE_VALUE", Date::fromMysql($_CONF['app']['universalDate'], $objSchedule->getStartDate()));
					
					$strValue = Date::fromMysql("%H", $objSchedule->getStartDate());
					if (!empty($strValue)) $intStartHour = $strValue;

					$strValue = Date::fromMysql("%M", $objSchedule->getStartDate());
					if (!empty($strValue)) $intStartMinute = $strValue;
					
					$objTpl->setVariable("START_DATE_ACTIVE", "checked=\"checked\"");
				} else {					
					$objTpl->setVariable("START_DATE_DISPLAY", "&nbsp;");
				}
				
				if ($objSchedule->getEndActive()) {
					$strValue = Date::fromMysql("%d %B %Y", $objSchedule->getEndDate());
					$objTpl->setVariable("END_DATE_DISPLAY", (empty($strValue)) ? "&nbsp;" : $strValue);
					
					$objTpl->setVariable("END_DATE_VALUE", Date::fromMysql($_CONF['app']['universalDate'], $objSchedule->getEndDate()));

					$strValue = Date::fromMysql("%H", $objSchedule->getEndDate());
					if (!empty($strValue)) $intEndHour = $strValue;

					$strValue = Date::fromMysql("%M", $objSchedule->getEndDate());
					if (!empty($strValue)) $intEndMinute = $strValue;
				
					$objTpl->setVariable("END_DATE_ACTIVE", "checked=\"checked\"");
				} else {					
					$objTpl->setVariable("END_DATE_DISPLAY", "&nbsp;");
				}
			} else {
				if ($blnError === FALSE) {
					if (Setting::getValueByName('elmnt_active_state') == 1) {
						$objTpl->setVariable("FORM_ACTIVE_VALUE", "checked=\"checked\"");
					}
				}
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_ELEMENTS . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				
				//*** Publish specific values.
				$objTpl->setVariable("START_DATE_DISPLAY", "&nbsp;");
				$objTpl->setVariable("END_DATE_DISPLAY", "&nbsp;");
			}
			
			//*** Render tabs.
			if (is_object($objTemplates) && ($objTemplates->count() == 1 || $strCommand == CMD_EDIT)) {
				if (!$blnIsFolder) {
					//*** Fields tab.
					$objTpl->setCurrentBlock("field-title");
					$objTpl->setVariable("HEADER", $objLang->get("fields", "label"));
					$objTpl->parseCurrentBlock();
					$objTpl->setCurrentBlock("description-fields");
					$objTpl->setVariable("LABEL", $objLang->get("requiredFields", "form"));
					$objTpl->parseCurrentBlock();
				}
				
				//*** Permissions tab.
//				$objTpl->setCurrentBlock("permission-title");
//				$objTpl->setVariable("HEADER", $objLang->get("permissions", "label"));
//				$objTpl->parseCurrentBlock();
//				$objTpl->setCurrentBlock("description-permission");
//				$objTpl->setVariable("LABEL", $objLang->get("permissionInfo", "form"));
//				$objTpl->parseCurrentBlock();
				
			}

			//*** Publish tab.			
			$objTpl->setCurrentBlock("publish-title");
			$objTpl->setVariable("HEADER", $objLang->get("publish", "label"));
			$objTpl->parseCurrentBlock();
			$objTpl->setCurrentBlock("description-publish");
			$objTpl->setVariable("LABEL", $objLang->get("publishInfo", "form"));
			$objTpl->parseCurrentBlock();
			
			//*** Publish specific labels
			$objTpl->setVariable("LABEL_START_DATE", $objLang->get("startDate", "label"));
			$objTpl->setVariable("LABEL_END_DATE", $objLang->get("endDate", "label"));
			$objTpl->setVariable("LABEL_DATE", $objLang->get("date", "label"));
			$objTpl->setVariable("LABEL_TIME", $objLang->get("time", "label"));
			
			foreach (range(0, 23) as $hour) {
				$objTpl->setCurrentBlock("date.start.hour");
				$objTpl->setVariable("VALUE", $hour);
				$objTpl->setVariable("LABEL", str_pad($hour, 2, 0, STR_PAD_LEFT));
				if (trim($intStartHour) == $hour) $objTpl->setVariable("SELECTED", "selected=\"selected\"");
				$objTpl->parseCurrentBlock();
			}
			
			foreach (range(0, 45, 15) as $minute) {
				$objTpl->setCurrentBlock("date.start.minute");
				$objTpl->setVariable("VALUE", $minute);
				$objTpl->setVariable("LABEL", str_pad($minute, 2, 0, STR_PAD_LEFT));
				if (trim($intStartMinute) == $minute) $objTpl->setVariable("SELECTED", "selected=\"selected\"");
				$objTpl->parseCurrentBlock();
			}
			
			foreach (range(0, 23) as $hour) {
				$objTpl->setCurrentBlock("date.end.hour");
				$objTpl->setVariable("VALUE", $hour);
				$objTpl->setVariable("LABEL", str_pad($hour, 2, 0, STR_PAD_LEFT));
				if (trim($intEndHour) == $hour) $objTpl->setVariable("SELECTED", "selected=\"selected\"");
				$objTpl->parseCurrentBlock();
			}
			
			foreach (range(0, 45, 15) as $minute) {
				$objTpl->setCurrentBlock("date.end.minute");
				$objTpl->setVariable("VALUE", $minute);
				$objTpl->setVariable("LABEL", str_pad($minute, 2, 0, STR_PAD_LEFT));
				if (trim($intEndMinute) == $minute) $objTpl->setVariable("SELECTED", "selected=\"selected\"");
				$objTpl->parseCurrentBlock();
			}
			
			
			$objTpl->setVariable("LANG", strtolower($objLang->get("abbr")));

			//*** Render the element form.
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_ELEMENTS);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);

			break;

	}

	return $objTpl->get();
}

function renderRecursiveFeedFields($objFeedFields, $strXPath = "") {
	$strReturn = "";
	
	foreach ($objFeedFields as $objFeedField) {
		$strPath = (empty($strXPath)) ? $objFeedField->getName() : $strXPath . "----" . $objFeedField->getName();
		$strReturn .= "<li id=\"ff_" . $strPath . "\">" . $objFeedField->getName();
		$objChildren = $objFeedField->children();
		if (count($objChildren) > 0) {
			$strReturn .= "<ul>";
			$strReturn .= renderRecursiveFeedFields($objChildren, $strPath);
			$strReturn .= "</ul>";
		}
		$strReturn .= "</li>";
	}

	return $strReturn;
}

?>
