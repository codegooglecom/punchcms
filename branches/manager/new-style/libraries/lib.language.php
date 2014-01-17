<?php

/* Language Selection Class v0.1.0
 * Used to get the default language, set a specific language and list
 * available languages.
 *
 * CHANGELOG
 * version 0.1.0, 03 Apr 2006
 *   NEW: Created class.
 */

class Language {
	public $name = "";
	public $language = "";
	private $defaultLang = "";
	private $langPath = "";
	private $arrLang = array();
	private $strError = "TRANSLATION NOT FOUND.";
	private $activeLang = "";

	public function __construct($strLang = "english-utf-8", $langPath = "./lng/") {
		$this->defaultLang = $strLang;
		$this->langPath = $langPath;

		$this->getLang();
	}

	public function getActiveLang() {
		return $this->activeLang;
	}

	public function get($strName, $strCategory = "global") {
		//*** Get a translation from the language file.
		$strReturn = $this->strError;

		if (is_array($this->arrLang) &&
				!empty($this->arrLang[$strCategory][$strName])) {
			$strReturn = $this->arrLang[$strCategory][$strName];
		}

		return $strReturn;
	}

	public function getLang($strLang = "") {
		/* Get a specific language or, if argument is empty, get the
		 * language by checking session, cookie and default.
		 */
		$blnReturn = FALSE;

		if (empty($strLang)) {
			if (!empty($_SESSION['language']) && file_exists($this->langPath . "/" . $_SESSION['language'] . ".php")) {
				//*** Session variable exists. Load the language file.
				$this->activeLang = $_SESSION['language'];
			} else if (!empty($_COOKIE['language']) && file_exists($this->langPath . "/" . $_COOKIE['language'] . ".php")) {
				//*** Cookie variable exists. Load the language file.
				$this->activeLang = $_COOKIE['language'];
			} else if (file_exists($this->langPath . "/" . $this->defaultLang . ".php")) {
				//*** Load default language file.
				$this->activeLang = $this->defaultLang;
			}
		} else if (file_exists($this->langPath . "/" . $strLang . ".php")) {
			//*** Load the specific language file.
			$this->activeLang = $strLang;
		}

		//*** Really load the file.
		if (!empty($this->activeLang)) {
			require_once($this->langPath . "/" . $this->activeLang . ".php");

			//*** Check if the expected variable exists.
			(isset($_LANG)) ? $this->arrLang = $_LANG : $this->arrLang = array();

			//*** Set internal variables.
			$this->name = $this->activeLang;

			$arrTemp = explode("-", $this->activeLang);
			$this->language = str_replace("_", " ", $arrTemp[0]);

			$blnReturn = TRUE;
		}

		return $blnReturn;
	}

	public function setLang($strLang) {
		//*** Set a specific language and write it to the session and cockie.
		$blnReturn = FALSE;

		//*** Check if the language file exists and is different from the current language.
		if (file_exists($this->langPath . "/" . $strLang . ".php") && $strLang !== $this->activeLang) {
			//*** Write to cookie.
			setcookie('language', $strLang, time()+60*60*24*30, '/');

			//*** Write to session.
			$_SESSION['language'] = $strLang;

			//*** Load new language file;
			$this->getLang($strLang);
			$blnReturn = TRUE;
		}

		return $blnReturn;
	}

	public function getLangs() {
		$objReturn = new Language_Collection();

		//*** List all files in the language directory.
		if (is_dir($this->langPath)) {
			if ($dirHandle = opendir($this->langPath)) {
				while (($objFile = readdir($dirHandle)) !== false) {
					if (is_file($this->langPath . $objFile)) {
						//*** Create Language_File object and set properties,
						$objLanguage = new Language_File();
						$objLanguage->name = basename($objFile, ".php");

						$arrTemp = explode("-", $objFile);
						$objLanguage->language = str_replace("_", " ", $arrTemp[0]);

						//*** Add to the collection.
						$objReturn->addObject($objLanguage);
					}
				}
				closedir($dirHandle);
		   }
		}

		return $objReturn;
	}
}


/* Language Collection Class v0.1.0
 * Collection that holds all available languages.
 *
 * CHANGELOG
 * version 0.1.0, 03 Apr 2006
 *   NEW: Created class.
 */

class Language_Collection implements Iterator {
	private $collection = array();

	public function __construct($initArray = array()) {
	   if (is_array($initArray)) {
		   $this->collection = $initArray;
	   }
	}

	public function addObject($value) {
		/* Add an object to the collection.
		 *
		 * Method arguments are:
		 * - object to add.
		 */

		array_push($this->collection, $value);
	}

	public function count() {
		return count($this->collection);
	}

    public function current() {
        return current($this->collection);
    }

    public function next() {
        return next($this->collection);
    }

    public function key() {
        return key($this->collection);
    }

    public function valid() {
        return $this->current() !== false;
    }

    public function rewind() {
        reset($this->collection);
    }
}


/* Language File Class v0.1.0
 * Object for a language file. Used in the Language Collection.
 *
 * CHANGELOG
 * version 0.1.0, 03 Apr 2006
 *   NEW: Created class.
 */

class Language_File {
	public $name;
	public $language;
}

?>