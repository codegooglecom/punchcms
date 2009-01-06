<?php

/***
 *
 * AnnounceUser DBA Class.
 *
 */

class DBA_AnnounceUser extends DBA__Object {
	protected $id = NULL;
	protected $messageid = 0;
	protected $permuserid = 0;

	//*** Constructor.
	public function DBA_AnnounceUser() {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "AnnounceUser";
		self::$__table = "pcms_announce_user";

		return parent::duplicate();
	}
}

?>