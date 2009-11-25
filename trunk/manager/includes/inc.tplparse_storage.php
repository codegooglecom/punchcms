<?php

function parseFiles($intElmntId, $strCommand) {
	global 	$objLang,
			$_CLEAN_POST,
			$objLiveUser,
			$_CONF,
			$_PATHS,
			$DBAConn,
			$objMultiUpload;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);

	switch ($strCommand) {
		case CMD_LIST:
			$objTpl->loadTemplatefile("multiview.tpl.htm");
			$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsStorage", "menu"));

			$objFolder = StorageItem::selectByPK($intElmntId);

			if (empty($intElmntId)) {
				$strFolderName = "ROOT";
			} else {
				if (is_object($objFolder)) {
					$strFolderName = $objFolder->getName();
				} else {
					$strFolderName = "";
				}
			}

			if (is_object($objFolder) || empty($intElmntId)) {
				if (empty($intElmntId)) {
					$objItems = StorageItems::getFromParent(0);
				} else {
					$objItems = StorageItems::getFromParent($intElmntId);
				}

				if (is_object($objItems) && $objItems->count() > 0) {
					//*** Initiate child item loop.
					$blnBreak = false;
					$listCount = 0;
					$intPosition = request("pos");
					$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
					$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];
					$objItems->seek($intPosition);

					//*** Loop through the items.
					foreach ($objItems as $objItem) {
						$strMeta = $objLang->get("editedBy", "label") . " " . $objItem->getUsername() . ", " . Date::fromMysql($objLang->get("datefmt"), $objItem->getModified());

						$objTpl->setCurrentBlock("multiview-item");
						$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
						$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:StorageItem.remove({$objItem->getId()});");
						$objTpl->setVariable("BUTTON_DUPLICATE", $objLang->get("duplicate", "button"));
						$objTpl->setVariable("BUTTON_DUPLICATE_HREF", "javascript:StorageItem.duplicate({$objItem->getId()});");

						$objTpl->setVariable("MULTIITEM_VALUE", $objItem->getId());
						$objTpl->setVariable("MULTIITEM_HREF", "href=\"?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$objItem->getId()}&amp;cmd=" . CMD_EDIT . "\"");
						$objTpl->setVariable("MULTIITEM_NAME", $objItem->getName());
						$objTpl->setVariable("MULTIITEM_META", $strMeta);
						
						switch ($objItem->getTypeId()) {
							case STORAGE_TYPE_FOLDER:
								$objTpl->setVariable("MULTIITEM_TYPE_CLASS", "folder");
								break;
							case STORAGE_TYPE_FILE:
								$objTpl->setVariable("MULTIITEM_TYPE_CLASS", "element");
								break;
						}
						
						$objTpl->parseCurrentBlock();

						$listCount++;
						if ($listCount >= $_SESSION["listCount"]) break;
					}

					//*** Render page navigation.
					$pageCount = ceil($objItems->count() / $_SESSION["listCount"]);
					if ($pageCount > 0) {
						$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
						$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
						$nextPos = (($intPosition + $_SESSION["listCount"]) < $objItems->count()) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

						$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
						$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
						$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId&amp;pos=$previousPos");
						$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
						$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId&amp;pos=$nextPos");

						//*** Top page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-top");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId&amp;pos=$position\"");
							}
							$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
							$objTpl->parseCurrentBlock();
						}

						//*** Bottom page navigation.
						for ($intCount = 0; $intCount < $pageCount; $intCount++) {
							$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
							$position = $intCount * $_SESSION["listCount"];
							if ($intCount != $intPosition / $_SESSION["listCount"]) {
								$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId&amp;pos=$position\"");
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
			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId\"");

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

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_STORAGE . "&amp;eid=$intElmntId");
			$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
			$objTpl->setVariable("LIST_ACTION_ONCHANGE", "StorageItem.multiDo(this, this[this.selectedIndex].value)");
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
			$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:StorageItem.multiSelect()");
			$objTpl->setVariable("BUTTON_NEWSUBJECT", $objLang->get("newFile", "button"));
			$objTpl->setVariable("BUTTON_NEWSUBJECT_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD);
			$objTpl->setVariable("BUTTON_NEWFOLDER", $objLang->get("newFolder", "button"));
			$objTpl->setVariable("BUTTON_NEWFOLDER_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_ADD_FOLDER);

			if ($intElmntId > 0) {
				$objTpl->setVariable("BUTTON_EDIT", $objLang->get("edit", "button"));
				$objTpl->setVariable("BUTTON_EDIT_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_EDIT);
			}

			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("removeFolder", "button"));
			$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:StorageItem.remove({$intElmntId});");
			$objTpl->setVariable("LABEL_SUBJECT", $objLang->get("mediaIn", "label") . " ");
			$objTpl->setVariable("SUBJECT_NAME", $strFolderName);

			$objTpl->parseCurrentBlock();

			break;

		case CMD_REMOVE:
			if (strpos($intElmntId, ',') !== FALSE) {
				//*** Multiple elements submitted.
				$arrElements = explode(',', $intElmntId);
				$objElements = StorageItem::selectByPK($arrElements);

				$intParent = $objElements->current()->getParentId();

				foreach ($objElements as $objElement) {
					$objElement->delete();
				}
			} else {
				//*** Single element submitted.
				$objElement = StorageItem::selectByPK($intElmntId);

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
				$objElements = StorageItem::selectByPK($arrElements);

				$intParent = $objElements->current()->getParentId();

				foreach ($objElements as $objElement) {
					$objElement->setUsername($objLiveUser->getProperty("name"));
					$objDuplicate = $objElement->duplicate($objLang->get("copyOf", "label"));
				}
			} else {
				//*** Single element submitted.
				$objElement = StorageItem::selectByPK($intElmntId);

				$objElement->setUsername($objLiveUser->getProperty("name"));
				$intParent = $objElement->getParentId();
				$objDuplicate = $objElement->duplicate($objLang->get("copyOf", "label"));
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

			$objTpl->loadTemplatefile("storageitems.tpl.htm");
			$blnError = FALSE;
			$blnIsFolder = FALSE;

			//*** Check the element type (element or folder)
			if ($strCommand == CMD_EDIT) {
				$objElement = StorageItem::selectByPK($intElmntId);
				if (is_object($objElement) && $objElement->getTypeId() == STORAGE_TYPE_FOLDER) {
					$blnIsFolder = TRUE;
				}
			} else if ($strCommand == CMD_ADD_FOLDER) {
				$blnIsFolder = TRUE;
			}

			//*** Check if the rootfolder has been submitted.
			if ($strCommand == CMD_EDIT && $intElmntId == 0) {
				//*** Redirect to list mode.
				header("Location: " . Request::getURI() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST . "&eid=" . $intElmntId);
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
			} else {
				if ($strCommand == CMD_EDIT) {
					$objTpl->setVariable("MAINTITLE", $objLang->get("fileDetailsFor", "label"));
					$objTpl->setVariable("MAINSUB", $objElement->getName());
				} else {
					$objTpl->setVariable("MAINTITLE", $objLang->get("fileDetails", "label"));
				}
			}	

			//*** Post the element form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "addStorageItem") {
				//*** The element form has been posted.

				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_name"]) && $strCommand != CMD_ADD) {
					$objTpl->setVariable("ERROR_NAME_ON", " error");
					$objTpl->setVariable("ERROR_NAME", $objLang->get("templateName", "formerror"));
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
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_NOTES_VALUE", $_POST["frm_description"]);
					$objTpl->setVariable("ERROR_MAIN", $objLang->get("main", "formerror"));

					//*** Display element specific errors.
					//*** TODO!!
				} else {
					//*** Input is valid. Save the element.
					if ($blnIsFolder || $strCommand == CMD_EDIT && is_array(Request::get('frm_file'))) {
						if ($strCommand == CMD_EDIT) {
							$objElement = StorageItem::selectByPK($intElmntId);
						} else {						
							$objElement = new StorageItem();
							$objElement->setParentId($_POST["eid"]);
							$objElement->setAccountId($_CONF['app']['account']->getId());
						}

						$objElement->setName($_CLEAN_POST["frm_name"]);
						$objElement->setDescription($_CLEAN_POST["frm_description"]);
						$objElement->setUsername($objLiveUser->getProperty("name"));
						
						if ($blnIsFolder) {
							$objElement->setTypeId(STORAGE_TYPE_FOLDER);
						} else {
							$objElement->setTypeId(STORAGE_TYPE_FILE);						
						}

						$objElement->save();
					} else {
						//*** Get remote settings.
						$strServer = Setting::getValueByName('ftp_server');
						$strUsername = Setting::getValueByName('ftp_username');
						$strPassword = Setting::getValueByName('ftp_password');
						$strRemoteFolder = Setting::getValueByName('ftp_remote_folder');
												
						if ($strCommand == CMD_EDIT) {
							$objElement = StorageItem::selectByPK($intElmntId);
							$objElement->setName($_CLEAN_POST["frm_name"]);
							$objElement->setDescription($_CLEAN_POST["frm_description"]);
							$objElement->setUsername($objLiveUser->getProperty("name"));
							$objElement->save();
							
							$objData = $objElement->getData();
							$strOldFile = $objData->getLocalName();
						}
																				
						if (isset($_FILES['frm_file_new'])) {	
							$objMultiUpload->setExtensions(explode(" ", Setting::getValueByName('file_upload_extensions') . " " . Setting::getValueByName('image_upload_extensions')));
							$objMultiUpload->setTempNames($_FILES['frm_file_new']['tmp_name']);
							$objMultiUpload->setOriginalNames($_FILES['frm_file_new']['name']);
							$objMultiUpload->setErrors($_FILES['frm_file_new']['error']);
							$objMultiUpload->uploadFiles();

							if ($objMultiUpload->getTotalFiles() == $objMultiUpload->getSuccessFiles()) {
								//*** Everything is cool.
								$localValues = $objMultiUpload->getLocalNames();
								$arrCleanup = array();
								foreach ($objMultiUpload->getOriginalNames() as $subkey => $subvalue) {
									$blnSkipData = FALSE;
									
									if (!empty($subvalue)) {										
										if ($strCommand == CMD_ADD) {
											if (FileIO::extension($subvalue) == "zip") {
												//*** Zip file. Extract and add.
												require_once('dzip/dUnzip2.inc.php');
												
												$blnSkipData = TRUE;
												
												$strZip = $_PATHS['upload'] .  $localValues[$subkey];
												$strTempDir = Account::generateId();
												$strTempPath = $_PATHS['upload'] . $strTempDir . "/";
												if (is_file($strZip)) {
													$objZip = new dUnzip2($strZip);
													if (is_object($objZip)) {
														array_push($arrCleanup, $localValues[$subkey]);
														
														mkdir($strTempPath);
														$objZip->unzipAll($strTempPath);
														
														if ($handle = opendir($strTempPath)) {
														    while (false !== ($file = readdir($handle))) {
														        if (is_file($strTempPath . $file)) {
																	$objElement = new StorageItem();
																	$objElement->setParentId($_POST["eid"]);
																	$objElement->setAccountId($_CONF['app']['account']->getId());
																	$objElement->setName($file);
																	$objElement->setDescription($_CLEAN_POST["frm_description"]);
																	$objElement->setUsername($objLiveUser->getProperty("name"));
																	$objElement->setTypeId(STORAGE_TYPE_FILE);	
																	$objElement->save();
																	
																	$objData = $objElement->getData();										
																	$objData->setItemId($objElement->getId());
																	$objData->setOriginalName($file);
																	$objData->setLocalName($file);
																	$objData->save();
																	
																	//*** Move file to remote server.
																	$objUpload = new SingleUpload();																		
																	if (!$objUpload->moveToFTP($file, $strTempPath, $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
																		Log::handleError("File could not be moved to remote server. " . $objUpload->errorMessage());
																	}
														        }
														    }
														
														    closedir($handle);
														}
														
														FileIO::unlinkDir($strTempPath);
													}
												}
											} else {
												$objElement = new StorageItem();
												$objElement->setParentId($_POST["eid"]);
												$objElement->setAccountId($_CONF['app']['account']->getId());
												$objElement->setName((empty($_CLEAN_POST["frm_name"])) ? $subvalue : $_CLEAN_POST["frm_name"]);
												$objElement->setDescription($_CLEAN_POST["frm_description"]);
												$objElement->setUsername($objLiveUser->getProperty("name"));
												$objElement->setTypeId(STORAGE_TYPE_FILE);	
												$objElement->save();
												
												$objData = $objElement->getData();
											}
										}
										
										if (!$blnSkipData) {
											$objData->setItemId($objElement->getId());
											$objData->setOriginalName($subvalue);
											$objData->setLocalName($localValues[$subkey]);
											$objData->save();
										}
									}
								}

								//*** Move file to remote server.
								if (!$objMultiUpload->moveToFTP($strServer, $strUsername, $strPassword, $strRemoteFolder)) {
									$strMessage = $objMultiUpload->errorMessage();
								}
								
								//*** Fix file linkage.
								if (is_object($objElement)) $objElement->fixLinkedElements();
								
								//*** Cleanup zip files.
								foreach ($arrCleanup as $value) {
									$objFtp = new FTP($strServer);
									$objFtp->login($strUsername, $strPassword);
									$strFile = $strRemoteFolder . $value;
									echo "Delete file " . $strFile;
									$objFtp->delete($strFile);
								}
							} else {
								$strMessage = $objMultiUpload->errorMessage() . "<br />";
								$strMessage .= "Files: " . $objMultiUpload->getTotalFiles() . " and Success: " . $objMultiUpload->getSuccessFiles();
							}
						}
												
						//*** Remove deleted files.
						if ($strCommand == CMD_EDIT && !empty($strOldFile)) {
							$objFtp = new FTP($strServer);
							$objFtp->login($strUsername, $strPassword);
							$strFile = $strRemoteFolder . $strOldFile;
							$objFtp->delete($strFile);
						}
					}

					//*** Redirect the page.
					if (empty($strMessage)) {
						header("Location: " . Request::getUri() . "/?cid=" . $_POST["cid"] . "&cmd=" . CMD_LIST . "&eid=" . $objElement->getParentId());
						exit();
					} else {
						echo $strMessage;
					}
				}
			}

			//*** Parse the page.
			$objElement = StorageItem::selectByPK($intElmntId);

			//*** Render the details tab.
			$objTpl->setCurrentBlock("headertitel_simple");
			$objTpl->setVariable("HEADER_TITLE", $objLang->get("details", "label"));
			$objTpl->parseCurrentBlock();

			//*** Render the element form.
			$objTpl->setCurrentBlock("description-details");
			$objTpl->setVariable("LABEL", $objLang->get("requiredFields", "form"));
			$objTpl->parseCurrentBlock();
			
			$objTpl->setVariable("LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("LABEL_REQUIRED", $objLang->get("requiredFields", "form"));
			$objTpl->setVariable("LABEL_SAVE", $objLang->get("save", "button"));

			if ($blnIsFolder) {
				$objTpl->setVariable("LABEL_ELEMENTNAME", $objLang->get("folderName", "form"));
				$objTpl->setVariable("LABEL_NOTES", $objLang->get("notes", "form"));
			} else {
				$objTpl->setVariable("LABEL_ELEMENTNAME", $objLang->get("fileName", "form"));
				$objTpl->setVariable("LABEL_ELEMENTNAME_TIP", $objLang->get("storageName", "tip"));
				$objTpl->setVariable("LABEL_CHOOSER", $objLang->get("browseImage", "label"));
				$objTpl->setVariable("LABEL_NOTES", $objLang->get("description", "form"));
				$objTpl->setVariable("FIELD_LABEL_REMOVE", $objLang->get("delete", "button"));
				$objTpl->setVariable("FIELD_THUMB_PATH", Setting::getValueByName("web_server") . Setting::getValueByName("file_folder"));
			}

			//*** Insert values if action is edit.
			if ($strCommand == CMD_EDIT) {
				if ($blnError === FALSE) {
					$objTpl->setVariable("FORM_NAME_VALUE", $objElement->getName());
					$objTpl->setVariable("FORM_NOTES_VALUE", $objElement->getDescription());
					
					if (!$blnIsFolder) {
						$objData = $objElement->getData();
						if (is_object($objData)) {
							$objTpl->setVariable("FORM_CHOOSER_VALUE", $objData->getOriginalName() . ":" . $objData->getLocalName());
						}
						$objTpl->setVariable("FIELD_CURRENT_FILES", 1);
						$objTpl->setVariable("FIELD_MAX_FILES", 1);
					}
				}
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$objElement->getParentId()}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$objElement->getParentId()}&amp;cmd=" . CMD_LIST);
			} else {
				if (!$blnIsFolder) {
					$objTpl->setVariable("FIELD_CURRENT_FILES", 0);
					$objTpl->setVariable("FIELD_MAX_FILES", 50);
				}
					
				$objTpl->setVariable("BUTTON_CANCEL_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
				$objTpl->setVariable("BUTTON_FORMCANCEL_HREF", "?cid=" . NAV_PCMS_STORAGE . "&amp;eid={$intElmntId}&amp;cmd=" . CMD_LIST);
			}

			//*** Render the element form.
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("back", "button"));
			$objTpl->setVariable("BUTTON_FORMCANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("CID", NAV_PCMS_STORAGE);
			$objTpl->setVariable("CMD", $strCommand);
			$objTpl->setVariable("EID", $intElmntId);

			break;

	}

	return $objTpl->get();
}

?>