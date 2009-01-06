<?php

/******************************
* Load constantes.
***/
require_once(dirname(__FILE__) . '/../../includes/inc.constantes.php');
require_once('inc.constantes.php');

/******************************
* Load configuration.
***/
if (!@include_once(dirname(__FILE__) . '/../../config.php')) {
	//*** Configuration not yet written.
	if (is_file('../install/index.php')) {
		//*** Redirect to installer.
		header("Location: ../install/index.php");
		exit();
	} else {
		//*** Installer not found. Exit with error.
		echo "Configuration file not found and installer could not be located. Please re-install PunchCMS.";
		exit;
	}
}
require_once(dirname(__FILE__) . '/../config.php');

/******************************
* Start session.
***/
session_start();

/******************************
* Set include paths.
***/
$_PATHS['includes'] 		= $_CONF['app']['basePath'] . 'includes/';
$_PATHS['libraries'] 		= $_CONF['app']['basePath'] . 'libraries/';
$_PATHS['pear']				= $_CONF['app']['basePath'] . 'pear/';
$_PATHS['backup']			= $_CONF['app']['basePath'] . 'backups/';
$_PATHS['templates']		= dirname(__FILE__) . '/../templates/';
$_PATHS['upload']			= $_CONF['app']['basePath'] . 'files/';

ini_set("include_path", $_PATHS['includes'] .
	PATH_SEPARATOR . $_PATHS['libraries'] .
	PATH_SEPARATOR . $_PATHS['pear']);

/******************************
* Load common methods.
***/
require_once('inc.common.php');

/******************************
* Connect to database.
***/
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
* Load other methods.
***/
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
        'secret' => 'extremlysecretkeycombination',
        'savedir' => $_CONF['app']['basePath'] . 'sessions',
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

?>