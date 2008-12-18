<?php

/* Request Class v0.1.0
 * Holds methods for request related methods.
 *
 * CHANGELOG
 * version 0.1.0, 20 Oct 2006
 *   NEW: Created class.
 */

class Request {

	public static function redirectInternal($intId) {
		if ($intId > 0) {
			$arrNeedle = array('iid'=>$intId);
			$strQuery = implode_with_keys(array_diff($_GET, $arrNeedle), "&") . "#label_{$intId}";
			header("Location: " . self::getURI() . "/?" . $strQuery);
		}
	}

	public static function getURI($strProtocol = "") {
		return self::getRootURI($strProtocol) . self::getSubURI();
	}

	public static function getProtocol() {
		if (array_key_exists("HTTPS", $_SERVER) && $_SERVER["HTTPS"] == "on") {
			$strReturn = "https";
		} else {
			$strReturn = "http";
		}

		return $strReturn;
	}

	public static function getRootURI($strProtocol = "") {
		if (empty($strProtocol)) $strProtocol = self::getProtocol();
		return $strProtocol . "://" . $_SERVER["HTTP_HOST"];
	}

	public static function getSubURI() {
		return (strlen(dirname($_SERVER['PHP_SELF'])) > 1) ? dirname($_SERVER['PHP_SELF']) : substr(dirname($_SERVER['PHP_SELF']), 0, -1);
	}

	public static function getVar($strRequest, $strVarName) {
		parse_str(array_pop(explode("?", $strRequest)), $arrRequest);
		foreach ($arrRequest as $key=>$value) {
			if (strtolower($key) == strtolower($strVarName)) {
				return $value;
			}
		}
	}

	public static function get($strParam, $strReplaceEmpty = "") {
		(isset($_REQUEST[$strParam])) ? $strReturn = $_REQUEST[$strParam] : $strReturn = "";

		if (empty($strReturn) && !is_numeric($strReturn) && $strReturn !== 0) $strReturn = $strReplaceEmpty;

		return $strReturn;
	}

}

?>