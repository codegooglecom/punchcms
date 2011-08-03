<?php

class InsertFeedElement {
	private $__template = NULL;
	private $__parent = NULL;
	private $__permissions = NULL;
	private $__defaultLanguage = NULL;
	private $__fields = array();
	private $active = FALSE;
	private $name = "";
	private $username = "";
	private $alias = "";
	private $sort = 0;

	public function __construct($objParent) {
		$this->__parent = $objParent;

		$this->__permissions = new ElementPermission();
		if (is_object($this->__parent)) {
			$objPermissions = $this->__parent->getPermissions();
			$this->__permissions->setUserId($objPermissions->getUserId());
			$this->__permissions->setGroupId($objPermissions->getGroupId());
		}

		$this->__defaultLanguage = ContentLanguage::getDefault()->getId();
	}

	public function setTemplate($intTemplateId) {
		$this->__template = Template::selectByPK($intTemplateId);
	}

	public function setTemplateName($strApiName) {
		$this->__template = Template::selectByName($strApiName);
	}

	public function addField($intTemplateFieldId, $varValue, $intLanguageId = NULL, $blnCascade = FALSE) {
		if (is_null($intLanguageId)) $intLanguageId = $this->__defaultLanguage;

		$arrField = (array_key_exists($intTemplateFieldId, $this->__fields)) ? $this->__fields[$intTemplateFieldId] : array();

		if (is_null($intLanguageId)) {
			//*** Insert for all languages.
			$objLangs = ContentLanguage::select();
			foreach ($objLangs as $objLang) {
				if (($blnCascade && !$objLang->default) || !$blnCascade) {
					$arrValue = array('value' => $varValue, 'cascade' => $blnCascade);
					$arrField[$objLang->getId()] = $arrValue;
				}
			}

			if ($blnCascade) {
				//*** Set the default language.
				$arrValue = array('value' => $varValue, 'cascade' => FALSE);
				$arrField[$this->__defaultLanguage] = $arrValue;
			}
		} else {
			$arrValue = array('value' => $varValue, 'cascade' => $blnCascade);
			$arrField[$intLanguageId] = $arrValue;
		}
		
		$this->__fields[$intTemplateFieldId] = $arrField;
	}

