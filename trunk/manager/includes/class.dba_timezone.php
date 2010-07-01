<?php

/***
 *
 * Timezone DBA Class.
 *
 */

class DBA_Timezone extends DBA__Object {
	protected $id = NULL;
	protected $shortname = "";
	protected $longname = "";

	//*** Constructor.
	public function DBA_Timezone() {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "Timezone";
		self::$__table = "punch_timezone";

		return parent::duplicate();
	}
}

?>