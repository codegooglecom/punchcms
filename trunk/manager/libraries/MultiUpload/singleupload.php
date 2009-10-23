<?php

/**
 * Single File Upload - version 1.2.5
 * Easy and reliable upload class for single file upload.
 *
 * Copyright (c)2006, Phixel.org
 *
 * CHANGELOG
 * version 1.2.5, 11 Oct 2009
 *   CHG: Changed access of getFileName from protected to public.
 * version 1.2.4, 20 Apr 2009
 *   BUG: Fixed an error regarding files with multiple dots in the name.
 * version 1.2.3, 18 Jun 2008
 *   BUG: Fixed an error retrival bug in getErrorMessage.
 * version 1.2.2, 05 May 2008
 *   BUG: Fixed an unlink bug after uploading.
 * version 1.2.1, 13 Nov 2007
 *   BUG: Fixed a bug involving uppercase extensions.
 * version 1.2.0, 07 Nov 2007
 *   CHG: Temp. file will always be unlinked.
 * version 1.1.0, 05 Mar 2007
 *   ADD: Added the moveToFTP method.
 * version 1.0.0, 04 Apr 2006
 *   NEW: Created class.
 */

class SingleUpload {
	protected $strOriginalName;
	protected $strTempName;
	protected $strUploadFolder;
	protected $blnReplace = FALSE;
	protected $blnRename = FALSE;
	protected $blnCheckFilename;
	protected $intMaxNameLength = 100;
	protected $arrExtensions = array();
	protected $strExtensions = "";
	protected $intHttpError;
	protected $strLocalName;
	protected $arrMessages = array();
	protected $blnCreateFolder = TRUE;

	//*** Constructor.
	public function SingleUpload() {

	}

	//*** Public Properties.
	public function setUploadFolder($value) {
		$this->strUploadFolder = $value;
	}

	public function getUploadFolder() {
		return $this->strUploadFolder;
	}

	public function getOriginalName() {
		return $this->strOriginalName;
	}

	public function setOriginalName($value) {
		$this->strOriginalName = $value;
	}

	public function getTempName() {
		return $this->strTempName;
	}

	public function getLocalName() {
		return $this->strLocalName;
	}

	public function setReplace($value) {
		$this->blnReplace = $value;
	}

	public function getReplace() {
		return $this->blnReplace;
	}

	public function setRename($value) {
		$this->blnRename = $value;
	}

	public function getRename() {
		return $this->blnRename;
	}

	public function setCheckFilename($value) {
		$this->blnCheckFilename = $value;
	}

	public function getCheckFilename() {
		return $this->blnCheckFilename;
	}

	public function setCreateFolder($value) {
		$this->blnCreateFolder = $value;
	}

	public function getCreateFolder() {
		return $this->blnCreateFolder;
	}

	public function setExtensions($value) {
		$this->arrExtensions = $value;
	}

	public function getExtensions() {
		return $this->arrExtensions;
	}

	//*** Public Methods.
	public function errorMessage() {
		$strReturn = "";

		foreach ($this->arrMessages as $value) {
			$strReturn .= $value . "<br>\n";
		}

		return $strReturn;
	}

	public function upload($strTargetName = "") {
		$strTempName = $this->getFileName($strTargetName);

		if ($this->checkFilename($strTempName)) {
			if ($this->validateExtension()) {
				if (is_uploaded_file($this->strTempName)) {
					$this->strLocalName = $strTempName;

					if ($this->moveUpload($this->strTempName, $this->strLocalName)) {
						$this->arrMessages[] = $this->getErrorMessage($this->intHttpError);
						if ($this->blnRename) $this->arrMessages[] = $this->getErrorMessage(16);
						return true;
					}
				} else {
					$this->arrMessages[] = $this->getErrorMessage($this->intHttpError);
					return false;
				}
			} else {
				$this->prepareExtensions();
				$this->arrMessages[] = $this->getErrorMessage(11);
				return false;
			}
		} else {
			return false;
		}
	}

	public function moveToFTP($strLocalName, $strUploadFolder, $strServer, $strUsername, $strPassword, $strRemoteFolder) {
		$blnReturn = true;
		
		if (!empty($strLocalName)) {
			//*** Connect to the server.
			$objFtp = new FTP($strServer);
			$objRet = $objFtp->login($strUsername, $strPassword);
			if (!$objRet) {
				$this->arrMessages[] = "Login failed. Check credentials.";
				$blnReturn = false;
			}
			
			//*** Passive mode.
			$objFtp->pasv(TRUE);

			//*** Transfer file.
			$objRet = $objFtp->nb_put($strRemoteFolder . $strLocalName, $strUploadFolder . $strLocalName, FTP_BINARY);
			while ($objRet == FTP_MOREDATA) {
			   // Continue uploading...
			   $objRet = $objFtp->nb_continue();
			}
			if ($objRet != FTP_FINISHED) {
				//*** Something went wrong.
				$this->arrMessages[] = $this->getErrorMessage($this->intHttpError);
				$blnReturn = false;
			}
			//*** Remove local file.
			@unlink($strUploadFolder . $strLocalName);
		}

		return $blnReturn;
	}

