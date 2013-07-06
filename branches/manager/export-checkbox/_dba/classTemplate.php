<?php

/***
 *
 * {className} DBA Class.
 *
 */

class DBA_{className} extends DBA__Object {
{classProperties}
	//*** Constructor.
	public function DBA_{className}() {
		self::$__object = "{className}";
		self::$__table = "{classTable}";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "{className}";
		self::$__table = "{classTable}";

		return parent::duplicate();
	}
}

?>