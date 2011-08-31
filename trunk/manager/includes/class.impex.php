<?php

/*
#########################################################################
#  Copyright (c) 2005-2006. Punch Software. All Rights Reserved.
#
#  Punch software [both binary and source (if released)] (hereafter,
#  Software) is intellectual property owned by Punch Software and
#  phixel.org and is copyright of Punch Software and phixel.org in all
#  countries in the world, and ownership remains with Punch Software and
#  phixel.org.
#
#  You (hereafter, Licensee) are not allowed to distribute the binary and
#  source code (if released) to third parties. Licensee is not allowed to
#  reverse engineer, disassemble or decompile code, or make any
#  modifications of the binary or source code, remove or alter any
#  trademark, logo, copyright or other proprietary notices, legends,
#  symbols, or labels in the Software.
#
#  Licensee is not allowed to sub-license the Software or any derivative
#  work based on or derived from the Software.
#
#  The Licensee acknowledges and agrees that the software is delivered
#  'as is' without warranty and without any support services (unless
#  agreed otherwise with Punch Software or phixel.org). Punch Software
#  and phixel.org make no warranties, either expressed or implied, as to
#  the software and its derivatives.
#
#  It is understood by Licensee that neither Punch Software nor
#  phixel.org shall be liable for any loss or damage that may arise,
#  including any indirect special or consequential loss or damage in
#  connection with or arising from the performance or use of the
#  software, including fitness for any particular purpose.
#
#  By using or copying this Software, Licensee agrees to abide by the
#  copyright law and all other applicable laws of The Netherlands
#  including, but not limited to, export control laws, and the terms of
#  this licence. Punch Software and/or phixel.org shall have the right to
#  terminate this licence immediately by written notice upon Licensee's
#  breach of, or non-compliance with, any of its terms. Licensee may be
#  held legally responsible for any copyright infringement that is caused
#  or encouraged by Licensee's failure to abide by the terms of this
#  licence.
#########################################################################
*/

/*
 * ImpEx Class v0.2.18
 * Exports and imports account data from the database.
 */
 
require_once('dzip/dZip.inc.php');
require_once('dzip/dUnzip2.inc.php');

$intDefaultLanguage = 0;
set_time_limit(60*60);

class ImpEx {

	public static function export($intAccountId = 0, $exportFiles = true) {
		global $objLiveAdmin,
				$_CONF,
				$_PATHS;

		//*** Init DOM object.
		$objDoc = new DOMDocument("1.0", "UTF-8");
		$objDoc->formatOutput = FALSE;
		$objDoc->preserveWhiteSpace = TRUE;
		
		//*** Init Zip archive.
		$strZipName = $_PATHS['upload'] . "exportZip_" . rand() . ".zip";
		$objZip = new dZip($strZipName, TRUE);
	
		//*** ACL. Users, groups and rights.
		$objAcl = self::exportAcl($objDoc, $intAccountId);
		
		//*** Products.
		$_CONF['app']['account'] = Account::getById($intAccountId);
		$objAccountProducts = AccountProduct::getByAccountId($intAccountId);
		$objProducts = $objDoc->createElement('products');
		foreach ($objAccountProducts as $objAccountProduct) {
			switch ($objAccountProduct->getProductId()) {
				case PRODUCT_PCMS:
					$arrFiles = array();
					
					//*** Settings.
					$objSettings = $objDoc->createElement('settings');

					$objDbSettings = SettingTemplate::select("SELECT * FROM pcms_setting_tpl ORDER BY section, sort");
					foreach ($objDbSettings as $objDbSetting) {
						$objSetting = $objDoc->createElement('setting');
						$objSetting->setAttribute("name", $objDbSetting->getName());
						$objSetting->setAttribute("value", Setting::getValueByName($objDbSetting->getName(), $intAccountId));
						$objSetting->setAttribute("section", $objDbSetting->getSection());
						$objSettings->appendChild($objSetting);
					}

					//*** Languages.
					$objLanguages = $objDoc->createElement('languages');

					$objContentLangs = ContentLanguage::select();
					foreach ($objContentLangs as $objContentLang) {
						$objLanguage = $objDoc->createElement('language');
						$objLanguage->setAttribute("id", $objContentLang->getId());
						$objLanguage->setAttribute("name", $objContentLang->getName());
						$objLanguage->setAttribute("abbr", $objContentLang->getAbbr());
						$objLanguage->setAttribute("default", $objContentLang->default);
						$objLanguage->setAttribute("active", $objContentLang->getActive());
						$objLanguage->setAttribute("sort", $objContentLang->getSort());
						$objLanguage->setAttribute("username", $objContentLang->getUsername());
						$objLanguages->appendChild($objLanguage);
					}

					//*** External feeds.
					$objFeeds = $objDoc->createElement('feeds');

					$objDbFeeds = Feed::select();
					foreach ($objDbFeeds as $objDbFeed) {
						$objFeed = $objDoc->createElement('feed');
						$objFeed->setAttribute("id", $objDbFeed->getId());
						$objFeed->setAttribute("name", $objDbFeed->getName());
						$objFeed->setAttribute("feed", $objDbFeed->getFeed());
						$objFeed->setAttribute("basepath", $objDbFeed->getBasepath());
						$objFeed->setAttribute("refresh", $objDbFeed->getRefresh());
						$objFeed->setAttribute("lastUpdate", $objDbFeed->getLastUpdate());
						$objFeed->setAttribute("active", $objDbFeed->getActive());
						$objFeed->setAttribute("sort", $objDbFeed->getSort());
						$objFeed->setAttribute("created", $objDbFeed->getCreated());
						$objFeed->setAttribute("modified", $objDbFeed->getModified());
						$objFeeds->appendChild($objFeed);
					}

					//*** Storage items.
					$objStorage = self::exportStorage($objDoc, $intAccountId, 0, $arrFiles);

					//*** Templates.
					$objTemplates = self::exportTemplate($objDoc, $intAccountId, 0);

					//*** Elements.
					$objElements = self::exportElement($objDoc, $intAccountId, 0, $arrFiles);

					//*** Aliases.
					$objAliases = $objDoc->createElement('aliases');

					$objDbAliases = Alias::select();
					foreach ($objDbAliases as $objDbAlias) {
						$objAlias = $objDoc->createElement('alias');
						$objAlias->setAttribute("alias", $objDbAlias->getAlias());
						$objAlias->setAttribute("url", $objDbAlias->getUrl());
						$objAlias->setAttribute("active", $objDbAlias->getActive());
						$objAlias->setAttribute("sort", $objDbAlias->getSort());
						$objAlias->setAttribute("created", $objDbAlias->getCreated());
						$objAlias->setAttribute("modified", $objDbAlias->getModified());
						$objAliases->appendChild($objAlias);
					}
					
					//*** Product.
					$objProduct = $objDoc->createElement('pcms');
					$objProduct->setAttribute("version", APP_VERSION);
					$objProduct->setAttribute("expires", $objAccountProduct->getExpires());
					$objProduct->appendChild($objSettings);
					$objProduct->appendChild($objLanguages);
					$objProduct->appendChild($objFeeds);
					$objProduct->appendChild($objStorage);
					$objProduct->appendChild($objTemplates);
					$objProduct->appendChild($objElements);
					$objProduct->appendChild($objAliases);
					$objProducts->appendChild($objProduct);
										
					//*** Files.
					if ($exportFiles) {
						$strServer = Setting::getValueByName("ftp_server");
						if ($strServer != "localhost") {
							$strLocation = "http://" . Setting::getValueByName("ftp_server") . Setting::getValueByName("file_folder");
							$objZip = self::exportFilesToZip($objZip, $arrFiles, $strLocation);
						}
					}
					
					break;
			}
		}
		
		//*** Account.
		$objDbAccount = Account::selectByPk($intAccountId);
		$objAccount = $objDoc->createElement('account');
		$objAccount->setAttribute("punchId", $objDbAccount->getPunchId());
		$objAccount->setAttribute("name", $objDbAccount->getName());
		$objAccount->setAttribute("uri", $objDbAccount->getUri());
		$objAccount->appendChild($objAcl);
		$objAccount->appendChild($objProducts);

		$objPunch = $objDoc->createElement('Punch');
		$objPunch->appendChild($objAccount);

		$objRoot = $objDoc->appendChild($objPunch);

		//*** Destroy temporary account object.
		unset($_CONF['app']['account']);
		
		//*** Return XML.
		$objZip->addFile(NULL, 'data.xml', "", $objDoc->saveXML());
   		$objZip->save();
    		
		return $strZipName;
	}

