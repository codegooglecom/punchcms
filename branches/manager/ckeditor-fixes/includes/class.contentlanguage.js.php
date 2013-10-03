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

/**************************************************************************
 * ContentLanguage Class.
 *************************************************************************/

/***
 * ContentLanguage object.
 */
var ContentLanguage = function() {
	this.version = '1.1.2'; // 2.0 when jQuery is implemented?
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
	var $objScript = jQuery("<script></script>");

	objScript.attr({
		type: "text/javascript",
		src: libraryName
	});
	jQuery("head").append($objScript);

}

ContentLanguage.load = function() {
	if(typeof jQuery == "undefined")
		throw("ContentLanguage class requires the jQuery library >= 1.4.2");

alert("Old loading code in class.contentlanguage.js.php on line 62");

//	$A(document.getElementsByTagName("script")).findAll( function(s) {
//		return (s.src && s.src.match(/pcms\.js(\?.*)?$/))
//	}).each( function(s) {
//		var path = s.src.replace(/pcms\.js(\?.*)?$/,'');
//		var includes = s.src.match(/\?.*load=([a-z,]*)/);
//		(includes ? includes[1] : 'ptemplate,pfield').split(',').each(
//			function(include) { ContentLanguage.require(path+include+'.js') });
//	});
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
	jQuery("#languageForm").slideDown();
}

ContentLanguage.prototype.init = function() {
 	jQuery("#language_active").bind("mouseover mouseout mousedown", function(event) {
 		switch(event.type){
 			case "mouseover":
			 	return objContentLanguage.buttonOver('activeElement', this);
 				break;
 			case "mouseout":
 				return objContentLanguage.buttonOut('activeElement', this);
 				break;
 			case "mousedown":
 				return objContentLanguage.toggleActiveElement();
 				break;
 		}
 	});

 	jQuery("#frm_language option[value="+ this.defaultLanguage +"]").attr("selected","selected");
}

ContentLanguage.prototype.swap = function(languageId) {
	var $objImage 		= jQuery("#language_cascade"),
		$objImageActive	= jQuery("#language_active"),
		$objButton		= $objImage.parent();

	this.toTemp();
	this.currentLanguage = languageId;

	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage || this.actives[this.currentLanguage] != true || this.actives[this.defaultLanguage] != true) {
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
					objReturn = objContentLanguage.buttonOver("cascadeElement", this);
					break;
				case "mouseout":
					objReturn = objContentLanguage.buttonOut("cascadeElement", this);
					break;
				case "click":
					objReturn = objContentLanguage.toggleCascadeElement();
					break;
			}

			return objReturn;
		});
	}

	//*** Check the active state.
	if (this.actives[this.currentLanguage] != true) {
		if (this.hover) overlib("<?php echo $objLang->get("langEnable", "tip") ?>");
		$objImageActive.attr("src", "images/lang_disabled.gif");
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langDisable", "tip") ?>");
		$objImageActive.attr("src", "images/lang_enabled.gif");
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

ContentLanguage.prototype.buttonOver = function(strButtonType, objImage, fieldId) {
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

		case "activeElement":
			if (this.actives[this.currentLanguage] != true) {
				overlib("<?php echo $objLang->get("langEnable", "tip") ?>");
				$objImage.attr("src", "images/lang_enabled.gif");
			} else {
				overlib("<?php echo $objLang->get("langDisable", "tip") ?>");
				$objImage.attr("src", "images/lang_disabled.gif");
			}
			break;
	}
}

