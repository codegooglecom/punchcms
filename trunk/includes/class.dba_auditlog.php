<?php

/***
 *
 * AuditLog DBA Class.
 *
 */

class DBA_AuditLog extends DBA__Object {
	protected $id = NULL;
	protected $accountid = 0;
	protected $type = 0;
	protected $typeid = 0;
	protected $typename = "";
	protected $userid = 0;
	protected $username = "";
	protected $action = "";
	protected $description = "";

	//*** Constructor.
	public function DBA_AuditLog() {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "AuditLog";
		self::$__table = "pcms_audit_log";

		return parent::duplicate();
	}
}

?>