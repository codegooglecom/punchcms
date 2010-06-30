<?php

function parseUsers($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$objLiveAdmin,
			$_CONF;

	$objTpl = new HTML_Template_ITX($_PATHS['templates']);
	$objTpl->loadTemplatefile("users.tpl.htm");

	switch ($intElmntId) {
		case NAV_MYPUNCH_USERS_USER:
			$objTpl->addBlockfile('USER_EDIT', 'users.users', 'users_users.tpl.htm');

			//*** Fetch all users.
			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->getUsers($filters);
			if (is_array($objUsers)) {
				foreach ($objUsers as $objUser) {
					$objTpl->setCurrentBlock("main-items");
					$objTpl->setVariable("VALUE", $objUser["perm_user_id"]);
					$objTpl->setVariable("TEXT", $objUser["handle"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Fetch all rights.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					foreach ($objAreas as $objArea) {
						$filters = array('filters' => array('area_id' => $objArea['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
						$objRights = $objLiveAdmin->perm->getRights($filters);

						foreach ($objRights as $objRight) {
							$objTpl->setCurrentBlock("user-allrights");
							$objTpl->setVariable("VALUE", $objRight['right_id']);
							$objTpl->setVariable("TEXT", $objApp['application_define_name'] . "::" . $objArea['area_define_name'] . "::" . $objRight['right_define_name']);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Fetch all groups.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objGroups = $objLiveAdmin->perm->getGroups($filters);
			if (is_array($objGroups)) {
				foreach ($objGroups as $objGroup) {
					$objTpl->setCurrentBlock("user-allgroups");
					$objTpl->setVariable("VALUE", $objGroup["group_id"]);
					$objTpl->setVariable("TEXT", $objGroup["group_define_name"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Specific parsing.
			$objTpl->setVariable("ACTIVE_LABEL", $objLang->get("active", "usersLabel"));
			$objTpl->setVariable("USER_TYPE_LABEL", $objLang->get("userType", "usersLabel"));
			$objTpl->setVariable("USER_ANONYMOUS_LABEL", $objLang->get("typeAnonymous", "usersLabel"));
			$objTpl->setVariable("USER_USER_LABEL", $objLang->get("typeUser", "usersLabel"));
			$objTpl->setVariable("USER_ADMIN_LABEL", $objLang->get("typeAdmin", "usersLabel"));
			$objTpl->setVariable("USER_AREA_ADMIN_LABEL", $objLang->get("typeAreaAdmin", "usersLabel"));
			$objTpl->setVariable("USER_SUPER_ADMIN_LABEL", $objLang->get("typeSuperAdmin", "usersLabel"));
			$objTpl->setVariable("USER_MASTER_ADMIN_LABEL", $objLang->get("typeMasterAdmin", "usersLabel"));
			$objTpl->setVariable("USER_USERNAME_LABEL", $objLang->get("userName", "usersLabel"));
			$objTpl->setVariable("NAME_LABEL", $objLang->get("name", "usersLabel"));
			$objTpl->setVariable("USER_EMAIL_LABEL", $objLang->get("emailAddress", "usersLabel"));
			$objTpl->setVariable("USER_PASSWORD_LABEL", $objLang->get("password", "usersLabel"));
			$objTpl->setVariable("RIGHTS_LABEL", $objLang->get("rights", "usersLabel"));
			$objTpl->setVariable("SELECTED_RIGHTS_LABEL", $objLang->get("selectedRights", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_RIGHTS_LABEL", $objLang->get("availableRights", "usersLabel"));
			$objTpl->setVariable("GROUPS_LABEL", $objLang->get("groups", "usersLabel"));
			$objTpl->setVariable("SELECTED_GROUPS_LABEL", $objLang->get("selectedGroups", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_GROUPS_LABEL", $objLang->get("availableGroups", "usersLabel"));


			//*** General parsing.
			$objTpl->setVariable("SUBTITLE", $objLang->get("userDetails", "usersLabel"));
			$objTpl->setVariable("BUTTON_NEW", $objLang->get("new", "button"));
			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("BUTTON_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("LISTTITLE", $objLang->get("users", "usersLabel"));
			$objTpl->setVariable("OBJECT", "User");
			$objTpl->setVariable("FORM", "userForm");
			$objTpl->setVariable("ACTIVE_USER", " class=\"active\"");

			break;

		case NAV_MYPUNCH_USERS_GROUP:
			$objTpl->addBlockfile('USER_EDIT', 'users.groups', 'users_groups.tpl.htm');

			//*** Fetch all groups.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objGroups = $objLiveAdmin->perm->getGroups($filters);
			if (is_array($objGroups)) {
				foreach ($objGroups as $objGroup) {
					$objTpl->setCurrentBlock("main-items");
					$objTpl->setVariable("VALUE", $objGroup["group_id"]);
					$objTpl->setVariable("TEXT", $objGroup["group_define_name"]);
					$objTpl->parseCurrentBlock();

					$objTpl->setCurrentBlock("group-allsubgroups");
					$objTpl->setVariable("VALUE", $objGroup["group_id"]);
					$objTpl->setVariable("TEXT", $objGroup["group_define_name"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Fetch all rights.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					foreach ($objAreas as $objArea) {
						$filters = array('filters' => array('area_id' => $objArea['area_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
						$objRights = $objLiveAdmin->perm->getRights($filters);

						foreach ($objRights as $objRight) {
							$objTpl->setCurrentBlock("group-allrights");
							$objTpl->setVariable("VALUE", $objRight['right_id']);
							$objTpl->setVariable("TEXT", $objApp['application_define_name'] . "::" . $objArea['area_define_name'] . "::" . $objRight['right_define_name']);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Fetch all users.
			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->getUsers($filters);
			if (is_array($objUsers)) {
				foreach ($objUsers as $objUser) {
					$objTpl->setCurrentBlock("group-allusers");
					$objTpl->setVariable("VALUE", $objUser["perm_user_id"]);
					$objTpl->setVariable("TEXT", $objUser["handle"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Specific parsing.
			$objTpl->setVariable("ACTIVE_LABEL", $objLang->get("active", "usersLabel"));
			$objTpl->setVariable("NAME_LABEL", $objLang->get("name", "usersLabel"));
			$objTpl->setVariable("GROUP_LEVEL_LABEL", $objLang->get("groupLevel", "usersLabel"));
			$objTpl->setVariable("LEVEL_USER_LABEL", $objLang->get("levelUser", "usersLabel"));
			$objTpl->setVariable("LEVEL_GROUP_LABEL", $objLang->get("levelGroup", "usersLabel"));
			$objTpl->setVariable("LEVEL_ALL_LABEL", $objLang->get("levelAll", "usersLabel"));
			$objTpl->setVariable("RIGHTS_LABEL", $objLang->get("rights", "usersLabel"));
			$objTpl->setVariable("SELECTED_RIGHTS_LABEL", $objLang->get("selectedRights", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_RIGHTS_LABEL", $objLang->get("availableRights", "usersLabel"));
			$objTpl->setVariable("SUBGROUPS_LABEL", $objLang->get("subGroups", "usersLabel"));
			$objTpl->setVariable("SELECTED_GROUPS_LABEL", $objLang->get("selectedGroups", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_GROUPS_LABEL", $objLang->get("availableGroups", "usersLabel"));
			$objTpl->setVariable("USERS_LABEL", $objLang->get("users", "usersLabel"));
			$objTpl->setVariable("SELECTED_USERS_LABEL", $objLang->get("selectedUsers", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_USERS_LABEL", $objLang->get("availableUsers", "usersLabel"));

			//*** General parsing.
			$objTpl->setVariable("SUBTITLE", $objLang->get("groupDetails", "usersLabel"));
			$objTpl->setVariable("BUTTON_NEW", $objLang->get("new", "button"));
			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("BUTTON_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("LISTTITLE", $objLang->get("groups", "usersLabel"));
			$objTpl->setVariable("OBJECT", "Group");
			$objTpl->setVariable("FORM", "groupForm");
			$objTpl->setVariable("ACTIVE_GROUP", " class=\"active\"");

			break;

		case NAV_MYPUNCH_USERS_APPLICATION:
			$objTpl->addBlockfile('USER_EDIT', 'users.apps', 'users_apps.tpl.htm');

			//*** Fetch all applications.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$objTpl->setCurrentBlock("main-items");
					$objTpl->setVariable("VALUE", $objApp["application_id"]);
					$objTpl->setVariable("TEXT", $objApp["application_define_name"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Specific parsing.
			$objTpl->setVariable("NAME_LABEL", $objLang->get("name", "usersLabel"));
			$objTpl->setVariable("AREAS_LABEL", $objLang->get("areas", "usersLabel"));
			$objTpl->setVariable("SELECTED_AREAS_LABEL", $objLang->get("selectedAreas", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_AREAS_LABEL", $objLang->get("availableAreas", "usersLabel"));

			//*** General parsing.
			$objTpl->setVariable("SUBTITLE", $objLang->get("applicationDetails", "usersLabel"));
			$objTpl->setVariable("BUTTON_NEW", $objLang->get("new", "button"));
			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("BUTTON_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("LISTTITLE", $objLang->get("applications", "usersLabel"));
			$objTpl->setVariable("OBJECT", "Application");
			$objTpl->setVariable("FORM", "applicationForm");
			$objTpl->setVariable("ACTIVE_APP", " class=\"active\"");

			break;

		case NAV_MYPUNCH_USERS_AREA:
			$objTpl->addBlockfile('USER_EDIT', 'users.areas', 'users_areas.tpl.htm');

			//*** Fetch all applications.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$objTpl->setCurrentBlock("area-application");
					$objTpl->setVariable("VALUE", $objApp["application_id"]);
					$objTpl->setVariable("TEXT", $objApp["application_define_name"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Fetch all areas.
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp["application_id"], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					if (is_array($objAreas)) {
						foreach ($objAreas as $objArea) {
							$objTpl->setCurrentBlock("main-items");
							$objTpl->setVariable("VALUE", $objArea["area_id"]);
							$objTpl->setVariable("TEXT", $objApp["application_define_name"] . "::" . $objArea["area_define_name"]);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Fetch all users.
			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->getUsers($filters);
			if (is_array($objUsers)) {
				foreach ($objUsers as $objUser) {
					if ($objUser['perm_type'] >= 3) {
						$objTpl->setCurrentBlock("area-allusers");
						$objTpl->setVariable("VALUE", $objUser["perm_user_id"]);
						$objTpl->setVariable("TEXT", $objUser["handle"]);
						$objTpl->parseCurrentBlock();
					}
				}
			}

			//*** Specific parsing.
			$objTpl->setVariable("NAME_LABEL", $objLang->get("name", "usersLabel"));
			$objTpl->setVariable("APPLICATION_LABEL", $objLang->get("application", "usersLabel"));
			$objTpl->setVariable("AREA_ADMINS_LABEL", $objLang->get("areaAdmins", "usersLabel"));
			$objTpl->setVariable("SELECTED_ADMINS_LABEL", $objLang->get("selectedAdmins", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_ADMINS_LABEL", $objLang->get("availableAdmins", "usersLabel"));
			$objTpl->setVariable("RIGHTS_LABEL", $objLang->get("rights", "usersLabel"));
			$objTpl->setVariable("SELECTED_RIGHTS_LABEL", $objLang->get("selectedRights", "usersLabel"));

			//*** General parsing.
			$objTpl->setVariable("SUBTITLE", $objLang->get("areaDetails", "usersLabel"));
			$objTpl->setVariable("BUTTON_NEW", $objLang->get("new", "button"));
			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("BUTTON_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("LISTTITLE", $objLang->get("areas", "usersLabel"));
			$objTpl->setVariable("OBJECT", "Area");
			$objTpl->setVariable("FORM", "areaForm");
			$objTpl->setVariable("ACTIVE_AREA", " class=\"active\"");

			break;

		case NAV_MYPUNCH_USERS_RIGHT:
			$objTpl->addBlockfile('USER_EDIT', 'users.rights', 'users_rights.tpl.htm');

			//*** Fetch all rights.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					if (is_array($objAreas)) {
						foreach ($objAreas as $objArea) {
							$filters = array('filters' => array('area_id' => $objArea["area_id"], 'account_id' => array('0', $_CONF['app']['account']->getId())));
							$objRights = $objLiveAdmin->perm->getRights($filters);

							if (is_array($objRights)) {
								foreach ($objRights as $objRight) {
									//*** List all rights.
									$objTpl->setCurrentBlock("main-items");
									$strRight = $objApp['application_define_name'] . "::" . $objArea["area_define_name"] . "::" . $objRight["right_define_name"];
									$strTitle = (strlen($strRight) > 20) ? " title=\"{$strRight}\"" : "";
									$objTpl->setVariable("VALUE", $objRight["right_id"]);
									$objTpl->setVariable("TITLE", $strTitle);
									$objTpl->setVariable("TEXT", $objApp['application_define_name'] . "::" . $objArea["area_define_name"] . "::" . $objRight["right_define_name"]);
									$objTpl->parseCurrentBlock();

									//*** List all rights for implied rights.
									$objTpl->setCurrentBlock("right-allrights");
									$objTpl->setVariable("VALUE", $objRight["right_id"]);
									$objTpl->setVariable("TEXT", $objApp['application_define_name'] . "::" . $objArea["area_define_name"] . "::" . $objRight["right_define_name"]);
									$objTpl->parseCurrentBlock();
								}
							}
						}
					}
				}
			}

			//*** Fetch all areas.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objApps = $objLiveAdmin->perm->getApplications($filters);
			if (is_array($objApps)) {
				foreach ($objApps as $objApp) {
					$filters = array('filters' => array('application_id' => $objApp['application_id'], 'account_id' => array('0', $_CONF['app']['account']->getId())));
					$objAreas = $objLiveAdmin->perm->getAreas($filters);
					if (is_array($objAreas)) {
						foreach ($objAreas as $objArea) {
							$objTpl->setCurrentBlock("right-area");
							$objTpl->setVariable("VALUE", $objArea["area_id"]);
							$objTpl->setVariable("TEXT", $objApp['application_define_name'] . "::" . $objArea["area_define_name"]);
							$objTpl->parseCurrentBlock();
						}
					}
				}
			}

			//*** Fetch all groups.
			$filters = array('filters' => array('account_id' => array('0', $_CONF['app']['account']->getId())));
			$objGroups = $objLiveAdmin->perm->getGroups($filters);
			if (is_array($objGroups)) {
				foreach ($objGroups as $objGroup) {
					$objTpl->setCurrentBlock("right-allgroups");
					$objTpl->setVariable("VALUE", $objGroup["group_id"]);
					$objTpl->setVariable("TEXT", $objGroup["group_define_name"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Fetch all users.
			$filters = array('container' => 'auth', 'filters' => array('account_id' => array($_CONF['app']['account']->getId())));
			$objUsers = $objLiveAdmin->getUsers($filters);
			if (is_array($objUsers)) {
				foreach ($objUsers as $objUser) {
					$objTpl->setCurrentBlock("right-allusers");
					$objTpl->setVariable("VALUE", $objUser["perm_user_id"]);
					$objTpl->setVariable("TEXT", $objUser["handle"]);
					$objTpl->parseCurrentBlock();
				}
			}

			//*** Specific parsing.
			$objTpl->setVariable("NAME_LABEL", $objLang->get("name", "usersLabel"));
			$objTpl->setVariable("AREA_LABEL", $objLang->get("area", "usersLabel"));
			$objTpl->setVariable("IMPLIED_RIGHTS_LABEL", $objLang->get("impliedRights", "usersLabel"));
			$objTpl->setVariable("SELECTED_RIGHTS_LABEL", $objLang->get("selectedRights", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_RIGHTS_LABEL", $objLang->get("availableRights", "usersLabel"));
			$objTpl->setVariable("GROUPS_LABEL", $objLang->get("groups", "usersLabel"));
			$objTpl->setVariable("SELECTED_GROUPS_LABEL", $objLang->get("selectedGroups", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_GROUPS_LABEL", $objLang->get("availableGroups", "usersLabel"));
			$objTpl->setVariable("USERS_LABEL", $objLang->get("users", "usersLabel"));
			$objTpl->setVariable("SELECTED_USERS_LABEL", $objLang->get("selectedUsers", "usersLabel"));
			$objTpl->setVariable("AVAILABLE_USERS_LABEL", $objLang->get("availableUsers", "usersLabel"));

			//*** General parsing.
			$objTpl->setVariable("SUBTITLE", $objLang->get("rightDetails", "usersLabel"));
			$objTpl->setVariable("BUTTON_NEW", $objLang->get("new", "button"));
			$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
			$objTpl->setVariable("BUTTON_CANCEL", $objLang->get("cancel", "button"));
			$objTpl->setVariable("BUTTON_SAVE", $objLang->get("save", "button"));
			$objTpl->setVariable("LISTTITLE", $objLang->get("rights", "usersLabel"));
			$objTpl->setVariable("OBJECT", "Right");
			$objTpl->setVariable("FORM", "rightForm");
			$objTpl->setVariable("ACTIVE_RIGHT", " class=\"active\"");

			break;

	}

	$objTpl->setVariable("TITLE", $objLang->get("pageTitle"));
	$objTpl->setVariable("MAINTITLE", $objLang->get("mypunchUsers", "menu"));

	$strReturn = $objTpl->get();

	return $strReturn;
}

?>