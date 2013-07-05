<?php

/************************
* Application Class.
*
* Usage:
*   $objApplication = new Application($objDb, applicationId [optional]);
**/

class Application {

	public static function add() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 	= "";
		$intAppId		= request("application_id");
		$strName 		= request("application_define_name");
		$arrAreas 		= request("areas");

		if ($intAppId == "") { //*** Add Mode
			$data = array(
				'application_define_name' => $strName,
				'account_id' => $_CONF['app']['account']->getId()
			);
			$intAppId = $objLiveAdmin->perm->addApplication($data);
		} else { //*** Edit Mode
			if (Account::validate('application', $intAppId, CMD_EDIT)) {
				$data = array('application_define_name' => $strName);
				$filters = array('application_id' => $intAppId);
				$objLiveAdmin->perm->updateApplication($data, $filters);
			}
		}

		$strReturn .= "<fields>";

		$strReturn .= "<field name=\"application_id\">";
		$strReturn .= "<value>$intAppId</value>";
		$strReturn .= "</field>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function load() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intAppId 		= request("application_id");

		$filters = array('filters' => array('application_id' => $intAppId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
		$objApps = $objLiveAdmin->perm->getApplications($filters);

		$strReturn .= "<fields>";

		foreach ($objApps as $objApp) {
			foreach ($objApp as $key => $value) {
				$strReturn .= "<field name=\"$key\">";
				$strReturn .= "<value>$value</value>";
				$strReturn .= "</field>";
			}

			//*** Areas.
			$filters = array('filters' => array('application_id' => $intAppId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
			$objAreas = $objLiveAdmin->perm->getAreas($filters);

			$strReturn .= "<widget name=\"areas\">";
			foreach ($objAreas as $objArea) {
				$strReturn .= "<value id=\"{$objArea['area_id']}\" name=\"area_{$objArea['area_id']}\">{$objArea['area_define_name']}</value>";
			}
			$strReturn .= "</widget>";
		}

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function remove() {
		global $objLiveAdmin;

		$strReturn 	= "";
		$intAppId	= request("application_id");

		$strReturn .= "<fields>";

		if (Account::validate('application', $intAppId, CMD_REMOVE)) {
			$filters = array('application_id' => $intAppId);
			$objLiveAdmin->perm->removeApplication($filters);
		}

		$strReturn .= "<field name=\"application_id\">";
		$strReturn .= "<value>$intAppId</value>";
		$strReturn .= "</field>";
		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function clearForm() {
		global $objLiveAdmin;

		$strReturn = "<fields>";

		//*** Areas.
		$strReturn .= "<widget name=\"areas\" />";

		$strReturn .= "</fields>";

		return $strReturn;
	}

}

?>