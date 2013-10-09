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

function PElement() {
	this.id = 0;
	this.checked = false;
}

PElement.executeCommand = function (url) {
    return $.get(url, PElement.updateItemList);
}

PElement.remove = function(intId, strRedirect) {
	var blnConfirm = confirm("<?php echo $objLang->get("elementRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
    	PElement.executeCommand("/?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE ?>");
	}
}

PElement.duplicate = function(intId, strRedirect) {
	PElement.executeCommand("/?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + intId + "&cmd=<?php echo CMD_DUPLICATE ?>");
}

PElement.updateItemList = function (data) {

    var itemlist = $(data).find("#itemlist");
    if (itemlist.length > 0) {
        $("#itemlist").html(itemlist.html());
    } else {
        if (console) {
            console.error("Failed to update item list.");
        }
    }

}

PElement.multiDo = function(objField, strAction) {
	var arrChecked = new Array();

	//*** Get all checkbox fields.
	arrCheckbox = $(".multiitem").get();

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
					var blnConfirm = confirm("<?php echo $objLang->get("elementsRemoveAlert", "alert") ?>");
				} else {
					var blnConfirm = confirm("<?php echo $objLang->get("elementRemoveAlert", "alert") ?>");
				}

				if (blnConfirm == true) {
    				PElement.executeCommand("?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + strIds + "&cmd=<?php echo CMD_REMOVE ?>");
				} else {
					//*** Reset pulldown.
					objField.selectedIndex = 0;
				}
				break;

			case "duplicate":
			    PElement.executeCommand("?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + strIds + "&cmd=<?php echo CMD_DUPLICATE ?>");
				break;

			case "activate":
			    PElement.executeCommand("?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + strIds + "&cmd=<?php echo CMD_ACTIVATE ?>");
				break;

			case "deactivate":
			    PElement.executeCommand("?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + strIds + "&cmd=<?php echo CMD_DEACTIVATE ?>");
				break;

			case "export":
			    PElement.executeCommand("?cid=<?php echo NAV_PCMS_ELEMENTS ?>&eid=" + strIds + "&cmd=<?php echo CMD_EXPORT_ELEMENT ?>&sel=1");
				break;
		}
	} else {
		//*** Alert and reset pulldown.
		alert("<?php echo $objLang->get("multiItemEmpty", "alert") ?>");
		objField.selectedIndex = 0;
	}
}

PElement.multiSelect = function() {
	(this.checked) ? this.checked = false : this.checked = true;

	//*** Get all checkbox fields.
	arrCheckbox = $(".multiitem").get();

	//*** Loop through the fields to check or uncheck.
	for (i = 0; i < arrCheckbox.length; i++) {
		arrCheckbox[i].checked = this.checked;
	}
}