	public static function import($strXml, $blnOverwrite = FALSE, $blnKeepSettings = FALSE) {
		global $objLiveAdmin,
				$intDefaultLanguage,
				$_CONF;

		$objReturn = NULL;
		$objSettings = NULL;
		$blnZip = FALSE;

		//*** Init DOM object.
		$objDoc = new DOMDocument("1.0", "UTF-8");
		$objDoc->formatOutput = FALSE;
		$objDoc->preserveWhiteSpace = TRUE;
		if (is_file($strXml)) {
			$objZip = new dUnzip2($strXml);
			if (is_object($objZip)) {
				//*** Zip file.
				$strXml = $objZip->unzip('data.xml');
				
				if ($strXml !== FALSE) {
					$objDoc->loadXML($strXml);
					$blnZip = TRUE;
				}
			} else {
				//*** XML file.
				$objDoc->load($strXml);
			}
		} else {
			$objDoc->loadXML($strXml);
		}

		//*** Build data structure.
		foreach ($objDoc->childNodes as $rootNode) {
			if ($rootNode->nodeName == "Punch") {
				//*** Valid Punch XML.
				foreach ($rootNode->childNodes as $accountNode) {
					if ($accountNode->nodeName == "account") {
						//*** Account node. 
						if ($blnOverwrite) {
							$objAccount = Account::getByPunchId($accountNode->getAttribute("punchId"));
							
							if (is_object($objAccount) && $blnKeepSettings) {
								//*** Save settings.
								$objSettings = Settings::getByAccount($objAccount->getId());
							}
						
							//*** Remove account.
							if (is_object($objAccount)) $objAccount->delete();
						}
						
						//*** Create account.
						$objAccount = new Account();
						$objAccount->setPunchId($accountNode->getAttribute("punchId"));
						$objAccount->setName($accountNode->getAttribute("name"));
						$objAccount->setUri($accountNode->getAttribute("uri"));
						$objAccount->setTimeZoneId(42);
						$objAccount->save();
						
						//*** Create temporary account object.
						$_CONF['app']['account'] = $objAccount;

						foreach ($accountNode->childNodes as $childNode) {
							switch ($childNode->nodeName) {
								case "acl":
									$arrUserIds = array();
									$arrGroupIds = array();
									self::importAcl($childNode, $objAccount->getId(), $arrUserIds, $arrGroupIds);
									break;

								case "products":
									//*** Add products to the account.
									foreach ($childNode->childNodes as $productNode) {
										switch ($productNode->nodeName) {
											case "pcms":
												//*** Add PunchCMS product to the account.
												$objAccountProduct = new AccountProduct();
												$objAccountProduct->setAccountId($objAccount->getId());
												$objAccountProduct->setProductId(PRODUCT_PCMS);
												$objAccountProduct->setExpires($productNode->getAttribute("expires"));
												$objAccountProduct->save();
												
												$arrStorageIds[0] = 0;
												$arrFeedIds[0] = 0;

												//*** Add PunchCMS data to the account.
												foreach ($productNode->childNodes as $pcmsNode) {
													switch ($pcmsNode->nodeName) {
														case "settings":
															//*** Add settings to the account.
															if ($blnKeepSettings && is_object($objSettings)) {
																foreach ($objSettings as $objSetting) {
																	$objSetting->setId(0);
																	$objSetting->setAccountId($objAccount->getId());
																	$objSetting->save();
																}
															} else {
																foreach ($pcmsNode->childNodes as $settingNode) {
																	$objSettingTemplate = SettingTemplate::selectByName($settingNode->getAttribute("name"));
																	if (is_object($objSettingTemplate)) {
																		$objSetting = new Setting();
																		$objSetting->setAccountId($objAccount->getId());
																		$objSetting->setSettingId($objSettingTemplate->getId());
																		$objSetting->setValue($settingNode->getAttribute("value"));
																		$objSetting->save();
																	}
																}
															}
															break;

														case "languages":
															//*** Add languages to the account.
															$arrLanguageIds[0] = 0;
															foreach ($pcmsNode->childNodes as $languageNode) {
																$objLanguage = new ContentLanguage();
																$objLanguage->setAccountId($objAccount->getId());
																$objLanguage->setName($languageNode->getAttribute("name"));
																$objLanguage->setAbbr($languageNode->getAttribute("abbr"));
																$objLanguage->default = $languageNode->getAttribute("default");
																$objLanguage->setActive($languageNode->getAttribute("active"));
																$objLanguage->setSort($languageNode->getAttribute("sort"));
																$objLanguage->setUsername($languageNode->getAttribute("username"));
																$objLanguage->save();
																$arrLanguageIds[$languageNode->getAttribute("id")] = $objLanguage->getId();

																if ($languageNode->getAttribute("default") == 1) $intDefaultLanguage = $objLanguage->getId();
															}
															break;

														case "feeds":
															//*** Add feeds to the account.
															$arrFeedIds[0] = 0;
															foreach ($pcmsNode->childNodes as $feedNode) {
																$objFeed = new Feed();
																$objFeed->setAccountId($objAccount->getId());
																$objFeed->setName($feedNode->getAttribute("name"));
																$objFeed->setFeed($feedNode->getAttribute("feed"));
																$objFeed->setBasePath($feedNode->getAttribute("basepath"));
																$objFeed->setRefresh($feedNode->getAttribute("refresh"));
																$objFeed->setLastUpdate($feedNode->getAttribute("lastUpdate"));
																$objFeed->setActive($feedNode->getAttribute("active"));
																$objFeed->setSort($feedNode->getAttribute("sort"));
																$objFeed->save();
																$arrFeedIds[$feedNode->getAttribute("id")] = $objFeed->getId();
															}
															break;

														case "storage":
															//*** Add media items to the account.
															self::importStorage($pcmsNode, $objAccount->getId(), $arrStorageIds);
															break;

														case "templates":
															//*** Add templates to the account.
															$arrTemplateIds[0] = 0;
															$arrTemplateFieldIds[0] = 0;
															$arrLinkFieldIds = array();
															self::importTemplates($pcmsNode, $objAccount->getId(), $arrTemplateIds, $arrTemplateFieldIds, $arrLinkFieldIds);
															break;

														case "elements":
															//*** Add elements to the account.
															$arrElementIds[0] = 0;
															$arrElementFieldIds["link"][0] = 0;
															$arrElementFieldIds["largeText"][0] = 0;
															self::importElements($pcmsNode, $objAccount->getId(), $arrTemplateIds, $arrTemplateFieldIds, $arrElementIds, $arrElementFieldIds, $arrLinkFieldIds, $arrLanguageIds, $arrUserIds, $arrGroupIds, $arrStorageIds, $arrFeedIds);
															break;

														case "aliases":
															//*** Add aliases to the account.
															foreach ($pcmsNode->childNodes as $aliasNode) {
																$objAlias = new Alias();
																$objAlias->setAccountId($objAccount->getId());
																$objAlias->setAlias($aliasNode->getAttribute("alias"));
																
																if (array_key_exists($aliasNode->getAttribute("url"), $arrElementIds)) {
																	$objAlias->setUrl($arrElementIds[$aliasNode->getAttribute("url")]);
																} else {
																	$objAlias->setUrl(0);
																}
																
																if (array_key_exists($aliasNode->getAttribute("language"), $arrLanguageIds)) {
																	$objAlias->setLanguageId($arrLanguageIds[$aliasNode->getAttribute("language")]);
																} else {
																	$objAlias->setLanguageId(0);
																}
																
																$objAlias->setCascade($aliasNode->getAttribute("cascade"));
																$objAlias->setActive($aliasNode->getAttribute("active"));
																$objAlias->setSort($aliasNode->getAttribute("sort"));
																$objAlias->setCreated($aliasNode->getAttribute("created"));
																$objAlias->setModified($aliasNode->getAttribute("modified"));
																$objAlias->save();
															}
															break;

													}
												}

												//*** Adjust the links for deeplink fields.
												self::adjustDeeplinks($arrElementFieldIds["link"], $arrElementIds, $arrLanguageIds);

												//*** Adjust the links in large text fields.
												self::adjustTextlinks($arrElementFieldIds["largeText"], $arrElementIds, $arrLanguageIds, $arrStorageIds);
												
												break;										
										}
									}
									break;
							}
						}
										
						//*** Destroy temporary account object.
						unset($_CONF['app']['account']);

						$objReturn = $objAccount;
					}
				}
			}
		}
		
		//*** Files.
		if ($blnZip && is_object($objReturn)) {
			self::importFiles($objZip, $objReturn);
			
			if ($blnKeepSettings) {
				//*** Move files to remote server.
				self::moveImportedFiles($objReturn);
			}
		}

		return $objReturn;
	}
	
