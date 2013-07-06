<?php
session_start();
require_once('config.php');


// autoloader
function system_autoload($class)
{
	if(file_exists('classes/'.strtolower($class).'.class.php'))
	{
		require_once('classes/'.strtolower($class).'.class.php');
	}
    else if(file_exists('classes/sys/'.strtolower($class).'.class.php'))
	{
		require_once('classes/sys/'.strtolower($class).'.class.php');
    }
}
spl_autoload_register('system_autoload');



/**
 * Initialize Twig
 */
require_once('libraries/twig/Autoloader.php');
Twig_Autoloader::register();

$loader     = new Twig_Loader_Filesystem(array('templates'));
$twig       = new Twig_Environment($loader, array(
    'charset' => 'utf-8',
    // 'cache' => 'cache',
    'auto_reload' => true,
    'debug' => true
));


?>