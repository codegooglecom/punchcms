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

/*** 
 * Storage object.
 */
var Storage = {
	fields: []
};

Storage.initField = function(strId, objOptions) {
	var field = new FileField(strId, objOptions);
	Storage.fields.push(field);
	
	field.toScreen();
};


/*** 
 * FileField object.
 */
function FileField(strId, objOptions) {
	this.id = strId;
	this.$trigger = jQuery(strId);
	this.subFiles = {};
	this.maxFiles = 1;
	this.maxChar = 50;
	this.fileCount = 1;
	this.thumbPath = "";
	var __this = this;
	
	//*** Parse the options.
	for (var intCount in objOptions) {
		this[intCount] = objOptions[intCount];
	}

	//*** Attach event to the file button.
	if (this.$trigger.is("input") && this.$trigger.attr("type") == "file") {
		//*** What to do when a file is selected.
		this.$trigger.bind("change", function(){
			__this.transferField();
		});
	} else {
		//*** This can only be applied to file input elements!
		jQuery.debug({title: "Punch error message", content: strId + " is not a file input element!"});
	}
	
	//*** Create containers.
	var intCurrent = (jQuery("#" + this.id + "_current").val()) ? parseInt(jQuery("#" + this.id + "_current").val()) : 0;
	this.subFiles = {
		currentFiles: intCurrent, 
		toUpload: [], 
		uploaded: []
	};

	for (var intCountX = 1; intCountX < intCurrent + 1; intCountX++) {
		this.subFiles.uploaded.push(jQuery("#" + this.id + "_" + intCountX));
		this.fileCount++;
	}
};

FileField.prototype.toScreen = function() {		
	//*** Insert value into the field.
	jQuery("#" + this.id + "_widget").show();	
	jQuery("#" + this.id + "_alt").hide();	

	//*** Insert upload rows.
	jQuery("#" + this.id + "_widget div.required").show();
	jQuery("#filelist_" + this.id).hide();
	jQuery("#filelist_" + this.id + " div.multifile").each(function() {
		jQuery(this).remove();
	});

	//*** Init object if not exists.
	if (!this.subFiles) {
		var intCurrent = (jQuery("#" + this.id + "_current").val()) ? parseInt(jQuery("#" + this.id + "_current").val()) : 0;
		this.subFiles = {
			currentFiles: intCurrent, 
			toUpload: [], 
			uploaded: []
		};

		for (var intCount = 1; intCount < intCurrent + 1; intCount++) {
			this.subFiles.uploaded.push(jQuery("#" + this.id + "_" + intCount).get(0));
			this.fileCount++;
		}
	}

	for (var intCount = 0; intCount < this.subFiles.toUpload.length; intCount++) {
		var $filledElement = this.subFiles.toUpload[intCount];
		this.addUploadRow($filledElement);
		jQuery("#filelist_" + this.id).show();
	}

	//*** Insert current rows.
	jQuery("#filelist_" + this.id).hide();
	jQuery("#filelist_" + this.id + " div.multifile").each(function() {
		jQuery(this).remove();
	});
	
	for (var intCount = 0; intCount < this.subFiles.uploaded.length; intCount++) {
		var filledElement = this.subFiles.uploaded[intCount];
		this.addCurrentRow(filledElement);
		jQuery("#filelist_" + this.id).show();
	}

	var strId = this.id;
	jQuery("#filelist_" + this.id).sortable({
		dropOnEmpty: true,
		update: function(){
			objContentLanguage.sort(strId);
		},
		axis: "y"
	});
}

FileField.prototype.transferField = function() {
	var $objFilledElement 	= jQuery("#" + this.id);
	var objParent 			= this.parent;
	var strId 				= this.id;
	var __this 				= this;

	jQuery("#filelist_" + this.id).show();

	//*** Set the id and name of the filled file field.
	
	this.subFiles.toUpload.push($objFilledElement);
	
	filledElement.id = this.id + "_" + this.fileCount++;
	filledElement.name = this.id + "_new[]";
	
	//*** Create empty replacement.
	var $objElement = jQuery("<input />", {
		"type": "file",
		"class": "input-file",
		"id": this.id,
		"name": this.id + "_new[]",
		change: function(){
			__this.transferField();
		}
	});

	jQuery.debug({title: "objFilledElement", content: $objFilledElement});
	$objElement.insertBefore($objFilledElement.next());
	
	//*** Add row to the upload list.
	this.addUploadRow($objFilledElement);
	
	//*** Appease Safari: display:none doesn't seem to work correctly in Safari.
	$objFilledElement.css({
		position: "absolute",
		left: "-10000px"
	});
}

FileField.prototype.addUploadRow = function($element) {
	var strId = this.id;
	var __this = this;
	
	var $objRow = jQuery("<div />", {
			"id": "file_" + $element.attr("id"),
			"class": "multifile",
			data: {
				"element": $element
			}
	});
	var $objButton = jQuery("<a/>", {
			"class": "button",
			html: this.removeLabel,
			"href": "",
			click: function(){
				__this.removeUploadField(this);
				return false;
			}
	});
	$objRow.append($objButton);
	
	var objRowValue = jQuery("<p/>", {
			html: this.shortName($element.val(), this.maxChar)
	});
	$objRow.append($objRowValue);
	jQuery("#filelist_" + this.id).append($objRow);
	
	//*** Check max files.
	if ((this.subFiles.toUpload.length + 1) + this.subFiles.currentFiles > this.maxFiles) {
		jQuery("#" + this.id + "_widget div.required").hide();
	}
	
	jQuery("#filelist_" + this.id).sortable({
		dropOnEmpty: true,
		update: function(){
			objContentLanguage.sort(strId);
		}
	});	
}

