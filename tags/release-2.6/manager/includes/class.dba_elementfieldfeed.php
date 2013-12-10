<?php

/***
 *
 * ElementFieldFeed DBA Class.
 *
 */

class DBA_ElementFieldFeed extends DBA__Object {
	protected $id = NULL;
	protected $elementid = 0;
	protected $templatefieldid = 0;
	protected $feedpath = "";
	protected $xpath = "";
	protected $languageid = 0;
	protected $cascade = 0;

	//*** Constructor.
	public function DBA_ElementFieldFeed() {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array(), $accountId = NULL) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::selectByPK($varValue, $arrFields, $accountId);
	}

	public static function select($strSql = "") {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = true) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete($accountId = NULL) {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::delete($accountId);
	}

	public function duplicate() {
		self::$__object = "ElementFieldFeed";
		self::$__table = "pcms_element_field_feed";

		return parent::duplicate();
	}
}

?>