<?php

function parseSearch($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang,
			$DBAConn;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("search.tpl.htm");

	$objTpl->setVariable("TITLE", $objLang->get("pageTitle"));
	$objTpl->setVariable("MAINTITLE", $objLang->get("pcmsSearch", "menu"));
	$objTpl->setVariable("BUTTON_INDEX_HREF", "?cid=" . NAV_PCMS_SEARCH . "&amp;cmd=" . CMD_BUILD_INDEX);
	$objTpl->setVariable("BUTTON_INDEX", $objLang->get("searchIndex", "button"));
	$objTpl->setVariable("SEARCH_BUTTON", $objLang->get("search", "button"));
	$objTpl->setVariable("SEARCH_ALL", $objLang->get("searchall", "label"));

	//*** Perform query if submitted.
	$objSearch 		= new Search();
	$strQuery 		= request('query');
	$strExact 		= request('search_all');
	$strCache 		= request('cache');
	$intPosition 	= request("pos");
	$blnExact 		= false;
	$blnCache 		= false;

	if ($strExact == 'on') $blnExact = true;
	if ($strCache == 'true') $blnCache = true;

	if ($strCommand == CMD_BUILD_INDEX) {
		//*** Rebuild search index.
		set_time_limit(60*60);
		$objSearch->clearIndex();
		$objSearch->updateIndex();
		$objTpl->setVariable("SEARCH_DESCRIPTION", $objLang->get("searchIndexed", "form"));
	}

	if (!empty($strQuery)) {
		$objResults = $objSearch->find($strQuery, $blnExact);

		//*** Cache results in the current session.
		$_SESSION["searchresult"] = serialize($objResults);
	} else if ($blnCache && isset($_SESSION["searchresult"]) && is_object(unserialize($_SESSION["searchresult"]))) {
		$objResults = unserialize($_SESSION["searchresult"]);
	}

	if (isset($objResults) && is_object($objResults)) {
		$objTpl->setVariable("RESULT_LABEL", $objLang->get("searchresult", "label"));
		$objTpl->setVariable("SEARCH_STRING", $objResults->getQuery());

		if ($objResults->count() > 0) {
			$listCount = 0;
			$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
			$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];
			$objResults->seek($intPosition);

			//*** Render results.
			foreach ($objResults as $objResult) {
				$objElement = Element::selectByPK($objResult->id);
				if (is_object($objElement)) {
					$strPath = "<b>" . $objLang->get("path", "label") . "</b>" . Element::recursivePath($objElement->getParentId());
				} else {
					$strPath = "";
				}
				$objTpl->setCurrentBlock("searchresult");
				$objTpl->setVariable("EID", $objResult->id);
				$objTpl->setVariable("CID", NAV_PCMS_ELEMENTS);
				$objTpl->setVariable("CMD", CMD_EDIT);
				$objTpl->setVariable("RESULT_NAME", $objResult->name);
				$objTpl->setVariable("RESULT_RATIO", $objResult->ratio);
				$strValue = strip_tags($objResult->value);
				if (!empty($strValue)) $objTpl->setVariable("RESULT_VALUE", $strValue);
				if (!empty($strPath)) $objTpl->setVariable("RESULT_PATH", $strPath);
				$objTpl->parseCurrentBlock();
			}

			//*** Render page navigation.
			$pageCount = ceil($objResults->count() / $_SESSION["listCount"]);
			if ($pageCount > 0) {
				$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
				$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
				$nextPos = (($intPosition + $_SESSION["listCount"]) < $objResults->count()) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

				$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
				$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
				$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_SEARCH . "&amp;pos=$previousPos&amp;cache=true");
				$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
				$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_SEARCH . "&amp;pos=$nextPos&amp;cache=true");

				//*** Bottom page navigation.
				for ($intCount = 0; $intCount < $pageCount; $intCount++) {
					$objTpl->setCurrentBlock("pagenavitem");
					$position = $intCount * $_SESSION["listCount"];
					if ($intCount != $intPosition / $_SESSION["listCount"]) {
						$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_SEARCH . "&amp;pos=$position&amp;cache=true\"");
					}
					$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
					$objTpl->parseCurrentBlock();
				}
			}
		} else {
			$objTpl->setCurrentBlock("searchresult");
			$objTpl->setVariable("RESULT_VALUE", $objLang->get("search_noresult", "label"));
			$objTpl->parseCurrentBlock();
		}
	}

	$objTpl->setVariable("CID", NAV_PCMS_SEARCH);

	$strReturn = $objTpl->get();

	return $strReturn;
}

?>