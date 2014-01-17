<?php

function parseFeeds($intFeedId, $strCommand) {
	global $_PATHS,
			$_CLEAN_POST,
			$_CONF,
			$objLang,
			$objLiveUser;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("feed.tpl.htm");
	
	$blnError = false;

	switch ($strCommand) {
		case CMD_LIST:			
		case CMD_ADD:
		case CMD_EDIT:
			//*** Post the profile form if submitted.
			if (count($_CLEAN_POST) > 0 && !empty($_CLEAN_POST['dispatch']) && $_CLEAN_POST['dispatch'] == "editFeed") {
				//*** The element form has been posted.
				
				//*** Check sanitized input.
				if (is_null($_CLEAN_POST["frm_active"])) {
					$blnError = true;
				}
				
				if (is_null($_CLEAN_POST["frm_name"])) {
					$blnError = true;
				}

				if (is_null($_CLEAN_POST["frm_feed"])) {
					$blnError = true;
				}

				if (is_null($_CLEAN_POST["frm_basepath"])) {
					$blnError = true;
				}

				if (is_null($_CLEAN_POST["frm_refresh"])) {
					$blnError = true;
				}
				
				if (is_null($_CLEAN_POST["dispatch"])) {
					$blnError = true;
				}

				if ($blnError === true) {
					//*** Display global error.
					$objTpl->setVariable("FORM_ACTIVE_VALUE", ($_POST["frm_active"] == "on") ? "checked=\"checked\"" : "");
					$objTpl->setVariable("FORM_NAME_VALUE", $_POST["frm_name"]);
					$objTpl->setVariable("FORM_FEED_VALUE", $_POST["frm_feed"]);
					$objTpl->setVariable("FORM_BASEPATH_VALUE", $_POST["frm_basepath"]);
					$objTpl->setVariable("FORM_REFRESH_VALUE", $_POST["frm_refresh"]);
					$objTpl->setVariable("ERROR_FEED_MAIN", $objLang->get("main", "formerror"));
				} else {
					//*** Input is valid. Save the feed.
					if ($strCommand == CMD_EDIT) {
						$objFeed = Feed::selectByPK($intFeedId);
					} else {
						$objFeed = new Feed();
					}
					
					$objFeed->setAccountId($_CONF['app']['account']->getId());
					$objFeed->setActive(($_POST["frm_active"] == "on") ? 1 : 0);
					$objFeed->setName($_CLEAN_POST["frm_name"]);
					$objFeed->setFeed($_CLEAN_POST["frm_feed"]);
					$objFeed->setBasepath($_CLEAN_POST["frm_basepath"]);
					$objFeed->setRefresh($_CLEAN_POST["frm_refresh"]);
					$objFeed->setLastUpdate(Date::toMysql());
					$objFeed->save();
					
					//*** Cache feed.
					$objFeed->cache();
				
					header("Location: " . Request::getURI() . "/?cid=" . NAV_PCMS_FEEDS);
					exit();
				}
			}			
			
			//*** Initiate child element loop.
			$objFeeds = Feed::selectSorted();
			$listCount = 0;
			$intPosition = request("pos");
			$intPosition = (!empty($intPosition) && is_numeric($intPosition)) ? $intPosition : 0;
			$intPosition = floor($intPosition / $_SESSION["listCount"]) * $_SESSION["listCount"];
			$objFeeds->seek($intPosition);
			
			foreach ($objFeeds as $objFeed) {			
				$objTpl->setCurrentBlock("multiview-item");
				$objTpl->setVariable("MULTIITEM_VALUE", $objFeed->getId());
				$objTpl->setVariable("BUTTON_REMOVE_HREF", "javascript:Feed.remove({$objFeed->getId()});");
				$objTpl->setVariable("BUTTON_REMOVE", $objLang->get("delete", "button"));
				$objTpl->setVariable("MULTIITEM_HREF", "?cid=" . NAV_PCMS_FEEDS . "&amp;eid={$objFeed->getId()}&amp;cmd=" . CMD_EDIT);
				$objTpl->setVariable("MULTIITEM_TYPE_CLASS", "feed");
				$objTpl->setVariable("MULTIITEM_NAME", $objFeed->getName());
				$objTpl->setVariable("MULTIITEM_POINTS_TO", $objLang->get("pointsTo", "label"));
				$objTpl->setVariable("MULTIITEM_FEED", $objFeed->getFeed());
				$objTpl->setVariable("MULTIITEM_FEED_HREF", $objFeed->getFeed());
				if (!$objFeed->getActive()) $objTpl->setVariable("MULTIITEM_ACTIVE", " class=\"inactive\"");
				$objTpl->parseCurrentBlock();
				
				$listCount++;
				if ($listCount >= $_SESSION["listCount"]) break;
			}

			//*** Render page navigation.
			$pageCount = ceil($objFeeds->count() / $_SESSION["listCount"]);
			if ($pageCount > 0) {
				$currentPage = ceil(($intPosition + 1) / $_SESSION["listCount"]);
				$previousPos = (($intPosition - $_SESSION["listCount"]) > 0) ? ($intPosition - $_SESSION["listCount"]) : 0;
				$nextPos = (($intPosition + $_SESSION["listCount"]) < $objFeeds->count()) ? ($intPosition + $_SESSION["listCount"]) : $intPosition;

				$objTpl->setVariable("PAGENAV_PAGE", sprintf($objLang->get("pageNavigation", "label"), $currentPage, $pageCount));
				$objTpl->setVariable("PAGENAV_PREVIOUS", $objLang->get("previous", "button"));
				$objTpl->setVariable("PAGENAV_PREVIOUS_HREF", "?cid=" . NAV_PCMS_FEEDS . "&amp;pos=$previousPos");
				$objTpl->setVariable("PAGENAV_NEXT", $objLang->get("next", "button"));
				$objTpl->setVariable("PAGENAV_NEXT_HREF", "?cid=" . NAV_PCMS_FEEDS . "&amp;pos=$nextPos");

				//*** Top page navigation.
				for ($intCount = 0; $intCount < $pageCount; $intCount++) {
					$objTpl->setCurrentBlock("multiview-pagenavitem-top");
					$position = $intCount * $_SESSION["listCount"];
					if ($intCount != $intPosition / $_SESSION["listCount"]) {
						$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_FEEDS . "&amp;pos=$position\"");
					}
					$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
					$objTpl->parseCurrentBlock();
				}

				//*** Bottom page navigation.
				for ($intCount = 0; $intCount < $pageCount; $intCount++) {
					$objTpl->setCurrentBlock("multiview-pagenavitem-bottom");
					$position = $intCount * $_SESSION["listCount"];
					if ($intCount != $intPosition / $_SESSION["listCount"]) {
						$objTpl->setVariable("PAGENAV_HREF", "href=\"?cid=" . NAV_PCMS_FEEDS . "&amp;pos=$position\"");
					}
					$objTpl->setVariable("PAGENAV_VALUE", $intCount + 1);
					$objTpl->parseCurrentBlock();
				}
			}
			
			//*** Render list action pulldown.
			$arrActions[$objLang->get("choose", "button")] = 0;
			$arrActions[$objLang->get("delete", "button")] = "delete";
			foreach ($arrActions as $key => $value) {
				$objTpl->setCurrentBlock("multiview-listactionitem");
				$objTpl->setVariable("LIST_ACTION_TEXT", $key);
				$objTpl->setVariable("LIST_ACTION_VALUE", $value);
				$objTpl->parseCurrentBlock();
			}
			
			$objTpl->setCurrentBlock("multiview");
			$objTpl->setVariable("LIST_LENGTH_HREF_10", "href=\"?list=10&amp;cid=" . NAV_PCMS_FEEDS . "\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_25", "href=\"?list=25&amp;cid=" . NAV_PCMS_FEEDS . "\"");
			$objTpl->setVariable("LIST_LENGTH_HREF_100", "href=\"?list=100&amp;cid=" . NAV_PCMS_FEEDS . "\"");

			switch ($_SESSION["listCount"]) {
				case 10:
					$objTpl->setVariable("LIST_LENGTH_HREF_10", "");
					break;

				case 25:
					$objTpl->setVariable("LIST_LENGTH_HREF_25", "");
					break;

				case 100:
					$objTpl->setVariable("LIST_LENGTH_HREF_100", "");
					break;
			}

			$objTpl->setVariable("LIST_LENGTH_HREF", "&amp;cid=" . NAV_PCMS_FEEDS);
			$objTpl->setVariable("LIST_WITH_SELECTED", $objLang->get("withSelected", "label"));
			$objTpl->setVariable("LIST_ACTION_ONCHANGE", "Feed.multiDo(this, this[this.selectedIndex].value)");
			$objTpl->setVariable("LIST_ITEMS_PER_PAGE", $objLang->get("itemsPerPage", "label"));
			$objTpl->setVariable("BUTTON_LIST_SELECT", $objLang->get("selectAll", "button"));
			$objTpl->setVariable("BUTTON_LIST_SELECT_HREF", "javascript:Feed.multiSelect()");
			$objTpl->parseCurrentBlock();
			
			$objTpl->setVariable("FEEDS", $objLang->get("feeds", "label"));
			$objTpl->setVariable("BUTTON_ADD", $objLang->get("feedAdd", "button"));
			
			//*** Form variables.
			if ($strCommand == CMD_EDIT) {
				$objFeed = Feed::selectByPK($intFeedId);
				$objTpl->setVariable("FORM_ACTIVE_VALUE", ($objFeed->getActive()) ? "checked=\"checked\"" : "");
				$objTpl->setVariable("FORM_NAME_VALUE", $objFeed->getName());
				$objTpl->setVariable("FORM_FEED_VALUE", $objFeed->getFeed());
				$objTpl->setVariable("FORM_BASEPATH_VALUE", $objFeed->getBasepath());
				$objTpl->setVariable("FORM_REFRESH_VALUE", $objFeed->getRefresh());
				$objTpl->setVariable("FRM_HEADER", $objLang->get("editFeed", "form"));
				$objTpl->setVariable("FRM_STYLE", "");
				$objTpl->setVariable("CMD", CMD_EDIT);				
			} else {
				$objTpl->setVariable("FORM_ACTIVE_VALUE", "checked=\"checked\"");
				$objTpl->setVariable("FRM_HEADER", $objLang->get("addFeed", "form"));
				if (!$blnError) $objTpl->setVariable("FRM_STYLE", " style=\"display:none\"");
				$objTpl->setVariable("CMD", CMD_ADD);
				
				$objElements = Elements::getFromParent(0);
				foreach ($objElements as $objElement) {
					$objTpl->setCurrentBlock("elements.item");				
					$objTpl->setVariable("VALUE", $objElement->getId());
					$objTpl->setVariable("LABEL", $objElement->getName());
					$objTpl->parseCurrentBlock();
				}
			}
			$objTpl->setVariable("FRM_LABEL_ACTIVE", $objLang->get("active", "form"));
			$objTpl->setVariable("FRM_LABEL_FEED", $objLang->get("feed", "form"));
			$objTpl->setVariable("FRM_DESCR_FEED", $objLang->get("feed", "tip"));
			$objTpl->setVariable("FRM_LABEL_BASEPATH", $objLang->get("basepath", "form"));
			$objTpl->setVariable("FRM_DESCR_BASEPATH", $objLang->get("basepath", "tip"));
			$objTpl->setVariable("FRM_LABEL_NAME", $objLang->get("name", "form"));
			$objTpl->setVariable("FRM_DESCR_REFRESH", $objLang->get("refresh", "tip"));
			$objTpl->setVariable("FRM_LABEL_REFRESH", $objLang->get("refresh", "form"));
			$objTpl->setVariable("FRM_LABEL_SAVE", $objLang->get("save", "button"));
			
			$objTpl->setVariable("CID", NAV_PCMS_FEEDS);
			$objTpl->setVariable("EID", $intFeedId);
			$objTpl->parseCurrentBlock();

			$strReturn = $objTpl->get();
			
			break;

		case CMD_REMOVE:
			if (strpos($intFeedId, ',') !== false) {
				//*** Multiple elements submitted.
				$arrFeeds = explode(',', $intFeedId);
				$objFeeds = Feed::selectByPK($arrFeeds);

				foreach ($objFeeds as $objFeed) {
					$objFeed->delete();
				}
			} else {
				//*** Single element submitted.
				$objFeed = Feed::selectByPK($intFeedId);
				$objFeed->delete();
			}

			//*** Redirect the page.
			$strReturnTo = request('returnTo');
			if (empty($strReturnTo)) {
				header("Location: " . Request::getUri() . "/?cid=" . request("cid") . "&cmd=" . CMD_LIST);
				exit();
			} else {
				header("Location: " . Request::getURI() . $strReturnTo);
				exit();
			}

			break;
	}

	return $strReturn;
}

?>