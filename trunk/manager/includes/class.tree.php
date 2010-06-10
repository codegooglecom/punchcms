<?php

/* Tree Class v0.1.0
 * Holds methods for the creation of navigation trees.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class Tree {

	public static function treeRender($strType, $intElmntId) {
		global $_CONF,
				$objLang,
				$strCommand;

		$intCid = 0;
		$strTreeConfig = "";
		$strReturn = "";
		$strDragMethod = "";

		switch ($strType) {
			case "elements":
				$intCid = NAV_PCMS_ELEMENTS;
				$strDragMethod = "Element::setParent";
				$strReturn .= "function doOnImageRollOver(itemId) { return overlib('" . self::escapeForXml($objLang->get("editElement", "tip")) . "'); }\n";
				$strTreeConfig = "objTree.enableDragAndDrop(true);\n";
				$strTreeConfig .= "objTree.enableMultiselection(true,true);\n";
				$strTreeConfig .= "objTree.setOnImageClickHandler(doOnImageSelect);\n";
				$strTreeConfig .= "objTree.setOnImageRollOverHandler(doOnImageRollOver);\n";
				$strTreeConfig .= "objTree.setOnImageRollOutHandler(doOnImageRollOut);\n";
				$strTreeConfig .= "objTree.setDragHandler(doOnDrag);\n";
				$strTreeConfig .= "objTree.setDragBehavior('complex');\n";
				break;

			case "templates":
			case "templatefields":
				$intCid = NAV_PCMS_TEMPLATES;
				$strDragMethod = "Template::setParent";
				$strReturn .= "function doOnImageRollOver(itemId) { return overlib('" . self::escapeForXml($objLang->get("editTemplate", "tip")) . "'); }\n";
				$strTreeConfig = "objTree.enableDragAndDrop(true);\n";
				$strTreeConfig .= "objTree.enableMultiselection(true,true);\n";
				$strTreeConfig .= "objTree.setOnImageClickHandler(doOnImageSelect);\n";
				$strTreeConfig .= "objTree.setOnImageRollOverHandler(doOnImageRollOver);\n";
				$strTreeConfig .= "objTree.setOnImageRollOutHandler(doOnImageRollOut);\n";
				$strTreeConfig .= "objTree.setDragHandler(doOnDrag);\n";
				$strTreeConfig .= "objTree.setDragBehavior('complex');\n";
				
				if ($strType == "templatefields") {
					$objTemplateField = TemplateField::selectByPk($intElmntId);
					$intElmntId = $objTemplateField->getTemplateId();
					$strType = "templates";
				}
				break;

			case "users":
				$intCid = NAV_MYPUNCH_USERS;
				break;

			case "forms":
				$intCid = NAV_PCMS_FORMS;
				$strReturn .= "function doOnImageRollOver(itemId) { return overlib('" . self::escapeForXml($objLang->get("editForm", "tip")) . "'); }\n";
				$strTreeConfig .= "objTree.setOnImageClickHandler(doOnImageSelect);\n";
				$strTreeConfig .= "objTree.setOnImageRollOverHandler(doOnImageRollOver);\n";
				$strTreeConfig .= "objTree.setOnImageRollOutHandler(doOnImageRollOut);\n";
				break;
				
			case "storage":
				$intCid = NAV_PCMS_STORAGE;
				$strDragMethod = "StorageItem::setParent";
				$strReturn .= "function doOnImageRollOver(itemId) { return overlib('" . self::escapeForXml($objLang->get("editFolder", "tip")) . "'); }\n";
				$strTreeConfig = "objTree.enableDragAndDrop(true);\n";
				$strTreeConfig .= "objTree.enableMultiselection(true,true);\n";
				$strTreeConfig .= "objTree.setOnImageClickHandler(doOnImageSelect);\n";
				$strTreeConfig .= "objTree.setOnImageRollOverHandler(doOnImageRollOver);\n";
				$strTreeConfig .= "objTree.setOnImageRollOutHandler(doOnImageRollOut);\n";
				$strTreeConfig .= "objTree.setDragHandler(doOnDrag);\n";
				$strTreeConfig .= "objTree.setDragBehavior('complex');\n";
				break;
		}

		$strReturn .= "var objTree;\n";
		$strReturn .= "function doOnLoad() { this.openItem({$intElmntId});this.selectItem({$intElmntId}, false, false); }\n";
		$strReturn .= "function doOnSelect(itemId) { if (objTree.getSelectedItemId().split(',').length == 1) { document.location.href = '?cid={$intCid}&eid=' + itemId; } }\n";
		$strReturn .= "function doOnImageSelect(itemId) { if (objTree.getSelectedItemId().split(',').length == 1) { document.location.href = '?cid={$intCid}&eid=' + itemId + '&cmd=3'; } }\n";
		$strReturn .= "function doOnImageRollOut(itemId) { return nd(); }\n";
		$strReturn .= "function doOnDrag(idSubject, idTarget, idTargetParent, objTreeSubject, objTreeTarget) {\n";
		$strReturn .= "var objSaver = new dtmlXMLLoaderObject(null, null, false);\n";
		$strReturn .= "objSaver.loadXML(\"ajax.php?cmd={$strDragMethod}&eid=\" + idSubject + \"&parentId=\" + idTarget);\n";
		$strReturn .= "var objRoot = objSaver.getXMLTopNode(\"value\");\n";
		$strReturn .= "if (objRoot) {\n";
		$strReturn .= "var id = objRoot.firstChild.text;\n";
		$strReturn .= "if (id == undefined) {\n";
		$strReturn .= "var id = objRoot.firstChild.nodeValue;\n";
		$strReturn .= "}\n";
		$strReturn .= "if (id == -1) {\n";
		$strReturn .= "alert(\"Save failed\");\n";
		$strReturn .= "return false;\n";
		$strReturn .= "} else {\n";
		$strReturn .= "//objTreeTarget.selectItem(idTarget, true);\n";
		$strReturn .= "return true;\n";
		$strReturn .= "}\n";
		$strReturn .= "} else {\n";
		$strReturn .= "return false;\n";
		$strReturn .= "}\n";
		$strReturn .= "}\n";

		$strReturn .= "function loadTree() {\n";
		$strReturn .= "objTree = new dhtmlXTreeObject('treeContainer', '100%', '100%', -1);\n";
		$strReturn .= "objTree.setXMLAutoLoading('ajaxtree.php?type=" . $strType . "');\n";
		$strReturn .= "objTree.setImagePath('images/xmltree/');\n";
		$strReturn .= $strTreeConfig;
		$strReturn .= "objTree.setOnClickHandler(doOnSelect);\n";
		$strReturn .= "objTree.loadXML('ajaxtree.php?cmd=init&type=" .  $strType . "&id=" . $intElmntId . "', doOnLoad);\n";
		$strReturn .= "objTree.openItem({$intElmntId});\n";
		$strReturn .= "objTree.selectItem({$intElmntId}, false, false);\n";

		//*** Add elementfield link field dragzones.
		if ($strType == "elements") {
			$objElement = Element::selectByPk($intElmntId);
			if (is_object($objElement)) {
				switch ($strCommand) {
					case CMD_ADD:
						$objTemplates = $objElement->getSubTemplates();
						if ($objTemplates->count() == 1) {
							//*** Only one template available.
							$objTemplate = $objTemplates->current();
							$objFields = TemplateField::selectByTypeId(FIELD_TYPE_LINK, $objTemplate->getId());
							foreach ($objFields as $objField) {
								$strReturn .= "objTree.dragger.addDragLanding(document.getElementById('efv_{$objField->getId()}'), new DragDropLink);\n";
							}							
						}
					case CMD_EDIT:
						$objFields = TemplateField::selectByTypeId(FIELD_TYPE_LINK, $objElement->getTemplateId());
						foreach ($objFields as $objField) {
							$strReturn .= "objTree.dragger.addDragLanding(document.getElementById('efv_{$objField->getId()}'), new DragDropLink);\n";
						}
						break;
				}
			}
		}

		$strReturn .= "}\n";

		return $strReturn;
	}
	
	public static function buildXmlTree($intElmntId, $strType, $strAction) {
		$strReturn = "";
		
		if ($strAction == "init" || empty($intElmntId)) {
			$strReturn .= "<?xml version=\"1.0\" encoding=\"utf-8\"?><tree id=\"-1\">";
			$strReturn .= "<item text=\"&lt;b&gt;Website&lt;/b&gt;\" id=\"0\" im0=\"webroot.gif\" im1=\"webroot.gif\" im2=\"webroot.gif\" open=\"1\">";
			$strReturn .= self::buildXmlNodes($intElmntId, $strType, $strAction, $intElmntId);
			$strReturn .= "</item>";
			$strReturn .= "</tree>";
		} else {
			$strReturn .= "<?xml version=\"1.0\" encoding=\"utf-8\"?><tree id=\"{$intElmntId}\">";
			$strReturn .= self::buildXmlNodes($intElmntId, $strType, $strAction);
			$strReturn .= "</tree>";
		}
		
		return $strReturn;
	}
		
	private static function buildXmlNodes($intElmntId = 0, $strType = "elements", $strAction = "list", $childId = 0, $strChildren = "") {
		global $objLang,
			$objLiveUser;
		
		$strReturn = "";

		switch ($strType) {
			case "elements":
				$objElements = Elements::getFromParent($intElmntId);
				foreach ($objElements as $objElement) {
					if ($objLiveUser->checkRightLevel(PUNCHCMS_ELEMENTS_VIEW, $objElement->getPermissions()->getUserId(), $objElement->getPermissions()->getGroupId())) {
						$objChildren = $objElement->getElements();

						if (is_object($objChildren) && $objChildren->count() > 0 && $objElement->getTypeId() != ELM_TYPE_FOLDER) {
							if (!$objElement->getActive()) {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" style=\"color:#999\" im0=\"elementClosedInactive.gif\" im1=\"elementOpenInactive.gif\" im2=\"elementClosedInactive.gif\" child=\"1\">";
							} else {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" im0=\"elementClosed.gif\" im1=\"elementOpen.gif\" im2=\"elementClosed.gif\" child=\"1\">";
							}

							if ($childId == $objElement->getId()) {
								$strReturn .= $strChildren;
							}

							$strReturn .= "</item>";
						} else if ($objElement->getTypeId() == ELM_TYPE_FOLDER) {
							$intChild = (is_object($objChildren) && $objChildren->count() > 0) ? 1 : 0;
							if (!$objElement->getActive()) {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" style=\"color:#999\" im0=\"folderClosedInactive.gif\" im1=\"folderOpenInactive.gif\" im2=\"folderClosedInactive.gif\" child=\"{$intChild}\">";
							} else {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" im0=\"folderClosed.gif\" im1=\"folderOpen.gif\" im2=\"folderClosed.gif\" child=\"{$intChild}\">";
							}

							if ($childId == $objElement->getId()) {
								$strReturn .= $strChildren;
							}

							$strReturn .= "</item>";						
						} else {
							if (!$objElement->getActive()) {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" style=\"color:#999\" im0=\"leafInactive.gif\" im1=\"leafInactive.gif\" im2=\"leafInactive.gif\" child=\"0\" />";
							} else {
								$strReturn .= "<item text=\"" . self::escapeForXml($objElement->getName()) . "\" id=\"{$objElement->getId()}\" child=\"0\" />";
							}
						}
					}
				}

				if ($strAction == "init") {
					$objParent = Element::selectByPk($intElmntId);
					if (is_object($objParent)) {
						$intParent = $objParent->getParentId();
						$strReturn = self::buildXmlNodes($intParent, $strType, $strAction, $intElmntId, $strReturn);
					}
				}
				
				break;
			case "templates":
				$objTemplates = Templates::getFromParent($intElmntId);
							
				foreach ($objTemplates as $objTemplate) {
					$objChildren = $objTemplate->getTemplates();

					if (is_object($objChildren) && $objChildren->count() > 0) {
						$strReturn .= "<item text=\"" . self::escapeForXml($objTemplate->getName()) . "\" id=\"{$objTemplate->getId()}\" im0=\"templateClosed.gif\" im1=\"templateOpen.gif\" im2=\"templateClosed.gif\" child=\"1\">";

						if ($childId == $objTemplate->getId()) {
							$strReturn .= $strChildren;
						}

						$strReturn .= "</item>";
					} else {
						$strReturn .= "<item text=\"" . self::escapeForXml($objTemplate->getName()) . "\" id=\"{$objTemplate->getId()}\" im0=\"template.gif\" im1=\"template.gif\" im2=\"template.gif\" child=\"0\" />";
					}
				}

				if ($strAction == "init") {
					$objParent = Template::selectByPk($intElmntId);
					if (is_object($objParent)) {
						$intParent = $objParent->getParentId();
						$strReturn = self::buildXmlNodes($intParent, $strType, $strAction, $intElmntId, $strReturn);
					}
				}
				
				break;
			case "users":
				$strReturn = "<item text=\"" . self::escapeForXml($objLang->get("users", "usersLabel")) . "\" id=\"" . NAV_MYPUNCH_USERS_USER . "\" im0=\"misc.gif\" im1=\"misc.gif\" im2=\"misc.gif\" child=\"0\" />";
				$strReturn .= "<item text=\"" . self::escapeForXml($objLang->get("groups", "usersLabel")) . "\" id=\"" . NAV_MYPUNCH_USERS_GROUP . "\" im0=\"misc.gif\" im1=\"misc.gif\" im2=\"misc.gif\" child=\"0\" />";
				$strReturn .= "<item text=\"" . self::escapeForXml($objLang->get("applications", "usersLabel")) . "\" id=\"" . NAV_MYPUNCH_USERS_APPLICATION . "\" im0=\"misc.gif\" im1=\"misc.gif\" im2=\"misc.gif\" child=\"0\" />";
				$strReturn .= "<item text=\"" . self::escapeForXml($objLang->get("areas", "usersLabel")) . "\" id=\"" . NAV_MYPUNCH_USERS_AREA . "\" im0=\"misc.gif\" im1=\"misc.gif\" im2=\"misc.gif\" child=\"0\" />";
				$strReturn .= "<item text=\"" . self::escapeForXml($objLang->get("rights", "usersLabel")) . "\" id=\"" . NAV_MYPUNCH_USERS_RIGHT . "\" im0=\"misc.gif\" im1=\"misc.gif\" im2=\"misc.gif\" child=\"0\" />";
				
				break;
			case "forms":
				$objForms = Form::selectByAccountId();

				foreach ($objForms as $objForm) {
					$strReturn .= "<item text=\"" . self::escapeForXml($objForm->getName()) . "\" id=\"{$objForm->getId()}\" im0=\"template.gif\" im1=\"template.gif\" im2=\"template.gif\" child=\"0\" />";
				}

				break;
			case "storage":
				$objFolders = StorageItems::getFromParent($intElmntId, STORAGE_TYPE_FOLDER);

				foreach ($objFolders as $objFolder) {
					$objChildren = $objFolder->getFolders();

					if (is_object($objChildren) && $objChildren->count() > 0) {
						$strReturn .= "<item text=\"" . self::escapeForXml($objFolder->getName()) . "\" id=\"{$objFolder->getId()}\" im0=\"folderClosed.gif\" im1=\"folderOpen.gif\" im2=\"folderClosed.gif\" child=\"1\">";

						if ($childId == $objFolder->getId()) {
							$strReturn .= $strChildren;
						}

						$strReturn .= "</item>";
					} else {
						$strReturn .= "<item text=\"" . self::escapeForXml($objFolder->getName()) . "\" id=\"{$objFolder->getId()}\" im0=\"folderClosed.gif\" im1=\"folderOpen.gif\" im2=\"folderClosed.gif\" child=\"0\" />";
					}
				}

				if ($strAction == "init") {
					$objParent = StorageItem::selectByPk($intElmntId);
					if (is_object($objParent)) {
						$intParent = $objParent->getParentId();
						$strReturn = self::buildXmlNodes($intParent, $strType, $strAction, $intElmntId, $strReturn);
					}
				}
				break;
		}

		return $strReturn;
	}
	
	private static function escapeForXml($strInput) {
		return str_replace("&", "&amp;", str_replace("\"", "&quot;", $strInput));		
	}
}

?>