<?php

/* ElementMeta Class v0.1.0
 * Handles ElementMeta properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class ElementMeta extends DBA_ElementMeta {
	private $languageId = 0;
	private $arrMeta = array("title" => "", "keywords" => "", "description" => "", "cascade" => 0);

	public function __construct($intElementId) {
		$this->elementId = $intElementId;
	}

	public function getTitle($intLanguageId = 0) {
		return $this->getMetaField("title", $intLanguageId);
	}

	public function getKeywords($intLanguageId = 0) {
		return $this->getMetaField("keywords", $intLanguageId);
	}

	public function getDescription($intLanguageId = 0) {
		return $this->getMetaField("description", $intLanguageId);
	}
	
	public function getCascades() {
		$arrReturn = Array();

		$objContentLangs = ContentLanguage::select();
		foreach ($objContentLangs as $objContentLanguage) {
			$strTemp = $this->getTitle($objContentLanguage->getId());
			if ($this->arrMeta["cascade"] == 1) {
				array_push($arrReturn, $objContentLanguage->getId());
			}
		}

		return $arrReturn;
	}

	private function getMetaField($strField, $intLanguageId = 0) {
		$strReturn = "";
		if ($intLanguageId == 0) $intLanguageId = ContentLanguage::getDefault()->getId();
		
		if ($intLanguageId == $this->languageId) {
			$strReturn = $this->arrMeta[$strField];
		} else {
			$this->arrMeta["title"] = "";
			$this->arrMeta["keywords"] = "";
			$this->arrMeta["description"] = "";
			$this->arrMeta["cascade"] = 0;

			$strSql = "SELECT * FROM pcms_element_meta WHERE elementId = '%s' AND languageId = '%s'";
			$objElements = parent::select(sprintf($strSql, $this->elementId, $intLanguageId));
			
			if ($objElements->count() > 0) {
				$objElement = $objElements->current();
				
				$this->arrMeta["title"] = $objElement->getTitle();
				$this->arrMeta["keywords"] = $objElement->getKeywords();
				$this->arrMeta["description"] = $objElement->getDescription();
				$this->arrMeta["cascade"] = $objElement->getCascade();
				
				$this->languageId = $intLanguageId;
				
				$strReturn = $this->arrMeta[$strField];
			}
		}

		return $strReturn;
	}
}

?>