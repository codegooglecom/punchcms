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

function PTemplateField() {
	this.id = 0;
	this.checked = false;
}

PTemplateField.remove = function(intId) {
	var blnConfirm = confirm("<?php echo $objLang->get("templateFieldRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
		document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE_FIELD ?>";
	}
}

PTemplateField.duplicate = function(intId) {
	document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + intId + "&cmd=<?php echo CMD_DUPLICATE_FIELD ?>";
}

PTemplateField.multiDo = function(objField, strAction, cId) {
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
					var blnConfirm = confirm("<?php echo $objLang->get("templateFieldsRemoveAlert", "alert") ?>");
				} else {
					var blnConfirm = confirm("<?php echo $objLang->get("templateFieldRemoveAlert", "alert") ?>");
				}

				if (blnConfirm == true) {
					document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + strIds + "&cmd=<?php echo CMD_REMOVE_FIELD ?>";
				} else {
					//*** Reset pulldown.
					objField.selectedIndex = 0;
				}
				break;

			case "duplicate":
				document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + strIds + "&cmd=<?php echo CMD_DUPLICATE_FIELD ?>";
				break;
		}
	} else {
		//*** Alert and reset pulldown.
		alert("<?php echo $objLang->get("multiItemEmpty", "alert") ?>");
		objField.selectedIndex = 0;
	}
}

PTemplateField.multiSelect = function() {
	(this.checked) ? this.checked = false : this.checked = true;

	//*** Get all checkbox fields.
	arrCheckbox = document.getElementsByClassName("multiitem");

	//*** Loop through the fields to check or uncheck.
	for (i = 0; i < arrCheckbox.length; i++) {
		arrCheckbox[i].checked = this.checked;
	}
}

PTemplateField.addSetting = function(type, trigger) {
	switch (type) {
		case "image":
			jQuery("#tfv_image_setting_name").parent("div").show();
			jQuery("#subImage").append(jQuery(trigger).parent().parent().clone(true));
			jQuery("a.removeButton").each(function(index) {
				if (index > 0) {
					jQuery(this).show();
				}
			});
			jQuery("#tfv_image_settings_count").val((jQuery("#tfv_image_settings_count").val() - 1) + 2);
			break;
	}
	
	return false;
}

PTemplateField.removeSetting = function(type, trigger) {
	switch (type) {
		case "image":
			trigger.up("fieldset").remove();
			jQuery("#tfv_image_settings_count").val(jQuery("#tfv_image_settings_count").val() - 1);
			
			if (jQuery("#tfv_image_settings_count").val() == 0) {
				jQuery("#tfv_image_setting_name").parent("div").hide();
			}
			break;
	}
	
	return false;
}