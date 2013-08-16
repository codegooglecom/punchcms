<?php

class Log {

	public static function handleError($message) {
		self::logError("ERROR: " . $message);
	}

	public static function logError($message) {
		global $_PATHS;
		file_put_contents($_PATHS['upload'] . "log.txt", "$message\n", FILE_APPEND);
	}
	
}