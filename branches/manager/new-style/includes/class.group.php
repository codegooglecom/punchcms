<?php

/************************
* Group Class.
*
* Usage:
*   $objGroup = new Group($objDb, groupId [optional]);
**/

class Group {

	public static function add() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 	= "";
		$intGroupId		= request("group_id");
		$blnActive 		= (request("is_active") == "on") ? true : false;
		$strName 		= request("group_define_name");
		$arrRights 		= request("rights");
		$arrUsers 		= request("users");
		$arrSubGroups	= request("subgroups");

		if ($intGroupId == "") { //*** Add Mode
			$data = array(
				'group_define_name' => $strName,
				'is_active' => $blnActive,
				'account_id' => $_CONF['app']['account']->getId()
			);
			$intGroupId = $objLiveAdmin->perm->addGroup($data);
		} else { //*** Edit Mode
			if (Account::validate('group', $intGroupId)) {
				$data = array('group_define_name' => $strName, 'is_active' => $blnActive);
				$filters = array('group_id' => $intGroupId);
				$objLiveAdmin->perm->updateGroup($data, $filters);
			}
		}

		//*** Subgroups.
		$filters = array('group_id' => $intGroupId);
		$result = $objLiveAdmin->perm->unassignSubGroup($filters);

		if (is_array($arrSubGroups)) {
			foreach ($arrSubGroups as $value) {
				if (Account::validate('group', $value)) {
					$data = array(
						'group_id' => $intGroupId,
						'subgroup_id' => $value,
					);
					$result = $objLiveAdmin->perm->assignSubGroup($data);
				}
			}
		}

		//*** Rights.
		$filters = array('group_id' => $intGroupId);
		$result = $objLiveAdmin->perm->revokeGroupRight($filters);

		if (is_array($arrRights)) {
			foreach ($arrRights as $value) {
				if (Account::validate('right', $value)) {
					$data = array(
						'group_id' => $intGroupId,
						'right_id' => $value,
						'right_level' => 3,
						'account_id' => $_CONF['app']['account']->getId(),
					);
					$result = $objLiveAdmin->perm->grantGroupRight($data);
				}
			}
		}

		//*** Users.
		$filters = array('group_id' => $intGroupId);
		$result = $objLiveAdmin->perm->removeUserFromGroup($filters);

		if (is_array($arrUsers)) {
			foreach ($arrUsers as $value) {
				if (Account::validate('perm_user', $value)) {
					$data = array(
						'group_id' => $intGroupId,
						'perm_user_id' => $value,
					);
					$result = $objLiveAdmin->perm->addUserToGroup($data);
				}
			}
		}

		$strReturn .= "<fields>";

		$strReturn .= "<field name=\"group_id\">";
		$strReturn .= "<value>$intGroupId</value>";
		$strReturn .= "</field>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function load() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intGroupId 	= request("group_id");

