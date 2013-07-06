<?php

/**
 *
 * Handles element language properties and methods.
 * @author felix
 * @version 0.1.0
 *
 */
class ElementLanguage extends DBA_ElementLanguage {

	public static function deleteByElement($intElementId) {
		self::$object = "ElementLanguage";
		self::$table = "pcms_element_language";

		$strSql = sprintf("DELETE FROM " . self::$table . " WHERE elementId = '%s'", self::quote($intElementId));
		self::select($strSql);
	}

	public static function selectByElement($intElementId) {
		self::$object = "ElementLanguage";
		self::$table = "pcms_element_language";

		$strSql = sprintf("SELECT * FROM " . self::$table . " WHERE elementId = '%s'", self::quote($intElementId));
		return self::select($strSql);
	}

	public static function deleteByLanguage($intLanguageId) {
		self::$object = "ElementLanguage";
		self::$table = "pcms_element_language";

		$strSql = sprintf("DELETE FROM " . self::$table . " WHERE languageId = '%s'", self::quote($intLanguageId));
		self::select($strSql);
	}
}
