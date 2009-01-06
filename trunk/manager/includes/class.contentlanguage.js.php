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

/*
###########################################################################
#  Copyright (c) 2006 Phixel.org (http://www.phixel.org)
#
#  Permission is hereby granted, free of charge, to any person obtaining
#  a copy of this software and associated documentation files (the
#  "Software"), to deal in the Software without restriction, including
#  without limitation the rights to use, copy, modify, merge, publish,
#  distribute, sublicense, and/or sell copies of the Software, and to
#  permit persons to whom the Software is furnished to do so, subject to
#  the following conditions:
#
#  The above copyright notice and this permission notice shall be
#  included in all copies or substantial portions of the Software.
#
#  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
#  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
#  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
#  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
#  LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
#  OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
#  WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
###########################################################################
*/

/**************************************************************************
 * ContentLanguage Class.
 *************************************************************************/

/*** 
 * ContentLanguage object.
 */
var ContentLanguage = function() {
	this.version = '1.1.2';
	this.currentLanguage = 0;
	this.hover = false;
	this.buttonType = "";
	this.defaultLanguage = 0;
	this.actives = {};
	this.cascades = {};
	this.fields = {};
	this.FIELDTYPE = {
			'DATE': '1',
			'TEXT': '2',
			'TEXTAREA': '3',
			'FILE': '4',
			'NUMBER': '5',
			'SELECT_LIST': '6',
			'IMAGE': '7',
			'USER': '8',
			'LINK': '9',
			'CHECKBOX': '10',
			'CHECK_LIST': '11',
			'SIMPLETEXT': '12'
		};
}

ContentLanguage.require = function(libraryName) {
	var objScript = document.createElement('script');
	objScript.setAttribute('type', 'text/javascript');
	objScript.setAttribute('src', libraryName);		
	var objHead = document.getElementsByTagName("head");
	objHead[0].appendChild(objScript);

	//*** Inserting via DOM fails in Safari 2.0, so brute force approach.
	//document.write('<script type="text/javascript" src="'+libraryName+'"></script>');
}

ContentLanguage.load = function() {
	if((typeof Prototype=='undefined') || 
			(typeof Element == 'undefined') || 
			(typeof Element.Methods=='undefined') ||
			parseFloat(Prototype.Version.split(".")[0] + "." +
			Prototype.Version.split(".")[1]) < 1.5)
		throw("ContentLanguage class requires the Prototype JavaScript framework >= 1.5.0");

	$A(document.getElementsByTagName("script")).findAll( function(s) {
		return (s.src && s.src.match(/pcms\.js(\?.*)?$/))
	}).each( function(s) {
		var path = s.src.replace(/pcms\.js(\?.*)?$/,'');
		var includes = s.src.match(/\?.*load=([a-z,]*)/);
		(includes ? includes[1] : 'ptemplate,pfield').split(',').each(
			function(include) { ContentLanguage.require(path+include+'.js') });
	});
}

ContentLanguage.remove = function(intId) {
	var blnConfirm = confirm("<?php echo $objLang->get("languageRemoveAlert", "alert") ?>");

	if (blnConfirm == true) {
		strReturnTo = "";
		document.location.href = "?cid=<?php echo NAV_PCMS_LANGUAGES ?>&eid=" + intId + "&cmd=<?php echo CMD_REMOVE ?>&returnTo=" + strReturnTo;
	}
}

ContentLanguage.setDefault = function(intId) {
	document.location.href = "?cid=<?php echo NAV_PCMS_LANGUAGES ?>&eid=" + intId + "&cmd=<?php echo CMD_SET_DEFAULT ?>";
}

ContentLanguage.prepareAdd = function() {
	Effect.BlindDown('languageForm');
}

ContentLanguage.prototype.init = function() {
 	$('language_active').onmouseover = function() { return objContentLanguage.buttonOver('activeElement', this); };
 	$('language_active').onmouseout = function() { return objContentLanguage.buttonOut('activeElement', this); };
 	$('language_active').onmousedown = function() { return objContentLanguage.toggleActiveElement(); };
}