	public static function adjustDeeplinks($arrElementFieldIds, $arrElementIds, $arrLanguageIds) {
		//*** Adjust the links for deeplink fields.
		if (is_array($arrElementFieldIds)) {
			foreach ($arrElementFieldIds as $fieldId) {
				$objField = ElementField::selectByPk($fieldId);
	
				if (is_object($objField)) {
					foreach ($arrLanguageIds as $intLanguageId) {
						$objValue = $objField->getValueObject($intLanguageId);
						$intOldValue = $objValue->getValue();
						if (!empty($intOldValue)) {
							$intNewValue = (array_key_exists($intOldValue, $arrElementIds)) ? $arrElementIds[$intOldValue] : "";
							$objValue->setValue($intNewValue, $intLanguageId, $objValue->getCascade());
							$objField->setValueObject($objValue);
						}
					}
				}
			}
		}
	}
	
	public static function adjustTextlinks($arrElementFieldIds, $arrElementIds, $arrLanguageIds, $arrStorageIds) {
		//*** Adjust the links in large text fields.
		if (is_array($arrElementFieldIds)) {
			$strElmntPattern = "/(\?eid=)([0-9]+)/ie";
			$strStoragePattern = "/(\?mid=)([0-9]+)/ie";
			$arrElementFieldIds = array_unique($arrElementFieldIds);
			foreach ($arrElementFieldIds as $fieldId) {
				$objField = ElementField::selectByPk($fieldId);
	
				if (is_object($objField)) {
					foreach ($arrLanguageIds as $intLanguageId) {
						$objValue = $objField->getValueObject($intLanguageId);
						$strOldValue = $objValue->getValue();
						if (!empty($strOldValue)) {
							$blnFound = FALSE;
							
							if (preg_match($strElmntPattern, $strOldValue) > 0) {
								$strOldValue = preg_replace($strElmntPattern, "'$1'.\$arrElementIds['$2']", $strOldValue);
								$blnFound = TRUE;
							}
							
							if (preg_match($strStoragePattern, $strOldValue) > 0) {
								$strOldValue = preg_replace($strStoragePattern, "'$1'.\$arrStorageIds['$2']", $strOldValue);
								$blnFound = TRUE;
							}
							
							if ($blnFound) {
								$objValue->setValue($strOldValue, $intLanguageId, $objValue->getCascade());
								$objField->setValueObject($objValue);
							}
						}
					}
				}
			}
		}
	}

	public static function importTemplates($objTemplates, $intAccountId, &$arrTemplateIds, &$arrTemplateFieldIds, &$arrLinkFieldIds, $intParentId = 0) {
		foreach ($objTemplates->childNodes as $templateNode) {
			$objTemplate = new Template();
			$objTemplate->setAccountId($intAccountId);
			$objTemplate->setParentId($intParentId);
			$objTemplate->setIsPage($templateNode->getAttribute("isPage"));
			$objTemplate->setIsContainer($templateNode->getAttribute("isContainer"));
			$objTemplate->setForceCreation($templateNode->getAttribute("forceCreation"));
			$objTemplate->setName($templateNode->getAttribute("name"));
			$objTemplate->setApiName($templateNode->getAttribute("apiName"));
			$objTemplate->setDescription($templateNode->getAttribute("description"));
			$objTemplate->setSort($templateNode->getAttribute("sort"));
			$objTemplate->save();

			$arrTemplateIds[$templateNode->getAttribute("id")] = $objTemplate->getId();

			//*** Add fields to the template.
			foreach ($templateNode->childNodes as $fieldsNode) {
				switch ($fieldsNode->nodeName) {
					case "fields":
						foreach ($fieldsNode->childNodes as $fieldNode) {
							$objField = new TemplateField();
							$objField->setTemplateId($objTemplate->getId());
							$objField->setRequired($fieldNode->getAttribute("required"));
							$objField->setName($fieldNode->getAttribute("name"));
							$objField->setApiName($fieldNode->getAttribute("apiName"));
							$objField->setDescription($fieldNode->getAttribute("description"));
							$objField->setTypeId($fieldNode->getAttribute("typeId"));
							$objField->setUsername($fieldNode->getAttribute("username"));
							$objField->setSort($fieldNode->getAttribute("sort"));
							$objField->save();

							$arrTemplateFieldIds[$fieldNode->getAttribute("id")] = $objField->getId();
							if ($fieldNode->getAttribute("typeId") == FIELD_TYPE_LINK) array_push($arrLinkFieldIds, $fieldNode->getAttribute("id"));

							//*** Add values to the field.
							foreach ($fieldNode->childNodes as $valuesNode) {
								switch ($valuesNode->nodeName) {
									case "values":
										foreach ($valuesNode->childNodes as $valueNode) {
											$objValue = new TemplateFieldValue();
											$objValue->setName($valueNode->getAttribute("name"));
											$objValue->setValue($valueNode->getAttribute("value"));
											$objValue->setFieldId($objField->getId());
											$objValue->save();
										}
										break;
								}
							}
						}
						break;

					case "templates":
						self::importTemplates($fieldsNode, $intAccountId, $arrTemplateIds, $arrTemplateFieldIds, $arrLinkFieldIds, $objTemplate->getId());
						break;
				}
			}
		}
	}

