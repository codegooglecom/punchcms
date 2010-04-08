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

	//*** TODO: jQuery method.
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
 	jQuery("#language_active").bind("mouseover", function() { return objContentLanguage.buttonOver('activeElement', this); });
 	jQuery("#language_active").bind("mouseout", function() { return objContentLanguage.buttonOut('activeElement', this); });
 	jQuery("#language_active").bind("mousedown", function() { return objContentLanguage.toggleActiveElement(); });
}

ContentLanguage.prototype.swap = function(languageId) {
	this.toTemp();
	this.currentLanguage = languageId;
	
	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage || this.actives[this.currentLanguage] != true || this.actives[this.defaultLanguage] != true) {
		jQuery("#language_cascade").attr("src", "images/lang_unlocked_disabled.gif");
 		jQuery("#language_cascade").unbind("onmouseover");
 		jQuery("#language_cascade").unbind("onmouseout");
 		jQuery("#language_cascade").unbind("onmousedown");
	} else {
		if (this.cascades[this.currentLanguage] !== true) {
			jQuery("#language_cascade").attr("src", "images/lang_unlocked.gif");
		} else {
			jQuery("#language_cascade").attr("src", "images/lang_locked.gif");
		}
 		jQuery("#language_cascade").bind("mouseover", function() { return objContentLanguage.buttonOver("cascadeElement", this); });
 		jQuery("#language_cascade").bind("mouseout", function() { return objContentLanguage.buttonOut("cascadeElement", this); });
 		jQuery("#language_cascade").bind("mousedown", function() { return objContentLanguage.toggleCascadeElement(); });
	}
	
	//*** Check the active state.
	if (this.actives[this.currentLanguage] != true) {
		//*** TODO: Replace the overlib library!
		if (this.hover) overlib("<?php echo $objLang->get("langEnable", "tip") ?>");
		jQuery("#language_active").attr("src", "images/lang_disabled.gif");
	} else {
		//*** TODO: Replace the overlib library!
		if (this.hover) overlib("<?php echo $objLang->get("langDisable", "tip") ?>");
		jQuery("#language_active").attr("src", "images/lang_enabled.gif");	
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
		jQuery("#language_cascade").attr("src", "images/lang_unlocked.gif");
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
		jQuery("#language_cascade").attr("src", "images/lang_locked.gif");
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
	jQuery("#language_cascade").attr("src", "images/lang_unlocked.gif");

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
	jQuery("#" + fieldId + "_cascades").val(strValue);
}

ContentLanguage.prototype.toggleActivesState = function() {		
	//*** Set the actives input field.
	jQuery("#language_actives").val(this.getActives());
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
	jQuery("#" + fieldId + "_" + this.currentLanguage).val(strValue);
}

ContentLanguage.prototype.loadStoragePage = function(objTrigger) {
	var strUrl = "ajax.php";
	var strPost = "cmd=StorageItems::getFileListHTML&parentId=" + jQuery('#frm_storage_' + objTrigger.id).find('options:selected').val().split("_").pop();
	
	var objFiles = jQuery("#storageBrowser_" + objTrigger.id + "ul");
	if (objFiles.length > 0) objFiles.find(":first").remove();
	
	var objList = jQuery("#storageBrowser_" + objTrigger.id + "div.storageList:first").get(0);
	var objLoader = document.createElement("div");
	objLoader.className = "storageLoader";
	objLoader.innerHTML = "<?php echo $objLang->get("loadingFiles", "form") ?>";
	objLoader.style.display = "block";
	objList.appendChild(objLoader);
	
	//*** TODO: jQuery ajax request.
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: function(objXHR){objTrigger.parent.showStoragePage(objXHR, objTrigger);}
			});
}

