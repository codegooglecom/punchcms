<?php

/***
 *
 * AccountProduct DBA Class.
 *
 */

class DBA_AccountProduct extends DBA__Object {
	protected $id = NULL;
	protected $accountid = 0;
	protected $productid = 0;
	protected $expires = "0000-00-00 00:00:00";

	//*** Constructor.
	public function DBA_AccountProduct() {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "AccountProduct";
		self::$__table = "punch_account_product";

		return parent::duplicate();
	}
}

?>