ContentLanguage.prototype.buttonOut = function(strButtonType, objImage, fieldId) {
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

		case "activeElement":
			if (this.actives[this.currentLanguage] != true) {
				$objImage.attr("src", "images/lang_disabled.gif");
			} else {
				$objImage.attr("src", "images/lang_enabled.gif");
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
	var strUrl 			= "ajax.php",
		$objFiles 		= jQuery("#storageBrowser_" + objTrigger.id + " ul"),
		$objList 		= jQuery("#storageBrowser_" + objTrigger.id + " div.storageList"),
		$objLoader 		= jQuery("<div/>",{
							"class":"storageLoader",
							"html": "<?php echo $objLang->get("loadingFiles", "form") ?>",
							"css":{
								"display":"block"
							}
						}),
		strPost 		= {
							cmd: "StorageItems::getFileListHTML",
							parentId: jQuery('#frm_storage_' + objTrigger.id).find('option:selected').val().split("_").pop()
						};

	if ($objFiles.length > 0) $objFiles.find(":first").remove();

	jQuery(".storageLoader").remove(); // Clear all loaders before inserting a new one.
	$objList.append($objLoader);

	var request = jQuery.get(strUrl, strPost, function(data) { objTrigger.parent.showStoragePage(data, objTrigger); }, "xml");
}

ContentLanguage.prototype.showStoragePage = function(objResponse, objTrigger) {
	var $objField 	= jQuery(objResponse).find("field"),
		$objLoader 	= jQuery("#storageBrowser_" + objTrigger.id + " div.storageLoader"),
		$objList 	= jQuery("#storageBrowser_" + objTrigger.id + " div.storageList");

	$objLoader.fadeOut();
	$objList.html($objField.text());

	//*** Attach events to the thumbs.
	jQuery("#storageBrowser_" + objTrigger.id + " li").each(function(i){
		var $objLink = jQuery(this).find("a:first");

		$objLink
			.bind("mouseover mouseout click", function(event){
				var $objLabel = jQuery(this).parent().find("span:first");

				switch(event.type){
					case "mouseover":
						return overlib($objLabel.html());
					break;
					case "mouseout":
						return nd();
					break;
					case "click":
						objTrigger.transferStorage(jQuery(this), $objLabel.html());
						return false;
					break;
				}
			});
	}); // End .each()
}

/***
 * ContentField object.
 */
function ContentField(strId, objParent, strCascades) {
	this.id 		= strId || 0;
	this.parent		= objParent || null;
	this.cascades 	= {};

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
	var $objImage 	= jQuery("#" + this.id + "_cascade"),
		$objButton	= $objImage.parent(),
		strId 		= this.id;

	//*** Attach mouse events to the cascade button.
	if (this.parent.currentLanguage == this.parent.defaultLanguage || this.parent.actives[this.parent.currentLanguage] != true || this.parent.actives[this.parent.defaultLanguage] != true) {
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
					objContentLanguage.buttonOver('cascadeField', this, strId);
				break;
				case "mouseout":
					objContentLanguage.buttonOut('cascadeField', this, strId);
				break;
				case "click":
					objContentLanguage.toggleCascadeField(strId);
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

ContentField.prototype.toScreen = function() {
	this.setIconCascade();
	jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());

	return true;
}

ContentField.prototype.toTemp = function() {
	var strValue = jQuery("#" + this.id).val();

	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(strValue);
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
		jQuery("#" + this.id + "_alt").html((strValue == "") ? "&nbsp;" : strValue);
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
        if(jQuery("#" + this.id + "_element").length > 0)
        {
           jQuery("#" + this.id + "_element").html(jQuery("#" + this.id + "_"+ this.parent.defaultLanguage +"_element").val());
        }
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id).show();
		jQuery("#" + this.id).val(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
        if(jQuery("#" + this.id + "_element").length > 0)
        {
           jQuery("#" + this.id + "_element").html(jQuery("#" + this.id + "_"+ this.parent.currentLanguage +"_element").val());
	}
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
                jQuery("#" + this.id + "_alt").show();
		jQuery("#cke_" + this.id).hide();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val();
		jQuery("#" + this.id + "_alt").html((strValue == "") ? "&nbsp;" : strValue);
		jQuery("#" + this.id + "_alt").show();
		jQuery("#cke_" + this.id).hide();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#cke_" + this.id).show();
		if (typeof CKEDITOR != "undefined") {
                        var objArea = CKEDITOR.instances[this.id];
			if (typeof objArea == "object"){
                            objArea.setData(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
			}else{
                            $("#" + this.id).html(jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
                        }
                }
        }
}

TextAreaField.prototype.toTemp = function() {
	var strValue = CKEDITOR.instances[this.id].getData();
	if (strValue == "<p>&nbsp;</p>") strValue = "";
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(strValue);
}

/***
 * FileField object.
 */
function FileField(strId, objParent, strCascades, objOptions) {
	var __this 		= this;

	this.base = ContentField;
	this.base(strId, objParent, strCascades);

	//*** Set local properties.
	this.$objTrigger = jQuery("#" + strId);
	this.subFiles = {};
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
	if (this.$objTrigger.is("input") && this.$objTrigger.attr("type") == "file"){
		//*** What to do when a file is selected.
		this.$objTrigger.bind("change", function(){
			objParent.transferField(strId);
		});
	} else {
		//*** This can only be applied to file input elements!
		alert("Error: " + strId + " is not a file input element!");
	}

	//*** Attach event to the library button.
	jQuery("#browseStorage_" + strId).toggle(
		function(){
			__this.openStorageBrowser();
			jQuery(this).text("<?php echo $objLang->get("pcmsInlineStorage", "menu"); ?>");
			return false;
		},
		function(){
			__this.closeStorageBrowser();
			jQuery(this).text("<?php echo $objLang->get("pcmsInlineStorage", "menu"); ?>");
			return false;
		}
	);

	//*** Attach event to the library folder select.
	jQuery("#frm_storage_" + strId).bind("change", function(){
		__this.parent.loadStoragePage(__this);
		return false;
	});

	//*** Create containers.
	var arrLang = [this.parent.currentLanguage, this.parent.defaultLanguage];
	for (var intCount = 0; intCount < arrLang.length; intCount++) {
		if (!this.subFiles[arrLang[intCount]]) {
			var intCurrent = (jQuery("#" + this.id + "_" + arrLang[intCount] + "_current").val()) ? parseInt(jQuery("#" + this.id + "_" + arrLang[intCount] + "_current").val()) : 0;
			this.subFiles[arrLang[intCount]] = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

			for (var intCountX = 1; intCountX < intCurrent + 1; intCountX++) {
				this.subFiles[arrLang[intCount]].uploaded.push(jQuery("#" + this.id + "_" + arrLang[intCount] + "_" + intCountX).get(0));
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
		button_window_mode: SWFUpload.WINDOW_MODE.OPAQUE,
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
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = "";
		for (var intCount = 0; intCount < this.subFiles[this.parent.defaultLanguage].uploaded.length; intCount++) {
			var arrValue = this.subFiles[this.parent.defaultLanguage].uploaded[intCount].value.split(":");
			strValue += this.shortName(arrValue[0], 40) + "<br />";
		}
		for (var intCount = 0; intCount < this.subFiles[this.parent.defaultLanguage].toUpload.length; intCount++) {
			var arrValue = this.subFiles[this.parent.defaultLanguage].toUpload[intCount].val().split(":");
			strValue += this.shortName(arrValue[0], 40) + "<br />";
		}
		jQuery("#" + this.id + "_alt").html((strValue == "") ? "&nbsp;" : strValue);
		jQuery("#" + this.id + "_widget").hide();
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_widget").show();
		jQuery("#" + this.id + "_alt").hide();

		//*** Insert upload rows.
		jQuery("#" + this.id + "_widget div.required").show();
		jQuery("#filelist_new_" + this.id).hide();

		jQuery("#filelist_new_" + this.id + " div.multifile").each(function() {
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

		jQuery("#filelist_current_" + this.id + " div.multifile").each(function() {
			jQuery(this).remove();
		});

		for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
			var filledElement = this.subFiles[this.parent.currentLanguage].uploaded[intCount],
				blnStorage 	  = (filledElement.value.split(":").length > 2) ? true : false;

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

		jQuery.debug({content: $("#" + this.id + "_widget h3").next()});
		$("#" + this.id + "_widget h3").next().each(function(){
			if($(this).children().length < 0){
				jQuery.debug("Niet zichtbaar");
			}
		});


		jQuery("#filelist_current_" + this.id).sortable({
			dropOnEmpty: true,
			connectWith: "#groups, #allgroups",
			update: function(){
				objContentLanguage.sort(strId);
			},
			axis: "y"
		});
		jQuery("#filelist_current_" + this.id).disableSelection();
	}
}

FileField.prototype.openStorageBrowser = function() {
	var __this 	   = this,
		closeLabel = "<?php echo $objLang->get("pcmsCloseInlineStorage", "menu"); ?>";

	//*** Slide open.
	jQuery("#storageBrowser_" + this.id).slideDown("fast", function(){
		jQuery("#browseStorage_" + __this.id).text(closeLabel);
		__this.parent.loadStoragePage(__this);
	});
}

FileField.prototype.closeStorageBrowser = function() {
	var __this = this,
		defaultLabel = "<?php echo $objLang->get("pcmsInlineStorage", "menu"); ?>";

	jQuery("#storageBrowser_" + this.id).slideUp("fast", function(){
		jQuery("#browseStorage_" + __this.id).text(defaultLabel);
	});
}

FileField.prototype.transferField = function() {
	var $filledElement = jQuery("#" + this.id),
		objParent 	   = this.parent,
		strId 		   = this.id;

	jQuery("#filelist_new_" + this.id).show();

	this.subFiles[this.parent.currentLanguage].toUpload.push($filledElement);

	$filledElement
		.attr("id", strId + "_" + objParent.currentLanguage + "_" + this.fileCount++)
		.attr("name", strId + "_" + objParent.currentLanguage + "_new[]");

	//*** Create empty replacement.
	var $objElement = jQuery("<input />")
	$objElement
		.attr("type", "file")
		.addClass("input-file")
		.attr("id", strId)
		.attr("name", strId + "_new")
		.bind("change", function(){
			objParent.transferField(strId);
		});

	$objElement.insertBefore($filledElement.next());

	//*** Add row to the upload list.
	this.addUploadRow($filledElement);

	//*** Appease Safari: display:none doesn't seem to work correctly in Safari.
	$filledElement.css({
		"position" : "absolute",
		"left" : "-100000px"
	});
}

FileField.prototype.addUploadRow = function(element) { // element is a jQuery object
	var objParent  	 = this.parent,
		strId 	   	 = this.id,
		$element   	 = (element instanceof jQuery) ? element : jQuery(element), // Make sure it's a jQuery object
		$objRow    	 = jQuery("<div />",{
							id: "file_" + $element.attr("id"),
							"class": "multifile",
							data: {"element": $element}
						}),
		$objButton 	 = jQuery("<a />"),
		$objRowValue = jQuery("<p />");

	//$objRow
	//	.attr("id", "file_" + $element.attr("id"))
	//	.addClass("multifile")
	//	.data("element", $element);

	$objButton
		.attr("href", "")
		.addClass("button")
		.html("&#735;")
		.attr("title", this.removeLabel)
		.bind("click", function(){
			objParent.removeUploadField(strId, this);
			return false;
		});
	$objRow.append($objButton);

	var arrValue = $element.val().split(":");
	var strValue = this.shortName(arrValue[0], this.maxChar);
	$objRowValue
		.html(strValue)
	$objRow
		.append($objRowValue);

	jQuery("#filelist_new_" + strId).append($objRow);

	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		jQuery("#" + this.id + "_widget div.required").hide();
		jQuery("#storageBrowser_" + this.id).hide();
	}
	jQuery("#filelist_new_" + this.id).sortable({
		items: "div",
		axis: "y",
		update: function(){
			objContentLanguage.sort(strId);
		}
	});
}

FileField.prototype.addCurrentRow = function(element, blnStorage) { // Element should be a jQuery element
	var objParent 		= this.parent,
		strId 			= this.id,
		$element   		= (element instanceof jQuery) ? element : jQuery(element), // Make sure it's a jQuery object
		$objRow			= jQuery("<div />"),
		$objButton  	= jQuery("<a />"),
		$objThumb		= jQuery("<a />"),
		$objRowValue 	= jQuery("<p />"),
		$objAltText 	= jQuery("<p />");

	$objRow
		.attr("id", 'file_' + $element.attr("id"))
		.addClass((blnStorage) ? "multifile storage" : "multifile")
		.css({"position":"relative"})
		.data("element", $element);

	$objButton
		.addClass("button")
		.html("&#735;")
		.attr("title", this.removeLabel)
		.attr("href", "#")
		.bind("click", function(){
			objParent.removeCurrentField(strId, this);
			return false;
		});

	$objRow.append($objButton);

	var arrValue = $element.attr("value").split(":"),
		labelValue = arrValue.shift(),
		fileValue = arrValue.shift(),
		libraryValue = arrValue.shift(),
		alttextValue = arrValue.shift();

	//*** Image thumbnail.
	if (this.thumbPath != "") {
		var __this = this;
		if (this.isImage(fileValue)) {
			$objThumb
				.addClass("thumbnail")
				.html("<img src=\"thumb.php?src=" + this.thumbPath + fileValue + "\" alt=\"\" />")
				.attr("href", "")
				.bind("mouseover mouseout", function(event){
					if(event.type == "mouseover"){
						return overlib('<img src="' + __this.thumbPath + fileValue + '" alt="" />', FULLHTML);
					}
					else {
						return nd();
					}
				});
		} else {
			$objThumb
				.addClass("document")
				.html("<img src=\"/images/ico_document.gif\" alt=\"\" />")
				.attr("href", "")
				.bind("click mouseover mouseout", function(event){
					switch(event.type){
						case "mouseover":
							return overlib('This file will open in a new window.');
						break;
						case "mouseout":
							return nd();
						break;
						case "click":
							window.open(__this.thumbPath + fileValue);
							return false;
						break;
					}
				});
		}

		$objRow.append($objThumb); // Add the thumb to the row
	}

	//*** Label.
	$objRowValue.html(labelValue);
	$objRow.append($objRowValue);

	//*** Description.
	//$objAltText
	//	.addClass("alt-text")
	//	.html((alttextValue == "" || alttextValue == undefined) ? this.altLabel : alttextValue)
	//	.bind("click", function() {
	//		__this.startAltEdit(jQuery(this));
	//	});

	$objRow.append($objAltText);

	jQuery("#filelist_current_" + strId).append($objRow);

	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		jQuery("#" + strId + "_widget div.required").hide();
		jQuery("#storageBrowser_" + strId).hide();
	}
}

FileField.prototype.addSwfUploadRow = function(element, file) {
	var __this   			= this,
		strId				= this.id,
		$element 			= (element instanceof jQuery) ? element : jQuery(element), // Make it a jQuery object
		$objRow 			= jQuery("<div/>"),
		$objButton 			= jQuery("<a/>"),
		$objThumb 			= jQuery("<a/>"),
		$tempFile 			= jQuery($element.data("file")),
		$objRowValue 		= jQuery("<p/>"),
		$objProgressBar 	= jQuery("<div/>"),
		$objProgressWrapper = jQuery("<div/>"),
		$objAltText 		= jQuery("<p/>");


	$objRow.attr("id", "file_" + $element.attr("id"));

	if (file !== undefined) {
		$objRow.addClass("multifile storage " + file.id);
	} else {
		$objRow.addClass("multifile storage " + $element.data("file").id);
	}

	$objRow
		.css("position","relative")
		.data("element", $element);

	if (file !== undefined) {
		$objRow.bind("mouseover mouseout", function(event) {
			if(event.type == "mouseover"){
				jQuery(this).find("a img").attr("src", "/images/ico_loading_mo.gif")
				//jQuery("#" + $objRow.attr("id")).find("a img").eq(0).attr("src", "/images/ico_loading_mo.gif");
			}
			else { // Then it's a mouseout event
				jQuery(this).find("a img").attr("src", "/images/ico_loading.gif");
				//jQuery("#" + $objRow.attr("id")).find("a img").eq(0).attr("src", "/images/ico_loading.gif");
			}
		});
	}

	//*** Delete button.
	$objButton.addClass("button");

	if (file !== undefined) {
		$objButton
    		.html("&#735;")
    		.attr("title", this.cancelLabel)
			.bind("click", function(event) {
				__this.cancelCurrentSwfUpload($element.attr("id"), file);
				event.stopPropagation();
				return false;
			});
	} else {
		$objButton
    		.html("&#735;")
    		.attr("title", this.removeLabel)
			.bind("click", function(event) {
				__this.cancelCurrentSwfUpload($element.attr("id"), $element.data("file"));
				event.stopPropagation();
				return false;
		});
	}

	$objButton.attr("href","");
	$objRow.append($objButton);

	var arrValue 		= $element.attr("value").split(":"),
		labelValue 		= arrValue.shift(),
		fileValue 		= arrValue.shift(),
		libraryValue 	= arrValue.shift(),
		alttextValue 	= arrValue.shift();

	//*** Image thumbnail.
	$objThumb.attr("href","")
	if (file !== undefined) {
		$objThumb
			.addClass("document")
			.html("<img src=\"/images/ico_loading.gif\" alt=\"\" />")
			.bind("mouseover mouseout", function(event) {
				if(event.type == "mouseover"){
					return overlib('This file is being uploaded.');
				}
				else { // It's a mouseout event
					return nd();
				}
			});
	} else {
		if (__this.thumbPath != "") {
			if (__this.isImage(tempFile.name)) {
				$objThumb
					.addClass("thumbnail")
					.html("<img src=\"thumb.php?src=" + __this.uploadPath + tempFile.name + "\" alt=\"\" />")
					.bind("mouseover mouseout", function(event) {
						if(event.type == "mouseover") {
							return overlib("<img src=\"" + __this.uploadPath + tempFile.name + "\" alt=\"\" />", FULLHTML);
						}
						else {
							return nd();
						}
					});
			} else {
				$objThumb
					.addClass("document")
					.html("<img src=\"/images/ico_document.gif\" alt=\"\" />")
					.bind("mouseover mouseout click", function(event) {
						switch(event.type){
							case "click":
								//window.open(__this.thumbPath + "upload/" + $tempFile.attr("name"));
								window.open(__this.uploadPath + tempFile.name);
								event.stopPropagation(); // kill all further bubbling
								return false;
							break;
							case "mouseover":
								return overlib("This file will open in a new window.");
							break;
							case "mouseout":
								return nd();
							break;
						}
					});
			}
		}
	}
	$objRow.append($objThumb);

	//*** Label.
	$objRowValue.html(labelValue);
	$objRow.append($objRowValue);

	if (file !== undefined) {
		//*** Progress.
		$objProgressBar.addClass("progressBar");
		$objProgressWrapper
			.addClass("progressWrapper")
			.append($objProgressBar);

		$objRow.append($objProgressWrapper);
	} else {
		//*** Description.
		//$objAltText
		//	.addClass("alt-text")
		//	.html(this.altLabel)
		//	.bind("click", function() {
		//		__this.startAltEdit(jQuery(this));
		//	});
		//$objRow.append($objAltText);
	}

	jQuery("#filelist_new_" + strId).append($objRow);

	//*** Check max files.
	if ((this.subFiles[this.parent.currentLanguage].toUpload.length + 1) + this.subFiles[this.parent.currentLanguage].currentFiles > this.maxFiles) {
		jQuery("#storageBrowser_" + strId).hide();
	}

	jQuery("#filelist_new_" + strId).sortable({
		items: "div",
		update: function(){
			objContentLanguage.sort(strId);
		},
		axis: "y"
	});
}

FileField.prototype.removeSwfUploadRow = function(inputId, file) {
	var arrTemp = new Array();

	jQuery("#" + inputId).remove();
	jQuery("#file_" + inputId).remove();

	//*** Remove remotely.
	objData = {
		"do": "remove",
		"file": file.name,
		"PHPSESSID": "<?php echo session_id(); ?>"
	};
	jQuery.post("upload.php", objData,
		function(data){
			// TODO: Implement some feedback here
		},
	"xml");

	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].toUpload[intCount].value != file.name) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].toUpload[intCount]);
		}
	}

	this.subFiles[this.parent.currentLanguage].toUpload = arrTemp;

	jQuery("#" + this.id + "_widget div.required").show();
	if (this.subFiles[this.parent.currentLanguage].toUpload.length == 0) {
		jQuery("#filelist_new_" + this.id).hide();
	}
}