ContentLanguage.prototype.showStoragePage = function(objXHR, objTrigger) {
	var objResponse = objXHR.responseXML;
	var objField = objResponse.getElementsByTagName("field")[0];
	
	var objLoader = jQuery("#storageBrowser_" + objTrigger.id + "div.storageLoader").get(0);
	objLoader.style.display = "none";
	
	var objList = jQuery("#storageBrowser_" + objTrigger.id + "div.storageList").get(0);
	objList.innerHTML = objField.firstChild.nodeValue;
	
	//*** Attach events to the thumbs.
	var objThumbs = jQuery("#storageBrowser_" + objTrigger.id + "li");
	for (var intCount = 0; intCount < objThumbs.length; intCount++) {
		var objLink = objThumbs.eq(intCount).find("a:first");
		objLink
		.bind("mouseover", function(){
			var objLabel = jQuery(this).parent("li").find("span:first");
			//*** TODO: Replace overlib library
			return overlib(objLabel.html());
		})
		.bind("mouseout", function(){
			return nd();
		})
		.bind("click", function(){
			var objLabel = jQuery(this).parent("li").find("span:first");
			objTrigger.transferStorage(this, objLabel.html());
			return false;
		});
	}
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
	jQuery("#" + this.id + "_cascades").val(this.getCascades());
}

ContentField.prototype.setIconCascade = function() {
	//*** Attach mouse events to the cascade button.
	if (this.parent.currentLanguage == this.parent.defaultLanguage || this.parent.actives[this.parent.currentLanguage] != true || this.parent.actives[this.parent.defaultLanguage] != true) {
		jQuery("#" + this.id + "_cascade").unbind("mouseover");
		jQuery("#" + this.id + "_cascade").unbind("mouseout");
		jQuery("#" + this.id + "_cascade").unbind("mousedown");

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			jQuery("#" + this.id + "_cascade").attr("src", "images/lang_locked_disabled.gif");
		} else {
			jQuery("#" + this.id + "_cascade").attr("src", "images/lang_unlocked_disabled.gif");
		}
	} else {
		var strId = this.id;
		jQuery("#" + this.id + "_cascade").bind("mouseover", function() { return objContentLanguage.buttonOver('cascadeField', this, strId); });
		jQuery("#" + this.id + "_cascade").bind("mouseout", function() { return objContentLanguage.buttonOut('cascadeField', this, strId); });
		jQuery("#" + this.id + "_cascade").bind("mousedown", function() { return objContentLanguage.toggleCascadeField(strId); });

		//*** Set the cascade icon.
		if (this.cascades[this.parent.currentLanguage] == true) {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldUnlock", "tip") ?>");
			jQuery("#" + this.id + "_cascade").attr("src", "images/lang_locked.gif");
		} else {
			if (this.parent.hover && this.parent.buttonType == "cascadeField") overlib("<?php echo $objLang->get("langFieldCascade", "tip") ?>");
			jQuery("#" + this.id + "_cascade").attr("src", "images/lang_unlocked.gif");
		}
	}
}

ContentField.prototype.toScreen = function() {
	this.setIconCascade();
	jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
	
	return true;
}

ContentField.prototype.toTemp = function() {
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(jQuery("#" + this.id).val());
	
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val();
		jQuery("#" + this.id + "_alt").get(0).innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id).show();
		jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
	}
}

TextField.prototype.toTemp = function() {
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(jQuery("#" + this.id).val());
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id + "___Frame").hide();
		//*** Replaced .show() and .setStyle({'display':'inline'}) for one line of jQuery
		jQuery("#" + this.id + "_alt").css("display","inline");
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val();
		jQuery("#" + this.id + "_alt").get(0).innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		jQuery("#" + this.id + "___Frame").hide();
		//*** Replaced .show() and .setStyle({'display':'inline'}) for one line of jQuery
		jQuery("#" + this.id + "_alt").css("display","inline");
	} else {
		//*** The field needs no special treatment.
		Element.hide(this.id + "_alt");
		Element.show(this.id + "___Frame");
		if (typeof FCKeditorAPI != "undefined") {
			var objArea = FCKeditorAPI.GetInstance(this.id);
			if (typeof objArea == "object") {
				if (objArea.Status == FCK_STATUS_COMPLETE) {
					objArea.SetHTML(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
				}
			}
		}
	}
}

TextAreaField.prototype.toTemp = function() {
	var strValue = FCKeditorAPI.GetInstance(this.id).GetXHTML();
	if (strValue == "<p>&nbsp;</p>") strValue = "";
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(strValue);
}

/*** 
 * FileField object.
 */
