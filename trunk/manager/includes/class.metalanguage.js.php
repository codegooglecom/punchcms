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

/**************************************************************************
 * MetaLanguage Class.
 *************************************************************************/

/*** 
 * MetaLanguage object.
 */
var MetaLanguage = function() {
	this.version = '1.1.2';
	this.currentLanguage = 0;
	this.hover = false;
	this.defaultLanguage = 0;
	this.cascades = {};
	this.fields = {};
}

MetaLanguage.require = function(libraryName) {
	var objScript = document.createElement('script');
	objScript.setAttribute('type', 'text/javascript');
	objScript.setAttribute('src', libraryName);		
	var objHead = document.getElementsByTagName("head");
	objHead[0].appendChild(objScript);

	//*** Inserting via DOM fails in Safari 2.0, so brute force approach.
	//document.write('<script type="text/javascript" src="'+libraryName+'"></script>');
}

MetaLanguage.load = function() {
	if((typeof Prototype=='undefined') || 
			(typeof Element == 'undefined') || 
			(typeof Element.Methods=='undefined') ||
			parseFloat(Prototype.Version.split(".")[0] + "." +
			Prototype.Version.split(".")[1]) < 1.5)
		throw("MetaLanguage class requires the Prototype JavaScript framework >= 1.5.0");

	$A(document.getElementsByTagName("script")).findAll( function(s) {
		return (s.src && s.src.match(/pcms\.js(\?.*)?$/))
	}).each( function(s) {
		var path = s.src.replace(/pcms\.js(\?.*)?$/,'');
		var includes = s.src.match(/\?.*load=([a-z,]*)/);
		(includes ? includes[1] : 'ptemplate,pfield').split(',').each(
			function(include) { MetaLanguage.require(path+include+'.js') });
	});
}

MetaLanguage.prototype.init = function() {
	// Nothing.
}

MetaLanguage.prototype.swap = function(languageId) {
	this.toTemp();
	this.currentLanguage = languageId;
	
	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage) {
		$('meta_language_cascade').src = "images/lang_unlocked_disabled.gif";
 		$('meta_language_cascade').onmouseover = null;
 		$('meta_language_cascade').onmouseout = null;
 		$('meta_language_cascade').onmousedown = null;
	} else {
		if (this.cascades[this.currentLanguage] !== true) {
			$('meta_language_cascade').src = "images/lang_unlocked.gif";
		} else {
			$('meta_language_cascade').src = "images/lang_locked.gif";
		}
 		$('meta_language_cascade').onmouseover = function() { return objMetaLanguage.buttonOver('cascadeElement', this); };
 		$('meta_language_cascade').onmouseout = function() { return objMetaLanguage.buttonOut('cascadeElement', this); };
 		$('meta_language_cascade').onmousedown = function() { return objMetaLanguage.toggleCascadeElement(); };
	}
		
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.fields[count].cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

MetaLanguage.prototype.addField = function(fieldId, strCascades) {
	//*** Create and store the field object in the global fields array.
	var objField = new MetaTextField(fieldId, this, strCascades);
		
	this.fields[fieldId] = objField;
	this.toScreen(fieldId);
}

MetaLanguage.prototype.toScreen = function(fieldId) {
	this.fields[fieldId].toScreen();
}

MetaLanguage.prototype.toTemp = function(fieldId) {
	if (fieldId == undefined) {
		for (var intCount in this.fields) {
			this.fields[intCount].toTemp();
		}
	} else {
		this.fields[fieldId].toTemp();
	}
}

MetaLanguage.prototype.buttonOver = function(strButtonType, objButton, fieldId) {
	this.hover = true;
	this.buttonType = strButtonType;
	
	switch (strButtonType) {
		case "cascadeElement":
			if (this.cascades[this.currentLanguage] !== true) {
				objButton.src = "images/lang_locked.gif";
				overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
			} else {
				objButton.src = "images/lang_unlocked.gif";
				overlib("<?php echo $objLang->get("langElementUnlock", "tip") ?>");
			}
			break;
			
		case "cascadeField":
			if (this.fields[fieldId].cascades[this.currentLanguage] !== true) {
				objButton.src = "images/lang_locked.gif";
				overlib("<?php echo $objLang->get("langFieldCascade", "tip") ?>");
			} else {
				objButton.src = "images/lang_unlocked.gif";
				overlib("<?php echo $objLang->get("langFieldUnlock", "tip") ?>");
			}
			break;			
	}
}