FileField.prototype.removeUploadField = function(objTrigger) {
	var strId 			= this.id,
		$objTrigger 	= (objTrigger instanceof jQuery) ? objTrigger : jQuery(objTrigger), // Make it a jQuery object
		cacheValue 		= $objTrigger.parent().data("element").val();


	$objTrigger.parent().data("element").remove();
	$objTrigger.parent().remove();
	// objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	// objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);

	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].toUpload.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].toUpload[intCount].value != cacheValue) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].toUpload[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].toUpload = arrTemp;

	jQuery("#" + strId + "_widget div.required").show();
	if (this.subFiles[this.parent.currentLanguage].toUpload.length == 0) {
		jQuery("#filelist_new_" + strId).hide();
	}
}

FileField.prototype.removeCurrentField = function(objTrigger) {
	var strId 			= this.id,
		$objTrigger 	= (objTrigger instanceof jQuery) ? objTrigger : jQuery(objTrigger), // Make it a jQuery object
		cacheValue 		= $objTrigger.parent().data("element").val(),
		arrTemp 		= new Array();

	jQuery("#" + this.id + "_widget div.required").show();

	for (var intCount = 0; intCount < this.subFiles[this.parent.currentLanguage].uploaded.length; intCount++) {
		if (this.subFiles[this.parent.currentLanguage].uploaded[intCount].value != cacheValue) {
			arrTemp.push(this.subFiles[this.parent.currentLanguage].uploaded[intCount]);
		}
	}
	this.subFiles[this.parent.currentLanguage].uploaded = arrTemp;
	this.subFiles[this.parent.currentLanguage].currentFiles--;

	$objTrigger.parent().data("element").remove();
	$objTrigger.parent().remove();

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

FileField.prototype.transferStorage = function(objLink, strLabel) { // objLink is a jQuery object.
	var $objElement = jQuery("<input />");

	objLink.fadeOut("fast", function(){ jQuery(this).fadeIn("slow"); });

	//*** Create input element.
	$objElement.attr({
		type: "hidden",
		id: this.id + "_" + this.parent.currentLanguage + "_" + this.fileCount++,
		name: this.id + "_" + this.parent.currentLanguage + "[]",
		value: strLabel + ":" + objLink.find("img:first").attr("alt").split("/").pop() + ":" + objLink.attr("id").split("_").pop()
	});

	jQuery("#filelist_new_" + this.id).append($objElement);

	this.subFiles[this.parent.currentLanguage].currentFiles++;
	this.subFiles[this.parent.currentLanguage].uploaded.push($objElement.get(0));

	jQuery("#filelist_current_" + this.id).show();
	this.addCurrentRow($objElement, true);
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
	var arrFields 	= jQuery("#filelist_current_" + this.id).sortable("serialize").split("&"),
		$objParent 	= jQuery("#" + this.id + "_widget");

	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		var strTemp = arrFields[intCount].replace("file_" + this.id + "_" + this.parent.currentLanguage + "[]=", ""),
			$objTemp = jQuery("#" + this.id + "_" + this.parent.currentLanguage + "_" + strTemp);

		if ($objTemp) {
			$objTemp.remove();
			$objParent.append($objTemp);
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
	var $objElement = jQuery("<input />");
	jQuery("#filelist_new_" + this.settings.jsParent.id).show();

	//*** Create input element.
	$objElement
		.attr("type", "hidden")
		.attr("id", this.settings.jsParent.id + "_" + this.settings.jsParent.parent.currentLanguage + "_" + this.settings.jsParent.fileCount++)
		.attr("name", this.settings.jsParent.id + "_" + this.settings.jsParent.parent.currentLanguage + "[]")
		.attr("value", file.name + ":::")
		.data("file", file);


	jQuery("#filelist_new_" + this.settings.jsParent.id).append($objElement);

	this.settings.jsParent.subFiles[this.settings.jsParent.parent.currentLanguage].toUpload.push($objElement);
	this.settings.jsParent.addSwfUploadRow($objElement, file);
}

FileField.prototype.uploadProgress = function(file, bytesLoaded, bytesTotal) {
	var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
	jQuery("div." + file.id + " div.progressBar").css({width:percent + "%"});
}

FileField.prototype.uploadSuccess = function(file, serverData) {
	var __this = this.settings.jsParent;
	jQuery("div." + file.id + " div.progressWrapper:first").remove();
	jQuery("div." + file.id + ":first").unbind("mouseover").unbind("mouseout");
	jQuery("div." + file.id + " a.button:first").html("&#735;").attr("title", __this.removeLabel);

	if (__this.thumbPath != "") {
		if (__this.isImage(file.name)) {
			jQuery("div." + file.id + " a img:first").attr("src","thumb.php?src=" + __this.uploadPath + file.name);
			jQuery("div." + file.id + " a.document:first")
				.removeClass("document")
				.addClass("thumbnail")
				.unbind("mouseover")
				.bind("mouseover", function() {
					return overlib("<img src=\"" + __this.uploadPath + file.name + "\" alt=\"\" />", FULLHTML);
				});
		} else {
			jQuery("div." + file.id + " a img:first").attr("src","/images/ico_document.gif");
			jQuery("div." + file.id + " a.document:first")
				.bind("click", function(event) {
					window.open(__this.uploadPath + file.name);
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
	//var $objAltText = jQuery("<p/>", {
	//	"class": "alt-text",
	//	text: __this.altLabel,
	//	click: function(){
	//		__this.startAltEdit(jQuery(this));
	//	}
	//});

	//jQuery("div." + file.id + ":first").append($objAltText);
}

FileField.prototype.startAltEdit = function($objElement) {
	var __this 		= this,
		$objParent 	= $objElement.parent(),
		strId  		= $objParent.attr("id"),
		strText 	= $objElement.html(),
		$objInput 	= jQuery("<input />", {
							type: "text",
							id: strId + "_altedit",
							name: strId + "_altedit",
							value: strText,
							"class": "alt-input"
						});

	//$objElement
	//	.unbind("click")
	//	.html($objInput)
	//	.bind("focusout", function(){
	//		__this.stopAltEdit(jQuery(this));
	//	});
	//jQuery("#" + strId + "_altedit").focus();
}

FileField.prototype.stopAltEdit = function($objElement) {
	var __this 			= this,
		$objParent 		= $objElement.parent(), // Parent div
		arrId			= $objParent.attr("id").split("_"),
		strTempId		= arrId.shift();

	var	strId 			= arrId.join("_");

		arrValue		= jQuery("#" + strId).val().split(":"),
		labelValue		= arrValue.shift(),
		fileValue		= (typeof arrValue.shift() == "undefined") ? "" : arrValue.shift(),
		libraryValue	= (arrValue.shift() == "" || arrValue.shift() == undefined) ? 0 : arrValue.shift(),
		strText			= $objElement.val();



	$objElement.unbind("focusout");
	jQuery("#" + strId).val(labelValue + ":" + fileValue + ":" + libraryValue + ":" + strText);

	$objParent
		.find("p")
		.bind("click", function(){
			__this.startAltEdit(jQuery(this));
		})
		.html(strText);
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
		jQuery.debug({content: "Upload encountered a problem.", type: "error"});
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id + "_canvas").hide();
		jQuery("#calendarButton_" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val();
		var objDate = Date.parseDate(strValue, "%d %B %Y %H:%M:%S");

		jQuery("#" + this.id + "_alt").get(0).innerHTML = (strValue == "") ? "&nbsp;" : objDate.print(jQuery("#" + this.id + "_format").val());
		jQuery("#" + this.id + "_canvas").hide();
		jQuery("#calendarButton_" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else {
		//*** The field needs no special treatment.
		var strValue = jQuery("#" + this.id + "_" + this.parent.currentLanguage).val();
		var objDate = Date.parseDate(strValue, "%d %B %Y %H:%M:%S");

		var strCanvasValue = (strValue == "") ? "&nbsp;" : objDate.print(jQuery("#" + this.id + "_format").val());
		jQuery("#" + this.id + "_canvas").html(strCanvasValue);
		jQuery("#" + this.id).val(strValue);
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id + "_canvas").show();
		jQuery("#calendarButton_" + this.id).show();
	}
}

DateField.prototype.toTemp = function() {
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(jQuery("#" + this.id).val());
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var strValue = (jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val() == "1" || jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val() == "true") ? "true" : "false";
		jQuery("#" + this.id + "_alt").html(strValue);
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id).show();
		if (jQuery("#" + this.id + "_" + this.parent.currentLanguage).val() == "1" || jQuery("#" + this.id + "_" + this.parent.currentLanguage).val() == "true") {
			jQuery("#" + this.id).attr("checked","checked");
		} else {
			jQuery("#" + this.id).removeAttr("checked");
		}
	}
}

CheckBox.prototype.toTemp = function() {
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(jQuery("#" + this.id).get(0).checked);
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var arrValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val().split(",");
		var strValue = "";
		for (var intCount = 0; intCount < jQuery("#" + this.id).find("option").length; intCount++) {
			if (arrValue.inArray(jQuery("#" + this.id).find("option:eq(" + intCount + ")").val())) {
				strValue += jQuery("#" + this.id).find("option:eq(" + intCount + ")").html() + "<br />";
			}
		}
		jQuery("#" + this.id + "_alt").get(0).innerHTML = (strValue == "") ? "&nbsp;" : strValue;
		jQuery("#" + this.id).hide();
		jQuery("#" + this.id + "_alt").show();
	} else {
		//*** The field needs no special treatment.
		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id).show();
		var arrDefault = jQuery("#" + this.id + "_" + this.parent.currentLanguage).val().split(",");
		jQuery("#" + this.id).find("option").removeAttr("selected");
		for (var intCount = 0; intCount < jQuery("#" + this.id).find("option").length; intCount++) {
			if (arrDefault.inArray(jQuery("#" + this.id).find("option:eq(" + intCount + ")").val())) {
				jQuery("#" + this.id).find("option:eq(" + intCount + ")").attr("selected","selected");
			}
		}
	}
}

SelectListField.prototype.toTemp = function() {
	var arrValue = [];
	for (var intCount = 0; intCount < jQuery("#" + this.id).find("option").length; intCount++) {
		if (jQuery("#" + this.id).find("option:eq(" + intCount + ")").is(":selected")) {
			arrValue.push(jQuery("#" + this.id).find("option:eq(" + intCount + ")").val());
		}
	}
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(arrValue.join(","));
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
		jQuery("#" + this.id + "_alt").html("<?php echo $objLang->get("langDisabled", "label") ?>");
		jQuery("#" + this.id + "_widget").hide();
		jQuery("#" + this.id + "_alt").show();
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		var arrValue = jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val().split(",");
		var strValue = "";
		var arrFields = jQuery("#" + this.id + "_widget").find("input");
		for (var intCount = 0; intCount < arrFields.length; intCount++) {
			if (arrFields.eq(intCount).attr("name") == this.id + "[]" && arrValue.inArray(arrFields.eq(intCount).val())) {
				var arrField = arrFields.eq(intCount);
				strValue += arrField.parent().get(0).lastChild.nodeValue + "<br />";
			}
		}

		var strAltValue = (strValue == "") ? "&nbsp;" : strValue;
		jQuery("#" + this.id + "_alt").html(strAltValue);
		jQuery("#" + this.id + "_widget").hide();
		jQuery("#" + this.id + "_alt").show();
		//Fadein
		jQuery("#" + this.id + "_alt").fadeIn();
	} else {
		//*** The field needs no special treatment.

		jQuery("#" + this.id + "_alt").hide();
		jQuery("#" + this.id + "_widget").show();
		var arrDefault = jQuery("#" + this.id + "_" + this.parent.currentLanguage).val().split(",");
		var arrFields = jQuery("#" + this.id + "_widget").find("input");
		arrFields.eq(intCount).removeAttr("checked");
		for (var intCount = 0; intCount < arrFields.length; intCount++) {
			if (arrFields.eq(intCount).attr("name") == this.id + "[]") {
				if (arrDefault.inArray(arrFields.eq(intCount).val())) {
					arrFields.eq(intCount).attr("checked", "checked");
				}
			}
		}
	}
}

CheckListField.prototype.toTemp = function() {
	var arrValue = [];
	var arrFields = jQuery("#" + this.id + "_widget").find("input");
	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		if (arrFields.eq(intCount).attr("name") == this.id + "[]" && arrFields.eq(intCount).is(":checked")) {
			arrValue.push(arrFields.eq(intCount).val());
		}
	}
	jQuery("#" + this.id + "_" + this.parent.currentLanguage).val(arrValue.join(","));
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
