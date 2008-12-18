<?php

/***
 *
 * AnnounceMessage DBA Class.
 *
 */

class DBA_AnnounceMessage extends DBA__Object {
	protected $id = NULL;
	protected $header = "";
	protected $message = "";

	//*** Constructor.
	public function DBA_AnnounceMessage() {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "AnnounceMessage";
		self::$__table = "pcms_announce_message";

		return parent::duplicate();
	}
}

?>