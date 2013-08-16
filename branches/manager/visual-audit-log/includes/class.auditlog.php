<?php

/* AuditLog Class v0.1.0
 * Handles AuditLog properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class AuditLog extends DBA_AuditLog
{
    protected static $arrStringToConstant = array(
        "Element" => AUDIT_TYPE_ELEMENT,
        "Template" => AUDIT_TYPE_TEMPLATE,
        "TemplateField" => AUDIT_TYPE_TEMPLATEFIELD,
        "Alias" => AUDIT_TYPE_ALIAS,
        "Language" => AUDIT_TYPE_LANGUAGE,
        "Setting" => AUDIT_TYPE_SETTING,
        "User" => AUDIT_TYPE_USER,
        "Storage" => AUDIT_TYPE_STORAGE,
        "Feed" => AUDIT_TYPE_FEED
    );

	public static function addLog($strType, $intId, $strName, $strAction, $strDescription = "")
	{
		if (Setting::selectByName("audit_enable")) {
			$objLog = new AuditLog();
			$objLog->setAccountId($GLOBALS["_CONF"]['app']['account']->getId());
			$objLog->setType($strType);
			$objLog->setTypeId($intId);
			$objLog->setTypeName($strName);
			$objLog->setUserId($GLOBALS["objLiveUser"]->getProperty("auth_user_id"));
			$objLog->setUserName($GLOBALS["objLiveUser"]->getProperty("name"));
			$objLog->setAction($strAction);
			$objLog->setDescription($strDescription);
			$objLog->save();
		}
	}

	public static function cleanLog($blnClearAll = false)
	{
		if (Setting::getValueByName("audit_enable")) {
			if ($blnClearAll) {
				$strSql = "DELETE FROM pcms_audit_log WHERE accountId = '%s'";
				$strSql = sprintf($strSql, $GLOBALS["_CONF"]['app']['account']->getId());
			} else {
				$strDate = Date::toMySql(time() - (60 * 60 * 24 * Setting::getValueByName("audit_rotation")));
				$strSql = "DELETE FROM pcms_audit_log WHERE created < '%s' AND accountId = '%s'";
				$strSql = sprintf($strSql, $strDate, $GLOBALS["_CONF"]['app']['account']->getId());
			}

			parent::select($strSql);
		}
	}

	public static function getHeaders()
	{
	    return array(
	        "Type"
	    );
	}

	public static function typeToString($intType)
	{
        $arrFlipped = array_flip(self::$arrStringToConstant);

	    return $arrFlipped[$intType];
	}

	public static function getLastRecords($intAmount = 30)
	{
	    $arrReturn = array();

	    $strSql = "SELECT * FROM pcms_audit_log WHERE accountId = '%s' ORDER BY created DESC LIMIT 0,{$intAmount}";
	    $strSql = sprintf($strSql, $GLOBALS["_CONF"]['app']['account']->getId());

	    $objRecords = parent::select($strSql);

        $arrProperties = array(
            "id",
            "accountid",
            "type",
            "typeid",
            "typename",
            "userid",
            "username",
            "action",
            "description"
        );

	    foreach ($objRecords as $objRecord) {
	        $arrRecord = array();
	        foreach ($arrProperties as $strProperty) {
	            $arrRecord[$strProperty] = call_user_func_array(array($objRecord, "get" . $strProperty), array());
	        }

	        array_push($arrReturn, $arrRecord);
	    }

	    return $arrReturn;
	}

	public static function getLastRecordsJson($intAmount = 30)
	{
        return json_encode(self::getLastRecords($intAmount));
	}
}
