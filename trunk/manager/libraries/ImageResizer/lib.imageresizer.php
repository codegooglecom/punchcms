<?php

/**
 * Image Resizer - version 2.0.0
 * Easy and robust image manipulation through simple templates.
 * Makes use of the ImageEditor class.
 *
 * Copyright (c)2006, Phixel.org
 *
 * USAGE
 * The following templates are available.
 * - RESIZE_CROP
 *   Crop according to the width and height starting from the center.
 *   Does not change the aspect ratio.
 * - RESIZE_FIT_CROP
 *   Resize the image first and then crop according to the width and 
 *   height starting from the center. Does not change the aspect ratio.
 * - RESIZE_DISTORT
 *   Resize the image according to the width and height. This can change
 *   the aspect ratio of the image.
 * - RESIZE_EXACT
 *   Resize the image to fit into the boundary box given by the width and 
 *   height parameters. This does not change the aspect ratio.
 *
 * CHANGELOG
 * version 2.0.0, 12 June 2008
 *   CHG: Completely rewritten.
 * version 1.1.5, 10 July 2007
 *   CHG: Changed the behaviour to centered crop for RESIZE_BOUND_CROP.
 * version 1.1.4, 28 June 2007
 *   ADD: Added the grayscale parameter.
 * version 1.1.3, 29 May 2007
 *   ADD: Added the toScreen parameter.
 * version 1.1.2, 07 Mar 2007
 *   BUG: Fixed a resize bug while using RESIZE_EXACT_CROP scaling.
 * version 1.1.1, 06 Mar 2007
 *   ADD: Added the quality parameter.
 * version 1.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

define('RESIZE_CROP', 1);
define('RESIZE_FIT_CROP', 2);
define('RESIZE_DISTORT', 3);
define('RESIZE_EXACT', 4);

class ImageResizer {

	//*** Public Methods.
	public static function resize($strFileName = "", $intWidth = 100, $intHeight = 75, $intTemplate = RESIZE_EXACT, $intQuality = 75, $blnOverwrite = FALSE, $strName = "", $blnToScreen = FALSE, $blnGrayscale = FALSE) {
		$strReturn = $strName;
		if (empty($strReturn)) {
			if (!$blnOverwrite) {
				//*** Generate a random name.
				$strExtension = self::getExtension(basename($strFileName));
				$strReturn = dirname($strFileName) . "/{$intWidth}x{$intHeight}_" . basename($strFileName, $strExtension) . "_" . strtotime("now") . "{$strExtension}";
			} else {
				$strReturn = $strFileName;
			}
		}

		$objEditor = new ImageEditor(basename($strFileName), dirname($strFileName) . "/");
				
		switch ($intTemplate) {
			case RESIZE_CROP:
				//*** Blow the image up if it is smaller then the destination.
				if ($intWidth > $objEditor->getWidth() || $intHeight > $objEditor->getHeight()) {
					//*** Portrait source.
					$destWidth = $intWidth;
					$destHeight = ($destWidth / $objEditor->getWidth()) * $objEditor->getHeight();
					if ($destHeight < $intHeight) {
						//*** Landscape source.
						$destHeight = $intHeight;
						$destWidth = ($destHeight / $objEditor->getHeight()) * $objEditor->getWidth();
					}
					$objEditor->resize(round($destWidth), round($destHeight));
				}
				
				$destX = ($objEditor->getWidth() - $intWidth) / 2;
				$destY = ($objEditor->getHeight() - $intHeight) / 2;
				$objEditor->crop(round($destX), round($destY), $intWidth, $intHeight);
				break;
			case RESIZE_FIT_CROP:
				//*** Resize the image to fit into the boundary box.
				//*** Portrait source.
				$destWidth = $intWidth;
				$destHeight = ($destWidth / $objEditor->getWidth()) * $objEditor->getHeight();
				if ($destHeight < $intHeight) {
					//*** Landscape source.
					$destHeight = $intHeight;
					$destWidth = ($destHeight / $objEditor->getHeight()) * $objEditor->getWidth();
				}
				$objEditor->resize(round($destWidth), round($destHeight));
				
				$destX = ($objEditor->getWidth() - $intWidth) / 2;
				$destY = ($objEditor->getHeight() - $intHeight) / 2;
				$objEditor->crop(round($destX), round($destY), $intWidth, $intHeight);
				break;

			case RESIZE_DISTORT:
				$objEditor->resize($intWidth, $intHeight);
				break;

			case RESIZE_EXACT:
				$destWidth = $intWidth;
				$destHeight = ($destWidth / $objEditor->getWidth()) * $objEditor->getHeight();
				if ($destHeight > $intHeight) {
					//*** Landscape source.
					$destHeight = $intHeight;
					$destWidth = ($destHeight / $objEditor->getHeight()) * $objEditor->getWidth();
				}
				$objEditor->resize(round($destWidth), round($destHeight));

				break;
		}
		
		if ($blnGrayscale) {
			$objEditor->grayscale();
		}

		if (!empty($strReturn)) {
			if ($blnToScreen) {
				//*** Return image.
				$objEditor->outputImage($intQuality);
			} else {
				//*** Write file to disk.
				$objEditor->outputFile(basename($strReturn), dirname($strReturn) . "/", $intQuality);
			}
		}

		return $strReturn;
	}

	public static function getExtension($strFile) {
		$strReturn = strtolower(strrchr($strFile, "."));
		return $strReturn;
	}
}
?>
