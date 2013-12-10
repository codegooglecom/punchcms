<?php

/***
 *
 * Product DBA Class.
 *
 */

class DBA_Product extends DBA__Object {
	protected $id = NULL;
	protected $parentid = 0;
	protected $name = "";
	protected $active = 0;

	//*** Constructor.
	public function DBA_Product() {
		self::$__object = "Product";
		self::$__table = "punch_product";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = true) {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "Product";
		self::$__table = "punch_product";

		return parent::duplicate();
	}
}

?>