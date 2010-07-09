<?php

/* Feed Class v0.1.0
 * Handles feed properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class Feed extends DBA_Feed {
	
	public function save($blnSaveModifiedDate = TRUE) {
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";
		
		$intId = $this->getId();
		
		$blnReturn = parent::save($blnSaveModifiedDate);
		AuditLog::addLog(AUDIT_TYPE_FEED, $this->getId(), $this->getFeed(), (empty($intId)) ? "create" : "edit", ($this->getActive()) ? "active" : "inactive");

		return $blnReturn;
	}

	public function delete() {
		global $_CONF, $_PATHS;
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";
			
		$objElementFeeds = ElementFeed::selectByFeed($this->getId());
		foreach ($objElementFeeds as $objElementFeed) {
			$objElement = Element::selectByPK($objElementFeed->getElementId());
			
			//*** Remove dynamic element.
			$objElement->delete();
		}
		
		//*** Remove cached feed.
		@unlink($_PATHS['upload'] . $this->getHash());
		
		AuditLog::addLog(AUDIT_TYPE_FEED, $this->getId(), $this->getFeed(), "delete");
		return parent::delete();
	}

	public static function select($strSql = "") {
		global $_CONF;
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";

		if (empty($strSql)) {
			$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' ORDER BY sort", $_CONF['app']['account']->getId());
		}

		return parent::select($strSql);
	}

	public static function selectActive() {
		global $_CONF;
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";

		$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' AND active = '1' ORDER BY sort", $_CONF['app']['account']->getId());

		return parent::select($strSql);
	}

	public static function selectSorted() {
		global $_CONF;
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";

		$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' ORDER BY name", $_CONF['app']['account']->getId());

		return parent::select($strSql);
	}

	public function cache() {
		global $_PATHS;
		
		@file_put_contents($_PATHS['upload'] . $this->getHash(), file_get_contents($this->getFeed()));
	}
	
	public function getRawBody() {
		global $_PATHS;
		
		$strReturn = @file_get_contents($_PATHS['upload'] . $this->getHash());
		
		return $strReturn;
	}
	
	public function getBody($strXpath = "") {
		$strBody = $this->getRawBody();
		
		$objXml = simplexml_load_string($strBody);
		$objReturn = $objXml->xpath($this->getBasepath() . $strXpath);
		
		return $objReturn;
	}
	
	public function getStructuredNodes($strXpath = "") {
		$arrReturn = array();
		
		$objElements = $this->getBody($strXpath);
		foreach ($objElements as $objElement) {
			if ($objElement instanceof SimpleXMLElement && !isset($arrReturn[$objElement->getName()])) {
				$arrReturn[$objElement->getName()] = $objElement;
			}
		}
		
		return $arrReturn;
	}
	
	public function getHash() {
		global $_CONF;
		
		return md5($_CONF['app']['account']->getId() . $this->getFeed() . $this->getBasePath());
	}
	
	public function updateElements() {
		global $_CONF;
		
		$this->cache();
		
		$objLangs = ContentLanguage::select();
		$objElementFeeds = ElementFeed::selectByFeed($this->getId());
		foreach ($objElementFeeds as $objElementFeed) {
			$objElement = Element::selectByPK($objElementFeed->getElementId());
			$objParent = Element::selectByPK($objElement->getParentId());
			if (is_object($objElement) && is_object($objParent) && $objParent->getTypeId() != ELM_TYPE_DYNAMIC) {
				//*** Remove old elements.
				$objOldElements = $objParent->getElements(FALSE, ELM_TYPE_LOCKED, $_CONF['app']['account']->getId());
				foreach ($objOldElements as $objOldElement) {
					$objOldElement->delete();
				}
				
				$this->recursiveFeedInsert($objElement, $objParent, NULL, $objLangs);
			}
		}
	}
	
	private function recursiveFeedInsert($objElement, $objParent, $objNode, $objLangs) {
		global $objLiveUser, $_CONF;
		
		$objElementFeed = $objElement->getFeed();
		$objTemplate = Template::selectByPK($objElement->getTemplateId());
		
		if (is_null($objNode)) {
			$objNodes = $objElementFeed->getBody();
		} else {
			$strFeedPath = $objElementFeed->getFeedPath();
			if (empty($strFeedPath)) {
				$objNodes = array($objNode);
			} else {
				$objNodes = $objNode->xpath($objElementFeed->getFeedPath());
			}
		}
		
		$intMaxItems = $objElementFeed->getMaxItems();
		if (empty($intMaxItems)) $intMaxItems = 0;
		$intCount = 1;
		
		foreach ($objNodes as $objNode) {
			//*** Create elements.
			$strName = "";
			$objInsertElement = new InsertFeedElement($objParent);
			$objInsertElement->setTemplate($objElement->getTemplateId());
			
			foreach ($objLangs as $objLang) {
				$objFeedFields = ElementFieldFeed::selectByElement($objElement->getId(), $objLang->getId());
				foreach ($objFeedFields as $objFeedField) {
					$strPath = $objFeedField->getXPath();
					if (stripos($strPath, "user->") !== FALSE) {
						$strValue = str_replace("user->", "", $strPath);
						$objInsertElement->addField($objFeedField->getTemplateFieldId(), $strValue, $objLang->getId(), $objFeedField->getCascade());
					} else {
						$objValue = (!empty($strPath)) ? $objNode->xpath($strPath) : NULL;
						if (!is_object($objValue) && count($objValue) > 0) {
							$strValue = (string) current($objValue);
							$objInsertElement->addField($objFeedField->getTemplateFieldId(), $strValue, $objLang->getId(), $objFeedField->getCascade());
							
							if (!is_numeric($strValue) && empty($strName)) {
								$strName = getShortValue($strValue, 40, TRUE, "");
							} 
						}
					}
				}	
			}
			
			$strName = (empty($strName)) ? "Dynamic" : $strName;
			$objInsertElement->setName($strName);
			$objInsertElement->setUsername($objLiveUser->getProperty('handle'));
			$objInsertElement->setActive(TRUE);
			$objInsertedElement = $objInsertElement->save();
						
			//*** Sub elements.
			$objSubElements = $objElement->getElements(FALSE, ELM_TYPE_DYNAMIC, $_CONF['app']['account']->getId());
			foreach ($objSubElements as $objSubElement) {
				$this->recursiveFeedInsert($objSubElement, $objInsertedElement, $objNode, $objLangs);
			}
			
			if ($intMaxItems > 0 && $intCount >= $intMaxItems) break;
			$intCount++;
		}
	}
}

?>