FileField.prototype.addCurrentRow = function($element) {
	var strId = this.id;
	var __this = this;
	
	var $objRow = jQuery("<div/>", {
			"id": "file_" + $element.attr("id"),
			"class": "multifile",
			css: {
				"position": "relative"
			},
			data: {
				"element": $element
			},
	});
	var $objButton = jQuery("<a/>", {
			"class": "button",
			html: this.removeLabel,
			"href": "#",
			click: function(){
				__this.removeCurrentField(this);
				return false;
			}
	});
	$objRow.append($objButton);

	var arrValue 	= $element.val().split(":");
	var labelValue 	= arrValue.shift();
	var fileValue 	= arrValue.shift();
	
	//*** Image thumbnail.
	if (this.thumbPath != "") {
		if (this.isImage(fileValue)) {
			var $objThumb = jQuery("<a/>", {
					"class": "thumbnail",
					html: "<img src=\"thumb.php?src=" + this.thumbPath + fileValue + "\" alt=\"\" />",
					"href": "#",
					mouseover: function(){ 
						return overlib('<img src="' + __this.thumbPath + fileValue + '" alt="" />', FULLHTML); 
					},
					mouseout: function(){
						return nd();
					}
			});
		} else {
			var $objThumb = jQuery("<a/>", {
					"class": "document",
					"html": "<img src=\"/images/ico_document.gif\" alt=\"\" />",
					"rel": "external",
					"href": __this.thumbPath + fileValue,
					mouseover: function(){
						return overlib("<?php echo $objLang->get("newWindow", "alert") ?>");
					},
					mouseout: function(){
						return nd();
					}
			});
		}
		$objRow.append($objThumb);
	}
	
	var $objRowValue = jQuery("<p/>", {
			"html": labelValue
	});
	$objRow.append($objRowValue);
	
	jQuery("#filelist_" + this.id).append($objRow);
	
	//*** Check max files.
	if ((this.subFiles.toUpload.length + 1) + this.subFiles.currentFiles > this.maxFiles) {
		jQuery("#" + this.id + "_widget div.required").fadeOut();
	}
}

FileField.prototype.removeUploadField = function(objTrigger) {
	jQuery(objTrigger).parent().data("element").remove();
	jQuery(objTrigger).parent().remove();
	
	var arrTemp = [];
	for (var intCount = 0; intCount < this.subFiles.toUpload.length; intCount++) {
		if (this.subFiles.toUpload[intCount].val() != jQuery(objTrigger).parent().data("element").val()) {
			arrTemp.push(this.subFiles.toUpload[intCount]);
		}
	}
	this.subFiles.toUpload = arrTemp;
	
	jQuery("#" + this.id + "_widget div.required").show();
	if (this.subFiles.toUpload.length == 0) {
		jQuery("#filelist_" + this.id).fadeOut();
	}
}

FileField.prototype.removeCurrentField = function(objTrigger) {	
	var arrTemp = [];
	for (var intCount = 0; intCount < this.subFiles.uploaded.length; intCount++) {
		if (this.subFiles.uploaded[intCount].val() != jQuery(objTrigger).parent().data("element").val()) {
			arrTemp.push(this.subFiles.uploaded[intCount]);
		}
	}
	this.subFiles.uploaded = arrTemp;
	this.subFiles.currentFiles--;
	
	jQuery(objTrigger).parent().data("element").remove();
	jQuery(objTrigger).parent().remove();
	
	if (this.subFiles.uploaded.length == 0) {
		jQuery("#filelist_" + this.id).fadeOut();
	}
	jQuery("#" + this.id + "_widget div.required").fadeIn();
}

FileField.prototype.shortName = function(strInput, maxLength) {
	if (strInput.length > maxLength) {
		//*** Get filename.
		var pathDelimiter 	= (strInput.search(/\\/gi) > -1) ? "\\" : "/";
		var arrPath 		= strInput.split(pathDelimiter);
		var strFile 		= arrPath.pop();
		var reminingLength 	= (maxLength - strFile.length > 0) ? maxLength - strFile.length : 3; // Calculate remaining length
		var strPath 		= arrPath.join(pathDelimiter);

		strInput = strPath.substr(0, reminingLength) + "..." + pathDelimiter + strFile;
	}
	
	return strInput;
}

FileField.prototype.toTemp = function() {};

FileField.prototype.isImage = function(fileName) {
	var blnReturn = false;
	var extension = fileName.split(".").pop();
	var arrImages = ["jpg", "jpeg", "gif", "png"];
	
	for (var count = 0; count < arrImages.length; count++) {
		if (arrImages[count] == extension) {
			blnReturn = true;
			break;
		}
	}
	
	return blnReturn;
}

FileField.prototype.sort = function() {
	var arrFields = jQuery("#filelist_" + this.id).sortable("serialize").split("&");
	var $objParent = jQuery("#" + this.id + "_widget");
	
	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		var strTemp = arrFields[intCount].replace("filelist_" + this.id + "[]=", "");
		var objTemp = jQuery("#" + this.id + "_" + strTemp);
		if (objTemp) {
			objTemp.remove();
			$objParent.append(objTemp);
		}
	}
}