	public static function importElements($objElements, $intAccountId, $arrTemplateIds, $arrTemplateFieldIds, &$arrElementIds, &$arrElementFieldIds, $arrLinkFieldIds, $arrLanguageIds, $arrUserIds, $arrGroupIds, $arrStorageIds, $arrFeedIds, $intParentId = 0) {
		global $intDefaultLanguage;
		
		$strElmntPattern = "/(\?eid=)([0-9]+)/ie";		
		$strStoragePattern = "/(\?mid=)([0-9]+)/ie";
		
		foreach ($objElements->childNodes as $elementNode) {
			if (!is_null($arrTemplateIds[$elementNode->getAttribute("templateId")])) {
				$objElement = new Element();
				$objElement->setAccountId($intAccountId);
				$objElement->setParentId($intParentId);
				$objElement->setActive($elementNode->getAttribute("active"));
				$objElement->setIsPage($elementNode->getAttribute("isPage"));
				$objElement->setName($elementNode->getAttribute("name"));
				$objElement->setApiName($elementNode->getAttribute("apiName"));
				$objElement->setDescription($elementNode->getAttribute("description"));
				$objElement->setUsername($elementNode->getAttribute("username"));
				$objElement->setTypeId($elementNode->getAttribute("typeId"));
				$objElement->setTemplateId($arrTemplateIds[$elementNode->getAttribute("templateId")]);
				$objElement->setSort($elementNode->getAttribute("sort"));
				$objElement->save(FALSE, FALSE);

				if ($elementNode->getAttribute("typeId") == 1) {
					$objElement->setLanguageActive($intDefaultLanguage, 1);
				}

				$arrElementIds[$elementNode->getAttribute("id")] = $objElement->getId();
				
				//*** Schedule.
				$objSchedule = new ElementSchedule();
				$objSchedule->setStartActive($elementNode->getAttribute("scheduleStartActive"));
				$objSchedule->setStartDate($elementNode->getAttribute("scheduleStartDate"));
				$objSchedule->setEndActive($elementNode->getAttribute("scheduleEndActive"));
				$objSchedule->setEndDate($elementNode->getAttribute("scheduleEndDate"));
				$objElement->setSchedule($objSchedule);

				//*** Add fields to the element.
				foreach ($elementNode->childNodes as $subNode) {
					switch ($subNode->nodeName) {
						case "fields":
							$arrActiveLangs = array();

							foreach ($subNode->childNodes as $fieldNode) {
								switch ($fieldNode->nodeName) {
									case "field":
										$objField = new ElementField();
										$objField->setElementId($objElement->getId());
										$objField->setTemplateFieldId($arrTemplateFieldIds[$fieldNode->getAttribute("templateFieldId")]);
										$objField->setSort($fieldNode->getAttribute("sort"));
										$objField->save();

										foreach ($fieldNode->childNodes as $languageNode) {
											$objValue = $objField->getNewValueObject();
											$objValue->setValue($languageNode->nodeValue);
											$objValue->setLanguageId($arrLanguageIds[$languageNode->getAttribute("id")]);
											$objValue->setCascade($languageNode->getAttribute("cascade"));

											$objField->setValueObject($objValue);
											$arrActiveLangs[$languageNode->getAttribute("id")] = $languageNode->getAttribute("active");

											if (preg_match($strElmntPattern, $languageNode->nodeValue) > 0) array_push($arrElementFieldIds["largeText"], $objField->getId());
											if (preg_match($strStoragePattern, $languageNode->nodeValue) > 0) array_push($arrElementFieldIds["largeText"], $objField->getId());
										}

										if (in_array($fieldNode->getAttribute("templateFieldId"), $arrLinkFieldIds)) array_push($arrElementFieldIds["link"], $objField->getId());
										break;
								}
							}

							foreach ($arrActiveLangs as $key => $value) {
								$objElement->setLanguageActive($arrLanguageIds[$key], $value);
							}
							break;
							
						case "feed":
							foreach ($subNode->childNodes as $feedFieldNode) {
								if ($feedFieldNode->nodeName == "feedfield") {
									$objFeedField = new ElementFieldFeed();
									$objFeedField->setElementId($objElement->getId());
									$objFeedField->setTemplateFieldId($arrTemplateFieldIds[$feedFieldNode->getAttribute("templateFieldId")]);
									$objFeedField->setFeedPath($feedFieldNode->getAttribute("feedPath"));
									$objFeedField->setXpath($feedFieldNode->getAttribute("xpath"));
									$objFeedField->setLanguageId($arrLanguageIds[$feedFieldNode->getAttribute("languageId")]);
									$objFeedField->setCascade($feedFieldNode->getAttribute("cascade"));
									$objFeedField->setSort($feedFieldNode->getAttribute("sort"));
									$objFeedField->save();
								}
							}
							
							$objFeed = new ElementFeed();
							$objFeed->setElementId($objElement->getId());
							$objFeed->setFeedId($arrFeedIds[$subNode->getAttribute("feedId")]);
							$objFeed->setFeedPath($subNode->getAttribute("feedPath"));
							$objFeed->setMaxItems($subNode->getAttribute("maxItems"));
							$objFeed->setSortBy($subNode->getAttribute("sortBy"));
							$objFeed->setAliasField($subNode->getAttribute("aliasField"));
							$objFeed->save();
							
							break;
							
						case "languages":
							foreach ($subNode->childNodes as $languageNode) {
								$objLanguage = new ElementLanguage();
								$objLanguage->setElementId($objElement->getId());
								$objLanguage->setLanguageId($arrLanguageIds[$languageNode->getAttribute("id")]);
								$objLanguage->setActive($languageNode->getAttribute("active"));
								$objLanguage->setCascade($languageNode->getAttribute("cascade"));
								$objLanguage->save();
							}
							break;
							
						case "permissions":
							$objUsers = array();
							$objTempUsers = explode(",", $subNode->getAttribute("users"));
							foreach ($objTempUsers as $value) {
								if (array_key_exists($value, $arrUserIds)) array_push($objUsers, $arrUserIds[$value]);
							}
							
							$objGroups = array();
							$objTempGroups = explode(",", $subNode->getAttribute("groups"));
							foreach ($objTempGroups as $value) {
								if (array_key_exists($value, $arrGroupIds)) array_push($objGroups, $arrGroupIds[$value]);
							}
	
							$objPermissions = new ElementPermission();
							$objPermissions->setUserId($objUsers);
							$objPermissions->setGroupId($objGroups);
						
							$objElement->setPermissions($objPermissions, TRUE);
							break;
							
						case "elements":
							self::importElements($subNode, $intAccountId, $arrTemplateIds, $arrTemplateFieldIds, $arrElementIds, $arrElementFieldIds, $arrLinkFieldIds, $arrLanguageIds, $arrUserIds, $arrGroupIds, $arrStorageIds, $arrFeedIds, $objElement->getId());
							break;
					}
				}
				
				//*** Update the search index.
				$objSearch = new Search();
				$objSearch->updateIndex($objElement->getId());
			}
		}

	}

