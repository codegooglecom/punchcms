<?php

/***********************************************************************
 * Configuration File
 **********************************************************************/

//*** Main configuration
$_CONF['app']['version']		= '1.0.0';
$_CONF['app']['titlePrefix'] 	= 'PunchCMS Admin :: ';
$_CONF['app']['name'] 			= 'PunchCMS Admin Area';
$_CONF['app']['menu'] 			= array(
										'1' => array(
												'Accounts',
												array(
													'1' => 'Browse',
													'2' => 'Create',
													'6' => 'Import'
												)
											),
										'3' => array(
												'Tools',
												array(
													'9' => 'Run SQL'
												)
											)
									);

?>