function FileField(strId, objParent, strCascades, objOptions) {
	var __this = this;
	this.base = ContentField;
	this.base(strId, objParent, strCascades);
	
	//*** Set local properties.
	this.trigger = jQuery("#" + strId).get(0); // Backwards compatibility, this has to be a DOM element.
	this.subFiles = new Object();
	this.maxFiles = 1;
	this.maxChar = 50;
	this.fileCount = 1;
	this.thumbPath = "";
	this.uploadPath = "";
	this.selectType = [];
	this.fileType = "*.*";
	this.swfUpload = null;
	
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
	
	//*** Attach event to the library button.
	jQuery("#browseStorage_" + strId).bind("click", function(){
		__this.openStorageBrowser();
		return false;
	});
	
	//*** Attach event to the library folder select.
	jQuery("#frm_storage_" + strId).bind("change", function(){
		__this.parent.loadStoragePage(__this);
		return false;
	});
	
	//*** Create containers.
	var langArray = [this.parent.currentLanguage, this.parent.defaultLanguage];
	for (var intCount = 0; intCount < langArray.length; intCount++) {
		if (!this.subFiles[langArray[intCount]]) {
			var intCurrent = (jQuery("#" + this.id + "_" + langArray[intCount] + "_current").val()) ? parseInt(jQuery("#" + this.id + "_" + langArray[intCount] + "_current").val()) : 0;
			this.subFiles[langArray[intCount]] = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

			for (var intCountX = 1; intCountX < intCurrent + 1; intCountX++) {
				this.subFiles[langArray[intCount]].uploaded.push(jQuery("#" + this.id + "_" + langArray[intCount] + "_" + intCountX).get(0));
				this.fileCount++;
			}
		}
	}
	
	//*** Initiate SWFUpload code.
	var settings = {
		jsParent : __this,
		flash_url : "/libraries/swfupload.swf",
		upload_url: "/upload.php",
		post_params: {
			"PHPSESSID" : "<?php echo session_id(); ?>",
			"fileId" : __this.id
		},
		file_size_limit : "100 MB",
		file_types : __this.fileType,
		file_types_description : "Files",
		file_upload_limit : __this.maxFiles - ((this.subFiles[this.parent.currentLanguage].toUpload.length) + this.subFiles[this.parent.currentLanguage].currentFiles),
		file_queue_limit : __this.maxFiles - ((this.subFiles[this.parent.currentLanguage].toUpload.length) + this.subFiles[this.parent.currentLanguage].currentFiles),
		custom_settings : {
			progressTarget : __this.id + "_uploadProgress",
			cancelButtonId : __this.id + "_cancel"
		},
		debug: false,

		// Button Settings
		button_image_url : "/images/XPButtonUploadText_61x22.png",
		button_placeholder_id : __this.id + "_browse",
		button_width: 61,
		button_height: 22,

		// The event handler functions are defined in handlers.js
		swfupload_loaded_handler : __this.swfUploadLoaded,
		file_queued_handler : __this.fileQueued,
		file_queue_error_handler : __this.fileQueueError,
		file_dialog_complete_handler : __this.fileDialogComplete,
		upload_start_handler : __this.uploadStart,
		upload_progress_handler : __this.uploadProgress,
		upload_error_handler : __this.uploadError,
		upload_success_handler : __this.uploadSuccess,
		upload_complete_handler : __this.uploadComplete,
		queue_complete_handler : __this.queueComplete,	// Queue plugin event
		
		// SWFObject settings
		minimum_flash_version : "9.0.28",
		swfupload_pre_load_handler : __this.swfUploadPreLoad,
		swfupload_load_failed_handler : __this.swfUploadLoadFailed
	};

	this.swfUpload = new SWFUpload(settings);
};

FileField.prototype = new ContentField();