	//*** Private Methods.
	protected function moveUpload($strTempFile, $strTargetFile) {
		umask(0);

		if (!$this->fileExists($strTargetFile)) {
			$strTargetPath = $this->strUploadFolder . $strTargetFile;
			if ($this->prepareFolder($this->strUploadFolder)) {
				if (move_uploaded_file($strTempFile, $strTargetPath)) {
					if ($this->blnReplace == TRUE) {
						//system("chmod 0777 $strTargetPath"); // maybe you need to use the system command in some cases...
						chmod($strTargetPath , 0777);
					} else {
						// system("chmod 0755 $strTargetPath");
						chmod($strTargetPath , 0755);
					}
					return true;
				} else {
					return false;
				}
			} else {
				$this->arrMessages[] = $this->getErrorMessage(14);
				return false;
			}
		} else {
			$this->arrMessages[] = $this->getErrorMessage(15);
			return false;
		}
	}

	protected function prepareExtensions() {
		//*** This method is only used for detailed error reporting.
		$this->strExtensions = implode(" ", $this->arrExtensions);
	}

	protected function fileExists($strFile) {
		if ($this->blnReplace == TRUE) {
			return false;
		} else {
			if (file_exists($this->strUploadFolder . $strFile)) {
				return true;
			} else {
				return false;
			}
		}
	}

	protected function prepareFolder($strFolder) {
		if (!is_dir($strFolder)) {
			if ($this->blnCreateFolder) {
				umask(0);
				mkdir($strFolder, 0777);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function getFileName($strName = "") {
		//*** This "conversion" is used for unique/new filenames.
		$strReturn = "";

		if ($this->blnRename) {
			if ($this->strOriginalName == "") return;
			$strExtension = $this->getExtension($this->strOriginalName);
			$strName = $this->fixFilename(basename(strtolower($strName), $strExtension));
			$strReturn = (empty($strName)) ? strtotime("now") : $strName . "__" . strtotime("now");
			$strReturn = $strReturn . $this->getExtension($this->strOriginalName);
		} else {
			$strReturn = $this->strOriginalName;
		}
		
		return $strReturn;
	}

	protected function checkFilename($strName) {
		if (!empty($strName)) {
			if (strlen($strName) > $this->intMaxNameLength) {
				$this->arrMessages[] = $this->getErrorMessage(13);
				return false;
			} else {
				if ($this->blnCheckFilename == TRUE) {
					if (preg_match("/^[a-z0-9_\.]*\.(.){1,5}$/i", strtolower($strName))) {
						return true;
					} else {
						$this->arrMessages[] = $this->getErrorMessage(12);
						return false;
					}
				} else {
					return true;
				}
			}
		} else {
			$this->arrMessages[] = $this->getErrorMessage(10);
			return false;
		}
	}

	protected function fixFilename($strName) {
		$strReturn = $strName;
		
		if (!empty($strReturn)) {
			if (strlen($strName) > $this->intMaxNameLength) {
				$strReturn = substr($strReturn, 0, $this->intMaxNameLength);
			}
			
			$strReturn = mb_strtolower($strReturn);

			$arrPatterns = array(
				"/\s/", # Whitespace
				"/\&/", # Ampersand
				"/\+/"  # Plus
			);

			$arrReplacements = array(
				"_",   # Whitespace
				"and", # Ampersand
				"plus" # Plus
			);

			$strReturn = preg_replace($arrPatterns, $arrReplacements, $strReturn);

			$strFiltered = "";
			for ($i = 0; $i < strlen($strReturn); $i++) {
				$strCurrentChar = substr($strReturn, $i, 1);
				if (ctype_alnum($strCurrentChar) == TRUE || $strCurrentChar == "_" || $strCurrentChar == ".") {
					$strFiltered .= $strCurrentChar;
				}
			}

			$strReturn = $strFiltered;
		}
		
		return $strReturn;
	}

	protected function validateExtension() {
		$strExtension = $this->getExtension($this->strOriginalName);

		if (in_array($strExtension, $this->arrExtensions)) {
			//*** Check mime type against allowed/restricted mime types (boolean check mimetype).
			return true;
		} else {
			return false;
		}
	}

	protected function getExtension($strFile) {
		$strReturn = strtolower(strrchr($strFile, "."));
		return $strReturn;
	}

	protected function getErrorMessage($intErrNumber) {
		//*** Some error (HTTP)reporting, change the messages or remove options if you like.

		$arrError[0] = "File: <b>".$this->strOriginalName."</b> successfully uploaded!";
		$arrError[1] = "The uploaded file exceeds the max. upload filesize directive in the server configuration.";
		$arrError[2] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.";
		$arrError[3] = "The uploaded file was only partially uploaded";
		$arrError[4] = "No file was uploaded";
		$arrError[10] = "Please select a file for upload.";
		$arrError[11] = "Only files with the following extensions are allowed: <b>".$this->strExtensions."</b>";
		$arrError[12] = "Sorry, the filename contains invalid characters. Use only alphanumerical chars and separate parts of the name (if needed) with an underscore. <br>A valid filename ends with one dot followed by the extension.";
		$arrError[13] = "The filename exceeds the maximum length of ".$this->intMaxNameLength." characters.";
		$arrError[14] = "Sorry, the upload directory doesn't exist!";
		$arrError[15] = "Uploading <b>".$this->strOriginalName."...Error!</b> Sorry, a file with this name already exitst.";
		$arrError[16] = "The uploaded file is renamed to <b>".$this->strLocalName."</b>.";

		return (array_key_exists($intErrNumber, $arrError)) ? $arrError[$intErrNumber] : "";
	}
}

?>