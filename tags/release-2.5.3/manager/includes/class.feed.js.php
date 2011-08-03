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

var Feed = {
	id:				0,
	checked:		false,
	targetField:	"frm_element",
	targetForm:		"feedForm"
}

Feed.prepareAdd = function() {
	jQuery("#" + Feed.targetForm).slideDown();
}

Feed.remove = function(intId, strRedirect) {
	var blnConfirm = confirm("<?php echo $objLang->get("feedRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
		strReturnTo = "";
		document.location.href = "?cid=<?php echo NAV_PCMS_FEEDS ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE ?>&returnTo=" + strReturnTo;
	}
}

Feed.multiDo = function(objField, strAction) {
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
					var blnConfirm = confirm("<?php echo $objLang->get("feedsRemoveAlert", "alert") ?>");
				} else {
					var blnConfirm = confirm("<?php echo $objLang->get("feedRemoveAlert", "alert") ?>");
				}

				if (blnConfirm == true) {
					document.location.href = "?cid=<?php echo NAV_PCMS_FEEDS ?>&eid=" + strIds + "&cmd=<?php echo CMD_REMOVE ?>";
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

Feed.multiSelect = function() {
	(this.checked) ? this.checked = false : this.checked = true;

	//*** Get all checkbox fields.
	arrCheckbox = jQuery(".multiitem");

	//*** Loop through the fields to check or uncheck.
	for (i = 0; i < arrCheckbox.length; i++) {
		arrCheckbox.attr("checked", this.checked); 
	}
}
