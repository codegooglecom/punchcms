<?php

require_once('includes/init.php');

function updateFeed() {
	global $_CONF;
	
	$objLangs = ContentLanguage::select();
	
	$objFeeds = Feed::selectActive();
	foreach ($objFeeds as $objFeed) {
		echo "Feed<br />";
		$objElementFeeds = ElementFeed::selectByFeed($objFeed->getId());
		foreach ($objElementFeeds as $objElementFeed) {
			echo "ElementFeed<br />";
			$objElement = Element::selectByPK($objElementFeed->getElementId());
			$objParent = Element::selectByPK($objElement->getParentId());
			if (is_object($objElement) && is_object($objParent) && $objParent->getTypeId() != ELM_TYPE_DYNAMIC) {
				//*** Remove old elements.
				$objOldElements = $objParent->getElements(FALSE, ELM_TYPE_LOCKED, $_CONF['app']['account']->getId());
				foreach ($objOldElements as $objOldElement) {
					$objOldElement->delete();
				}
				
				recursiveFeedInsert($objElement, $objParent, NULL, $objLangs);
			}
		}
	}
}

function recursiveFeedInsert($objElement, $objParent, $objNode, $objLangs) {
	global $objLiveUser, $_CONF;
	
	$objElementFeed = $objElement->getFeed();
	$objTemplate = Template::selectByPK($objElement->getTemplateId());
	
	//echo "FeedPath: " . $objElementFeed->getFeedPath() . "<br />";
	if (is_null($objNode)) {
		$objNodes = $objElementFeed->getBody();
	} else {
		$strFeedPath = $objElementFeed->getFeedPath();
		if (empty($strFeedPath)) {
			$objNodes = array($objNode);
		} else {
			$objNodes = $objNode->xpath($objElementFeed->getFeedPath());
		}
	}
	
	$intMaxItems = $objElementFeed->getMaxItems();
	if (empty($intMaxItems)) $intMaxItems = 0;
	$intCount = 1;
	
	foreach ($objNodes as $objNode) {
		//*** Create elements.
		$strName = "";
		$objInsertElement = new InsertFeedElement($objParent);
		$objInsertElement->setTemplate($objElement->getTemplateId());
		
		foreach ($objLangs as $objLang) {
			$objFeedFields = ElementFieldFeed::selectByElement($objElement->getId(), $objLang->getId());
			foreach ($objFeedFields as $objFeedField) {
				$strPath = $objFeedField->getXPath();
				//echo "path:" . $strPath . "<br />";
				if (stripos($strPath, "user->") !== FALSE) {
					$strValue = str_replace("user->", "", $strPath);
					$objInsertElement->addField($objFeedField->getTemplateFieldId(), $strValue, $objLang->getId(), $objFeedField->getCascade());
				} else {
					$objValue = (!empty($strPath)) ? $objNode->xpath($strPath) : NULL;
					if (!is_object($objValue) && count($objValue) > 0) {
						$strValue = (string) current($objValue);
						$objInsertElement->addField($objFeedField->getTemplateFieldId(), $strValue, $objLang->getId(), $objFeedField->getCascade());
						
						if (!is_numeric($strValue) && empty($strName)) {
							$strName = getShortValue($strValue, 40, TRUE, "");
						} 
					}
				}
				//echo "value: " . $strValue . "<br /><br /><br />";
			}	
		}
		
		$strName = (empty($strName)) ? "Dynamic" : $strName;
		$objInsertElement->setName($strName);
		$objInsertElement->setUsername($objLiveUser->getProperty('handle'));
		$objInsertElement->setActive(TRUE);
		$objInsertedElement = $objInsertElement->save();
		
		//echo $intCount . ": " . $objElement->getName() . " -> " . $objNode->getName() . "<br />";
		
		//*** Sub elements.
		$objSubElements = $objElement->getElements(FALSE, ELM_TYPE_DYNAMIC, $_CONF['app']['account']->getId());
		foreach ($objSubElements as $objSubElement) {
			recursiveFeedInsert($objSubElement, $objInsertedElement, $objNode, $objLangs);
		}
		
		if ($intMaxItems > 0 && $intCount >= $intMaxItems) break;
		$intCount++;
	}
}

updateFeed();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Insert title here</title>
</head>
<body>

<?php echo $strOutput; ?>

</body>
</html>