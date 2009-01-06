<?php

/* Settings Class v0.1.0
 * Holds methods for PunchCMS settings.
 *
 * CHANGELOG
 * version 0.1.0, 16 Oct 2006
 *   NEW: Created class.
 */

class Settings extends DBA__Collection {

	function __construct($intAccountId = 0) {
		global $_CONF;

		if ($intAccountId == 0) {
			$this->AccountId = $_CONF['app']['account']->getId();
		} else {
			$this->AccountId = $intAccountId;
		}

		$objSettings = SettingTemplate::select("SELECT * FROM pcms_setting_tpl ORDER BY section, sort");

		parent::__construct($objSettings);
	}
	
	public static function getByAccount($intAccountId = 0) {
		global $_CONF;

		if ($intAccountId == 0) {
			$intAccountId = $_CONF['app']['account']->getId();
		}
		
		$objSettings = Setting::select(sprintf("SELECT * FROM pcms_setting WHERE accountId = '%s' ORDER BY sort", quote_smart($intAccountId)));
		return $objSettings;
	}

}

?>