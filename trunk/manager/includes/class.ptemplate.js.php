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

PTemplate = {
	id: 0,
	FIELD_TYPE_DATE: 1,
	FIELD_TYPE_SMALLTEXT: 2,
	FIELD_TYPE_LARGETEXT: 3,
	FIELD_TYPE_FILE: 4,
	FIELD_TYPE_NUMBER: 5,
	FIELD_TYPE_SELECT_LIST_MULTI: 6,
	FIELD_TYPE_IMAGE: 7,
	FIELD_TYPE_USER: 8,
	FIELD_TYPE_LINK: 9,
	FIELD_TYPE_BOOLEAN: 10,
	FIELD_TYPE_SELECT_LIST_SINGLE: 11,
	FIELD_TYPE_CHECK_LIST_MULTI: 12,
	FIELD_TYPE_CHECK_LIST_SINGLE: 13,
	FIELD_TYPE_SIMPLETEXT: 14
}

PTemplate.remove = function(intId) {
	var blnConfirm = confirm("<?php echo $objLang->get("templateRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
		document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE ?>";
	}
}

PTemplate.duplicate = function(intId, strRedirect) {
	strReturnTo = "";
	document.location.href = "?cid=<?php echo NAV_PCMS_TEMPLATES ?>&eid=" + intId + "&cmd=<?php echo CMD_DUPLICATE ?>&returnTo=" + strReturnTo;
}

PTemplate.fieldTypeChange = function(objList) {
	var arrObjects = ["subSingleList","subImage","subMaxCharacters","subFormat","subMinMaxValue","subFile","subMultiList", "subBoolean"];
	var arrSelect = [];

	switch (parseInt(objList[objList.selectedIndex].value)) {
		case this.FIELD_TYPE_DATE:
			arrSelect = new Array(0,0,0,1,0,0,0,0);
			break;

		case this.FIELD_TYPE_FILE:
			arrSelect = new Array(0,0,0,0,0,1,0,0);
			break;

		case this.FIELD_TYPE_IMAGE:
			arrSelect = new Array(0,1,0,0,0,0,0,0);
			break;

		case this.FIELD_TYPE_SMALLTEXT:
		case this.FIELD_TYPE_LARGETEXT:
		case this.FIELD_TYPE_SIMPLETEXT:
			arrSelect = new Array(0,0,1,0,0,0,0,0);
			break;

		case this.FIELD_TYPE_NUMBER:
			arrSelect = new Array(0,0,0,0,1,0,0,0);
			break;

		case this.FIELD_TYPE_SELECT_LIST_SINGLE:
		case this.FIELD_TYPE_CHECK_LIST_SINGLE:
			arrSelect = new Array(1,0,0,0,0,0,0,0);
			break;

		case this.FIELD_TYPE_SELECT_LIST_MULTI:
		case this.FIELD_TYPE_CHECK_LIST_MULTI:
			arrSelect = new Array(0,0,0,0,0,0,1,0);
			break;

		case this.FIELD_TYPE_LINK:
			arrSelect = new Array(0,0,0,0,0,0,0,0);
			break;

		case this.FIELD_TYPE_USER:
			arrSelect = new Array(0,0,0,0,0,0,0,0);
			break;

		case this.FIELD_TYPE_BOOLEAN:
			arrSelect = new Array(0,0,0,0,0,0,0,1);
			break;

	}

	for (var i = 0; i < arrSelect.length; i++) {
		if (arrSelect[i] == 1) {
			jQuery("#" + arrObjects[i]).show();
		} else {
			jQuery("#" + arrObjects[i]).hide();
		}
	}
}
