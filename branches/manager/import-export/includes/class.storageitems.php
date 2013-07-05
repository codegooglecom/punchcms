<?php

/* StorageItems Class v0.1.0
 * Collection class for the StorageItem objects.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class StorageItems extends DBA__Collection {

	public static function getFromParent($lngParentId, $intItemType = STORAGE_TYPE_ALL, $intAccountId = 0) {
		global $_CONF;

		if ($intAccountId == 0) {
			$intAccountId = $_CONF['app']['account']->getId();
		}

		$strSql = sprintf("SELECT * FROM pcms_storage_item WHERE parentId = '%s' AND typeId IN (%s) AND accountId = '%s' ORDER BY sort", $lngParentId, $intItemType, $intAccountId);
		$objItems = StorageItem::select($strSql);
		
		return $objItems;
	}

	public static function sortChildren($intItemId) {
		$lastSort = 0;
		$arrItemlist = request("itemlist");
		$lastPosition = request("pos", 0);

		if (is_array($arrItemlist) && count($arrItemlist) > 0) {
			//*** Find last sort position.
			if ($lastPosition > 0) {
				$objFiles = StorageItems::getFromParent($intItemId);
				$objFiles->seek($lastPosition);
				$lastSort = $objFiles->current()->getSort();
			}

			//*** Loop through the items and manipulate the sort order.
			foreach ($arrItemlist as $value) {
				$lastSort++;
				$objFile = StorageItem::selectByPK($value);
				$objFile->setSort($lastSort);
				$objFile->save(FALSE);
			}
		}
	}
	
	public static function getParentHTML() {
		global $_CONF;
		$strReturn = "";
		
		$intId = request("eid", 0);
		
		if ($intId > 0) {
			$objElement = StorageItem::selectByPK($intId);
			if ($objElement) {
				$strReturn .= self::getChildrenHTML($objElement->getParentId(), true, $intId);
			} else {
				$strReturn .= self::getChildrenHTML(0, true, $intId);
			}
		}
		
		return $strReturn;
	}

	public static function getChildrenHTML($intParentId = NULL, $blnRecursive = NULL, $intChildId = NULL) {
		global $_CONF;
		$strReturn = "";
		
		$intParentId = (is_null($intParentId)) ? request("parentId", 0) : $intParentId;
		$blnRecursive = (is_null($blnRecursive)) ? request("recursive", 0) : $blnRecursive;
		$intAccountId = $_CONF['app']['account']->getId();
		
		if ($blnRecursive && $intParentId > 0) {
			$objElement = StorageItem::selectByPK($intParentId);
			if ($objElement) {
				$strReturn .= self::getChildrenHTML($objElement->getParentId(), $blnRecursive, $intParentId);
			}
		}
		
		$strSql = sprintf("SELECT * FROM pcms_storage_item WHERE parentId = '%s' AND accountId = '%s' ORDER BY sort", $intParentId, $intAccountId);
		$objElements = StorageItem::select($strSql);
		
		$strReturn .= "<field id=\"{$intParentId}\"><![CDATA[";
		foreach ($objElements as $objElement) {
			$strSelected = ($intChildId == $objElement->getId()) ? " selected=\"selected\"" : "";
			$strReturn .= "<option value=\"{$objElement->getTypeId()}_{$objElement->getId()}\"{$strSelected}>" . str_replace("&", "&amp;", $objElement->getName()) . "</option>\n";
		}
		$strReturn .= "]]></field>";
		
		return $strReturn;
	}
	
	public static function getFolderListHTML($intParentId = 0, $intDepth = 1) {
		global $_CONF;
		$strReturn = "";
		
		$intAccountId = $_CONF['app']['account']->getId();

		if ($intParentId == 0) {
			$strReturn .= "<option value=\"eid_0\">Website</option>\n";
		}

		$strSql = sprintf("SELECT * FROM pcms_storage_item WHERE parentId = '%s' AND typeId IN (%s) AND accountId = '%s' ORDER BY sort", $intParentId, STORAGE_TYPE_FOLDER, $intAccountId);
		$objElements = StorageItem::select($strSql);
		foreach ($objElements as $objElement) {
			$strReturn .= "<option value=\"eid_{$objElement->getId()}\">" . str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $intDepth) . str_replace("&", "&amp;", $objElement->getName()) . "</option>\n";
			$strReturn .= StorageItems::getFolderListHTML($objElement->getId(), $intDepth + 1);
		}
		
		return $strReturn;
	}
	
	public static function getFileListHTML() {
		global $_CONF;
		$strReturn = "";
		
		$intParentId = request("parentId", 0);
		$intAccountId = $_CONF['app']['account']->getId();
		$arrImages = array('jpg', 'jpeg', 'gif', 'png');
		
		$strSql = sprintf("SELECT * FROM pcms_storage_item WHERE parentId = '%s' AND typeId IN (%s) AND accountId = '%s' ORDER BY name", $intParentId, STORAGE_TYPE_FILE, $intAccountId);
		$objElements = StorageItem::select($strSql);
		
		$strReturn .= "<field id=\"{$intParentId}\"><![CDATA[";
		$strReturn .= "<ul>";
		foreach ($objElements as $objElement) {
			$objData = $objElement->getData();
			$strExtension = substr(strrchr($objData->getLocalName(), '.'), 1);
			$strImageSrc = (in_array($strExtension, $arrImages)) ? Setting::getValueByName("web_server") . Setting::getValueByName("file_folder") . $objData->getLocalName() : "/images/ico_document_big.gif";
			$strReturn .= "<li><a href=\"\" id=\"eid_{$objElement->getId()}\"><img width=\"100\" height=\"100\" src=\"{$strImageSrc}\" alt=\"{$objData->getLocalName()}\" /></a><span>" . str_replace("&", "&amp;", $objElement->getName()) . "</span></li>";
		}
		$strReturn .= "</ul>";
		$strReturn .= "]]></field>";
		
		return $strReturn;
	}

}

?>
