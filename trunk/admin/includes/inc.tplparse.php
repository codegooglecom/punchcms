<?php

function parseHeader($cId, $eId, $cmd) {
	global $_PATHS,
		$_CONF,
		$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("header.tpl.htm");
	
	$strTitle = "";

	if ($objLiveUser->isLoggedIn()) {
		//*** Build the main menu.
		foreach ($_CONF['app']['menu'] as $key => $value) {
			$objTpl->setCurrentBlock("menu-main-item");
			$objTpl->setVariable('MAIN_LABEL', htmlspecialchars($value[0]));
			$objTpl->setVariable('MAIN_URL', "?cid={$key}");
			if ($key == $cId) {
				$objTpl->setVariable('MAIN_CLASS', "active");
				$strTitle .= htmlspecialchars($value[0]);
			}
			$objTpl->parseCurrentBlock();
		}

		//*** Build the sub menu.
		if (key_exists($cId, $_CONF['app']['menu'])
				&& count($_CONF['app']['menu'][$cId]) > 1
				&& is_array($_CONF['app']['menu'][$cId][1])) {

			$intCount = 1;
			foreach ($_CONF['app']['menu'][$cId][1] as $key => $value) {
				$strClass = "";
				$objTpl->setCurrentBlock("menu-sub-item");
				$objTpl->setVariable('SUB_LABEL', htmlspecialchars($value));
				$objTpl->setVariable('SUB_URL', "?cid={$cId}&amp;cmd={$key}");
				if ($cmd == $key) {
					$strClass .= " active";
					$strTitle .= " :: " . htmlspecialchars($value);
				}
				if ($intCount == count($_CONF['app']['menu'][$cId][1])) {
					$strClass .= " last";
				}
				$objTpl->setVariable('SUB_CLASS', $strClass);
				$objTpl->parseCurrentBlock();
				$intCount++;
			}
		}

		$objTpl->setCurrentBlock("common");
		$objTpl->setVariable('APP_TITLE', $_CONF['app']['name']);
		$objTpl->setVariable('LOGIN_NAME', $objLiveUser->getProperty('name'));
		$objTpl->setVariable('LOGOUT_URL', "?cmd=" . CMD_LOGOUT);
		$objTpl->parseCurrentBlock();

	} else {
		$objTpl->setCurrentBlock("login");
		$objTpl->setVariable('APP_TITLE', $_CONF['app']['name']);
		$objTpl->parseCurrentBlock();
		
		$strTitle = "Login";
	}
	
	$objTpl->setVariable('PAGE_TITLE', $_CONF['app']['titlePrefix'] . $strTitle);

	//*** Return the output.
	return $objTpl->get();
}

function parseLogin() {
	global $_PATHS,
		$_CONF;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("login.tpl.htm");

	$objTpl->setVariable('LOGIN_TITLE', "Please Login");
	
	$strUser = request('handle');
	if (!empty($strUser)) {
		//*** The login form was submitted, but the login failed.
		$objTpl->setCurrentBlock("error");
		$objTpl->setVariable("LOGIN_ERROR", "The login failed. Check your username and password.");
		$objTpl->parseCurrentBlock();
	}

	//*** Return the output.
	return $objTpl->get();
}