FileField.prototype.toScreen = function() {
	//*** Attach mouse events to the cascade button.
	this.setIconCascade();
		
	//*** Insert value into the field.
	if (this.parent.actives[this.parent.currentLanguage] != true) {
		//*** The element is not active.
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id + "_widget").hide();
		jQuery("#" + this.id + "_alt").css("display","inline");
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
		jQuery("#" + this.id + "_alt").get(0).innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		jQuery("#" + this.id + "_widget").hide();
		jQuery("#" + this.id + "_alt").css("display","inline");
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_widget").show();	
		jQuery("#" + this.id + "_alt").hide();
		
		//*** Insert upload rows.
		jQuery("#" + this.id + "_widget div.required").show();
		jQuery("#filelist_new_" + this.id).hide();
		var objItems = jQuery("#filelist_new_" + this.id + " div.multifile");
		objItems.each(function() {
			jQuery(this).remove();
		});
		
		//*** Init object if not exists.
		if (!this.subFiles[this.parent.currentLanguage]) {
			var intCurrent = (jQuery("#" + this.id + "_" + this.parent.currentLanguage + "_current").val()) ? parseInt(jQuery("#" + this.id + "_" + this.parent.currentLanguage + "_current").val()) : 0;
			this.subFiles[this.parent.currentLanguage] = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

			for (var intCount = 1; intCount < intCurrent + 1; intCount++) {
				this.subFiles[this.parent.currentLanguage].uploaded.push(jQuery("#" + this.id + "_" + this.parent.currentLanguage + "_" + intCount).get(0));
				this.fileCount++;
			}
		}
		
		for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
			var filledElement = this.subFiles[this.parent.currentLanguage].toUpload[intCount];
			if (this.swfUpload.movieCount > 0) {
				this.addSwfUploadRow(filledElement);
			} else {
				this.addUploadRow(filledElement);
			}
			jQuery("#filelist_new_" + this.id).show();
		}
		
		//*** Insert current rows.
		jQuery("#filelist_current_" + this.id).hide();
		var objItems = jQuery("#filelist_current_" + this.id + " div.multifile");
		objItems.each(function() {
			jQuery(this).remove();
		});
		for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
			var filledElement = this.subFiles[this.parent.currentLanguage].uploaded[intCount];
			var blnStorage = (filledElement.value.split(":").length > 2) ? true : false;
			this.addCurrentRow(filledElement, blnStorage);
			jQuery("#filelist_current_" + this.id).show();
		}
		
		//*** SWFUpload fields.
		try {
			this.swfUpload.setFileUploadLimit(this.maxFiles - ((this.subFiles[this.parent.currentLanguage].toUpload.length) + this.subFiles[this.parent.currentLanguage].currentFiles));
			this.swfUpload.setFileQueueLimit(this.maxFiles - ((this.subFiles[this.parent.currentLanguage].toUpload.length) + this.subFiles[this.parent.currentLanguage].currentFiles));
			var objStats = this.swfUpload.getStats();
			objStats.successful_uploads = this.subFiles[this.parent.currentLanguage].toUpload.length;
			this.swfUpload.setStats(objStats);
		} catch (e) {
			//*** Nothing.	
		}
		
		var strId = this.id;
		Sortable.create("filelist_current_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
	}
}

FileField.prototype.openStorageBrowser = function() {
	var __this = this;

	//*** Slide open.
	alert("TEST");
	jQuery("#storageBrowser_" + this.id).fadeIn("slow");
	
	//*** Fill file list.
	this.parent.loadStoragePage(this);
	
	//*** Switch button.
	var storageButton = jQuery("#browseStorage_" + this.id);
	var labelCache = storageButton.html();
	
	//*** TODO: Get this string from the language library
	storageButton
		.html("Sluit Media Bibliotheek")
		.bind("click", function(){
			Effect.SlideUp('storageBrowser_' + __this.id);
			storageButton.innerHTML = labelCache;
			
			storageButton.onclick = function(){
				__this.openStorageBrowser();
				return false;
			}
			
			return false;
		});
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
		jQuery("#" + this.id + "_widget div.required").hide();
		jQuery("#storageBrowser_" + this.id).hide();
	}
		
	Sortable.create("filelist_new_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
}

