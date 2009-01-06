<?php

class PCMS_Installer {
	private $__db_server;
	private $__db_name;
	private $__db_username;
	private $__db_passwd;

	public function __construct() {}

	public function checkRequirements() {
		$strReturn = "";
		
		//*** PHP version.
		if (version_compare(PHP_VERSION, '5.0.0') < 1) {
			$strReturn .= "<li>Your version of PHP is too old. Minimum required version is <b>5.0.0</b>. Please upgrade.</li>";
		}
		
		//*** Folder permissions.
		if (!$this->__hasWrite("../install")) $strReturn .= "<li>The &quot;<b>install</b>&quot; folder needs write permissions. Please adjust the settings.</li>";
		if (!$this->__hasWrite("../backups")) $strReturn .= "<li>The &quot;<b>backups</b>&quot; folder needs write permissions. Please adjust the settings.</li>";
		if (!$this->__hasWrite("../files")) $strReturn .= "<li>The &quot;<b>files</b>&quot; folder needs write permissions. Please adjust the settings.</li>";
	
		return $strReturn;
	}
	
	public function getForm() {
		$strMaxLength = "Your input is too long. Maximum length is %s";
		$strMinLength = "Your input is too short. Minimum length is %s";
		$strRequired = "This field is required.";

		$objForm = new ValidForm("installForm");
		$objForm->setMainAlert("One or more errors occured. Check the marked fields and try again.");

		$objForm->addFieldset("Administrator settings", NULL, "This is the account for the admin area. Later you can create an admin per website.");
		$objForm->addField("username", "Username", VFORM_STRING, array("maxLength" => 255, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Enter only letters and spaces."));
		$objForm->addField("passwd", "Password", VFORM_PASSWORD, array("maxLength" => 255, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Enter only letters and numbers."));
		$objForm->addField("email", "Email address", VFORM_EMAIL, array("maxLength" => 32, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Use the format name@domain.extension."), array("tip" => "This address will be used as the sender address for password reminders."));

		$objForm->addFieldset("MySQL settings", NULL, "The database and user must already exist, otherwise the installation will fail.");
		$objForm->addField("db_server", "Server address", VFORM_STRING, array("maxLength" => 255, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Enter the address of the MySQL server."), array("default" => "localhost"));
		$objForm->addField("db_name", "Database name", VFORM_STRING, array("maxLength" => 255, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Enter the name of the designated database."), array("default" => "punchcms"));
		$objForm->addField("db_username", "Username", VFORM_STRING, array("maxLength" => 255, "required" => TRUE), array("maxLength" => $strMaxLength, "required" => $strRequired, "type" => "Enter the username for the database."));
		$objForm->addField("db_passwd", "Password", VFORM_PASSWORD, array("maxLength" => 32, "required" => FALSE), array("maxLength" => $strMaxLength, "type" => "Enter the password for the database."));

		$objForm->setSubmitLabel("Submit");
		
		return $objForm;
	}

	public function installDb($server, $name, $username, $passwd) {
		$strReturn = "";
	
		$this->__db_server = $server;
		$this->__db_name = $name;
		$this->__db_username = $username;
		$this->__db_passwd = $passwd;
	
		$strReturn = $this->__clearDb();
		if (empty($strReturn)) {
			$strReturn = $this->__executeSql("punchcms.sql");
		}
	
		return $strReturn;
	}

	public function writeConfig($username, $passwd, $email) {
		$strReturn = "";

		if (is_file("configTemplate.php")) {
			$strConfig = file_get_contents("configTemplate.php");
			$strConfig = str_replace("!!DB_SERVER!!", $this->__db_server, $strConfig);
			$strConfig = str_replace("!!DB_NAME!!", $this->__db_name, $strConfig);
			$strConfig = str_replace("!!DB_USERNAME!!", $this->__db_username, $strConfig);
			$strConfig = str_replace("!!DB_PASSWORD!!", $this->__db_passwd, $strConfig);
			$strConfig = str_replace("!!MAIL_ADMIN!!", $email, $strConfig);
			
			//*** Write config file.
			if (@file_put_contents("config.php", $strConfig) === FALSE) {
				$strReturn = "Error while writing to the config file.";
			} else {
				//*** Create super admin.
				$strReturn = $this->__createSuperAdmin($username, $passwd, $email);
			}
		} else {
			$strReturn = "Configuration template not found.";
		}
	
		return $strReturn;
	}
	
	private function __getDsn() {
		return "mysql://{$this->__db_username}:{$this->__db_passwd}@{$this->__db_server}/{$this->__db_name}?charset=utf8";
	}
	
	private function __createSuperAdmin($username, $passwd, $email) {
		$strReturn = "";
		//require_once("LiveUser/Admin.php");
	
		$liveuserConfig = array(
			'cache_perm' => false,
			'session' => array(
				'name' => 'PHPSESSID',
				'varname' => 'userSession',
			),
			'logout' => array(
				'destroy'  => true,
			),
			'cookie' => array(
				'name' => 'loginInfoPunch',
				'lifetime' => 30,
				'path' => null,
				'domain' => null,
				'secret' => 'extremlysecretkeycombination',
				'savedir' => 'sessions',
				'secure' => false,
			),
			'authContainers'    => array(
				'MDB2' => array(
					'type' => 'MDB2',
					'expireTime' => 0,
					'idleTime' => 0,
					'passwordEncryptionMode' => 'SHA1',
					'secret' => 'Spice up your life with a little salt.',
					'storage' => array(
						'dsn' => $this->__getDsn(),
						'prefix' => 'punch_liveuser_',
						'tables' => array(
							'users' => array(
								'fields' => array(
									'lastlogin' => false,
									'is_active' => false,
									'email' => false,
									'name' => false,
									'account_id' => false,
									'time_zone_id' => false,
									'owner_user_id' => false,
									'owner_group_id' => false,
								),
							),
						),
						'fields' => array(
							'lastlogin' => 'timestamp',
							'is_active' => 'boolean',
							'email' => 'text',
							'name' => 'text',
							'account_id' => 'integer',
							'time_zone_id' => 'integer',
							'owner_user_id' => 'integer',
							'owner_group_id' => 'integer',
						),
						'alias' => array(
							'auth_user_id' => 'authuserid',
							'lastlogin' => 'lastlogin',
							'is_active' => 'isactive',
							'email' => 'email',
							'name' => 'name',
							'account_id' => 'account_id',
							'time_zone_id' => 'time_zone_id',
							'owner_user_id' => 'owner_user_id',
							'owner_group_id' => 'owner_group_id',
						),
					),
				),
			),
			'permContainer' => array(
				'type'  => 'Complex',
				'storage' => array(
					'MDB2' => array(
						'dsn' => $this->__getDsn(),
						'prefix' => 'punch_liveuser_',
						'force_seq' => 'false',
						'tables' => array(
							'groups' => array(
								'fields' => array(
									'is_active' => false,
									'account_id' => false,
									'owner_user_id' => false,
									'owner_group_id' => false,
								),
							),
							'areas' => array(
								'fields' => array(
									'account_id' => false,
								),
							),
							'applications' => array(
								'fields' => array(
									'account_id' => false,
								),
							),
							'rights' => array(
								'fields' => array(
									'account_id' => false,
								),
							),
							'grouprights' => array(
								'fields' => array(
									'account_id' => false,
								),
							),
							'userrights' => array(
								'fields' => array(
									'account_id' => false,
								),
							),
						),
						'fields' => array(
							'is_active' => 'boolean',
							'account_id' => 'integer',
							'owner_user_id' => 'integer',
							'owner_group_id' => 'integer',
						),
						'alias' => array(
							'is_active' => 'isactive',
							'account_id' => 'account_id',
							'owner_user_id' => 'owner_user_id',
							'owner_group_id' => 'owner_group_id',
						),
					),
				),
			),
		);
	
		$objLiveAdmin 	=& LiveUser_Admin::factory($liveuserConfig);
		$objLiveAdmin->init();
	
		$data = array(
			'handle' => $username,
			'name' => "PunchCMS Super Admin",
			'passwd' => $passwd,
			'is_active' => true,
			'email' => $email,
			'account_id' => 0,
			'perm_type' => 5
		);
		
		$intPermId = $objLiveAdmin->addUser($data);
		
		if (!$intPermId) {
			$strReturn = "Error during Super Admin creation.";
		}
		
		return $strReturn;
	}
	
	private function __clearDb() {
		$strReturn = $this->__executeSql("cleardb.sql");
		
		return $strReturn;
	}
	
	private function __executeSql($filename) {
		$strReturn = "";
		
		$objConn =& MDB2::connect($this->__getDsn());
		
		if (PEAR::isError($objConn)) {
			$strReturn = "Database connection failed: " . $objConn->getMessage();
		} else {
			if (is_file($filename)) {
				//*** Execute each file.
				$strSql = html_entity_decode(file_get_contents($filename), ENT_QUOTES, "UTF-8");
				$arrLines = explode(';', $strSql);

				foreach ($arrLines as $strLine) {
					$strLine = trim($strLine, " ");
					if (!empty($strLine)) {
						$objResult = $objConn->exec($strLine);		

						if (PEAR::isError($objResult) && $objResult->code != -18) {
							$strReturn = "Database error: " . $objResult->getMessage() . "<br />" . $objResult->toString();
							break;
						}
					}
				}
			} else {
				$strReturn = "SQL file not found.";
			}
		}
		
		return $strReturn;
	}
	
	private function __hasWrite($folder) {
		$blnReturn = FALSE;
		
		$blnReturn = is_writable($folder);
		
		return $blnReturn;
	}
		
}

?>