	public static function importStorage($objElements, $intAccountId, &$arrStorageIds, $intParentId = 0) {
		foreach ($objElements->childNodes as $elementNode) {
			$objElement = new StorageItem();
			$objElement->setAccountId($intAccountId);
			$objElement->setParentId($intParentId);
			$objElement->setName($elementNode->getAttribute("name"));
			$objElement->setDescription($elementNode->getAttribute("description"));
			$objElement->setTypeId($elementNode->getAttribute("typeId"));
			$objElement->setUsername($elementNode->getAttribute("username"));
			$objElement->setSort($elementNode->getAttribute("sort"));
			$objElement->save(FALSE, FALSE);

			if ($elementNode->getAttribute("typeId") == 2) {
				$objData = $objElement->getData();
				$objData->setItemId($objElement->getId());
				$objData->setOriginalName($elementNode->getAttribute("originalName"));
				$objData->setLocalName($elementNode->getAttribute("localName"));
				$objData->save();
			}

			$arrStorageIds[$elementNode->getAttribute("id")] = $objElement->getId();

			//*** Add sub media to the media item.
			foreach ($elementNode->childNodes as $subNode) {
				switch ($subNode->nodeName) {							
					case "items":
						self::importStorage($subNode, $intAccountId, $arrStorageIds,  $objElement->getId());
						break;
				}
			}
		}
	}
	
	private static function importAcl($objRoot, $intAccountId, &$arrUserIds, &$arrGroupIds) {
		global $objLiveAdmin;
		
		$arrRightIds = array();
		$arrOptions = array('naming' => LIVEUSER_SECTION_APPLICATION, 'filters' => array('account_id' => array(0)));
		$arrDbRights = $objLiveAdmin->perm->outputRightsConstants('array', $arrOptions, 'direct');
		$arrSystemRights = array();
		foreach ($arrDbRights as $key => $value) {
			$arrSystemRights[strtoupper($key)] = $value;
		}
	
		foreach ($objRoot->childNodes as $childNode) {
			switch ($childNode->nodeName) {
				case "applications":
					//*** Add applications to the account.
					foreach ($childNode->getElementsByTagName("application") as $appNode) {
						$data = array(
							'application_define_name' => $appNode->getAttribute("name"),
							'account_id' => $intAccountId
						);
						$intAppId = $objLiveAdmin->perm->addApplication($data);
						
						//*** Add areas to the account.
						foreach ($appNode->getElementsByTagName("areas")->item(0)->getElementsByTagName("area") as $areaNode) {
							$data = array(
								'area_define_name' => $areaNode->getAttribute("name"),
								'application_id' => $intAppId,
								'account_id' => $intAccountId
							);
							$intAreaId = $objLiveAdmin->perm->addArea($data);
						
							//*** Add rights to the account.
							foreach ($areaNode->getElementsByTagName("rights")->item(0)->getElementsByTagName("right") as $rightNode) {
								$data = array(
									'right_define_name' => $rightNode->getAttribute("name"),
									'area_id' => $intAreaId,
									'account_id' => $intAccountId
								);
								$intRightId = $objLiveAdmin->perm->addRight($data);
								$arrRightIds[$rightNode->getAttribute("id")] = $intRightId;
							}
						}
					}
					
					break;
				case "implied_rights":
					//*** Add implied rights to the account.
					foreach ($childNode->getElementsByTagName("right") as $rightNode) {
						foreach ($rightNode->getElementsByTagName("right") as $impliedRightNode) {							
							$data = array(
								'right_id' => self::getRightId($arrRightIds, $arrSystemRights, $rightNode->getAttribute("id")),
								'implied_right_id' => self::getRightId($arrRightIds, $arrSystemRights, $impliedRightNode->getAttribute("id"))
							);
							$objLiveAdmin->perm->implyRight($data);
						}
					}
					
					break;
				case "groups":
					//*** Add groups to the account.
					foreach ($childNode->getElementsByTagName("group") as $groupNode) {
						$data = array(
							'group_define_name' => $groupNode->getAttribute("name"),
							'is_active' => $groupNode->getAttribute("is_active"),
							'account_id' => $intAccountId
						);
						$intGroupId = $objLiveAdmin->perm->addGroup($data);
						$arrGroupIds[$groupNode->getAttribute("id")] = $intGroupId;
						
						//*** Add rights to the group.
						foreach ($groupNode->getElementsByTagName("rights")->item(0)->getElementsByTagName("right") as $rightNode) {
							$intLevel = $rightNode->getAttribute("right_level");
							$intLevel = (empty($intLevel)) ? 3 : $intLevel;
							$data = array(
								'group_id' => $intGroupId,
								'right_id' => self::getRightId($arrRightIds, $arrSystemRights, $rightNode->getAttribute("id")),
								'right_level' => $intLevel,
								'account_id' => $intAccountId,
							);
							$objLiveAdmin->perm->grantGroupRight($data);
						}
					}
					
					break;
				case "users":
					//*** Add users to the account.
					foreach ($childNode->getElementsByTagName("user") as $userNode) {
						$data = array(
							'handle' => $userNode->getAttribute("handle"),
							'name' => $userNode->getAttribute("name"),
							'passwd' => $userNode->getAttribute("passwd"),
							'is_active' => $userNode->getAttribute("isactive"),
							'email' => $userNode->getAttribute("email"),
							'account_id' => $intAccountId,
							'perm_type' => $userNode->getAttribute("permType")
						);
						$intPermUserId = $objLiveAdmin->addUser($data, FALSE);
						$arrUserIds[$userNode->getAttribute("perm_id")] = $intPermUserId;
						
						//*** Add groups to the user.
						foreach ($userNode->getElementsByTagName("groups")->item(0)->getElementsByTagName("group") as $groupNode) {
							$data = array(
								'perm_user_id' => $intPermUserId,
								'group_id' => self::getRightId($arrGroupIds, array(), $groupNode->getAttribute("id")),
							);
							$objLiveAdmin->perm->addUserToGroup($data);
						}
						
						//*** Add rights to the user.
						foreach ($userNode->getElementsByTagName("rights")->item(0)->getElementsByTagName("right") as $rightNode) {
							$intLevel = $rightNode->getAttribute("right_level");
							$intLevel = (empty($intLevel)) ? 3 : $intLevel;
							$data = array(
								'perm_user_id' => $intPermUserId,
								'right_id' => self::getRightId($arrRightIds, $arrSystemRights, $rightNode->getAttribute("id")),
								'right_level' => $intLevel,
								'account_id' => $intAccountId
							);
							$objLiveAdmin->perm->grantUserRight($data);
						}
					}
					break;
			}
		}
	}