ContentLanguage.prototype.swap = function(languageId) {
	this.toTemp();
	this.currentLanguage = languageId;
	
	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage || this.actives[this.currentLanguage] != true || this.actives[this.defaultLanguage] != true) {
		$('language_cascade').src = "images/lang_unlocked_disabled.gif";
 		$('language_cascade').onmouseover = null;
 		$('language_cascade').onmouseout = null;
 		$('language_cascade').onmousedown = null;
	} else {
		if (this.cascades[this.currentLanguage] !== true) {
			$('language_cascade').src = "images/lang_unlocked.gif";
		} else {
			$('language_cascade').src = "images/lang_locked.gif";
		}
 		$('language_cascade').onmouseover = function() { return objContentLanguage.buttonOver('cascadeElement', this); };
 		$('language_cascade').onmouseout = function() { return objContentLanguage.buttonOut('cascadeElement', this); };
 		$('language_cascade').onmousedown = function() { return objContentLanguage.toggleCascadeElement(); };
	}
	
	//*** Check the active state.
	if (this.actives[this.currentLanguage] != true) {
		if (this.hover) overlib("<?php echo $objLang->get("langEnable", "tip") ?>");
		$('language_active').src = "images/lang_disabled.gif";
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langDisable", "tip") ?>");
		$('language_active').src = "images/lang_enabled.gif";	
	}
	
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.fields[count].cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

ContentLanguage.prototype.addField = function(fieldId, strCascades, intType, objOptions) {
	//*** Create and store the field object in the global fields array.
	switch (intType) {
		case this.FIELDTYPE.DATE:
			var objField = new DateField(fieldId, this, strCascades);
			break;
			
		case this.FIELDTYPE.TEXT:
		case this.FIELDTYPE.SIMPLETEXT:
			var objField = new TextField(fieldId, this, strCascades);
			break;
			
		case this.FIELDTYPE.CHECKBOX:
			var objField = new CheckBox(fieldId, this, strCascades);
			break;
			
		case this.FIELDTYPE.TEXTAREA:
			var objField = new TextAreaField(fieldId, this, strCascades);
			break;
			
		case this.FIELDTYPE.FILE:
		case this.FIELDTYPE.IMAGE:
			var objField = new FileField(fieldId, this, strCascades, objOptions);
			break;
			
		case this.FIELDTYPE.SELECT_LIST:
			var objField = new SelectListField(fieldId, this, strCascades);
			break;
			
		case this.FIELDTYPE.CHECK_LIST:
			var objField = new CheckListField(fieldId, this, strCascades);
			break;
			
	}
		
	this.fields[fieldId] = objField;
	this.toScreen(fieldId);
}

ContentLanguage.prototype.toScreen = function(fieldId) {
	this.fields[fieldId].toScreen();
}

ContentLanguage.prototype.toTemp = function(fieldId) {
	if (fieldId == undefined) {
		for (var intCount in this.fields) {
			this.fields[intCount].toTemp();
		}
	} else {
		this.fields[fieldId].toTemp();
	}
}

ContentLanguage.prototype.dateToTemp = function(objDate) {
	if (objDate != undefined) {
		objContentLanguage.toTemp(objDate.params.inputField.id);
	}
}

ContentLanguage.prototype.transferField = function(fieldId) {
	this.fields[fieldId].transferField();
}

ContentLanguage.prototype.sort = function(fieldId) {
	this.fields[fieldId].sort();
}

ContentLanguage.prototype.removeUploadField = function(fieldId, objTrigger) {
	this.fields[fieldId].removeUploadField(objTrigger);
}

ContentLanguage.prototype.removeCurrentField = function(fieldId, objTrigger) {
	this.fields[fieldId].removeCurrentField(objTrigger);
}

ContentLanguage.prototype.buttonOver = function(strButtonType, objButton, fieldId) {
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
			
		case "activeElement":
			if (this.actives[this.currentLanguage] != true) {
				overlib("<?php echo $objLang->get("langEnable", "tip") ?>");
				objButton.src = "images/lang_enabled.gif";
			} else {
				overlib("<?php echo $objLang->get("langDisable", "tip") ?>");
				objButton.src = "images/lang_disabled.gif";
			}
			break;
	}
}

