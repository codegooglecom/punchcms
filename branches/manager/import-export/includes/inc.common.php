<?php

function __autoload($class_name) {
	$strClass = 'class.' . strtolower($class_name) . '.php';
	if (include_exists($strClass)) {
		require_once($strClass);
		return;
	}

	$strClass = implode('/', explode('_', $class_name)) . '.php';
	if (include_exists($strClass)) {
		require_once($strClass);
		return;
	}
}

function request($strParam, $strReplaceEmpty = "") {
	(isset($_REQUEST[$strParam])) ? $strReturn = $_REQUEST[$strParam] : $strReturn = "";

	if (empty($strReturn) && !is_numeric($strReturn) && $strReturn !== 0) $strReturn = $strReplaceEmpty;

	return $strReturn;
}

function include_exists($file) {
   static $include_dirs = null;
   static $include_path = null;

   // set include_dirs
   if (is_null($include_dirs) || get_include_path() !== $include_path) {
	   $include_path    = get_include_path();
	   foreach (explode(PATH_SEPARATOR, $include_path) as $include_dir) {
		   if (substr($include_dir, -1) != '/') {
			   $include_dir .= '/';
		   }
		   $include_dirs[]    = $include_dir;
	   }
   }

   if (substr($file, 0, 1) == '/') { //absolute filepath - what about file:///?
	   return (file_exists($file));
   }

   // if ((substr($file, 0, 7) == 'http://' || substr($file, 0, 6) == 'ftp://') && ini_get('allow_url_fopen')) {
	   // return true;
   // }

   foreach ($include_dirs as $include_dir) {
	   if (file_exists($include_dir.$file)) {
		   return true;
	   }
   }

   return false;
}

function connectDB() {
	global $_CONF;

	$objConnID =& MDB2::factory($_CONF['db']['dsn']);

	if (MDB2::isError($objConnID)) {
		throw new Exception('Database connection failed: ' . $objConnID->getMessage(), SQL_CONN_ERROR);
	}

	return($objConnID);
}

function quote_smart($value) {
   // Stripslashes
   if (get_magic_quotes_gpc()) {
       $value = (is_string($value)) ? stripslashes($value) : $value;
   }

   // Quote if not integer
   if (!is_numeric($value)) {
       $value = (is_string($value)) ? mysql_real_escape_string($value) : $value;
   }

   return $value;
}

function xhtmlsave($strInput) {
	return str_replace("&", "&amp;", $strInput);
}

function getShortValue($strInput, $intCharLength = 200, $blnPreserveWord = TRUE, $strAppend = " ...") {
	//*** Get a short version of the value.
	$strReturn = $strInput; // Set the default return value.

	$strReturn = substr($strInput, 0, $intCharLength);

	if ($blnPreserveWord == TRUE && strlen($strReturn) < strlen($strInput)) {
		$intLastSpace = strrpos($strReturn, " ");
		$strReturn = substr($strReturn, 0, $intLastSpace);
	}

	if (strlen($strReturn) < strlen($strInput)) {
		$strReturn .= $strAppend;
	}

	return $strReturn;
}

function createTemplateTree($objTemplate) {
    $str = '<input type="checkbox" name="tmpl['. $objTemplate->getId() .']" id="tmpl_'. $objTemplate->getId() .'" checked="checked" /><label for="tmpl_'. $objTemplate->getId() .'">'. $objTemplate->getName() .'</label>';

    $objTemplates = $objTemplate->getTemplates();
    if ($objTemplates->count() > 0) {
        $str .= '<div class="level">';
        foreach($objTemplates as $objTempl)
        {
            $str .= createTemplateTree($objTempl);
        }
        $str .= '</div>';
    }

    return $str;
}

function createElementTree($objElement, $first = true) {
    if(!$first) $str = '<input type="checkbox" name="elem['. $objElement->getId() .']" id="elem_'. $objElement->getId() .'" checked="checked" /><label for="elem_'. $objElement->getId() .'">'. $objElement->getName() .'</label>';

    $objElements = $objElement->getElements();
    if ($objElements->count() > 0) {
        if (!$first) $str .= '<div class="level">';
        foreach($objElements as $objElement)
        {
            $str .= createElementTree($objElement, false);
        }
        if(!$first) $str .= '</div>';
    }

    return $str;
}

?>