	private static function exportTemplate($objDoc, $intAccountId, $intId) {
		$objTemplates = $objDoc->createElement('templates');

		$objDbTemplates = Templates::getFromParent($intId, FALSE, $intAccountId);

		if ($objDbTemplates->count() > 0) {
			foreach ($objDbTemplates as $objDbTemplate) {
				$objTemplate = $objDoc->createElement('template');
				$objTemplate->setAttribute("id", $objDbTemplate->getId());
				$objTemplate->setAttribute("name", $objDbTemplate->getName());
				$objTemplate->setAttribute("apiName", $objDbTemplate->getApiName());
				$objTemplate->setAttribute("description", $objDbTemplate->getDescription());
				$objTemplate->setAttribute("sort", $objDbTemplate->getSort());
				$objTemplate->setAttribute("isPage", $objDbTemplate->getIsPage());
				$objTemplate->setAttribute("forceCreation", $objDbTemplate->getForceCreation());
				$objTemplate->setAttribute("isContainer", $objDbTemplate->getIsContainer());
				$objTemplate->setAttribute("active", $objDbTemplate->getActive());

				$objFields = $objDoc->createElement('fields');
				foreach ($objDbTemplate->getFields() as $objDbField) {
					$objField = $objDoc->createElement('field');
					$objField->setAttribute("id", $objDbField->getId());
					$objField->setAttribute("required", $objDbField->getRequired());
					$objField->setAttribute("typeId", $objDbField->getTypeId());
					$objField->setAttribute("name", $objDbField->getName());
					$objField->setAttribute("apiName", $objDbField->getApiName());
					$objField->setAttribute("description", $objDbField->getDescription());
					$objField->setAttribute("username", $objDbField->getUsername());
					$objField->setAttribute("sort", $objDbField->getSort());		

					$objValues = $objDoc->createElement('values');
					foreach ($objDbField->getValues() as $objDbValue) {
						$objValue = $objDoc->createElement('value');
						$objValue->setAttribute("name", $objDbValue->getName());
						$objValue->setAttribute("value", $objDbValue->getValue());
						$objValues->appendChild($objValue);
					}

					$objField->appendChild($objValues);
					$objFields->appendChild($objField);
				}

				$objTemplate->appendChild($objFields);

				$objSubTemplates = self::exportTemplate($objDoc, $intAccountId, $objDbTemplate->getId());
				if ($objSubTemplates) {
					$objTemplate->appendChild($objSubTemplates);
				}

				$objTemplates->appendChild($objTemplate);
			}
		}

		return $objTemplates;
	}

	private static function exportElement($objDoc, $intAccountId, $intId, &$arrFiles) {
		global $_CONF;
		
		$objElements = $objDoc->createElement('elements');

		$objDbElements = Elements::getFromParent($intId, FALSE, "'1', '2', '3', '4', '5'", $intAccountId);

		if ($objDbElements->count() > 0) {
			foreach ($objDbElements as $objDbElement) {
				$objElement = $objDoc->createElement('element');
				$objElement->setAttribute("id", $objDbElement->getId());
				$objElement->setAttribute("name", $objDbElement->getName());
				$objElement->setAttribute("nameCount", $objDbElement->getNameCount());
				$objElement->setAttribute("apiName", $objDbElement->getApiName());
				$objElement->setAttribute("description", $objDbElement->getDescription());
				$objElement->setAttribute("typeId", $objDbElement->getTypeId());
				$objElement->setAttribute("templateId", $objDbElement->getTemplateId());
				$objElement->setAttribute("isPage", $objDbElement->getIsPage());
				$objElement->setAttribute("userId", $objDbElement->getUserId());
				$objElement->setAttribute("groupId", $objDbElement->getGroupId());
				$objElement->setAttribute("sort", $objDbElement->getSort());
				$objElement->setAttribute("active", $objDbElement->getActive());
				$objElement->setAttribute("username", $objDbElement->getUsername());
				$objElement->setAttribute("created", $objDbElement->getCreated());
				$objElement->setAttribute("modified", $objDbElement->getModified());
				
				//*** Schedule.
				$objSchedule = $objDbElement->getSchedule();
				$objElement->setAttribute("scheduleStartActive", $objSchedule->getStartActive());
				$objElement->setAttribute("scheduleStartDate", $objSchedule->getStartDate());
				$objElement->setAttribute("scheduleEndActive", $objSchedule->getEndActive());
				$objElement->setAttribute("scheduleEndDate", $objSchedule->getEndDate());

				//*** Fields.
				$arrActiveLangs = $objDbElement->getLanguageActives();
				$objContentLangs = ContentLanguage::select();

				$objFields = $objDoc->createElement('fields');
				$objDbFields = $objDbElement->getFields();
				foreach ($objDbFields as $objDbField) {
					$objField = $objDoc->createElement('field');
					$objField->setAttribute("templateFieldId", $objDbField->getTemplateFieldId());
					$objField->setAttribute("sort", $objDbField->getSort());					
				
					foreach ($objContentLangs as $objContentLanguage) {
						$objValue = $objDbField->getValueObject($objContentLanguage->getId());
						
						if (is_object($objValue)) {
							$strValue = str_replace("&", "&amp;", $objValue->getValue());

							$objLanguage = $objDoc->createElement('language', $strValue);
							$objLanguage->setAttribute("id", $objContentLanguage->getId());
							$objLanguage->setAttribute("active", (in_array($objContentLanguage->getId(), $arrActiveLangs)) ? 1 : 0);
							$objLanguage->setAttribute("cascade", $objValue->getCascade());
							$objField->appendChild($objLanguage);
							
							switch ($objDbField->getTypeId()) {
								case FIELD_TYPE_FILE:
									$arrFileTemp = explode("\n", $strValue);
									foreach ($arrFileTemp as $fileValue) {
										if (!empty($fileValue)) {
											$arrTemp = explode(":", $fileValue);
											$strSrc = (count($arrTemp) > 1) ? $arrTemp[1] : $arrTemp[0];
											array_push($arrFiles, $strSrc);
										}
									}
									break;
								case FIELD_TYPE_IMAGE:
									$arrFileTemp = explode("\n", $strValue);
									foreach ($arrFileTemp as $fileValue) {
										if (!empty($fileValue)) {
											$arrTemp = explode(":", $fileValue);
											$strSrc = (count($arrTemp) > 1) ? $arrTemp[1] : $arrTemp[0];

											$objImageField = new ImageField($objDbField->getTemplateFieldId());
											$arrSettings = $objImageField->getSettings();
											foreach ($arrSettings as $key => $arrSetting) {
												if (!empty($arrSetting['width']) ||	!empty($arrSetting['height'])) {
													//*** Add file.
													array_push($arrFiles, FileIO::add2Base($strSrc, $arrSetting['key']));
												}
											}
											
											array_push($arrFiles, $strSrc);
										}
									}
									break;
							}
						}
					}

					$objFields->appendChild($objField);
				}
				
				if ($objDbFields->count() > 0) {
					$objElement->appendChild($objFields);
				} else {
					$objDbLanguages = ElementLanguage::selectByElement($objDbElement->getId());
					$objLanguages = $objDoc->createElement('languages');
					foreach($objDbLanguages as $objDbLanguage) {
						if ($objDbLanguage->getActive()) {
							$objLanguage = $objDoc->createElement('language');
							$objLanguage->setAttribute("id", $objDbLanguage->getLanguageId());
							$objLanguage->setAttribute("active", $objDbLanguage->getActive());
							$objLanguage->setAttribute("cascade", $objDbLanguage->getCascade());
							$objLanguages->appendChild($objLanguage);
						}
					}
					
					if ($objDbLanguages->count() > 0) $objElement->appendChild($objLanguages);
				}
				
				//*** Feed fields.
				$objDbFeed = $objDbElement->getFeed();
				if (is_object($objDbFeed) && $objDbFeed->getId() > 0) {
					$objFeed = $objDoc->createElement('feed');
					$objFeed->setAttribute("feedId", $objDbFeed->getFeedId());
					$objFeed->setAttribute("feedPath", $objDbFeed->getFeedPath());
					$objFeed->setAttribute("maxItems", $objDbFeed->getMaxItems());
					$objFeed->setAttribute("sortBy", $objDbFeed->getSortBy());
					$objFeed->setAttribute("aliasField", $objDbFeed->getAliasField());
					
					$objDbFields = ElementFieldFeed::selectByElement($objDbElement->getId());
					foreach ($objDbFields as $objDbField) {
						$objField = $objDoc->createElement('feedfield');
						$objField->setAttribute("templateFieldId", $objDbField->getTemplateFieldId());
						$objField->setAttribute("feedPath", $objDbField->getFeedPath());
						$objField->setAttribute("xpath", $objDbField->getXpath());
						$objField->setAttribute("languageId", $objDbField->getLanguageId());
						$objField->setAttribute("cascade", $objDbField->getCascade());
						$objField->setAttribute("sort", $objDbField->getSort());
						$objFeed->appendChild($objField);	
					}
					
					$objElement->appendChild($objFeed);			
				}
				
				//*** Permissions.
				$objPermissions = $objDoc->createElement('permissions');
				$objDbPermissions = $objDbElement->getPermissions();
				$objPermissions->setAttribute("users", implode(",", $objDbPermissions->getUserId()));
				$objPermissions->setAttribute("groups", implode(",", $objDbPermissions->getGroupId()));
				$objElement->appendChild($objPermissions);		

				//*** Sub elements.
				$objSubElements = self::exportElement($objDoc, $intAccountId, $objDbElement->getId(), $arrFiles);
				if ($objSubElements) {
					$objElement->appendChild($objSubElements);
				}

				$objElements->appendChild($objElement);
			}
		}

		return $objElements;
	}