ContentLanguage.prototype.buttonOut = function(strButtonType, objButton, fieldId) {
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
			
		case "activeElement":
			if (this.actives[this.currentLanguage] != true) {
				objButton.src = "images/lang_disabled.gif";
			} else {
				objButton.src = "images/lang_enabled.gif";
			}
			nd();
			break;
	}
}

ContentLanguage.prototype.toggleCascadeElement = function() {
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
		$('language_cascade').src = "images/lang_unlocked.gif";
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
		$('language_cascade').src = "images/lang_locked.gif";
	}

	//*** Take action according to the state.
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

ContentLanguage.prototype.toggleCascadeField = function(fieldId) {
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
	$('language_cascade').src = "images/lang_unlocked.gif";

	//*** Take action according to the state.
	this.toggleCascadeState(this.fields[fieldId].id, this.fields[fieldId].cascades[this.currentLanguage]);
	this.toScreen(this.fields[fieldId].id);
}

ContentLanguage.prototype.toggleActiveElement = function() {
	if (this.actives[this.currentLanguage] == true) {
		this.actives[this.currentLanguage] = false;
	} else {
		this.actives[this.currentLanguage] = true;
	}
	
	//*** Reset cascades if default language is disabled.
	if (this.currentLanguage == this.defaultLanguage && this.actives[this.currentLanguage] != true) {
		//*** Reset element cascade.
		this.cascades[this.currentLanguage] = false;
		
		//*** Reset field cascades.
		for (var count in this.fields) {
			this.fields[count].cascades = {};
		}
	}
	
	//*** Set buttons and other stuff.
	this.toggleActivesState();
	this.swap(this.currentLanguage);
}

ContentLanguage.prototype.toggleCascadeState = function(fieldId, state) {	
	//*** Toggle object property.
	this.fields[fieldId].cascades[this.currentLanguage] = state;
	
	//*** Set the cascade input field.
	var strValue = this.fields[fieldId].getCascades();
	$(fieldId + "_cascades").value = strValue;
}

ContentLanguage.prototype.toggleActivesState = function() {		
	//*** Set the actives input field.
	$("language_actives").value = this.getActives();
}

ContentLanguage.prototype.setActives = function(strActives) {
	var arrActives = strActives.split(",");
	
	this.actives = {};
	for (var count = 0; count < arrActives.length; count++) {
		this.actives[arrActives[count]] = true;
	}
	
	this.swap(this.currentLanguage);
}

ContentLanguage.prototype.getActives = function() {
	var strReturn = "";
	var arrTemp = new Array();

	for (var count in this.actives) {
		if (this.actives[count] == true) {
			arrTemp.push(count);
		}
	}

	strReturn = arrTemp.join(",");
	return strReturn;
}

ContentLanguage.prototype.setFieldValue = function(fieldId, strValue) {
	$(fieldId + "_" + this.currentLanguage).value = strValue;
}

/*** 
 * ContentField object.
 */
function ContentField(strId, objParent, strCascades) {
	this.id 				= strId || 0;
	this.parent				= objParent || null;
	this.cascades 			= {};
	
	if (strCascades != undefined) this.setCascades(strCascades);
}
	
ContentField.prototype.getCascades = function() {
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
	
ContentField.prototype.setCascades = function(strCascades) {
	var arrCascades = strCascades.split(",");

	this.cascades = {};
	for (var count = 0; count < arrCascades.length; count++) {
		this.cascades[arrCascades[count]] = true;
	}
	$(this.id + "_cascades").value = this.getCascades();
}

ContentField.prototype.setIconCascade = function() {
	//*** Attach mouse events to the cascade button.
	if (this.parent.currentLanguage == this.parent.defaultLanguage || this.parent.actives[this.parent.currentLanguage] != true || this.parent.actives[this.parent.defaultLanguage] != true) {
		$(this.id + "_cascade").onmouseover = null;
		$(this.id + "_cascade").onmouseout = null;
		$(this.id + "_cascade").onmousedown = null;

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			$(this.id + "_cascade").src = "images/lang_locked_disabled.gif";
		} else {
			$(this.id + "_cascade").src = "images/lang_unlocked_disabled.gif";
		}
	} else {
		var strId = this.id;
		$(this.id + "_cascade").onmouseover = function() { return objContentLanguage.buttonOver('cascadeField', this, strId); };
		$(this.id + "_cascade").onmouseout = function() { return objContentLanguage.buttonOut('cascadeField', this, strId); };
		$(this.id + "_cascade").onmousedown = function() { return objContentLanguage.toggleCascadeField(strId); };

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldUnlock", "tip") ?>");
			$(this.id + "_cascade").src = "images/lang_locked.gif";
		} else {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldCascade", "tip") ?>");
			$(this.id + "_cascade").src = "images/lang_unlocked.gif";
		}
	}
}