	public function save() {
		global $_CONF, $_PATHS;
		
		if (is_object($this->__template)) {
			$strServer = Setting::getValueByName('ftp_server');
			$strUsername = Setting::getValueByName('ftp_username');
			$strPassword = Setting::getValueByName('ftp_password');
			$strRemoteFolder = Setting::getValueByName('ftp_remote_folder');
		
			//*** Element.
			$objElement = new Element();
			$objElement->setParentId($this->__parent->getId());
			$objElement->setAccountId($_CONF['app']['account']->getId());
			$objElement->setPermissions($this->__permissions);
			$objElement->setActive($this->active);
			$objElement->setName($this->name);
			$objElement->setUsername($this->username);
			$objElement->setSort($this->sort);
			$objElement->setTypeId(ELM_TYPE_LOCKED);
			$objElement->setTemplateId($this->__template->getId());
			$objElement->save(TRUE, FALSE);
			
			//*** Alias.
			if (!empty($this->alias)) $objElement->setAlias($this->alias);

			//*** Activate default schedule.
			$objSchedule = new ElementSchedule();
			$objSchedule->setStartActive(0);
			$objSchedule->setStartDate(APP_DEFAULT_STARTDATE);
			$objSchedule->setEndActive(0);
			$objSchedule->setEndDate(APP_DEFAULT_ENDDATE);
			$objElement->setSchedule($objSchedule);

			foreach ($this->__fields as $intTemplateFieldId => $arrField) {
				$objTemplateField = TemplateField::selectByPK($intTemplateFieldId);
				$objField = new ElementField();
				$objField->setElementId($objElement->getId());
				$objField->setTemplateFieldId($objTemplateField->getId());
				$objField->save();

				foreach ($arrField as $intLanguage => $arrValue) {
					$objValue = $objField->getNewValueObject();
					
					switch ($objField->getTypeId()) {
						case FIELD_TYPE_FILE:
						case FIELD_TYPE_IMAGE:
							//*** Upload file.
							$arrPath = parse_url($arrValue['value']);
							if ($arrPath !== FALSE) {
								$strFile = @file_get_contents(str_replace(" ", "%20", $arrValue['value']));
								if ($strFile !== FALSE) {
									$strOriginalName = array_pop(explode("/", $arrPath['path']));
									$strLocalValue = ImageField::filename2LocalName($strOriginalName);
									$objImageField = new ImageField($intTemplateFieldId);
									$arrSettings = $objImageField->getSettings();

									if (file_put_contents($_PATHS['upload'] . $strOriginalName, $strFile) !== FALSE) {
										if (count($arrSettings) > 1) {
											foreach ($arrSettings as $key => $arrSetting) {																	
												$strFileName = FileIO::add2Base($strLocalValue, $arrSetting['key']);
												if (copy($_PATHS['upload'] . $strOriginalName, $_PATHS['upload'] . $strFileName)) {							
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
														Log::handleError("File {$strFileName} could not be moved to remote server. " . $objUpload->errorMessage());
													}
												}						
											}			
						
											//*** Move original file.
											if (rename($_PATHS['upload'] . $strOriginalName, $_PATHS['upload'] . $strLocalValue)) {	
												$objUpload = new SingleUpload();																	
												if (!$objUpload->moveToFTP($strLocalValue, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
													Log::handleError("File {$strLocalValue} could not be moved to remote server. " . $objUpload->errorMessage());
												}
											}
											
											//*** Unlink original file.
											@unlink($_PATHS['upload'] . $strOriginalName);
										} else {																
											if ($objTemplateField->getTypeId() == FIELD_TYPE_IMAGE && (
													!empty($arrSettings[0]['width']) ||
													!empty($arrSettings[0]['height']))) {
																		
												$strFileName = FileIO::add2Base($strLocalValue, $arrSettings[0]['key']);
												
												//*** Resize the image.
												if (rename($_PATHS['upload'] . $strOriginalName, $_PATHS['upload'] . $strFileName)) {	
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
														Log::handleError("File {$strFileName} could not be moved to remote server.");
													}
												}																
											}
											
											//*** Move original file.
											if (file_exists($_PATHS['upload'] . $strOriginalName) && rename($_PATHS['upload'] . $strOriginalName, $_PATHS['upload'] . $strLocalValue)) {	
												//*** Move file to remote server.
												$objUpload = new SingleUpload();
												if (!$objUpload->moveToFTP($strLocalValue, $_PATHS['upload'], $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
													Log::handleError("File {$strLocalValue} could not be moved to remote server.");
												}
											}
											
											//*** Unlink original file.
											@unlink($_PATHS['upload'] . $strOriginalName);																
										}
									
										$objValue->setValue($strOriginalName . ":" . $strLocalValue . "\n");										
									}
								}
							}
							break;
						
						default:
							$objValue->setValue($arrValue['value']);
					}
					
					$objValue->setLanguageId($intLanguage);
					$objValue->setCascade($arrValue['cascade']);
					$objField->setValueObject($objValue);
					
					//*** Activate the language.
					$objElement->setLanguageActive($intLanguage, TRUE);
				}
			}

			return $objElement;
		}
	}

	public function __get($property) {
		$property = strtolower($property);

		if (isset($this->$property) || is_null($this->$property)) {
			return $this->$property;
		} else {
			echo "Property Error in " . self::$__object . "::get({$property}) on line " . __LINE__ . ".";
		}
	}

	public function __set($property, $value) {
		$property = strtolower($property);

		if (isset($this->$property) || is_null($this->$property)) {
			$this->$property = $value;
		} else {
			echo "Property Error in " . self::$__object . "::set({$property}) on line " . __LINE__ . ".";
		}
	}

	public function __call($method, $values) {
		if (substr($method, 0, 3) == "get") {
			$property = substr($method, 3);
			return $this->$property;
		}

		if (substr($method, 0, 3) == "set") {
			$property = substr($method, 3);
			$this->$property = $values[0];
			return;
		}

		echo "Method Error in " . self::$__object . "::{$method} on line " . __LINE__ . ".";
	}

}

?>