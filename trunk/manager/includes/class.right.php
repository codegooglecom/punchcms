<?php

/************************
* Right Class.
*
* Usage:
*   $objRight = new Right($objDb, rightId [optional]);
**/

class Right {

	public static function add() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 	= "";
		$intRightId		= request("right_id");
		$strName 		= request("right_define_name");
		$intAreaId 		= request("area_id");
		$arrRights 		= request("rights");
		$arrUsers 		= request("users");
		$arrGroups 		= request("groups");

		if ($intRightId == "") { //*** Add Mode
			$data = array(
				'right_define_name' => $strName,
				'area_id' => $intAreaId,
				'account_id' => $_CONF['app']['account']->getId()
			);
			$intRightId = $objLiveAdmin->perm->addRight($data);
		} else { //*** Edit Mode
			if (Account::validate('right', $intRightId, CMD_EDIT)) {
				$data = array('right_define_name' => $strName, 'area_id' => $intAreaId);
				$filters = array('right_id' => $intRightId);
				$objLiveAdmin->perm->updateRight($data, $filters);
			}
		}

		//*** Handle Implied rights.
		$filters = array('right_id' => $intRightId);
		$result = $objLiveAdmin->perm->unimplyRight($filters);

		if (is_array($arrRights)) {
			foreach ($arrRights as $value) {
				if (Account::validate('right', $intRightId, CMD_EDIT) && Account::validate('right', $value)) {
					$data = array(
						'right_id' => $intRightId,
						'implied_right_id' => $value,
					);
					$result = $objLiveAdmin->perm->implyRight($data);
				}
			}
		}

		//*** Handle Groups.
		$filters = array('right_id' => $intRightId);
		$result = $objLiveAdmin->perm->revokeGroupRight($filters);

		if (is_array($arrGroups)) {
			foreach ($arrGroups as $value) {
				if (Account::validate('group', $value)) {
					$data = array(
						'right_id' => $intRightId,
						'group_id' => $value,
						'right_level' => 3,
						'account_id' => $_CONF['app']['account']->getId(),
					);
					$result = $objLiveAdmin->perm->grantGroupRight($data);
				}
			}
		}

		//*** Handle Users.
		$filters = array('right_id' => $intRightId);
		$result = $objLiveAdmin->perm->revokeUserRight($filters);

		if (is_array($arrUsers)) {
			foreach ($arrUsers as $value) {
				if (Account::validate('perm_user', $value)) {
					$data = array(
						'right_id' => $intRightId,
						'perm_user_id' => $value,
						'right_level' => 3,
						'account_id' => $_CONF['app']['account']->getId(),
					);
					$result = $objLiveAdmin->perm->grantUserRight($data);
				}
			}
		}

		$strReturn .= "<fields>";

		$strReturn .= "<field name=\"right_id\">";
		$strReturn .= "<value>$intRightId</value>";
		$strReturn .= "</field>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function load() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intRightId		= request("right_id");

