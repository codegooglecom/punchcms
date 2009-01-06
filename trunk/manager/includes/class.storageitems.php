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

}

?>
