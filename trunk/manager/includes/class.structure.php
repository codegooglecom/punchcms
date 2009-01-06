<?php

class Structure extends DBA_Structure {
	private $__name = "";
	private $__description = "";

	public static function selectBySection($strType) {
		$strSql = sprintf("SELECT * FROM pcms_structure WHERE section = '%s' ORDER BY sort", $strType);
		return Structure::select($strSql);
	}
	
	public static function hasSelect($intId) {
		$blnReturn = FALSE;
		$objDoc = Structure::getXmlDoc($intId);
		
		foreach ($objDoc->childNodes as $rootNode) {
			if ($rootNode->nodeName == "structure") {
				//*** Valid structure XML.
				foreach ($rootNode->childNodes as $selectsNode) {
					if ($selectsNode->nodeName == "selects") {
						foreach ($selectsNode->childNodes as $selectNode) {
							$blnReturn = TRUE;
							break 3;
						}
					}
				}
			}
		}
		
		return $blnReturn;
	}
	
	public static function getSelectsById($intId) {
		$objDoc = Structure::getXmlDoc($intId);
		$objSelects = new DBA__Collection();
		
		foreach ($objDoc->childNodes as $rootNode) {
			if ($rootNode->nodeName == "structure") {
				//*** Valid structure XML.
				foreach ($rootNode->childNodes as $selectsNode) {
					if ($selectsNode->nodeName == "selects") {
						foreach ($selectsNode->childNodes as $selectNode) {
							if ($selectNode->nodeName == "select") {
								$objSelect = new StructureSelect($selectNode);
								$objSelects->addObject($objSelect);
							}
						}
					}
				}
			}
		}
		
		return $objSelects;
	}
	
	public static function addById($intId, $intParentId = 0) {
		global $_CONF;
		
		$objDoc = Structure::getXmlDoc($intId);
		
		$arrUserIds = array();
		$arrGroupIds = array();
		$arrLanguageIds[0] = 0;
		$arrTemplateIds[0] = 0;
		$arrTemplateFieldIds[0] = 0;
		$arrLinkFieldIds = array();
		$arrElementIds[0] = 0;
		$arrElementFieldIds["link"][0] = 0;
		$arrElementFieldIds["largeText"][0] = 0;
		$intTemplateParentId = 0;
		$intElementParentId = 0;
		
		//*** Get structure fields from selects.
		if (Structure::hasSelect($intId)) {
			$objSelects = Structure::getSelectsById($intId);
			foreach ($objSelects as $objSelect) {
				switch ($objSelect->getType()) {
					case "language":
						$intId = Request::get("frm_select_{$objSelect->getId()}");
						$arrLanguageIds[$objSelect->getLogicId()] = $intId;
						break;
					case "element":
						$intId = Request::get("frm_select_{$objSelect->getId()}");
						if ($objSelect->getLogicId() == "PARENT") {
							$intElementParentId = $intId;
						} else {
							$arrElementIds[$objSelect->getLogicId()] = $intId;
						}
						break;
				}
			}
		}
				
		foreach ($objDoc->childNodes as $rootNode) {
			if ($rootNode->nodeName == "structure") {
				//*** Valid structure XML.
				switch ($rootNode->getAttribute("type")) {
					case "template":
						$intTemplateParentId = $intParentId;
						break;
					case "element":
						$intElementParentId = $intParentId;
						break;					
				}
				
				foreach ($rootNode->childNodes as $logicNode) {
					if ($logicNode->nodeName == "logic") {
						foreach ($logicNode->childNodes as $childNode) {
							switch ($childNode->nodeName) {
								case "languages":
									//*** Add languages to the account.
									foreach ($childNode->childNodes as $languageNode) {
										$objLanguage = new ContentLanguage();
										$objLanguage->setAccountId($_CONF['app']['account']->getId());
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

								case "templates":
									//*** Add templates to the account.
									ExImport::importTemplates($childNode, $_CONF['app']['account']->getId(), $arrTemplateIds, $arrTemplateFieldIds, $arrLinkFieldIds, $intTemplateParentId);
									break;

								case "elements":
									//*** Add elements to the account.
									ExImport::importElements($childNode, $_CONF['app']['account']->getId(), $arrTemplateIds, $arrTemplateFieldIds, $arrElementIds, $arrElementFieldIds, $arrLinkFieldIds, $arrLanguageIds, $arrUserIds, $arrGroupIds, $intElementParentId);
									break;

								case "aliases":
									//*** Add aliases to the account.
									foreach ($childNode->childNodes as $aliasNode) {
										$objAlias = new Alias();
										$objAlias->setAccountId($_CONF['app']['account']->getId());
										$objAlias->setAlias($aliasNode->getAttribute("alias"));
										if (array_key_exists($aliasNode->getAttribute("url"), $arrElementIds)) {
											$objAlias->setUrl($arrElementIds[$aliasNode->getAttribute("url")]);
										} else {
											$objAlias->setUrl(0);
										}
										$objAlias->setActive($aliasNode->getAttribute("active"));
										$objAlias->setSort($aliasNode->getAttribute("sort"));
										$objAlias->setCreated($aliasNode->getAttribute("created"));
										$objAlias->setModified($aliasNode->getAttribute("modified"));
										$objAlias->save();
									}
									break;

							}
							
							//*** Adjust the links for deeplink fields.
							ExImport::adjustDeeplinks($arrElementFieldIds["link"], $arrElementIds, $arrLanguageIds);
							
							//*** Adjust the links in large text fields.
							ExImport::adjustTextlinks($arrElementFieldIds["largeText"], $arrElementIds, $arrLanguageIds, array(0));
							
						}
					}
				}
			}
		}
	}
	
	public function getName() {
		if (empty($this->__name)) $this->getMeta();
		
		return $this->__name;
	}
	
	public function getDescription() {
		if (empty($this->__description)) $this->getMeta();
		
		return $this->__description;	
	}
	
	public static function getXmlDoc($intId) {
		global $_PATHS;
		
		$objStructure = Structure::selectByPk($intId);
		$strXml = $_PATHS['structures'] . $objStructure->getFileName() . ".xml";
		
		//*** Init DOM object.
		$objDoc = new DOMDocument("1.0", "UTF-8");
		$objDoc->formatOutput = FALSE;
		$objDoc->preserveWhiteSpace = TRUE;
		if (is_file($strXml)) {
			$objDoc->load($strXml);
		} else {
			$objDoc->loadXML($strXml);
		}
		
		return $objDoc;
	}
	
	private function getMeta() {
		global $objLang;
		
		$strSql = sprintf("SELECT * FROM pcms_structure_meta WHERE structureId = '%s' AND language = '%s' ORDER BY sort", $this->getId(), $objLang->language);
		$objElements = StructureDetails::select($strSql);
		
		if ($objElements->count() > 0) {
			$objElement = $objElements->current();
			
			$this->__name = $objElement->getName();
			$this->__description = $objElement->getDescription();
		}
	}
}

?>