	private static function exportStorage($objDoc, $intAccountId, $intId, &$arrFiles) {
		global $_CONF;
		
		$objElements = ($intId == 0) ? $objDoc->createElement('storage') : $objDoc->createElement('items');

		$objDbElements = StorageItems::getFromParent($intId, "'1', '2'", $intAccountId);

		if ($objDbElements->count() > 0) {
			foreach ($objDbElements as $objDbElement) {
				$objElement = $objDoc->createElement('item');
				$objElement->setAttribute("id", $objDbElement->getId());
				$objElement->setAttribute("name", $objDbElement->getName());
				$objElement->setAttribute("description", $objDbElement->getDescription());
				$objElement->setAttribute("typeId", $objDbElement->getTypeId());
				$objElement->setAttribute("username", $objDbElement->getUsername());
				$objElement->setAttribute("sort", $objDbElement->getSort());
				$objElement->setAttribute("created", $objDbElement->getCreated());
				$objElement->setAttribute("modified", $objDbElement->getModified());
				
				if ($objDbElement->getTypeId() == 2) {				
					$objElement->setAttribute("localName", $objDbElement->getData()->getLocalName());
					$objElement->setAttribute("originalName", $objDbElement->getData()->getOriginalName());
					array_push($arrFiles, $objDbElement->getData()->getLocalName());
				}

				//*** Sub elements.
				$objSubElements = self::exportStorage($objDoc, $intAccountId, $objDbElement->getId(), $arrFiles);
				if ($objSubElements) {
					$objElement->appendChild($objSubElements);
				}

				$objElements->appendChild($objElement);
			}
		}

		return $objElements;
	}
	
	private static function exportAcl($objDoc, $intAccountId) {
		global $objLiveAdmin;
		
		$objReturn = $objDoc->createElement('acl');
		$objImpliedRights = $objDoc->createElement('implied_rights');
		$arrOptions = array('naming' => LIVEUSER_SECTION_APPLICATION, 'filters' => array('account_id' => array(0)));
		$arrDbRights = $objLiveAdmin->perm->outputRightsConstants('array', $arrOptions, 'direct');
		$arrRights = array();
		if (is_array($arrDbRights)) {
			foreach ($arrDbRights as $key => $value) {
				$arrRights[strtoupper($key)] = $value;
			}
		}
		
		//*** ACL applications.
		$objApps = $objDoc->createElement('applications');
		$objDbApps = $objLiveAdmin->perm->getApplications(array('filters' => array('account_id' => $intAccountId)));
		if (is_array($objDbApps)) {
			foreach ($objDbApps as $objDbApp) {				
				$objApp = $objDoc->createElement('application');
				$objApp->setAttribute("name", $objDbApp['application_define_name']);

				//*** ACL areas.
				$objAreas = $objDoc->createElement('areas');
				$objDbAreas = $objLiveAdmin->perm->getAreas(array('filters' => array('application_id' => $objDbApp['application_id'], 'account_id' => $intAccountId)));
				if (is_array($objDbAreas)) {
					foreach ($objDbAreas as $objDbArea) {	
						$objArea = $objDoc->createElement('area');
						$objArea->setAttribute("name", $objDbArea['area_define_name']);

						//*** ACL rights.
						$objRights = $objDoc->createElement('rights');
						$objDbRights = $objLiveAdmin->perm->getRights(array('filters' => array('area_id' => $objDbArea['area_id'], 'account_id' => $intAccountId)));
						if (is_array($objDbRights)) {
							foreach ($objDbRights as $objDbRight) {
								$objRight = $objDoc->createElement('right');
								$intRight = (in_array($objDbRight['right_id'], $arrRights)) ? array_search($objDbRight['right_id'], $arrRights) : $objDbRight['right_id'];
								$objRight->setAttribute("id", $intRight);
								$objRight->setAttribute("name", $objDbRight['right_define_name']);
								$objRights->appendChild($objRight);
								
								//*** ACL implied rights.
								$filters = array('fields' => array('implied_right_id'),	'filters' => array('right_id' => $objDbRight['right_id'], 'account_id' => $intAccountId));
								$objDbImpliedRights = $objLiveAdmin->getRights($filters);
								if (is_array($objDbImpliedRights) && count($objDbImpliedRights) > 0) {
									$objRight = $objDoc->createElement('right');
									$objRight->setAttribute("id", $intRight);
									foreach ($objDbImpliedRights as $objDbImpliedRight) {
										$objImpliedRight = $objDoc->createElement('right');
										$intRight = (in_array($objDbImpliedRight['implied_right_id'], $arrRights)) ? array_search($objDbImpliedRight['implied_right_id'], $arrRights) : $objDbImpliedRight['implied_right_id'];
										$objImpliedRight->setAttribute("id", $intRight);
										$objRight->appendChild($objImpliedRight);
									}
									
									$objImpliedRights->appendChild($objRight);
								}
							}
						}		

						$objArea->appendChild($objRights);
						$objAreas->appendChild($objArea);					
					}
				}

				$objApp->appendChild($objAreas);
				$objApps->appendChild($objApp);
			}
		}
		
		$objReturn->appendChild($objApps);	
		$objReturn->appendChild($objImpliedRights);	
		
		//*** ACL groups.
		$objGroups = $objDoc->createElement('groups');
		$objDbGroups = $objLiveAdmin->perm->getGroups(array('filters' => array('account_id' => $intAccountId)));
		if (is_array($objDbGroups)) {
			foreach ($objDbGroups as $objDbGroup) {
				$objGroup = $objDoc->createElement('group');
				$objGroup->setAttribute("id", $objDbGroup['group_id']);
				$objGroup->setAttribute("type", $objDbGroup['group_type']);
				$objGroup->setAttribute("name", $objDbGroup['group_define_name']);
				$objGroup->setAttribute("is_active", $objDbGroup['is_active']);
				$objGroup->setAttribute("owner_user_id", $objDbGroup['owner_user_id']);
				$objGroup->setAttribute("owner_group_id", $objDbGroup['owner_group_id']);
				
				//*** ACL rights.
				$objRights = $objDoc->createElement('rights');
				$filters = array('fields' => array('right_id', 'right_level'), 'filters' => array('group_id' => $objDbGroup['group_id'], 'account_id' => $intAccountId));
				$objDbRights = $objLiveAdmin->perm->getGroups($filters);
				if (is_array($objDbRights)) {
					foreach ($objDbRights as $objDbRight) {
						$objRight = $objDoc->createElement('right');
						$intRight = (in_array($objDbRight['right_id'], $arrRights)) ? array_search($objDbRight['right_id'], $arrRights) : $objDbRight['right_id'];
						$objRight->setAttribute("id", $intRight);
						$objRight->setAttribute("right_level", $objDbRight['right_level']);
						$objRights->appendChild($objRight);
					}
				}
				
				$objGroup->appendChild($objRights);
				$objGroups->appendChild($objGroup);
			}
		}
		
		$objReturn->appendChild($objGroups);
		
		//*** ACL users.
		$objUsers = $objDoc->createElement('users');
		$objDbUsers = $objLiveAdmin->getUsers(array('container' => 'auth', 'filters' => array('account_id' => $intAccountId)));
		if (is_array($objDbUsers)) {
			foreach ($objDbUsers as $objDbUser) {
				$objUser = $objDoc->createElement('user');
				$objUser->setAttribute("perm_id", $objDbUser['perm_user_id']);
				$objUser->setAttribute("handle", $objDbUser['handle']);
				$objUser->setAttribute("passwd", $objDbUser['passwd']);
				$objUser->setAttribute("owner_user_id", $objDbUser['owner_user_id']);
				$objUser->setAttribute("owner_group_id", $objDbUser['owner_group_id']);
				$objUser->setAttribute("lastLogin", $objDbUser['lastlogin']);
				$objUser->setAttribute("isactive", $objDbUser['is_active']);
				$objUser->setAttribute("name", $objDbUser['name']);
				$objUser->setAttribute("email", $objDbUser['email']);
				$objUser->setAttribute("permType", $objDbUser['perm_type']);
				
				//*** ACL groups.
				$objGroups = $objDoc->createElement('groups');
				$objDbGroups = $objLiveAdmin->perm->getGroups(array('filters' => array('perm_user_id' => $objDbUser['perm_user_id'], 'account_id' => $intAccountId)));
				if (is_array($objDbGroups)) {
					foreach ($objDbGroups as $objDbGroup) {
						$objGroup = $objDoc->createElement('group');
						$objGroup->setAttribute("id", $objDbGroup['group_id']);
						$objGroups->appendChild($objGroup);
					}
				}
				
				//*** ACL rights.
				$objRights = $objDoc->createElement('rights');
				$objDbApps = $objLiveAdmin->perm->getApplications(array('filters' => array('account_id' => array(0, $intAccountId))));
				if (is_array($objDbApps)) {
					foreach ($objDbApps as $objDbApp) {	
						$objDbAreas = $objLiveAdmin->perm->getAreas(array('filters' => array('application_id' => $objDbApp['application_id'], 'account_id' => array(0, $intAccountId))));
						if (is_array($objDbAreas)) {
							foreach ($objDbAreas as $objDbArea) {
								$filters = array('fields' => array('right_id', 'right_level'), 'filters' => array('area_id' => $objDbArea['area_id'], 'perm_user_id' => $objDbUser['perm_user_id'], 'account_id' => array(0, $intAccountId)));
								$objDbRights = $objLiveAdmin->perm->getRights($filters);
								if (is_array($objDbRights)) {
									foreach ($objDbRights as $objDbRight) {
										$objRight = $objDoc->createElement('right');
										$intRight = (in_array($objDbRight['right_id'], $arrRights)) ? array_search($objDbRight['right_id'], $arrRights) : $objDbRight['right_id'];
										$objRight->setAttribute("id", $intRight);
										$objRight->setAttribute("right_level", $objDbRight['right_level']);
										$objRights->appendChild($objRight);
									}
								}
							}
						}
					}
				}
				
				$objUser->appendChild($objGroups);
				$objUser->appendChild($objRights);
				$objUsers->appendChild($objUser);
			}
		}

		$objReturn->appendChild($objUsers);
		
		return $objReturn;
	}

