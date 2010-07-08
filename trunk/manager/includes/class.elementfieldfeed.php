<?php

/* ElementFieldFeed Class v0.1.0
 * Handles ElementFieldFeed properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class ElementFieldFeed extends DBA_ElementFieldFeed {

	public static function selectByElement($intElementId, $intLanguageId = NULL) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";
	
		$objReturn = NULL;
	
		$strSql = "SELECT * FROM pcms_element_field_feed WHERE elementId = '%s'";
		if (is_numeric($intLanguageId)) $strSql .= " AND languageId = '%s'";
		$strSql .= " ORDER BY sort";
		$strSql = (is_numeric($intLanguageId)) ? sprintf($strSql, quote_smart($intElementId), quote_smart($intLanguageId)) : sprintf($strSql, quote_smart($intElementId));
		$objReturn = self::select($strSql);
		
		return $objReturn;
	}

	public static function selectByTemplateField($intTemplateFieldId, $intLanguageId) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";
	
		$objReturn = NULL;
	
		$strSql = "SELECT * FROM pcms_element_field_feed WHERE templateFieldId = '%s' AND languageId = '%s'";
		$strSql = sprintf($strSql, quote_smart($intTemplateFieldId), quote_smart($intLanguageId));
		$objFields = self::select($strSql);
		
		if ($objFields->count() > 0) $objReturn = $objFields->current();
		
		return $objReturn;
	}
	
	public function getCascades() {
		$arrReturn = Array();

		if ($this->id > 0) {
			$objContentLangs = ContentLanguage::select();
			foreach ($objContentLangs as $objContentLanguage) {
				if ($this->getCascade() == 1) {
					array_push($arrReturn, $objContentLanguage->getId());
				}
			}
		}

		return $arrReturn;
	}

}

?>