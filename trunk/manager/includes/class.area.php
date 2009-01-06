<?php

/************************
* Area Class.
*
* Usage:
*   $objArea = new Area($objDb, areaId [optional]);
**/

class Area {

	public static function add() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 	= "";
		$intAreaId		= request("area_id");
		$strName 		= request("area_define_name");
		$intApplication	= request("application_id");
		$arrUsers 		= request("users");

		if ($intAreaId == "") { //*** Add Mode
			$data = array(
				'area_define_name' => $strName,
				'application_id' => $intApplication,
				'account_id' => $_CONF['app']['account']->getId()
			);
			$intAreaId = $objLiveAdmin->perm->addArea($data);
		} else { //*** Edit Mode
			if (Account::validate('area', $intAreaId, CMD_EDIT)) {
				$data = array('area_define_name' => $strName, 'application_id' => $intApplication);
				$filters = array('area_id' => $intAreaId);
				$objLiveAdmin->perm->updateArea($data, $filters);
			}
		}

		$filters = array('area_id' => $intAreaId);
		$result = $objLiveAdmin->perm->removeAreaAdmin($filters);

		if (is_array($arrUsers)) {
			foreach ($arrUsers as $value) {
				if (Account::validate('perm_user', $value)) {
					$data = array(
						'area_id' => $intAreaId,
						'perm_user_id' => $value,
					);
					$result = $objLiveAdmin->perm->addAreaAdmin($data);
				}
			}
		}

		$strReturn .= "<fields>";

		$strReturn .= "<field name=\"area_id\">";
		$strReturn .= "<value>$intAreaId</value>";
		$strReturn .= "</field>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function load() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intAreaId 		= request("area_id");

		$filters = array('filters' => array('area_id' => $intAreaId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
		$objAreas = $objLiveAdmin->perm->getAreas($filters);

		$strReturn .= "<fields>";

		foreach ($objAreas as $objArea) {
			foreach ($objArea as $key => $value) {
				if ($key == "application_id") {
					$strReturn .= "<field clear=\"false\" name=\"$key\">";
				} else {
					$strReturn .= "<field name=\"$key\">";
				}
				$strReturn .= "<value>$value</value>";
				$strReturn .= "</field>";
			}

			//*** Users.
			$filters = array('fields' => array('perm_user_id'), 'filters' => array('area_id' => $intAreaId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->perm->getAreas($filters);

			$strReturn .= "<widget name=\"users\" contain=\"allusers\">";
			foreach ($objUsers as $objUser) {
				$filters = array('filters' => array('perm_user_id' => $objUser['perm_user_id']), 'select' => 'row');
				$objUser = $objLiveAdmin->getUsers($filters);
				$strReturn .= "<value id=\"{$objUser['perm_user_id']}\" name=\"user_{$objUser['perm_user_id']}\">{$objUser['handle']}</value>";
			}
			$strReturn .= "</widget>";

			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objAllUsers = $objLiveAdmin->getUsers($filters);

			$strReturn .= "<widget name=\"allusers\" contain=\"users\">";
			foreach ($objAllUsers as $objAllUser) {
				if (!in_array(array('perm_user_id' => $objAllUser['perm_user_id']), $objUsers)) {
					if ($objAllUser['perm_type'] >= 3) {
						$strReturn .= "<value id=\"{$objAllUser['perm_user_id']}\" name=\"user_{$objAllUser['perm_user_id']}\">{$objAllUser['handle']}</value>";
					}
				}
			}
			$strReturn .= "</widget>";

			//*** Rights.
			$strReturn .= "<widget name=\"rights\">";
			$filters = array('filters' => array('area_id' => $intAreaId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
			$objRights = $objLiveAdmin->perm->getRights($filters);

			if (is_array($objRights)) {
				foreach ($objRights as $objRight) {
					$strReturn .= "<value id=\"{$objRight['right_id']}\" name=\"right_{$objRight['right_id']}\">{$objRight['right_define_name']}</value>";
				}
			}
			$strReturn .= "</widget>";
		}

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function remove() {
		global $objLiveAdmin;

		$strReturn 	= "";
		$intAreaId 	= request("area_id");

		$strReturn .= "<fields>";

		if (Account::validate('area', $intAreaId, CMD_REMOVE)) {
			$filters = array('area_id' => $intAreaId);
			$objLiveAdmin->perm->removeArea($filters);
		}

		$strReturn .= "<field name=\"area_id\">";
		$strReturn .= "<value>$intAreaId</value>";
		$strReturn .= "</field>";
		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function clearForm() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn = "<fields>";

		//*** Users.
		$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
		$objUsers = $objLiveAdmin->getUsers($filters);

		$strReturn .= "<widget name=\"users\" contain=\"allusers\" />";
		$strReturn .= "<widget name=\"allusers\" contain=\"users\">";
		foreach ($objUsers as $objUser) {
			if ($objUser['perm_type'] >= 3) {
				$strReturn .= "<value id=\"{$objUser['perm_user_id']}\" name=\"user_{$objUser['perm_user_id']}\">{$objUser['handle']}</value>";
			}
		}
		$strReturn .= "</widget>";

		//*** Rights.
		$strReturn .= "<widget name=\"rights\" />";

		$strReturn .= "</fields>";

		return $strReturn;
	}

}

?>