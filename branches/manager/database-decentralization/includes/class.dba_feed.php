<?php

/***
 *
 * Feed DBA Class.
 *
 */

class DBA_Feed extends DBA__Object {
	protected $id = NULL;
	protected $accountid = 0;
	protected $name = "";
	protected $feed = "";
	protected $basepath = "";
	protected $refresh = 0;
	protected $lastupdate = "0000-00-00 00:00:00";
	protected $active = 1;

	//*** Constructor.
	public function DBA_Feed() {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "Feed";
		self::$__table = "pcms_feed";

		return parent::duplicate();
	}
}

?>