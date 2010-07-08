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
		parent::$__object = "Feed";
		parent::$__table = "pcms_feed";
		
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
	
}

?>