ContentField.prototype.toScreen = function() {
	this.setIconCascade();
	$(this.id).value = $(this.id + "_" + this.parent.currentLanguage).value;
	
	return true;
}

ContentField.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).value;
	
	return true;
}

/*** 
 * TextField object.
 */
function TextField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

TextField.prototype = new ContentField();

TextField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id);
		Element.show(this.id + "_alt");
	} else if (this.cascades[this.parent.currentLanguage] == true) {
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

TextField.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).value;
}

/*** 
 * TextAreaField object.
 */
function TextAreaField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

TextAreaField.prototype = new ContentField();

TextAreaField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();
	
	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id + "___Frame");
		Element.show(this.id + "_alt");
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = $(this.id + "_" + this.parent.defaultLanguage).value;
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		Element.hide(this.id + "___Frame");
		Element.show(this.id + "_alt");
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id + "___Frame");
		if (typeof FCKeditorAPI != "undefined") {
			var objArea = FCKeditorAPI.GetInstance(this.id);
			if (typeof objArea == "object") {
				if (objArea.Status == FCK_STATUS_COMPLETE) {
					objArea.SetHTML($(this.id + "_" + this.parent.currentLanguage).value);
				}
			}
		}
	}
}

TextAreaField.prototype.toTemp = function() {
	var strValue = FCKeditorAPI.GetInstance(this.id).GetXHTML();
	if (strValue == "<p>&nbsp;</p>") strValue = "";
	$(this.id + "_" + this.parent.currentLanguage).value = strValue;
}

/*** 
 * FileField object.
 */
function FileField(strId, objParent, strCascades, objOptions) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
	
	//*** Set local properties.
	this.trigger = $(strId);
	this.subFiles = new Object();
	this.maxFiles = 1;
	this.maxChar = 50;
	this.fileCount = 1;
	
	//*** Parse the options.
	for (var intCount in objOptions) {
		this[intCount] = objOptions[intCount];
	}

	//*** Attach event to the file button.
	if (this.trigger.tagName.toUpperCase() == 'INPUT' && this.trigger.type == 'file') {
		//*** What to do when a file is selected.
		this.trigger.onchange = function() {
			objParent.transferField(strId);
		};
	} else {
		//*** This can only be applied to file input elements!
		alert('Error: ' + strId + ' is not a file input element!');
	}
	
	//*** Create containers.
	var langArray = [this.parent.currentLanguage, this.parent.defaultLanguage];
	for (var intCount = 0; intCount < langArray.length; intCount++) {
		if (!this.subFiles[langArray[intCount]]) {
			var intCurrent = ($(this.id + "_" + langArray[intCount] + '_current').value) ? parseInt($(this.id + "_" + langArray[intCount] + '_current').value) : 0;
			this.subFiles[langArray[intCount]] = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

			for (var intCountX = 1; intCountX < intCurrent + 1; intCountX++) {
				this.subFiles[langArray[intCount]].uploaded.push($(this.id + "_" + langArray[intCount] + "_" + intCountX));
				this.fileCount++;
			}
		}
	}
};

FileField.prototype = new ContentField();