function parseBrowse($cId, $eId, $cmd) {
	global $_PATHS,
		$_CONF;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("common.tpl.htm");

	switch ($cId) {
		case NAV_ACCOUNT:
			$objAccounts = Account::select("SELECT * FROM punch_account ORDER BY name");
			$arrDomain = explode('.', $_SERVER['HTTP_HOST']);
			$strRootDomain = (count($arrDomain) > 2) ? str_replace(array_shift($arrDomain) . ".", "", $_SERVER['HTTP_HOST']) : $_SERVER['HTTP_HOST'];

			//*** Render list.
			foreach ($objAccounts as $objTempAccount) {
				$objTpl->setCurrentBlock("list-item");
				$objTpl->setVariable('ITEM_LINK', "http://" . $objTempAccount->getUri() . "." . $strRootDomain);
				$objTpl->setVariable('ITEM_LABEL', htmlentities($objTempAccount->getName()));
				$objTpl->setVariable('ITEM_EXPORT', "export.php?eid=" . $objTempAccount->getId());
				$objTpl->setVariable('ITEM_RESTORE', "?cid=" . NAV_ACCOUNT . "&amp;cmd=" . CMD_RESTORE . "&amp;eid=" . $objTempAccount->getId());
				$objTpl->setVariable('ITEM_EDIT', "?cid=" . NAV_ACCOUNT . "&amp;cmd=" . CMD_EDIT . "&amp;eid=" . $objTempAccount->getId());
				$objTpl->setVariable('ITEM_TYPE', "Account");
				$objTpl->setVariable('ITEM_ID', $objTempAccount->getId());
				$objTpl->parseCurrentBlock();
			}
			break;

		case NAV_ADMIN:
			break;
			
	}

	//*** Return the output.
	return $objTpl->get();
}

