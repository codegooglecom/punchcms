<?php

/************************
* User Class.
*
* Usage:
*   $objUser = new User($objDb, userId [optional]);
**/

class User {

	public static function add() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intPermUserId	= request("perm_user_id");
		$blnActive 		= (request("is_active") == "on") ? true : false;
		$strHandle 		= request("handle");
		$strName 		= request("name");
		$strEmail 		= request("email");
		$strPassword	= request("passwd");
		$arrRights 		= request("rights");
		$arrGroups 		= request("groups");
		$intPermType	= request("perm_type");

		if ($intPermUserId == "") { //*** Add Mode
			$data = array(
				'handle' => $strHandle,
				'name' => $strName,
				'passwd' => $strPassword,
				'is_active' => $blnActive,
				'email' => $strEmail,
				'account_id' => $_CONF['app']['account']->getId(),
				'perm_type' => $intPermType
			);
			$intPermUserId = $objLiveAdmin->addUser($data);

			$filters = array('filters' => array('perm_user_id' => $intPermUserId));
			$objUsers = $objLiveAdmin->getUsers($filters);
			$intAuthUserId = $objUsers[0]['auth_user_id'];
		} else { //*** Edit Mode
			if (Account::validate('perm_user', $intPermUserId)) {
				$data = array(
					'handle' => $strHandle,
					'name' => $strName,
					'is_active' => $blnActive,
					'email' => $strEmail,
					'perm_type' => $intPermType
				);
				if (!empty($strPassword)) $data['passwd'] = $strPassword;
				$objLiveAdmin->updateUser($data, $intPermUserId);

				$filters = array('filters' => array('perm_user_id' => $intPermUserId));
				$objUsers = $objLiveAdmin->getUsers($filters);
				$intAuthUserId = $objUsers[0]['auth_user_id'];
			}
		}

		if (Account::validate('perm_user', $intPermUserId)) {
			$filters = array('perm_user_id' => $intPermUserId);
			$result = $objLiveAdmin->perm->revokeUserRight($filters);

			if (is_array($arrRights)) {
				foreach ($arrRights as $value) {
					if (Account::validate('right', $value)) {
						$data = array(
							'perm_user_id' => $intPermUserId,
							'right_id' => $value,
							'right_level' => 3,
							'account_id' => $_CONF['app']['account']->getId(),
						);

						$result = $objLiveAdmin->perm->grantUserRight($data);
					}
				}
			}

			$filters = array('perm_user_id' => $intPermUserId);
			$result = $objLiveAdmin->perm->removeUserFromGroup($filters);

			if (is_array($arrGroups)) {
				foreach ($arrGroups as $value) {
					if (Account::validate('group', $value)) {
						$data = array(
							'perm_user_id' => $intPermUserId,
							'group_id' => $value,
						);
						$result = $objLiveAdmin->perm->addUserToGroup($data);
					}
				}
			}
		}

		$strReturn .= "<fields>";

		$strReturn .= "<field name=\"auth_user_id\">";
		$strReturn .= "<value>$intAuthUserId</value>";
		$strReturn .= "</field>";

		$strReturn .= "<field name=\"perm_user_id\">";
		$strReturn .= "<value>$intPermUserId</value>";
		$strReturn .= "</field>";

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function load() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn 		= "";
		$intPermUserId	= request("perm_user_id");

		$filters = array('container' => 'perm', 'filters' => array('perm_user_id' => $intPermUserId));
		$objUsers = $objLiveAdmin->getUsers($filters);

		$strReturn = "<fields>";

