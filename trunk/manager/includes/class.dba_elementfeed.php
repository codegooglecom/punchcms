<?php

/***
 *
 * ElementFeed DBA Class.
 *
 */

class DBA_ElementFeed extends DBA__Object {
	protected $id = NULL;
	protected $elementid = 0;
	protected $feedid = 0;
	protected $feedpath = "";
	protected $maxitems = "";
	protected $sortby = "";

	//*** Constructor.
	public function DBA_ElementFeed() {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "ElementFeed";
		self::$__table = "pcms_element_feed";

		return parent::duplicate();
	}
}

?>