function parseAccount($eId, $cmd) {
	global $_PATHS,
		$_CONF,
		$objUpload,
		$objLiveAdmin;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("account.tpl.htm");

	switch ($cmd) {
		case CMD_EDIT:
			$strDispatch	= Request::get('dispatch');
			$intPermId		= Request::get('frm_userid');
			$strPunchId		= Request::get('frm_punchid');
			$strName 		= Request::get('frm_name');
			$strDomain 		= Request::get('frm_uri');
			$strUserName 	= Request::get('frm_account_name');
			$strUserPass 	= Request::get('frm_account_pass');
			$strUserEmail 	= Request::get('frm_account_email');
			//$arrProducts 	= Request::get('frm_account_product', array());
		
			$objAccount = Account::selectByPk($eId);
			
			if ($strDispatch == "editAccount") {
				if (is_object($objAccount)) {
					$objAccount->setName($strName);
					$objAccount->setUri($strDomain);
					$objAccount->save();

					//*** Set products.
					$objAccount->clearProducts();
					/*
					foreach ($arrProducts as $intProduct) {
						$objAccount->addProduct($intProduct);
					}
					*/
					$objAccount->addProduct(1);

					//*** Edit Admin user.
					$data = array(
						'name' => $strUserName,
						'email' => $strUserEmail
					);
					if (!empty($strUserPass)) $data['passwd'] = $strUserPass;
					$objLiveAdmin->updateUser($data, $intPermId);

					$objTpl->setCurrentBlock("text");
					$objTpl->setVariable('BODY', "<p>Account saved successfully.</p>");
					$objTpl->parseCurrentBlock();
				}
			} else {			
				if (is_object($objAccount)) {
					$strAdminName = "";
					$strAdminEmail = "";

					//*** Admin user.
					$filters = array('container' => 'auth', 'filters' => array('account_id' => array($objAccount->getId())));
					$objUsers = $objLiveAdmin->getUsers($filters);
					if (is_array($objUsers)) {
						foreach ($objUsers as $objUser) {
							if ($objUser["perm_type"] == 4) {
								$strAdminName = $objUser["name"];
								$strAdminEmail = $objUser["email"];
								$intPermId = $objUser["perm_user_id"];
								break;
							}
						}
					}

					$objTpl->setCurrentBlock("form.field.punchid");
					$objTpl->setVariable('PUNCH_ID_VALUE', $objAccount->getPunchId());
					$objTpl->parseCurrentBlock();

					/*
					$objProducts = Product::getProducts();
					$objAccountProducts = AccountProduct::getByAccountId($objAccount->getId());
					foreach ($objProducts as $objProduct) {
						$objTpl->setCurrentBlock("form.field.product");
						$objTpl->setVariable('ID', $objProduct->getId());
						$objTpl->setVariable('LABEL', $objProduct->getName());
						$objTpl->setVariable('VALUE', $objProduct->getId());
						
						foreach ($objAccountProducts as $objAccountProduct) {
							if ($objAccountProduct->getProductId() == $objProduct->getId()) {
								$objTpl->setVariable('CHECKED', "checked=\"checked\"");
							}
						}
						
						$objTpl->parseCurrentBlock();
					}
					*/

					$objTpl->setCurrentBlock("form.edit");
					$objTpl->setVariable('NAME_VALUE', $objAccount->getName());
					$objTpl->setVariable('URI_VALUE', $objAccount->getUri());
					$objTpl->setVariable('ACCOUNT_NAME_VALUE', $strAdminName);
					$objTpl->setVariable('ACCOUNT_EMAIL_VALUE', $strAdminEmail);
					$objTpl->setVariable('USER_ID', $intPermId);

					$objTpl->setVariable('CID', NAV_ACCOUNT);
					$objTpl->setVariable('EID', $eId);
					$objTpl->setVariable('CMD', $cmd);
					$objTpl->parseCurrentBlock();
				}
			}
		
			break;		
		case CMD_ADD:
			$strDispatch	= Request::get('dispatch');
			$strName 		= Request::get('frm_name');
			$strDomain 		= Request::get('frm_uri');
			$strUserName 	= Request::get('frm_account_name');
			$strUserPass 	= Request::get('frm_account_pass');
			$strUserEmail 	= Request::get('frm_account_email');
			//$arrProducts 	= Request::get('frm_account_product');
			
			if ($strDispatch == "editAccount") {
				//*** Generate new punchId.
				$strPunchId = Account::generateId();

				$objAccount = new Account();
				$objAccount->setPunchId($strPunchId);
				$objAccount->setName($strName);
				$objAccount->setUri($strDomain);
				$objAccount->setTimeZoneId(42);
				$objAccount->save();
				
				//*** Set products.
				$objAccount->clearProducts();
				/*
				foreach ($arrProducts as $intProduct) {
					$objAccount->addProduct($intProduct);
				}
				*/
				$objAccount->addProduct(1);

				//*** Add Admin user to the account.
				$data = array(
					'handle' => 'admin',
					'name' => $strUserName,
					'passwd' => $strUserPass,
					'is_active' => true,
					'email' => $strUserEmail,
					'account_id' => $objAccount->getId(),
					'perm_type' => 4
				);
				$intPermId = $objLiveAdmin->addUser($data);

				$strOutput = "<p>Account successfully created.</p>\n";
				$strOutput .= "<p>PunchId: <b>{$objAccount->PunchId}</b></p>\n";
				
				$objTpl->setCurrentBlock("text");
				$objTpl->setVariable('BODY', $strOutput);
				$objTpl->parseCurrentBlock();
			} else {
				/*
				$objProducts = Product::getProducts();
				foreach ($objProducts as $objProduct) {
					$objTpl->setCurrentBlock("form.field.product");
					$objTpl->setVariable('ID', $objProduct->getId());
					$objTpl->setVariable('LABEL', $objProduct->getName());
					$objTpl->setVariable('VALUE', $objProduct->getId());
					$objTpl->parseCurrentBlock();
				}
				*/

				$objTpl->setCurrentBlock("form.edit");
				$objTpl->setVariable('CID', NAV_ACCOUNT);
				$objTpl->setVariable('EID', $eId);
				$objTpl->setVariable('CMD', $cmd);
				$objTpl->parseCurrentBlock();
			}
		
			break;		
		case CMD_IMPORT:
			$strDispatch		= Request::get('dispatch');
			$blnOverwrite		= (Request::get('frm_import_overwrite') == "true") ? TRUE : FALSE;
			$blnKeepSettings	= (Request::get('frm_import_keep_settings') == "true") ? TRUE : FALSE;
						
			if ($strDispatch == "importAccount") {
				if (empty($_FILES['frm_file']['name'])) {
					$strOutput = "<p>Error importing the file. It's empty...</p>";
				} else {
					if (is_uploaded_file($_FILES['frm_file']['tmp_name'])) {
						$objAccount = ExImport::import($_FILES['frm_file']['tmp_name'], $blnOverwrite, $blnKeepSettings);
						if (is_object($objAccount)) {
							$strOutput = "<p>Account for <b>{$objAccount->getName()}</b> has been imported successfully with PunchId <b>{$objAccount->PunchId}</b>.</p>\n";
						} else {
							$strOutput = "<p>Import failed! Check the XML and try again.</p>\n";
						}
					}
				}
				
				$objTpl->setCurrentBlock("text");
				$objTpl->setVariable('BODY', $strOutput);
				$objTpl->parseCurrentBlock();
			} else {
				$objTpl->setCurrentBlock("form.import");
				$objTpl->setVariable('CID', NAV_ACCOUNT);
				$objTpl->setVariable('CMD', $cmd);
				$objTpl->parseCurrentBlock();
			}
			
			break;
		case CMD_RESTORE:
			$strDispatch	= Request::get('dispatch');
			$strBackupName 	= Request::get('frm_backup');
			
			$objAccount = Account::selectByPk($eId);
			
			if ($strDispatch == "restoreAccount") {
				if (is_object($objAccount)) {
					if ($objAccount->restoreBackup($_PATHS['backup'] . $strBackupName)) {
						$strOutput = "<p>Account successfully restored.</p>\n";
					} else {
						$strOutput = "<p>Errors occured while restoring the account.</p>\n";
					}

					$objTpl->setCurrentBlock("text");
					$objTpl->setVariable('BODY', $strOutput);
					$objTpl->parseCurrentBlock();
				}				
			} else {
				if (is_object($objAccount)) {
					$objBackups = $objAccount->getBackups();
					foreach ($objBackups as $objBackup) {
						$objTpl->setCurrentBlock("form.field.backup");
						$objTpl->setVariable('VALUE', $objBackup["file"]);
						$objTpl->setVariable('LABEL', $objBackup["label"]);
						$objTpl->parseCurrentBlock();
					}

					$objTpl->setCurrentBlock("form.restore");
					$objTpl->setVariable('ACCOUNT_VALUE', $objAccount->getName());
					$objTpl->setVariable('CID', NAV_ACCOUNT);
					$objTpl->setVariable('EID', $eId);
					$objTpl->setVariable('CMD', $cmd);
					$objTpl->parseCurrentBlock();
				}
			}
			
			break;
	}

	//*** Return the output.
	return $objTpl->get();
}

