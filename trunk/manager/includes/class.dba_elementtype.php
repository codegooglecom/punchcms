<?php

/***
 *
 * ElementType DBA Class.
 *
 */

class DBA_ElementType extends DBA__Object {
	protected $id = NULL;
	protected $name = "";

	//*** Constructor.
	public function DBA_ElementType() {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "ElementType";
		self::$__table = "pcms_element_type";

		return parent::duplicate();
	}
}

?>