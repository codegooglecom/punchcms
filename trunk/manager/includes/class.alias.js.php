<?php
session_save_path($_SERVER["DOCUMENT_ROOT"] . "/sessions");
session_start();

require_once('./inc.constantes.php');
require_once('../libraries/lib.language.php');

$objLang = null;
if (array_key_exists("objLang", $_SESSION)) $objLang = unserialize($_SESSION["objLang"]);
if (!is_object($objLang)) {
	require_once('../config.php');
	$objLang = new Language($_CONF['app']['defaultLang'], $_CONF['app']['langPath']);
}

?>

var intId = 0;

var Alias = {
	id:				0,
	checked:		false,
	targetField:	"frm_element",
	targetForm:		"aliasForm"
}

Alias.prepareAdd = function() {
	jQuery("#" + Alias.targetForm).slideDown();
	//jQuery("#" + Alias.targetForm).dialog("open");
}

Alias.remove = function(intId, strRedirect) {
	var blnConfirm = confirm("<?php echo $objLang->get("aliasRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
		strReturnTo = "";
		document.location.href = "?cid=<?php echo NAV_PCMS_ALIASES ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE ?>&returnTo=" + strReturnTo;
	}
}

Alias.multiDo = function(objField, strAction) {
	var arrChecked = new Array();
	$arrCheckbox = jQuery(".multiitem");

	//*** Loop through the fields to find the checked ones.
	$arrCheckbox.each(function(i){
		if(jQuery(this).is(":checked")){
			var strId = jQuery(this).attr("id").substr(5);
			arrChecked.push(strId);
		}
	});

	//*** Any fields checked?
	if (arrChecked.length > 0) {
		//*** Build URI and redirect.
		var strIds = arrChecked.join(',');

		switch (strAction) {
			case "delete":
				if (arrChecked.length > 1) {
					var blnConfirm = confirm("<?php echo $objLang->get("aliasesRemoveAlert", "alert") ?>");
				} else {
					var blnConfirm = confirm("<?php echo $objLang->get("aliasRemoveAlert", "alert") ?>");
				}

				if (blnConfirm == true) {
					document.location.href = "?cid=<?php echo NAV_PCMS_ALIASES ?>&eid=" + strIds + "&cmd=<?php echo CMD_REMOVE ?>";
				} else {
					//*** Reset pulldown.
					objField.selectedIndex = 0;
				}
				break;
		}
	} else {
		//*** Alert and reset pulldown.
		alert("<?php echo $objLang->get("multiItemEmpty", "alert") ?>");
		objField.selectedIndex = 0;
	}
}

Alias.multiSelect = function() {
	(this.checked) ? this.checked = false : this.checked = true;

	//*** Get all checkbox fields.
	arrCheckbox = jQuery(".multiitem");

	//*** Loop through the fields to check or uncheck.
	for (i = 0; i < arrCheckbox.length; i++) {
		arrCheckbox.attr("checked", this.checked);
	}
}

Alias.loadElements = function(blnInit, strTargetField, strTargetForm) {
	var strUrl = "ajax.php";

	if (strTargetField != undefined) Alias.targetField = strTargetField;
	if (strTargetForm != undefined) Alias.targetForm = strTargetForm;


	if (blnInit != undefined && blnInit) {
		intId = jQuery("#" + Alias.targetField).val();
		var strPost = "cmd=Elements::getParentHTML&eid=" + intId;
	} else {
		var strPost = "cmd=Elements::getChildrenHTML&params=" + intId;
	}

	var $objTraverse = jQuery("#" + Alias.targetForm).find("div.traverse");
	var $objFieldset = $objTraverse.parent();

	if ($objTraverse.length > 0) $objTraverse.remove();
	$objFieldset.append(Alias.elementsLoader());

	var request = jQuery.get(strUrl, strPost, Alias.showElements, "xml");
}

Alias.setElement = function(objSelect) {
	var $objSelect 		= (objSelect instanceof jQuery) ? objSelect : jQuery(objSelect), // Make sure it's a jQuery object
		intParentId 	= $objSelect.find("option:selected").val(),
		$objParentRow 	= $objSelect.parent(),
		$objRows 		= $objParentRow.nextAll("div"),
		$objSelects 	= jQuery("#" + Alias.targetForm + " select"),
		$objFieldset 	= jQuery("#" + Alias.targetForm + " fieldset.traverse"),
		$objTraverse 	= $objFieldset.find("div.traverse");

	$objSelects.each(function(){
		var __this = this,
			$objRow = jQuery(__this).parent();

		$objRows.each(function(){
			if(jQuery(this).get(0) == $objRow.get(0)){ // Use .get(0) to compare DOM objects instead of jQuery objects
				$objRow.remove();
			}
		});
	});

	if ($objTraverse.length > 0) $objTraverse.remove();
	$objFieldset.append(Alias.elementsTraverse());

	intId = intParentId;
	jQuery("#" + Alias.targetField).val(intParentId);
}

Alias.showElements = function(objXHR) {
	var $objResponse 	= jQuery(objXHR),
		$objFields 		= $objResponse.find("field"),
		$objFieldset 	= jQuery("#" + Alias.targetForm).find("fieldset.traverse"),
		$objLoading 	= $objFieldset.find("div.loading");

	if ($objLoading.length > 0) $objLoading.remove();

	//*** Can't be a jQuery each() loop.
	for (var i = 0; i < $objFields.length; i++) {
		var strValue = $objFields.get(i).firstChild.nodeValue;

		if (strValue) {
			var intParentId = $objFields.get(i).attributes[0].value;
			$objFieldset.append(Alias.elementsRow(strValue, intParentId));
		}
	}

	if(typeof intParentId != "undefined"){
		var $objSelect = jQuery("#" + Alias.targetField + "_" + intParentId);
		Alias.setElement($objSelect);
	}
}

Alias.elementsLoader = function() {
	var strReturn = "<div class=\"required loading\"><label for=\"" + Alias.targetField + "\">&nbsp;</label><?php echo $objLang->get("loading", "form") ?></div>";

	return strReturn;
}

Alias.elementsTraverse = function() {
	var strReturn = "<div class=\"required traverse\"><label for=\"" + Alias.targetField + "\">&nbsp;</label><a href=\"javascript:;\" onclick=\"Alias.loadElements()\" rel=\"internal\"><?php echo $objLang->get("oneLevelDeeper", "form") ?></a></div>";

	return strReturn;
}

/*
 * Check onchange compatibility with Internet Explorer >= 7
 */
Alias.elementsRow = function(strValue, intParentId) {
	var strLabel = (intParentId == 0) ? "* <?php echo $objLang->get("element", "form") ?>" : "&nbsp;";
	var strReturn = "<div class=\"required\"><label for=\"" + Alias.targetField + "\">" + strLabel + "</label><select id=\"" + Alias.targetField + "_" + intParentId + "\" class=\"select-one\" onchange=\"Alias.setElement(this)\">";
	strReturn += strValue;
	strReturn += "</select></div>";

	return strReturn;
}