<?php

/***
 *
 * Structure DBA Class.
 *
 */

class DBA_Structure extends DBA__Object {
	protected $id = NULL;
	protected $filename = "";
	protected $section = "";

	//*** Constructor.
	public function DBA_Structure() {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "Structure";
		self::$__table = "pcms_structure";

		return parent::duplicate();
	}
}

?>