<?php
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
	id:0,
	checked:false,
	targetField:"frm_element",
	targetForm:"aliasForm"
}

Alias.prepareAdd = function() {
	Effect.BlindDown(Alias.targetForm);
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

	//*** Get all checkbox fields.
	arrCheckbox = document.getElementsByClassName("multiitem");

	//*** Loop through the fields to find the checked ones.
	for (i = 0; i < arrCheckbox.length; i++) {
		if (arrCheckbox[i].checked) {
			var strId = arrCheckbox[i].id.substr(5);
			arrChecked.push(strId);
		}
	}

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
	arrCheckbox = document.getElementsByClassName("multiitem");

	//*** Loop through the fields to check or uncheck.
	for (i = 0; i < arrCheckbox.length; i++) {
		arrCheckbox[i].checked = this.checked;
	}
}

Alias.loadElements = function(blnInit, strTargetField, strTargetForm) {
	if (strTargetField != undefined) Alias.targetField = strTargetField;
	if (strTargetForm != undefined) Alias.targetForm = strTargetForm;
	var strUrl = "ajax.php";
	
	if (blnInit != undefined && blnInit) {
		intId = $(Alias.targetField).value;
		var strPost = "cmd=Elements::getParentHTML&eid=" + intId;
	} else {
		var strPost = "cmd=Elements::getChildrenHTML&parentId=" + intId;
	}
	var objTraverse = $(Alias.targetForm).getElementsBySelector('div.traverse');
	var objFieldset = $(objTraverse[0]).up('fieldset', 0);

	if (objTraverse.length > 0) objTraverse[0].remove();
	new Insertion.Bottom(objFieldset, Alias.elementsLoader());
		
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Alias.showElements
			});
}

Alias.setElement = function(objSelect) {
	var intParentId = objSelect[objSelect.selectedIndex].value;
	var objParentRow = $(objSelect.id).up('div');
	var objRows = objParentRow.nextSiblings();
	var objSelects = $(Alias.targetForm).getElementsBySelector('select');
	var objFieldset = $(Alias.targetForm).getElementsBySelector('fieldset.traverse')[0];
	
	for (var intCount = 0; intCount < objSelects.length; intCount++) {
		var objRow = $(objSelects[intCount].id).up('div');
		for (var i = 0; i < objRows.length; i++) {
			if (objRows[i] == objRow) {
				objRow.remove();
			}
		}
	}

	var objTraverse = objFieldset.getElementsBySelector('div.traverse');
	if (objTraverse.length > 0) objTraverse[0].remove();
	new Insertion.Bottom(objFieldset, Alias.elementsTraverse());
	
	intId = intParentId;
	$(Alias.targetField).value = intParentId;
}

Alias.showElements = function(objXHR) {
	var objResponse = objXHR.responseXML;
	var objFields = objResponse.getElementsByTagName("field");
	var objFieldset = $(Alias.targetForm).getElementsBySelector('fieldset.traverse')[0];
	
	var objLoading = objFieldset.getElementsBySelector('div.loading');
	if (objLoading.length > 0) objLoading[0].remove();
	
	for (var i = 0; i < objFields.length; i++) {
		var strValue = objFields[i].firstChild.nodeValue;
		var intParentId = objFields[i].attributes[0].value;
		if (strValue) {
			new Insertion.Bottom(objFieldset, Alias.elementsRow(strValue, intParentId));
		}
	}
			
	var objSelect = $(Alias.targetField + "_" + intParentId);
	Alias.setElement(objSelect);
}

Alias.elementsLoader = function() {
	var strReturn = "<div class=\"required loading\"><label for=\"" + Alias.targetField + "\">&nbsp;</label><?php echo $objLang->get("loading", "form") ?></div>";
	
	return strReturn;
}

Alias.elementsTraverse = function() {
	var strReturn = "<div class=\"required traverse\"><label for=\"" + Alias.targetField + "\">&nbsp;</label><a href=\"javascript:;\" onclick=\"Alias.loadElements()\" rel=\"internal\"><?php echo $objLang->get("oneLevelDeeper", "form") ?></a></div>";
	
	return strReturn;
}

Alias.elementsRow = function(strValue, intParentId) {
	var strLabel = (intParentId == 0) ? "* <?php echo $objLang->get("element", "form") ?>" : "&nbsp;";
	var strReturn = "<div class=\"required\"><label for=\"" + Alias.targetField + "\">" + strLabel + "</label><select id=\"" + Alias.targetField + "_" + intParentId + "\" class=\"select-one\" onchange=\"Alias.setElement(this)\">";
	strReturn += strValue;
	strReturn += "</select></div>";
	
	return strReturn;
}