FileField.prototype.addCurrentRow = function(element, blnStorage) {
	var objParent = this.parent;
	var strId = this.id;
	
	var objRow = document.createElement('div');
	objRow.id = 'file_' + element.id;
	objRow.className = (blnStorage) ? 'multifile storage' : 'multifile';
	objRow.style.position = 'relative';
	objRow.element = element;

	//*** Delete button.
	var objButton = document.createElement('a');
	objButton.className = 'button';
	objButton.innerHTML = this.removeLabel;
	objButton.href = '';
	objButton.onclick = function() {
		objParent.removeCurrentField(strId, this);
		return false;
	};
	objRow.appendChild(objButton);
	
	var arrValue = element.value.split(":");
	var labelValue = arrValue.shift();
	var fileValue = arrValue.shift();
	var libraryValue = arrValue.shift();
	var alttextValue = arrValue.shift();
	
	//*** Image thumbnail.
	if (this.thumbPath != "") {
		var __this = this;
		if (this.isImage(fileValue)) {
			var objThumb = document.createElement('a');
			objThumb.className = 'thumbnail';
			objThumb.innerHTML = '<img src="thumb.php?src=' + this.thumbPath + fileValue + '" alt="" />';
			objThumb.href = '';
			objThumb.onmouseover = function() {
				return overlib('<img src="' + __this.thumbPath + fileValue + '" alt="" />', FULLHTML);
			};
			objThumb.onmouseout = function() {
				return nd();
			};
		} else {
			var objThumb = document.createElement('a');
			objThumb.className = 'document';
			objThumb.innerHTML = '<img src="/images/ico_document.gif" alt="" />';
			objThumb.href = '';
			objThumb.onclick = function(){
				window.open(__this.thumbPath + fileValue);
				return false;
			};
			objThumb.onmouseover = function() {
				return overlib('This file will open in a new window.');
			};
			objThumb.onmouseout = function() {
				return nd();
			};
		}
		objRow.appendChild(objThumb);
	}
	
	//*** Label.
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = labelValue;
	objRow.appendChild(objRowValue);
	
	//*** Description.
	var objAltText = document.createElement('p');
	objAltText = Element.extend(objAltText);
	objAltText.className = 'alt-text';
	objAltText.innerHTML = (alttextValue == "" || alttextValue == undefined) ? this.altLabel : alttextValue;
	objAltText.observe("click", function(event) {
		__this.startAltEdit(event);
	});
	objRow.appendChild(objAltText);

	$("filelist_current_" + this.id).appendChild(objRow);
	
	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		jQuery("#" + this.id + "_widget div.required").hide();
		jQuery("#storageBrowser_" + this.id).hide();
	}
}