		foreach ($objUsers as $objUser) {
			if (Account::validate('perm_user', $objUser['perm_user_id'])) {
				foreach ($objUser as $key => $value) {
					if ($key != "passwd") {
						if ($key == "perm_type") {
							$strReturn .= "<field clear=\"false\" name=\"$key\">";
						} else {
							$strReturn .= "<field name=\"$key\">";
						}
						$strReturn .= "<value>$value</value>";
						$strReturn .= "</field>";
					}
				}

				//*** Groups.
				$filters = array('filters' => array('perm_user_id' => $objUser['perm_user_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
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
						$strReturn .= "<value id=\"{$objAllGroup['group_id']}\" name=\"group_{$objAllGroup['group_id']}\">{$objAllGroup['group_define_name']}</value>";
					}
				}
				$strReturn .= "</widget>";

				//*** Rights.
				$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
				$objApps = $objLiveAdmin->perm->getApplications($filters);
				if (is_array($objApps)) {

					$strReturn .= "<widget name=\"rights\" contain=\"allrights\">";
					foreach ($objApps as $objApp) {
						$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
						$objAreas = $objLiveAdmin->perm->getAreas($filters);
						foreach ($objAreas as $objArea) {
							$filters = array('filters' => array('area_id' => $objArea['area_id'], 'perm_user_id' => $intPermUserId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
							$objRights = $objLiveAdmin->perm->getRights($filters);

							foreach ($objRights as $objRight) {
								$strReturn .= "<value id=\"{$objRight['right_id']}\" name=\"right_{$objRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objRight['right_define_name']}</value>";
							}
						}
					}
					$strReturn .= "</widget>";

					$strReturn .= "<widget name=\"allrights\" contain=\"rights\">";
					foreach ($objApps as $objApp) {
						$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
						$objAreas = $objLiveAdmin->perm->getAreas($filters);
						foreach ($objAreas as $objArea) {
							$filters = array('filters' => array('area_id' => $objArea['area_id'], 'perm_user_id' => $intPermUserId, 'account_id' => array('0', $_CONF['app']['account']->getId())));
							$objRights = $objLiveAdmin->perm->getRights($filters);

							$filters = array('filters' => array('area_id' => $objArea['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
							$objAllRights = $objLiveAdmin->perm->getRights($filters);

							foreach ($objAllRights as $objAllRight) {
								if (!in_array($objAllRight, $objRights)) {
									$strReturn .= "<value id=\"{$objAllRight['right_id']}\" name=\"right_{$objAllRight['right_id']}\">{$objApp['application_define_name']}::{$objArea['area_define_name']}::{$objAllRight['right_define_name']}</value>";
								}
							}
						}
					}
					$strReturn .= "</widget>";
				}
			}
		}

		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function remove() {
		global $objLiveAdmin;

		$strReturn 		= "";
		$intPermUserId	= request("perm_user_id");

		$strReturn .= "<fields>";

		if (Account::validate('perm_user', $intPermUserId)) {
			$filters = array('perm_user_id' => $intPermUserId);
			$objLiveAdmin->removeUser($filters);
		}

		$strReturn .= "<field name=\"perm_user_id\">";
		$strReturn .= "<value>$intPermUserId</value>";
		$strReturn .= "</field>";
		$strReturn .= "</fields>";

		return $strReturn;
	}

	public static function clearForm() {
		global $objLiveAdmin,
				$_CONF;

		$strReturn = "<fields>";

		//*** Groups.
		$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
		$objGroups = $objLiveAdmin->perm->getGroups($filters);

		$strReturn .= "<widget name=\"groups\" contain=\"allgroups\" />";
		$strReturn .= "<widget name=\"allgroups\" contain=\"groups\">";
		foreach ($objGroups as $objGroup) {
			$strReturn .= "<value id=\"{$objGroup['group_id']}\" name=\"group_{$objGroup['group_id']}\">{$objGroup['group_define_name']}</value>";
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

	public static function remindPassword($strUser) {
		global $objLiveAdmin,
				$objLang,
				$_CONF;

		$blnReturn = false;

		$filters = array('container' => 'auth', 'filters' => array('handle' => $strUser, 'account_id' => array($_CONF['app']['account']->getId())));
		$arrUsers = $objLiveAdmin->getUsers($filters);

		if (is_array($arrUsers)) {
			foreach ($arrUsers as $objUser) {
				//*** A valid user account has been found.

				//*** Generate a new password.
				$strPass = self::makePasswd(8);

				//*** Update user account.
				$data = array('passwd' => $strPass);
				$objLiveAdmin->updateUser($data, $objUser['perm_user_id']);

				//*** Mail the new password to the user.
				$objMail = new htmlMimeMail5();

				$objMail->setFrom($_CONF['comm']['mailFrom']);
				$objMail->setSubject($objLang->get("subjectReminder", "login"));
				$objMail->setText(sprintf($objLang->get("textReminder", "login"), $objUser['name'], $strPass, Request::getRootUri()));
				$objMail->send(array($objUser['email']));

				$blnReturn = true;
			}
		}

		return $blnReturn;
	}

	public static function makePasswd($intLength = 8) {
		$strChars = "abcdefghijkmnopqrstuvwxyz023456789ABCDEFGHIJKLMN!@#^";
		srand((double)microtime()*1000000);
		$strReturn = '';

		for ($i = 0; $i <= $intLength - 1; $i++) {
			$intNum = rand() % (strlen($strChars) - 1);
			$strTmp = substr($strChars, $intNum, 1);
			$strReturn .= $strTmp;
		}

		return $strReturn;
	}

}

?>