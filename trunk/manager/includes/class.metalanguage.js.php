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
	this.version = '1.2.0';
	this.currentLanguage = 0;
	this.hover = false;
	this.buttonType = "";
	this.defaultLanguage = 0;
	this.cascades = {};
	this.fields = {};
}

MetaLanguage.require = function(libraryName) {
	var $objScript = jQuery("<script></script>");
	
	objScript.attr({
		type: "text/javascript",
		src: libraryName
	});
	jQuery("head").append($objScript);
}

MetaLanguage.load = function() {
	if(typeof jQuery == "undefined")
		throw("ContentLanguage class requires the jQuery library >= 1.4.2");

	alert("Old loading code in class.metalanguage.js.php on line 46");
}

MetaLanguage.prototype.init = function() {
 	jQuery("#frm_meta_language option[value="+ this.defaultLanguage +"]").attr("selected","selected");
}

MetaLanguage.prototype.swap = function(languageId) {
	var $objImage 		= jQuery("#meta_language_cascade"),
		$objButton		= $objImage.parent();
		
	this.toTemp();
	this.currentLanguage = languageId;
	
	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage) {
		$objImage.attr("src", "images/lang_unlocked_disabled.gif");
 		$objButton.unbind("mouseover mouseout click");
	} else {
		if (this.cascades[this.currentLanguage] !== true) {
			$objImage.attr("src", "images/lang_unlocked.gif");
		} else {
			$objImage.attr("src", "images/lang_locked.gif");
		}
		$objButton.bind("mouseover mouseout click", function(event){
			var objReturn;
			
			switch(event.type){
				case "mouseover":
					objReturn = objMetaLanguage.buttonOver("cascadeElement", this);
					break;
				case "mouseout":
					objReturn = objMetaLanguage.buttonOut("cascadeElement", this);
					break;
				case "click":
					objReturn = objMetaLanguage.toggleCascadeElement();
					break;
			}
			
			return objReturn;
		});
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

MetaLanguage.prototype.buttonOver = function(strButtonType, objImage, fieldId) {
	var $objImage 	= (objImage instanceof jQuery) ? objImage : jQuery(objImage), // Make sure it's a jQuery object
		$objButton	= $objImage.parent();
		
	this.hover = true;
	this.buttonType = strButtonType;
	
	switch (strButtonType) {
		case "cascadeElement":
			if (this.cascades[this.currentLanguage] !== true) {
				$objImage.attr("src", "images/lang_locked.gif");
				overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
			} else {
				$objImage.attr("src", "images/lang_unlocked.gif");
				overlib("<?php echo $objLang->get("langElementUnlock", "tip") ?>");
			}
			break;
			
		case "cascadeField":
			if (this.fields[fieldId].cascades[this.currentLanguage] !== true) {
				$objImage.attr("src", "images/lang_locked.gif");
				overlib("<?php echo $objLang->get("langFieldCascade", "tip") ?>");
			} else {
				$objImage.attr("src", "images/lang_unlocked.gif");
				overlib("<?php echo $objLang->get("langFieldUnlock", "tip") ?>");
			}
			break;			
	}
}

MetaLanguage.prototype.buttonOut = function(strButtonType, objImage, fieldId) {
	var $objImage 	= (objImage instanceof jQuery) ? objImage : jQuery(objImage), // Make sure it's a jQuery object
		$objButton	= $objImage.parent();
		
	this.hover = false;
	this.buttonType = strButtonType;
	
	switch (strButtonType) {
		case "cascadeElement":
			if (this.cascades[this.currentLanguage] !== true) {
				$objImage.attr("src", "images/lang_unlocked.gif");
			} else {
				$objImage.attr("src", "images/lang_locked.gif");
			}
			nd();
			break;
			
		case "cascadeField":
			if (this.fields[fieldId].cascades[this.currentLanguage] !== true) {
				$objImage.attr("src", "images/lang_unlocked.gif");
			} else {
				$objImage.attr("src", "images/lang_locked.gif");
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
		jQuery("#meta_language_cascade").attr("src", "images/lang_unlocked.gif");
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
		jQuery("#meta_language_cascade").attr("src", "images/lang_locked.gif");
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
	jQuery("#meta_language_cascade").attr("src", "images/lang_unlocked.gif");

	//*** Take action according to the state.
	this.toggleCascadeState(this.fields[fieldId].id, this.fields[fieldId].cascades[this.currentLanguage]);
	this.toScreen(this.fields[fieldId].id);
}

MetaLanguage.prototype.toggleCascadeState = function(fieldId, state) {	
	//*** Toggle object property.
	this.fields[fieldId].cascades[this.currentLanguage] = state;
	
	//*** Set the cascade input field.
	var strValue = this.fields[fieldId].getCascades();
	jQuery("#" + fieldId + "_cascades").val(strValue);
}

MetaLanguage.prototype.setFieldValue = function(fieldId, strValue) {
	jQuery("#" + fieldId + "_" + this.currentLanguage).val(strValue);
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
	jQuery("#" + this.id + "_cascades").val(this.getCascades());
}

MetaField.prototype.setIconCascade = function() {
	var $objImage 	= jQuery("#" + this.id + "_cascade"),
		$objButton	= $objImage.parent(),
		strId 		= this.id;

	//*** Attach mouse events to the cascade button.
	if (this.parent.currentLanguage == this.parent.defaultLanguage) {
		$objButton.unbind("mouseover mouseout click");

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			var strImageSrc = "images/lang_locked_disabled.gif";
		} else {
			var strImageSrc = "images/lang_unlocked_disabled.gif";
			$objButton.bind("click", function(){ return false; }); // No need to be clickable
		}
		$objImage.attr("src", strImageSrc);
	
	} else {
		$objButton.unbind("mouseover mouseout click"); // Clear all events before binding new ones
		$objButton.bind("mouseover mouseout click", function(event){
			var objReturn;
			
			switch(event.type){
				case "mouseover":
					objMetaLanguage.buttonOver('cascadeField', this, strId);
					break;
				case "mouseout":
					objMetaLanguage.buttonOut('cascadeField', this, strId);
					break;
				case "click":
					objMetaLanguage.toggleCascadeField(strId);
					break;
			}
			
			return false;
		});

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldUnlock", "tip") ?>");
			$objImage.attr("src", "images/lang_locked.gif");
		} else {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldCascade", "tip") ?>");
			$objImage.attr("src", "images/lang_unlocked.gif");
		}
	}
}

MetaField.prototype.toScreen = function() {
	this.setIconCascade();
	jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
	
	return true;
}

MetaField.prototype.toTemp = function() {
	var strValue = jQuery("#" + this.id).val();
	
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(strValue);
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
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val();
		jQuery("#" + this.id + "_alt").html((strValue == "") ? "&nbsp;" : strValue);
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id).show();
		jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
	}
}

MetaTextField.prototype.toTemp = function() {
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(jQuery("#" + this.id).val());
}