FileField.prototype.addSwfUploadRow = function(element, file) {	
	var __this = this;
	
	var objRow = document.createElement('div');
	objRow = Element.extend(objRow);
	objRow.id = 'file_' + element.id;
	
	if (file !== undefined) {
		objRow.className = 'multifile storage ' + file.id;
	} else {
		objRow.className = 'multifile storage ' + element.retrieve("file").id;
	}
	
	objRow.style.position = 'relative';
	objRow.element = element;
	
	if (file !== undefined) {
		objRow.observe("mouseover", function() {
			$(objRow.id).select("a img")[0].src = "/images/ico_loading_mo.gif";
		})
		.observe("mouseout", function() {
				$(objRow.id).select("a img")[0].src = "/images/ico_loading.gif";
		});
	}

	//*** Delete button.
	var objButton = document.createElement('a');
	objButton = Element.extend(objButton);
	objButton.className = 'button';
	
	if (file !== undefined) {
		objButton.innerHTML = this.cancelLabel;
		objButton.observe("click", function(event) {
			__this.cancelCurrentSwfUpload(element.id, file);
			event.stop();
			return false;
		});
	} else {
		objButton.innerHTML = this.removeLabel;
		objButton.observe("click", function(event) {
			__this.cancelCurrentSwfUpload(element.id, element.retrieve("file"));
			event.stop();
			return false;
		});
	}
	objButton.href = '';
	objRow.appendChild(objButton);
	
	var arrValue = element.value.split(":");
	var labelValue = arrValue.shift();
	var fileValue = arrValue.shift();
	var libraryValue = arrValue.shift();
	var alttextValue = arrValue.shift();
		
	//*** Image thumbnail.
	var objThumb = document.createElement('a');
	objThumb = Element.extend(objThumb);
	objThumb.href = '';
	if (file !== undefined) {
		objThumb.className = 'document';
		objThumb.innerHTML = '<img src="/images/ico_loading.gif" alt="" />';
		objThumb.observe("mouseover", function() {
			return overlib('This file is being uploaded.');
		})
		.observe("mouseout", function() {
			return nd();
		});
	} else {
		var tempFile = element.retrieve("file");
		if (__this.thumbPath != "") {
			if (__this.isImage(tempFile.name)) {
				objThumb.className = 'thumbnail';
				objThumb.innerHTML = "<img src=\"thumb.php?src=" + __this.uploadPath + tempFile.name + "\" alt=\"\" />";
				objThumb.observe("mouseover", function() {
					return overlib("<img src=\"" + __this.uploadPath + tempFile.name + "\" alt=\"\" />", FULLHTML);
				})
				.observe("mouseout", function() {
					return nd();
				});
			} else {
				objThumb.className = 'document';
				objThumb.innerHTML = '<img src="/images/ico_document.gif" alt="" />';
				objThumb.observe("click", function(event) {
					window.open(__this.thumbPath + "upload/" + tempFile.name);
					event.stop();
					return false;
				})
				.observe("mouseover", function() {
					return overlib("This file will open in a new window.");
				})
				.observe("mouseout", function() {
					return nd();
				});
			}
		}	
	}
	objRow.appendChild(objThumb);
	
	//*** Label.
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = labelValue;
	objRow.appendChild(objRowValue);
	
	if (file !== undefined) {
		//*** Progress.
		var objProgressBar = document.createElement('div');
		objProgressBar.className = 'progressBar';
		
		var objProgressWrapper = document.createElement('div');
		objProgressWrapper.className = 'progressWrapper';
		objProgressWrapper.appendChild(objProgressBar);
		objRow.appendChild(objProgressWrapper);
	} else {
		//*** Description.
		var objAltText = document.createElement('p');
		objAltText = Element.extend(objAltText);
		objAltText.className = 'alt-text';
		objAltText.innerHTML = this.altLabel;
		objAltText.observe("click", function(event) {
			__this.startAltEdit(event);
		});
		objRow.appendChild(objAltText);
	}

	$("filelist_new_" + this.id).appendChild(objRow);
	
	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		jQuery("#storageBrowser_" + this.id).hide();
	}
	
	var strId = this.id;
	Sortable.create("filelist_new_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
}

FileField.prototype.removeSwfUploadRow = function(inputId, file) {
	$(inputId).remove();
	$("file_" + inputId).remove();
	
	//*** Remove remotely.
	new Ajax.Request("upload.php", {
		method: 'post',
		parameters: {
			'do': 'remove', 
			file: file.name,
			PHPSESSID: "<?php echo session_id(); ?>"
		}
	});
	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].toUpload[intCount].value != file.name) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].toUpload[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].toUpload = arrTemp;
	
	jQuery("#" + this.id + "_widget div.required").show();
	if (this.subFiles[this.parent.currentLanguage].toUpload.length == 0) {
		$("filelist_new_" + this.id).hide();
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
	
	jQuery("#" + this.id + "_widget div.required").show();
	if (this.subFiles[this.parent.currentLanguage].toUpload.length == 0) {
		jQuery("#filelist_new_" + this.id).hide();
	}
}

FileField.prototype.removeCurrentField = function(objTrigger) {	
	jQuery("#" + this.id + "_widget div.required").show();
	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].uploaded[intCount].value != objTrigger.parentNode.element.value) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].uploaded[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].uploaded = arrTemp;
	this.subFiles[this.parent.currentLanguage].currentFiles--;
	
	//*** TODO: convert javascript (parentNode etc) to jQuery
	objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);
	
	if (this.subFiles[this.parent.currentLanguage].uploaded.length == 0) {
		jQuery("#filelist_current_" + this.id).hide();
	}
	
	this.toScreen();
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