function parseAdmin($eId, $cmd) {
	global $_PATHS,
		$_CONF,
		$objLiveAdmin;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("admin.tpl.htm");

	switch ($cmd) {
		case CMD_ADD:
			$strDispatch	= Request::get('dispatch');
			$strUserHandle 	= request('frm_admin_handle');
			$strUserName 	= request('frm_admin_name');
			$strUserPass 	= request('frm_admin_pass');
			$strUserEmail 	= request('frm_admin_email');

			if ($strDispatch == "editAdmin") {
				//*** Create new admin.
				$data = array(
					'handle' => $strUserHandle,
					'name' => $strUserName,
					'passwd' => $strUserPass,
					'is_active' => true,
					'email' => $strUserEmail,
					'account_id' => 0,
					'perm_type' => 5
				);
				$intPermId = $objLiveAdmin->addUser($data);

				$strOutput = "<p>Admin successfully created.</p>\n";
				$strOutput .= "<p><a href=\"index.php\">Login</a> with the new admin credentials.</p>\n";

				$objTpl->setCurrentBlock("text");
				$objTpl->setVariable('BODY', $strOutput);
				$objTpl->parseCurrentBlock();
			} else {
				$objTpl->setCurrentBlock("form.add");
				$objTpl->setVariable('CID', NAV_ADMIN);
				$objTpl->setVariable('CMD', $cmd);
				$objTpl->parseCurrentBlock();
			}
			
			break;
	}

	//*** Return the output.
	return $objTpl->get();
}

function parseFooter($cId, $eId) {
	global $_PATHS,
		$_CONF;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("footer.tpl.htm", FALSE, FALSE);

	//*** Return the output.
	return $objTpl->get();
}

?>