FileField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();
		
	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id + "_widget");
		Element.show(this.id + "_alt");
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = "";
		for (var intCount = 0; intCount < this.subFiles[this.parent.defaultLanguage].uploaded.length; intCount++) {
			var arrValue = this.subFiles[this.parent.defaultLanguage].uploaded[intCount].value.split(":");
			strValue += this.shortName(arrValue[0], 40) + "<br />";
		}
		for (var intCount = 0; intCount < this.subFiles[this.parent.defaultLanguage].toUpload.length; intCount++) {
			strValue += this.shortName(this.subFiles[this.parent.defaultLanguage].toUpload[intCount].value, 40) + "<br />";
		}
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		Element.hide(this.id + "_widget");
		Element.show(this.id + "_alt");	
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else {
		//*** The field needs no special treatment.
		Element.show(this.id + "_widget");	
		Element.hide(this.id + "_alt");	
		
		//*** Insert upload rows.
		$$("#" + this.id + "_widget div.required").invoke("show");
		$("filelist_new_" + this.id).hide();
		var objItems = $$("#filelist_new_" + this.id + " div.multifile");
		objItems.each( function(familyMember) {
			Element.remove(familyMember);
		});
		
		//*** Init object if not exists.
		if (!this.subFiles[this.parent.currentLanguage]) {
			var intCurrent = ($(this.id + "_" + this.parent.currentLanguage + '_current').value) ? parseInt($(this.id + "_" + this.parent.currentLanguage + '_current').value) : 0;
			this.subFiles[this.parent.currentLanguage] = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

			for (var intCount = 1; intCount < intCurrent + 1; intCount++) {
				this.subFiles[this.parent.currentLanguage].uploaded.push($(this.id + "_" + this.parent.currentLanguage + "_" + intCount));
				this.fileCount++;
			}
		}
		
		for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
			var filledElement = this.subFiles[this.parent.currentLanguage].toUpload[intCount];
			this.addUploadRow(filledElement);
			$("filelist_new_" + this.id).show();
		}
		
		//*** Insert current rows.
		$("filelist_current_" + this.id).hide();
		var objItems = $$("#filelist_current_" + this.id + " div.multifile");
		objItems.each( function(familyMember) {
			Element.remove(familyMember);
		});
		for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
			var filledElement = this.subFiles[this.parent.currentLanguage].uploaded[intCount];
			this.addCurrentRow(filledElement);
			$("filelist_current_" + this.id).show();
		}
		
		var strId = this.id;
		Sortable.create("filelist_current_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
	}
}

FileField.prototype.transferField = function() {
	$("filelist_new_" + this.id).show();

	//*** Set the id and name of the filled file field.
	var filledElement = $(this.id);
	var objParent = this.parent;
	var strId = this.id;
	
	this.subFiles[this.parent.currentLanguage].toUpload.push(filledElement);
	
	filledElement.id = this.id + "_" + this.parent.currentLanguage + "_" + this.fileCount++;
	filledElement.name = this.id + "_" + this.parent.currentLanguage + "_new[]";
	
	//*** Create empty replacement.
	var objElement = document.createElement('input');
	objElement.type = 'file';
	objElement.className = 'input-file';
	objElement.id = this.id;
	objElement.name = this.id + "_new";
	
	objElement.onchange = function() {
		objParent.transferField(strId);
	};

	filledElement.parentNode.insertBefore(objElement, filledElement.nextSibling);

	//*** Add row to the upload list.
	this.addUploadRow(filledElement);
	
	//*** Appease Safari: display:none doesn't seem to work correctly in Safari.
	filledElement.style.position = 'absolute';
	filledElement.style.left = '-1000px';
}

FileField.prototype.addUploadRow = function(element) {
	var objParent = this.parent;
	var strId = this.id;
	
	var objRow = document.createElement('div');
	objRow.id = 'file_' + element.id;
	objRow.className = 'multifile';
	objRow.element = element;

	var objButton = document.createElement('a');
	objButton.className = 'button';
	objButton.innerHTML = this.removeLabel;
	objButton.href = '';

	//*** Delete function.
	objButton.onclick = function() {
		objParent.removeUploadField(strId, this);
		return false;
	};
		
	objRow.appendChild(objButton);
	
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = this.shortName(element.value, this.maxChar);
	objRow.appendChild(objRowValue);

	$("filelist_new_" + this.id).appendChild(objRow);
	
	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		$$("#" + this.id + "_widget div.required").invoke("hide");
	}
		
	Sortable.create("filelist_new_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
}

