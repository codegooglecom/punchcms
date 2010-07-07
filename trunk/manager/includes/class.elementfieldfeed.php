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
		if (is_int($intLanguageId)) $strSql .= " AND languageId = '%s'";
		$strSql .= " ORDER BY sort";
		$strSql = (is_int($intLanguageId)) ? sprintf($strSql, quote_smart($intElementId), quote_smart($intLanguageId)) : sprintf($strSql, quote_smart($intElementId));
		$objReturn = self::select($strSql);
		
		return $objReturn;
	}

}

?>