FileField.prototype.transferStorage = function(objLink, strLabel) {
	Effect.SwitchOff(objLink);
	setTimeout(function(){Effect.Appear(objLink)}, 1200);

	//*** Create input element.
	var objElement = document.createElement('input');
	objElement.type = 'hidden';
	objElement.id = this.id + "_" + this.parent.currentLanguage + "_" + this.fileCount++;
	objElement.name = this.id + "_" + this.parent.currentLanguage + "[]";
	objElement.value = strLabel + ":" + $(objLink).getElementsBySelector("img")[0].alt.split("/").pop() + ":" + $(objLink).id.split("_").pop();
	$("filelist_new_" + this.id).appendChild(objElement);
	
	this.subFiles[this.parent.currentLanguage].currentFiles++;
	this.subFiles[this.parent.currentLanguage].uploaded.push(objElement);
	
	$("filelist_current_" + this.id).show();
	this.addCurrentRow(objElement, true);
}

FileField.prototype.isImage = function(fileName) {
	var blnReturn = false;
	var extension = fileName.toLowerCase().split(".").pop();
	var arrImages = ['jpg', 'jpeg', 'gif', 'png'];
	for (var count = 0; count < arrImages.length; count++) {
		if (arrImages[count] == extension) {
			blnReturn = true;
			break;
		}
	}
	
	return blnReturn;
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

FileField.prototype.swfUploadPreLoad = function() {
	//alert("swfUploadPreLoad");
}

FileField.prototype.swfUploadLoaded = function() {
	jQuery("#" + this.settings.jsParent.id).hide();
}

FileField.prototype.swfUploadLoadFailed = function() {
	//*** TODO: Modal jQueryUI feedback box? 
	//alert("swfUploadLoadFailed");
}

FileField.prototype.fileQueued = function(file) {
	//alert("FileField.prototype.fileQueued: " + file.name);
}

FileField.prototype.fileQueueError = function(file, errorCode, message) {
	try {
		if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
			alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may select " + (message > 1 ? "up to " + message + " files." : "one file.")));
			return;
		}

		switch (errorCode) {
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				alert("File " + file.name + " is too big.");
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				alert("File " + file.name + " is a zero size file.");
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				alert("File " + file.name + " is an invalid file type.");
				break;
			default:
				alert("Upload encountered a problem.");
				break;
		}
	} catch (ex) {
		alert("Upload encountered a problem.");
    }
}

FileField.prototype.fileDialogComplete = function(numFilesSelected, numFilesQueued) {
	try {
		if (numFilesSelected > 0) {
			//document.getElementById("btnCancel").style.display = "inline";
		}
		this.startUpload();
	} catch (ex)  {
        //this.debug(ex);
	}
}

FileField.prototype.uploadStart = function(file) {
	$("filelist_new_" + this.settings.jsParent.id).show();
	
	//*** Create input element.
	var objElement = document.createElement('input');
	objElement = Element.extend(objElement);
	objElement.type = 'hidden';
	objElement.id = this.settings.jsParent.id + "_" + this.settings.jsParent.parent.currentLanguage + "_" + this.settings.jsParent.fileCount++;
	objElement.name = this.settings.jsParent.id + "_" + this.settings.jsParent.parent.currentLanguage + "[]";
	objElement.value = file.name + ":::";
	objElement.store("file", file);
	$("filelist_new_" + this.settings.jsParent.id).appendChild(objElement);
		
	this.settings.jsParent.subFiles[this.settings.jsParent.parent.currentLanguage].toUpload.push(objElement);
	
	this.settings.jsParent.addSwfUploadRow(objElement, file);
}

FileField.prototype.uploadProgress = function(file, bytesLoaded, bytesTotal) {
	var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
	jQuery("div." + file.id + " div.progressBar")[0].setStyle({width:percent + "%"});
}