MetaLanguage.prototype.buttonOut = function(strButtonType, objButton, fieldId) {
	this.hover = false;
	this.buttonType = strButtonType;
	
	switch (strButtonType) {
		case "cascadeElement":
			if (this.cascades[this.currentLanguage] !== true) {
				objButton.src = "images/lang_unlocked.gif";
			} else {
				objButton.src = "images/lang_locked.gif";
			}
			nd();
			break;
			
		case "cascadeField":
			if (this.fields[fieldId].cascades[this.currentLanguage] !== true) {
				objButton.src = "images/lang_unlocked.gif";
			} else {
				objButton.src = "images/lang_locked.gif";
			}
			nd();
			break;
	}
}

MetaLanguage.prototype.toggleCascadeElement = function() {
	//*** Set the toggle in the object.
	if (this.cascades[this.currentLanguage]) {
		if (this.cascades[this.currentLanguage] == true) {
			this.cascades[this.currentLanguage] = false;
		} else {
			this.cascades[this.currentLanguage] = true;
		}
	} else {
		this.cascades[this.currentLanguage] = true;
	}
	
	//*** Toggle button image.  
	if (this.cascades[this.currentLanguage] == true) {
		if (this.hover) overlib("<?php echo $objLang->get("langElementUnlock", "tip") ?>");
		$('meta_language_cascade').src = "images/lang_unlocked.gif";
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
		$('meta_language_cascade').src = "images/lang_locked.gif";
	}

	//*** Take action according to the state.
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

MetaLanguage.prototype.toggleCascadeField = function(fieldId) {
	//*** Set the toggle in the object.
	if (this.fields[fieldId].cascades[this.currentLanguage]) {
		if (this.fields[fieldId].cascades[this.currentLanguage] == true) {
			this.fields[fieldId].cascades[this.currentLanguage] = false;
		} else {
			this.fields[fieldId].cascades[this.currentLanguage] = true;
		}
	} else {
		this.fields[fieldId].cascades[this.currentLanguage] = true;
	}
	
	//*** Reset global cascade state.
	this.cascades[this.currentLanguage] = false;
	$('meta_language_cascade').src = "images/lang_unlocked.gif";

	//*** Take action according to the state.
	this.toggleCascadeState(this.fields[fieldId].id, this.fields[fieldId].cascades[this.currentLanguage]);
	this.toScreen(this.fields[fieldId].id);
}

MetaLanguage.prototype.toggleCascadeState = function(fieldId, state) {	
	//*** Toggle object property.
	this.fields[fieldId].cascades[this.currentLanguage] = state;
	
	//*** Set the cascade input field.
	var strValue = this.fields[fieldId].getCascades();
	$(fieldId + "_cascades").value = strValue;
}

MetaLanguage.prototype.setFieldValue = function(fieldId, strValue) {
	$(fieldId + "_" + this.currentLanguage).value = strValue;
}

/*** 
 * MetaField object.
 */
function MetaField(strId, objParent, strCascades) {
	this.id 				= strId || 0;
	this.parent				= objParent || null;
	this.cascades 			= {};
	
	if (strCascades != undefined) this.setCascades(strCascades);
}
	
MetaField.prototype.getCascades = function() {
	var strReturn = "";
	var arrTemp = new Array();

	for (var count in this.cascades) {
		if (this.cascades[count] == true && count != "") {
			arrTemp.push(count);
		}
	}

	strReturn = arrTemp.join(",");
	return strReturn;
}
	
MetaField.prototype.setCascades = function(strCascades) {
	var arrCascades = strCascades.split(",");

	this.cascades = {};
	for (var count = 0; count < arrCascades.length; count++) {
		this.cascades[arrCascades[count]] = true;
	}
	$(this.id + "_cascades").value = this.getCascades();
}

MetaField.prototype.toScreen = function() {
	$(this.id).value = $(this.id + "_" + this.parent.currentLanguage).value;
	
	return true;
}

MetaField.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).value;
	
	return true;
}

/*** 
 * MetaTextField object.
 */
function MetaTextField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

MetaTextField.prototype = new MetaField();

MetaTextField.prototype.toScreen = function() {
	//*** Insert value into the field.
	if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = $(this.id + "_" + this.parent.defaultLanguage).value;
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		Element.hide(this.id);
		Element.show(this.id + "_alt");
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id);
		$(this.id).value = $(this.id + "_" + this.parent.currentLanguage).value;
	}
}

MetaTextField.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).value;
}