		$filters = array('filters' => array('right_id' => $intRightId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
		$objRights = $objLiveAdmin->perm->getRights($filters);

		$strReturn .= "<fields>";

		foreach ($objRights as $objRight) {
			foreach ($objRight as $key => $value) {
				if ($key == "area_id") {
					$strReturn .= "<field clear=\"false\" name=\"$key\">";
				} else {
					$strReturn .= "<field name=\"$key\">";
				}
				$strReturn .= "<value>$value</value>";
				$strReturn .= "</field>";
			}

			//*** Implied Rights.
			$strReturn .= "<widget name=\"rights\" contain=\"allrights\">";
			$filters = array('fields' => array('implied_right_id'),	'filters' => array('right_id' => $intRightId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
			$objRights = $objLiveAdmin->getRights($filters);

			if (is_array($objRights)) {
				foreach ($objRights as $objRight) {
					//*** Fetch right details.
					$filters = array('filters' => array('right_id' => $objRight['implied_right_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())), 'select' => 'row');
					$objRight = $objLiveAdmin->getRights($filters);

					//*** Fetch area details.
					$filters = array('fields' => array('area_define_name', 'application_define_name'), 'filters' => array('area_id' => $objRight['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())), 'select' => 'row');
					$objArea = $objLiveAdmin->perm->getAreas($filters);

					$strReturn .= "<value id=\"{$objRight['right_id']}\" name=\"right_{$objRight['right_id']}\">{$objArea['application_define_name']}::{$objArea['area_define_name']}::{$objRight['right_define_name']}</value>";
				}
			}
			$strReturn .= "</widget>";

			$strReturn .= "<widget name=\"allrights\" contain=\"rights\">";

			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					foreach ($objAreas as $objArea) {
						$filters = array('filters' => array('area_id' => $objArea['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
						$objAllRights = $objLiveAdmin->getRights($filters);

						if (is_array($objAllRights)) {
							foreach ($objAllRights as $objAllRight) {
								if (
									!is_array($objRights) && $objAllRight['right_id'] != $intRightId ||
									is_array($objRights) && !in_array(array('implied_right_id' => $objAllRight['right_id']), $objRights)  && $objAllRight['right_id'] != $intRightId) {
									$strReturn .= "<value id=\"{$objAllRight['right_id']}\" name=\"right_{$objAllRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objAllRight['right_define_name']}</value>";
								}
							}
						}
					}
				}
			}
			$strReturn .= "</widget>";

			//*** Users.
			$filters = array('filters' => array('right_id' => $intRightId, 'account_id' => array($_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->getUsers($filters);

			$strReturn .= "<widget name=\"users\" contain=\"allusers\">";
			foreach ($objUsers as $objUser) {
				$strReturn .= "<value id=\"{$objUser['perm_user_id']}\" name=\"user_{$objUser['perm_user_id']}\">{$objUser['handle']}</value>";
			}
			$strReturn .= "</widget>";

			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objAllUsers = $objLiveAdmin->getUsers($filters);

			$strReturn .= "<widget name=\"allusers\" contain=\"users\">";
			foreach ($objAllUsers as $objAllUser) {
				if (!in_array($objAllUser, $objUsers)) {
					$strReturn .= "<value id=\"{$objAllUser['perm_user_id']}\" name=\"user_{$objAllUser['perm_user_id']}\">{$objAllUser['handle']}</value>";
				}
			}
			$strReturn .= "</widget>";

			//*** Groups.
			$filters = array('filters' => array('right_id' => $intRightId, 'account_id' => array($_CONF['app']['account']->getId())));
			$objGroups = $objLiveAdmin->perm->getGroups($filters);

			$strReturn .= "<widget name=\"groups\" contain=\"allgroups\">";
			foreach ($objGroups as $objGroup) {
				$strReturn .= "<value id=\"{$objGroup['group_id']}\" name=\"group_{$objGroup['group_id']}\">{$objGroup['group_define_name']}</value>";
			}
			$strReturn .= "</widget>";

			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objAllGroups = $objLiveAdmin->perm->getGroups($filters);

			$strReturn .= "<widget name=\"allgroups\" contain=\"groups\">";
			foreach ($objAllGroups as $objAllGroup) {
				if (!in_array($objAllGroup, $objGroups)) {
					$strReturn .= "<value id=\"{$objAllGroup['group_id']}\" name=\"user_{$objAllGroup['group_id']}\">{$objAllGroup['group_define_name']}</value>";
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
		$intRightId	= request("right_id");

		$strReturn .= "<fields>";

		if (Account::validate('right', $intRightId, CMD_REMOVE)) {
			$filters = array('right_id' => $intRightId);
			$objLiveAdmin->perm->removeRight($filters);
		}

		$strReturn .= "<field name=\"right_id\">";
		$strReturn .= "<value>$intRightId</value>";
		$strReturn .= "</field>";
		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function clearForm() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn = "<fields>";

		//*** Implied Rights.
		$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
		$objApps = $objLiveAdmin->perm->getApplications($filters);

		$strReturn .= "<widget name=\"rights\" contain=\"allrights\" />";
		$strReturn .= "<widget name=\"allrights\" contain=\"rights\">";

		if (is_array($objApps)) {
			foreach ($objApps as $objApp) {
				$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
				$objAreas = $objLiveAdmin->perm->getAreas($filters);
				foreach ($objAreas as $objArea) {
					$filters = array('filters' => array('area_id' => $objArea['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAllRights = $objLiveAdmin->getRights($filters);

					if (is_array($objAllRights)) {
						foreach ($objAllRights as $objAllRight) {
							if (
								!is_array($objRights) && $objAllRight['right_id'] != $intRightId ||
								is_array($objRights) && !in_array(array('implied_right_id' => $objAllRight['right_id']), $objRights)  && $objAllRight['right_id'] != $intRightId) {
								$strReturn .= "<value id=\"{$objAllRight['right_id']}\" name=\"right_{$objAllRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objAllRight['right_define_name']}</value>";
							}
						}
					}
				}
			}
		}
		$strReturn .= "</widget>";

		//*** Groups.
		$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
		$objGroups = $objLiveAdmin->perm->getGroups($filters);

		$strReturn .= "<widget name=\"groups\" contain=\"allgroups\" />";
		$strReturn .= "<widget name=\"allgroups\" contain=\"groups\">";
		foreach ($objGroups as $objGroup) {
			$strReturn .= "<value id=\"{$objGroup['group_id']}\" name=\"group_{$objGroup['group_id']}\">{$objGroup['group_define_name']}</value>";
		}
		$strReturn .= "</widget>";

		//*** Users.
		$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
		$objUsers = $objLiveAdmin->getUsers($filters);

		$strReturn .= "<widget name=\"users\" contain=\"allusers\" />";
		$strReturn .= "<widget name=\"allusers\" contain=\"users\">";
		foreach ($objUsers as $objUser) {
			$strReturn .= "<value id=\"{$objUser['perm_user_id']}\" name=\"user_{$objUser['perm_user_id']}\">{$objUser['handle']}</value>";
		}
		$strReturn .= "</widget>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

}

?>