	private static function getRightId($arrMatchedRights, $arrSystemRights, $varId) {
		$intReturn = $varId;
		
		if (is_numeric($varId)) {
			if (array_key_exists($varId, $arrMatchedRights)) {
				$intReturn = $arrMatchedRights[$varId];
			}
		} else if (array_key_exists($varId, $arrSystemRights)) {
				$intReturn = $arrSystemRights[$varId];
		}
		
		return $intReturn;
	}
	
	private static function array_normalize($arrInput) {
		$arrOutput = array();
		
		foreach ($arrInput as $value) {
			if (!in_array($value, $arrOutput)) array_push($arrOutput, $value);
		}
		
		return $arrOutput;
	}
	
	private static function exportFilesToZip($objZip, $arrFiles, $strLocation) {
		$arrFiles = self::array_normalize($arrFiles);
		
		foreach ($arrFiles as $value) {
      		$strContents = @file_get_contents($strLocation . $value);
       		if ($strContents !== FALSE) {
        		$objZip->addFile(NULL, "files/" . $value, "", $strContents);
       		}
		}
		
		return $objZip;
	}
	
	private static function importFiles($objZip, $objAccount) {
		global $_CONF, $_PATHS;
		
		$arrFiles = $objZip->getList();
		foreach ($arrFiles as $name => $arrFile) {
			$dirname = dirname($name);
			if ($dirname == "files") {
				//*** Create a subfolder in the upload dir.
				$targetDir = $_PATHS['upload'] . $objAccount->getId();
				if (!is_dir($targetDir)) {
					mkdir($targetDir, 0777, TRUE);
					chmod($targetDir, 0777);
				}

				//*** Move files to a subfolder of the upload dir.
				$filename = basename($name);
				$path = $targetDir . "/" . $filename;
				if ($objHandle = fopen($path, 'wb')) {
					fwrite($objHandle, $objZip->unzip($name));
					fclose($objHandle);
					chmod($path, 0755);
				}							
			}
		}
		$objZip->close();
	}
	
	public static function moveImportedFiles($objAccount) {
		global $_CONF, $_PATHS;
		
		$sourceDir = $_PATHS['upload'] . $objAccount->getId() . "/";
		if (is_dir($sourceDir)) {
			$strServer = Setting::getValueByName('ftp_server', $objAccount->getId());
			$strUsername = Setting::getValueByName('ftp_username', $objAccount->getId());
			$strPassword = Setting::getValueByName('ftp_password', $objAccount->getId());
			$strRemoteFolder = Setting::getValueByName('ftp_remote_folder', $objAccount->getId());
		
			//*** Try to move the files.
			$objFtp = new FTP($strServer);
			if ($objFtp->login($strUsername, $strPassword) === TRUE) {
				//*** Passive mode.
				$objFtp->pasv(TRUE);
				
				if ($objHandle = opendir($sourceDir)) {
					while (FALSE !== ($strFile = readdir($objHandle))) {
						if ($strFile != "." && $strFile != "..") {
							//*** Transfer file.
							$objRet = $objFtp->nb_put($strRemoteFolder . $strFile, $sourceDir . $strFile, FTP_BINARY);
							while ($objRet == FTP_MOREDATA) {
							   // Continue uploading...
							   $objRet = $objFtp->nb_continue();
							}

							if ($objRet != FTP_FINISHED) {
								//*** Something went wrong. Continue without error.							
							} else {
								//*** Remove local file.
								@unlink($sourceDir . $strFile);
							}
						}
					}
					
					closedir($objHandle);
				}
			}
			
			//*** Remove dir if empty.
			if (count(scandir($sourceDir)) <= 2) {
				rmdir($sourceDir);
			}
		}		
	}

}

?>