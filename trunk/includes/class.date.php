<?php

/* Date Class v0.2.1
 * Holds methods for misc. date calls.
 *
 * CHANGELOG
 * version 0.2.1, 14 Feb 2008
 *   CHG: Changed fromMysql. Removed language check.
 * version 0.2.0, 15 Nov 2007
 *   CHG: Extended toMysql method.
 * version 0.1.0, 12 Apr 2006
 *   NEW: Created class.
 */

class Date {

	public static function fromMysql($strFormat, $strDateTime) {
		$strReturn = $strDateTime;

		if ($strDateTime != "0000-00-00 00:00:00" && !empty($strDateTime)) {
			$strTStamp = strtotime($strDateTime);

			if ($strTStamp !== -1 || $strTStamp !== FALSE) {
				$strReturn = strftime($strFormat, $strTStamp);
			}
		} else {
			$strReturn = "";
		}

		return $strReturn;
	}

	public static function toMysql($strDateTime = "") {
		$strReturn = $strDateTime;
		$strFormat = "%Y-%m-%d %H:%M:%S";

		if (empty($strDateTime)) {
			$strTStamp = strtotime("now");
		} else if (is_numeric($strDateTime)) {
			$strTStamp = $strDateTime;
		} else {
			$strTStamp = strtotime($strDateTime);
		}

		if ($strTStamp !== -1 || $strTStamp !== FALSE) {
			$strReturn = strftime($strFormat, $strTStamp);
		}

		return $strReturn;
	}

	public static function parseDate($strDate, $strFormat) {
		/* This method parses a date/time value using a defined format. 
		 * It returns a timestamp that can be used with strftime.
		*/
		
		$arrDate = strptime($strDate, $strFormat);
		$timestamp = mktime($arrDate['tm_hour'], $arrDate['tm_min'], $arrDate['tm_sec'], $arrDate['tm_mon'] + 1, $arrDate['tm_mday'], $arrDate['tm_year']);
		
		return $timestamp;
	}

	public static function convertDate($strDate, $strInFormat, $strOutFormat) {
		/* This method takes a date/time value and converts it from one format to the other. 
		 * It returns the converted value.
		*/
		
		return strftime($strOutFormat, self::parseDate($strDate, $strInFormat));
	}

}

?>