FileField.prototype.addCurrentRow = function(element) {
	var objParent = this.parent;
	var strId = this.id;
	
	var objRow = document.createElement('div');
	objRow.id = 'file_' + element.id;
	objRow.className = 'multifile';
	objRow.style.position = 'relative';
	objRow.element = element;

	var objButton = document.createElement('a');
	objButton.className = 'button';
	objButton.innerHTML = this.removeLabel;
	objButton.href = '';

	//*** Delete function.
	objButton.onclick = function() {
		objParent.removeCurrentField(strId, this);
		return false;
	};
	
	objRow.appendChild(objButton);
	
	var arrValue = element.value.split(":");
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = arrValue[0];
	objRow.appendChild(objRowValue);

	$("filelist_current_" + this.id).appendChild(objRow);
	
	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		$$("#" + this.id + "_widget div.required").invoke("hide");
	}
}

FileField.prototype.removeUploadField = function(objTrigger) {	
	objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);
	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].toUpload[intCount].value != objTrigger.parentNode.element.value) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].toUpload[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].toUpload = arrTemp;
	
	$$("#" + this.id + "_widget div.required").invoke("show");
	if (this.subFiles[this.parent.currentLanguage].toUpload.length == 0) {
		$("filelist_new_" + this.id).hide();
	}
}

FileField.prototype.removeCurrentField = function(objTrigger) {	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].uploaded[intCount].value != objTrigger.parentNode.element.value) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].uploaded[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].uploaded = arrTemp;
	this.subFiles[this.parent.currentLanguage].currentFiles--;
	
	objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);
	
	if (this.subFiles[this.parent.currentLanguage].uploaded.length == 0) {
		$("filelist_current_" + this.id).hide();
	}
	$$("#" + this.id + "_widget div.required").invoke("show");
}

FileField.prototype.shortName = function(strInput, maxLength) {
	if (strInput.length > maxLength) {
		//*** Get filename.
		var pathDelimiter = (strInput.search(/\\/gi) > -1) ? "\\" : "/";
		var arrPath = strInput.split(pathDelimiter);
		var strFile = arrPath.pop();

		//*** Calculate remaining length.
		var reminingLength = (maxLength - strFile.length > 0) ? maxLength - strFile.length : 3;

		var strPath = arrPath.join(pathDelimiter);
		strInput = strPath.substr(0, reminingLength) + "..." + pathDelimiter + strFile;
	}
	
	return strInput;
}

FileField.prototype.toTemp = function() {};

FileField.prototype.sort = function() {
	var arrFields = Sortable.serialize('filelist_current_' + this.id).split("&");
	var objParent = $(this.id + '_widget');
	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		var strTemp = arrFields[intCount].replace('filelist_current_' + this.id + "[]=", "");
		var objTemp = $(this.id + "_" + this.parent.currentLanguage + "_" + strTemp);
		if (objTemp) {
			Element.remove(objTemp);
			objParent.appendChild(objTemp);
		}
	}
}

/*** 
 * DateField object.
 */
function DateField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

DateField.prototype = new ContentField();

DateField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id + "_canvas");
		Element.hide("calendarButton_" + this.id);
		Element.show(this.id + "_alt");
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = $(this.id + "_" + this.parent.defaultLanguage).value;
		var objDate = Date.parseDate(strValue, "%d %B %Y %k:%M:%S");
		
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : objDate.print($(this.id + "_format").value);
		Element.hide(this.id + "_canvas");
		Element.hide("calendarButton_" + this.id);
		Element.show(this.id + "_alt");	
	} else {
		//*** The field needs no special treatment.
		var strValue = $(this.id + "_" + this.parent.currentLanguage).value;
		var objDate = Date.parseDate(strValue, "%d %B %Y %k:%M:%S");
		
		$(this.id + "_canvas").innerHTML = (strValue == "") ? "&nbsp;" : objDate.print($(this.id + "_format").value);
		$(this.id).value = strValue;
		Element.hide(this.id + "_alt");
		Element.show(this.id + "_canvas");
		Element.show("calendarButton_" + this.id);
	}
}

DateField.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).value;
}

/*** 
 * CheckBox object.
 */
