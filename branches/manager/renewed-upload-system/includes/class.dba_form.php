<?php

/***
 *
 * Form DBA Class.
 *
 */

class DBA_Form extends DBA__Object {
	protected $id = NULL;
	protected $accountid = 0;
	protected $name = "";
	protected $apiname = "";
	protected $description = "";
	protected $active = 0;
	protected $username = "";

	//*** Constructor.
	public function DBA_Form() {
		self::$__object = "Form";
		self::$__table = "pcms_form";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "Form";
		self::$__table = "pcms_form";

		return parent::duplicate();
	}
}

?>