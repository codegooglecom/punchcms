<?php

/**
 * Multi File Upload - version 1.1.0
 * Easy and reliable upload class for multi file upload.
 *
 * Copyright (c)2006, Phixel.org
 *
 * CHANGELOG
 * version 1.1.0, 05 Mar 2007
 *   ADD: Added the moveToFTP method.
 * version 1.0.0, 04 Apr 2006
 *   NEW: Created class.
 */

class MultiUpload extends SingleUpload {
	var $intTotalFiles = 0;
	var $arrOriginalNames = array();
	var $arrTempNames = array();
	var $arrLocalNames = array();
	var $arrErrors;
	var $intWrongExtension = 0;
	var $intBadName = 0;
	var $intSuccess = 0;

	//*** Constructor.
	public function MultiUpload() {

	}

	//*** Public Properties.
	public function setTempNames($value) {
		$this->arrTempNames = $value;
	}

	public function getTempNames() {
		return $this->arrTempNames;
	}

	public function setOriginalNames($value) {
		$this->arrOriginalNames = $value;
	}

	public function getOriginalNames() {
		return $this->arrOriginalNames;
	}

	public function getLocalNames() {
		return $this->arrLocalNames;
	}

	public function setErrors($value) {
		$this->arrErrors = $value;
	}

	public function getErrors() {
		return $this->arrErrors;
	}

	public function getTotalFiles() {
		return $this->intTotalFiles;
	}

	public function getSuccessFiles() {
		return $this->intSuccess;
	}

	//*** Public Methods.
	public function uploadFiles() {
		$this->arrMessages = array();

		if ($this->countTotalFiles()) {
			foreach ($this->arrOriginalNames as $key => $value) {
				$this->arrLocalNames[$key] = "";

				if (!empty($value)) {
					$this->strOriginalName = $value;
					$strLocalName = $this->getFileName($value);

					if ($this->checkFilename($strLocalName)) {
						if ($this->validateExtension()) {
							$this->strLocalName = $strLocalName;
							$this->arrLocalNames[$key] = $strLocalName;
							$this->strTempName = $this->arrTempNames[$key];

							if (is_uploaded_file($this->strTempName)) {
								if ($this->moveUpload($this->strTempName, $this->strLocalName)) {
									$this->arrMessages[] = $this->getErrorMessage($this->arrErrors[$key]);
									if ($this->blnRename) {
										$this->arrMessages[] = $this->getErrorMessage(16);

										//*** wait a seconds to get an new timestamp.
										sleep(1);
									}
									$this->intSuccess++;
								}
							} else {
								$this->arrMessages[] = $this->extraText(1);
								$this->arrMessages[] = $this->getErrorMessage($this->arrErrors[$key]);
							}
						} else {
							$this->intWrongExtension++;
						}
					} else {
						$this->intBadName++;
					}
				}
			}
			if ($this->intBadName > 0) $this->arrMessages[] = $this->extraText(5);
			if ($this->intWrongExtension > 0) {
				$this->prepareExtensions();
				$this->arrMessages[] = $this->extraText(2);
			}
		} else {
			$this->arrMessages[] = $this->extraText(3);
		}
	}

	public function moveToFTP($strServer, $strUsername, $strPassword, $strRemoteFolder) {
		$blnReturn = true;
		
		foreach ($this->arrLocalNames as $key => $value) {
			if (!parent::moveToFTP($value, $this->strUploadFolder, $strServer, $strUsername, $strPassword, $strRemoteFolder)) {
				$blnReturn = false;
			}
		}
		
		return $blnReturn;
	}

	//*** Private Methods.
	private function extraText($intNumber) {
		$arrExtraText[1] = "Error for: <b>" . $this->strOriginalName . "</b>";
		$arrExtraText[2] = "You have tried to upload " . $this->intWrongExtension . " files with a bad extension, the following extensions are allowed: <b>" . $this->strExtensions . "</b>";
		$arrExtraText[3] = "Select at least on file.";
		$arrExtraText[4] = "Select the file(s) for upload.";
		$arrExtraText[5] = "You have tried to upload <b>" . $this->intBadName . " files</b> with invalid characters inside the filename.";

		return $arrExtraText[$intNumber];
	}

	private function countTotalFiles() {
		//*** This method checkes the number of files for upload.
		foreach ($this->arrOriginalNames as $test) {
			if ($test != "") {
				$this->intTotalFiles++;
			}
		}

		if ($this->intTotalFiles > 0) {
			return true;
		} else {
			return false;
		}
	}
}

?>