function CheckBox(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

CheckBox.prototype = new ContentField();

CheckBox.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id);
		$(this.id + "_alt").show();
		$(this.id + "_alt").style.display = "inline";
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = ($(this.id + "_" + this.parent.defaultLanguage).value == "1" || $(this.id + "_" + this.parent.defaultLanguage).value == "true") ? "true" : "false";
		$(this.id + "_alt").innerHTML = strValue;
		Element.hide(this.id);
		$(this.id + "_alt").show();
		$(this.id + "_alt").style.display = "inline";
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id);
		if ($(this.id + "_" + this.parent.currentLanguage).value == "1" || $(this.id + "_" + this.parent.currentLanguage).value == "true") {
			$(this.id).checked = true;
		} else {
			$(this.id).checked = false;
		}
	}
}

CheckBox.prototype.toTemp = function() {
	$(this.id + "_" + this.parent.currentLanguage).value = $(this.id).checked;
}

/*** 
 * SelectListField object.
 */
function SelectListField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

SelectListField.prototype = new ContentField();

SelectListField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id);
		Element.show(this.id + "_alt");
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var arrValue = $(this.id + "_" + this.parent.defaultLanguage).value.split(",");
		var strValue = "";
		for (var intCount = 0; intCount < $(this.id).options.length; intCount++) {
			if (arrValue.inArray($(this.id).options[intCount].value)) {
				strValue += $(this.id).options[intCount].innerHTML + "<br />";
			}
		}
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		Element.hide(this.id);
		Element.show(this.id + "_alt");	
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id);
		var arrDefault = $(this.id + "_" + this.parent.currentLanguage).value.split(",");
		for (var intCount = 0; intCount < $(this.id).options.length; intCount++) {
			if (arrDefault.inArray($(this.id).options[intCount].value)) {
				$(this.id).options[intCount].selected = true;
			} else {
				$(this.id).options[intCount].selected = false;
			}
		}
	}
}

SelectListField.prototype.toTemp = function() {
	var arrValue = [];
	for (var intCount = 0; intCount < $(this.id).options.length; intCount++) {
		if ($(this.id).options[intCount].selected) {
			arrValue.push($(this.id).options[intCount].value);
		}
	}
	$(this.id + "_" + this.parent.currentLanguage).value = arrValue.join(",");
}

/*** 
 * CheckListField object.
 */
function CheckListField(strId, objParent, strCascades) {
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
};

CheckListField.prototype = new ContentField();

CheckListField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();

	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		$(this.id + "_alt").innerHTML = "<?php echo $objLang->get("langDisabled", "label") ?>";
		Element.hide(this.id + "_widget");
		Element.show(this.id + "_alt");
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var arrValue = $(this.id + "_" + this.parent.defaultLanguage).value.split(",");
		var strValue = "";
		var arrFields = $(this.id + "_widget").getElementsByTagName("input");
		for (var intCount = 0; intCount < arrFields.length; intCount++) {
			if (arrFields[intCount].name == this.id + "[]" && arrValue.inArray(arrFields[intCount].value)) {
				var arrField = $(arrFields[intCount]);
				strValue += arrField.up().lastChild.nodeValue + "<br />";
			}
		}
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		Element.hide(this.id + "_widget");
		Element.show(this.id + "_alt");	
		$(this.id + "_alt").setStyle({'display':'inline'});
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id + "_widget");
		var arrDefault = $(this.id + "_" + this.parent.currentLanguage).value.split(",");
		var arrFields = $(this.id + "_widget").getElementsByTagName("input");
		for (var intCount = 0; intCount < arrFields.length; intCount++) {
			if (arrFields[intCount].name == this.id + "[]") {
				if (arrDefault.inArray(arrFields[intCount].value)) {
					arrFields[intCount].checked = true;
				} else {
					arrFields[intCount].checked = false;
				}
			}
		}
	}
}

CheckListField.prototype.toTemp = function() {
	var arrValue = [];
	var arrFields = $(this.id + "_widget").getElementsByTagName("input");
	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		if (arrFields[intCount].name == this.id + "[]" && arrFields[intCount].checked == true) {
			arrValue.push(arrFields[intCount].value);
		}
	}
	$(this.id + "_" + this.parent.currentLanguage).value = arrValue.join(",");
}

/*** 
 * Utility functions.
 */
Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};