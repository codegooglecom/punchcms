<?php

/* AuditLog Class v0.1.0
 * Handles AuditLog properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class AuditLog extends DBA_AuditLog {

	public static function addLog($strType, $intId, $strName, $strAction, $strDescription = "") {
		global $objLiveUser,
			$_CONF;
		
		if (Setting::selectByName("audit_enable")) {
			$objLog = new AuditLog();
			$objLog->setAccountId($_CONF['app']['account']->getId());
			$objLog->setType($strType);
			$objLog->setTypeId($intId);
			$objLog->setTypeName($strName);
			$objLog->setUserId($objLiveUser->getProperty("auth_user_id"));
			$objLog->setUserName($objLiveUser->getProperty("name"));
			$objLog->setAction($strAction);
			$objLog->setDescription($strDescription);
			$objLog->save();
		}
	}

	public static function cleanLog($blnClearAll = FALSE) {
		global $_CONF;
					
		if (Setting::getValueByName("audit_enable")) {
			if ($blnClearAll) {
				$strSql = "DELETE FROM pcms_audit_log WHERE accountId = '%s'";
				$strSql = sprintf($strSql, $_CONF['app']['account']->getId());
			} else {
				$strDate = Date::toMySql(time() - (60 * 60 * 24 * Setting::getValueByName("audit_rotation")));
				$strSql = "DELETE FROM pcms_audit_log WHERE created < '%s' AND accountId = '%s'";
				$strSql = sprintf($strSql, $strDate, $_CONF['app']['account']->getId());
			}

			parent::select($strSql);
		}
	}

}

?>