FileField.prototype.uploadSuccess = function(file, serverData) {
	var __this = this.settings.jsParent;
	jQuery("div." + file.id + " div.progressWrapper:first").remove();
	jQuery("div." + file.id + ":first").stopObserving("mouseover").stopObserving("mouseout");
	jQuery("div." + file.id + " a.button:first").html(__this.removeLabel);

	if (__this.thumbPath != "") {
		if (__this.isImage(file.name)) {
			jQuery("div." + file.id + " a img:first").attr("src","thumb.php?src=" + __this.uploadPath + file.name);
			jQuery("div." + file.id + " a.document:first")
				.removeClass("document")
				.addClass("thumbnail")
				.stopObserving("mouseover")
				.observe("mouseover", function() {
					return overlib("<img src=\"" + __this.uploadPath + file.name + "\" alt=\"\" />", FULLHTML);
				});
		} else {
			jQuery("div." + file.id + " a img:first").attr("src","/images/ico_document.gif");
			jQuery("div." + file.id + " a.document:first")
				.bind("click", function(event) {
					window.open(__this.thumbPath + "upload/" + file.name);
					event.stopPropagation();
					return false;
				})
				.unbind("mouseover")
				.bind("mouseover", function() {
					//*** TODO: Replace overlib library by a solid tooltip function
					return overlib("This file will open in a new window.");
				});
		}
	}	
	
	//*** Description.
	var objAltText = document.createElement('p');
	objAltText = Element.extend(objAltText);
	objAltText.className = 'alt-text';
	objAltText.innerHTML = __this.altLabel;
	objAltText.observe("click", function(event) {
		__this.startAltEdit(event);
	});
	jQuery("div." + file.id + ":first").append(objAltText);
}

FileField.prototype.startAltEdit = function(event) {
	var __this = this;
	
	var strId = event.findElement("div").id;
	var strText = event.findElement().innerHTML;
	event.findElement().stopObserving("click").innerHTML = "<input type=\"text\" id=\"" + strId + "_altedit" + "\" name=\"" + strId + "_altedit" + "\" value=\"" + strText + "\" class=\"alt-input\"></input>";
	event.findElement().select("input")[0].observe("blur", function(event){
		__this.stopAltEdit(event);
	}).select();
}

FileField.prototype.stopAltEdit = function(event) {
	var __this = this;
	
	var arrId = event.findElement("div").id.split("_");
	arrId.shift();
	var strId = arrId.join("_");
	var arrValue = $(strId).value.split(":");
	var labelValue = arrValue.shift();
	var fileValue = arrValue.shift();
	fileValue = (fileValue == undefined) ? "" : fileValue;
	var libraryValue = arrValue.shift();
	libraryValue = (libraryValue == "" || libraryValue == undefined) ? 0 : libraryValue;	
	var strText = event.findElement().value;
	event.findElement().stopObserving("blur");
	
	$(strId).value = labelValue + ":" + fileValue + ":" + libraryValue + ":" + strText;
	
	event.findElement("p").observe("click", function(event) {
		__this.startAltEdit(event);
	}).innerHTML = strText;
}

FileField.prototype.uploadError = function(file, errorCode, message) {
	//*** Nothing.
	try {
		switch (errorCode) {
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				alert("File " + file.name + " is too big.");
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				alert("File " + file.name + " is a zero size file.");
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				alert("File " + file.name + " is an invalid file type.");
				break;
			default:
				alert("Upload encountered a problem." + errorCode);
				break;
		}
	} catch (ex) {
		//alert("Upload encountered a problem.");
    }
}

FileField.prototype.uploadComplete = function(file) {
	//alert("FileField.prototype.uploadComplete: " + file.name);
}

FileField.prototype.queueComplete = function(numFilesUploaded) {
	//alert("FileField.prototype.queueComplete: " + numFilesUploaded);
}

FileField.prototype.cancelCurrentSwfUpload = function(inputId, file) {
	this.swfUpload.cancelUpload(file.id, false);
	var objStats = this.swfUpload.getStats();
	objStats.upload_cancelled++;
	objStats.successful_uploads--;
	this.swfUpload.setStats(objStats);
	this.removeSwfUploadRow(inputId, file);
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
		var objDate = Date.parseDate(strValue, "%d %B %Y %H:%M:%S");
		
		$(this.id + "_alt").innerHTML = (strValue == "") ? "&nbsp;" : objDate.print($(this.id + "_format").value);
		Element.hide(this.id + "_canvas");
		Element.hide("calendarButton_" + this.id);
		Element.show(this.id + "_alt");	
	} else {
		//*** The field needs no special treatment.
		var strValue = $(this.id + "_" + this.parent.currentLanguage).value;
		var objDate = Date.parseDate(strValue, "%d %B %Y %H:%M:%S");
		
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