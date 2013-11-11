<?php

/******************************
* Load constantes..
***/
require_once('inc.constantes.php');

/******************************
* Load configuration.
***/
if (!@include_once($_SERVER["DOCUMENT_ROOT"] .'/config.php')) {
	//*** Configuration not yet written.
	if (is_file($_SERVER["DOCUMENT_ROOT"]  .'/install/index.php')) {
		//*** Redirect to installer.
		header("Location: install/index.php");
		exit();
	} else {
		//*** Installer not found. Exit with error.
		echo "Configuration file not found and installer could not be located. Please re-install PunchCMS.";
		exit;
	}
}

/******************************
* Start session.
***/
session_save_path($_SERVER["DOCUMENT_ROOT"] . "/sessions");
session_start();

/******************************
* Set include paths.
***/
$_PATHS['includes'] 	= $_CONF['app']['basePath'] . 'includes/';
$_PATHS['libraries'] 	= $_CONF['app']['basePath'] . 'libraries/';
$_PATHS['pear']			= $_CONF['app']['basePath'] . 'pear/';
$_PATHS['backup']		= $_CONF['app']['basePath'] . 'backups/';
$_PATHS['templates']	= $_CONF['app']['basePath'] . 'templates/';
$_PATHS['upload']		= $_CONF['app']['basePath'] . 'files/';
$_PATHS['structures']	= $_CONF['app']['basePath'] . 'structures/';

ini_set("include_path", $_PATHS['includes'] .
	PATH_SEPARATOR . $_PATHS['libraries'] .
	PATH_SEPARATOR . $_PATHS['pear']);

/******************************
* Load common methods.
***/
require_once('inc.common.php');

/******************************
* Set default app timezone.
***/
date_default_timezone_set($GLOBALS["_CONF"]['app']['timezone']);

/******************************
* Load Language library.
***/
require_once('lib.language.php');

/******************************
* Connect to database.
***/
require_once('MDB2.php');
$DBAConn = null;
try {
	$DBAConn = connectDb();
} catch (Exception $e) {
	switch ($e->getCode()) {
		case SQL_CONN_ERROR:
			print 'Op dit moment is er geen verbinding met de database server mogelijk. Probeer het later opnieuw.';
			exit();
			break;

		case SQL_DB_ERROR:
			print 'Op it moment is er geen selectie op de database mogelijk. Probeer het later opnieuw.';
			exit();
			break;

		default:
			print 'Er is een probleem met de database server opgetreden. Probeer het later opnieuw.';
			exit();

	}
}

/******************************
* Load Language object.
***/
$objLang = null;
//if (array_key_exists("objLang", $_SESSION)) $objLang = unserialize($_SESSION["objLang"]);
if (!is_object($objLang)) {
	$objLang = new Language($_CONF['app']['defaultLang'], $_CONF['app']['langPath']);

	//*** PHP 5.2.1 bugfix. Save to a temp var before serializing.
	$objTemp = $objLang;
	$_SESSION["objLang"] = serialize($objLang);
	$objLang = $objTemp;
}

/******************************
* Check Punch account.
***/
require_once('inc.account.php');

/******************************
* Load Template object.
***/
require_once('HTML/Template/IT.php');

/******************************
* Load Mail class.
***/
require_once('htmlMimeMail5/htmlMimeMail5.php');

/******************************
* Load Upload libraries.
***/
require_once('MultiUpload/singleupload.php');
require_once('MultiUpload/multiupload.php');
$objMultiUpload = new MultiUpload;
$objMultiUpload->setUploadFolder($_PATHS['upload']);
$objMultiUpload->setRename(TRUE);
$objMultiUpload->setCheckFilename(TRUE);

/******************************
* Load Image libraries.
***/
require_once('ImageResizer/ImageEditor.php');
require_once('ImageResizer/lib.imageresizer.php');

/******************************
* Load other methods.
***/
require_once('inc.validate.php');
require_once('inc.postdispatch.php');
require_once('inc.session.php');

/******************************
* Load LiveUser classes.
***/
require_once("LiveUser.php");
require_once("LiveUser/Admin.php");

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
        'secret' => 'extremlysecretkeycombina',
        'savedir' => session_save_path(),
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
                'dsn' => $_CONF['db']['dsn'],
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
                'dsn' => $_CONF['db']['dsn'],
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

$objLiveUser 	=& LiveUser::factory($liveuserConfig);
$objLiveUser->init();

$objLiveAdmin 	=& LiveUser_Admin::factory($liveuserConfig);
$objLiveAdmin->init();

/******************************
* Load the User Rights.
***/
$arrOptions = array (
					'naming' => LIVEUSER_SECTION_APPLICATION,
					'filters' => array(
						'account_id' => array(0, $_CONF['app']['account']->getId())
					),
				);
$blnRights = $objLiveAdmin->perm->outputRightsConstants('constant', $arrOptions, 'php');
if (!$blnRights) {
	die("User Rights could not be retrieved. Error: " . print_r($objLiveAdmin->getErrors()));
}

/******************************
* Load the Menu Rights.
***/
require_once('inc.menurights.php');

/******************************
* Load the TextEditor.
***/
//require_once("fckeditor/fckeditor.php");

?>