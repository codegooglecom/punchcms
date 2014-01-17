<?php

/**
 * Quick and dirty, short and simple, easy and peasy database update script.
 */

//*** As of version 2.6, Always include previous update script.
require_once("../update-2.6/index.php");

$strVersion = "2.6.1";

ini_set("include_path", dirname(__FILE__) . "/../");
require_once('init.php');
require_once("../../config.php");

$objDb = MDB2::connect($GLOBALS["_CONF"]["db"]["dsn"]);

$check = $objDb->exec(
    "SELECT * FROM `" . $GLOBALS["_CONF"]["db"]["dbName"] . "`.`pcms_setting_tpl`
    WHERE
        `name`='edit_after_save'
    "
);
$blnError = PEAR::isError($check);
if ($blnError || $check > 0) {
    header("HTTP/1.0 500 Internal Server Error");
    if ($blnError) {
        echo "Failed to update database: " . $check->getMessage();
    }
} else {
    $resource = $objDb->exec(
        "INSERT INTO  `" . $GLOBALS["_CONF"]["db"]["dbName"] . "`.`pcms_setting_tpl` (
        	`name` ,
        	`value` ,
        	`section` ,
        	`type` ,
        	`sort` ,
        	`created` ,
        	`modified`
        ) VALUES (
            'edit_after_save',
            '0',
            'general',
            'checkbox',
            '502',
            '2013-11-05 13:37:00',
            NOW()
        );"
    );

    if (PEAR::isError($resource)) {
        header("HTTP/1.0 500 Internal Server Error");
        echo "Failed to update database: " . $resource->getMessage();
    } else {
        echo "Successfully updated database to PunchCMS Manager {$strVersion}";
    }
}