		$filters = array('filters' => array('group_id' => $intGroupId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
		$objGroups = $objLiveAdmin->perm->getGroups($filters);

		$strReturn .= "<fields>";

		foreach ($objGroups as $objGroup) {
			foreach ($objGroup as $key => $value) {
				$strReturn .= "<field name=\"$key\">";
				$strReturn .= "<value>$value</value>";
				$strReturn .= "</field>";
			}

			//*** Subgroups.
			//*** Get all subgroups recursivly for this group.
			$filters = array(
				'select' => 'all',
            	'rekey' => true,
            	'filters' => array('group_id' => $intGroupId),
            	'hierarchy' => true,
            );
			$objSubGroups = $objLiveAdmin->perm->getGroups($filters);
			$objAllSubGroups = array();
			$objDirectSubGroups = array();

			//*** Filter the subgroups into a flat array.
			if (isset($objSubGroups[$intGroupId]['subgroups'])) {
				$objDirectSubGroups = self::filterSubGroups($objSubGroups[$intGroupId]['subgroups'], false);
			}

			//*** Render contained subgroups in the first level.
			$strReturn .= "<widget name=\"subgroups\" contain=\"allsubgroups\">";
			foreach ($objDirectSubGroups as $objDirectSubGroup) {
				$strReturn .= "<value id=\"{$objDirectSubGroup['group_id']}\" name=\"group_{$objDirectSubGroup['group_id']}\">{$objDirectSubGroup['group_define_name']}</value>";
			}
			$strReturn .= "</widget>";

			//*** Get the remainig available groups.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objAllGroups = $objLiveAdmin->perm->getGroups($filters);

			$strReturn .= "<widget name=\"allsubgroups\" contain=\"subgroups\">";
			foreach ($objAllGroups as $objAllGroup) {
				//*** Get the subgroups for every group.
				$filters = array(
					'select' => 'all',
					'rekey' => true,
					'filters' => array('group_id' => $objAllGroup['group_id']),
					'hierarchy' => true,
				);
				$objSubGroups = $objLiveAdmin->perm->getGroups($filters);
				if (isset($objSubGroups[$objAllGroup['group_id']]['subgroups'])) {
					$objAllSubGroups = self::filterSubGroups($objSubGroups[$objAllGroup['group_id']]['subgroups']);
				} else {
					$objAllSubGroups = array();
				}

				//*** Check if the subgroup is a parent for this group.
				if (!self::hasSubGroup($objAllSubGroups, $intGroupId)
						&& $objAllGroup['group_id'] != $intGroupId
						&& !self::deep_in_array($objDirectSubGroups, $objAllGroup['group_id'], "group_id")) {
					$strReturn .= "<value id=\"{$objAllGroup['group_id']}\" name=\"group_{$objAllGroup['group_id']}\">{$objAllGroup['group_define_name']}</value>";
				}
			}
			$strReturn .= "</widget>";

			//*** Users.
			$filters = array('container' => 'perm', 'filters' => array('group_id' => $intGroupId));
			$objUsers = $objLiveAdmin->getUsers($filters);

			$strReturn .= "<widget name=\"users\" contain=\"allusers\">";
			foreach ($objUsers as $objUser) {
				$strReturn .= "<value id=\"{$objUser['perm_user_id']}\" name=\"user_{$objUser['perm_user_id']}\">{$objUser['handle']}</value>";
			}
			$strReturn .= "</widget>";

			//*** Get the remainig available users.
			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objAllUsers = $objLiveAdmin->getUsers($filters);

			$strReturn .= "<widget name=\"allusers\" contain=\"users\">";
			foreach ($objAllUsers as $objAllUser) {
				if (!in_array($objAllUser, $objUsers)) {
					$strReturn .= "<value id=\"{$objAllUser['perm_user_id']}\" name=\"user_{$objAllUser['perm_user_id']}\">{$objAllUser['handle']}</value>";
				}
			}
			$strReturn .= "</widget>";

			//*** Rights.
			$strReturn .= "<widget name=\"rights\" contain=\"allrights\">";
			$filters = array('fields' => array('right_id'),	'filters' => array('group_id' => $intGroupId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
			$objRights = $objLiveAdmin->perm->getGroups($filters);

			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objAreas = $objLiveAdmin->perm->getAreas($filters);

			if (is_array($objRights)) {
				foreach ($objRights as $objRight) {
					//*** Fetch right details.
					$filters = array('filters' => array('right_id' => $objRight['right_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())), 'select' => 'row');
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
								if (!is_array($objRights) || is_array($objRights) && !in_array(array('right_id' => $objAllRight['right_id']), $objRights)) {
									$strReturn .= "<value id=\"{$objAllRight['right_id']}\" name=\"right_{$objAllRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objAllRight['right_define_name']}</value>";
								}
							}
						}
					}
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
		$intGroupId	= request("group_id");

		$strReturn .= "<fields>";

		if (Account::validate('group', $intGroupId)) {
			$filters = array('group_id' => $intGroupId);
			$objLiveAdmin->perm->removeGroup($filters);
		}

		$strReturn .= "<field name=\"group_id\">";
		$strReturn .= "<value>$intGroupId</value>";
		$strReturn .= "</field>";
		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function clearForm() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn = "<fields>";

		//*** Subgroups.
		$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
		$objSubGroups = $objLiveAdmin->perm->getGroups($filters);

		$strReturn .= "<widget name=\"subgroups\" contain=\"allsubgroups\" />";
		$strReturn .= "<widget name=\"allsubgroups\" contain=\"subgroups\">";
		foreach ($objSubGroups as $objSubGroup) {
			$strReturn .= "<value id=\"{$objSubGroup['group_id']}\" name=\"group_{$objSubGroup['group_id']}\">{$objSubGroup['group_define_name']}</value>";
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

		//*** Rights.
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
					$objRights = $objLiveAdmin->perm->getRights($filters);

					foreach ($objRights as $objRight) {
						$strReturn .= "<value id=\"{$objRight['right_id']}\" name=\"right_{$objRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objRight['right_define_name']}</value>";
					}
				}
			}
		}
		$strReturn .= "</widget>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function getSubGroups($arrGroups) {
		$arrReturn = array();

		foreach ($arrGroups as $key => $value)  {
			if ($key == "subgroups") {
				foreach ($value as $subKey => $subValue) {
					$arrReturn["$subKey"] = $subValue;
					$arrReturn["$subKey"]['subgroups'] = NULL;
				}
			}
			if (is_array($value)) {
				$arrTemp = self::getSubGroups($value);
				if (is_array($arrTemp) && count($arrTemp) > 0) $arrReturn[] = $arrTemp;
			}
		}

		return $arrReturn;
	}

	private static function filterSubGroups($arrGroups, $blnRecursive = true) {
		$arrReturn = array();

		foreach ($arrGroups as $key => $value) {
			if (is_numeric($key)) {
				if (isset($value['subgroups']) && $blnRecursive == true) {
					$arrReturn = self::filterSubGroups($value['subgroups'], $blnRecursive);
				}

				unset($value['subgroups']);
				$value['group_id'] = $key;
				$arrReturn[] = $value;
			}
		}

		return $arrReturn;
	}

	private static function hasSubGroup($arrGroups, $intGroupId) {
		$blnReturn = false;

		foreach ($arrGroups as $key => $value) {
			if (isset($value['group_id']) && $value['group_id'] == $intGroupId) {
				$blnReturn = true;
			}
		}

		return $blnReturn;
	}

	private static function deep_in_array($arrHay, $needleValue, $needleKey = "") {
		$blnReturn = false;

		foreach($arrHay as $key => $value) {
			if (is_array($value)) {
				$blnReturn = self::deep_in_array($value, $needleValue, $needleKey);
			} else {
				if (!empty($needleKey)) {
					if ($needleKey == $key && $needleValue == $value) $blnReturn = true;
				} else {
					if ($needleValue == $value) $blnReturn = true;
				}
			}
		}

		return $blnReturn;
	}

}

?>