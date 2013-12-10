<?php

class FeedParserElement extends SimpleXMLElement {
	
	public function getStructure() {
		$arrReturn = array();
		
		foreach ($this as $objElement) {
			if (!isset($arrReturn[$objElement->getName()])) {
				$arrReturn[$objElement->getName()] = $objElement;
			}
		}
		
		return $arrReturn;
	}
	
	public function getAttributes() {
		return $this->attributes();
	}
	
	public function getXpath($objRoot) {
		$objDom = dom_import_simplexml($objRoot);
		print_r($objDom);
		$objParent = $objDom->parentNode;
		print_r($objParent);
		$strReturn = "/";

		if ($objParent->length > 0) {
			$strReturn = "/" + $this->getName() + "[1]";
		}

		$strReturn = ($strReturn != "/") ? new FeedParserElement($objParent) . $strReturn : "";
	
		return $strReturn;
	}
	
}

?>