

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
	//*** Set local properties.
	this.id = strId;
	this.trigger = jQuery(strId).get(0);
	this.subFiles = new Object();
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
	if (this.trigger.tagName.toUpperCase() == 'INPUT' && this.trigger.type == 'file') {
		//*** What to do when a file is selected.
		this.trigger.onchange = function() {
			__this.transferField();
		};
	} else {
		//*** This can only be applied to file input elements!
		alert('Error: ' + strId + ' is not a file input element!');
	}
	
	//*** Create containers.
	var intCurrent = (jQuery("#" + this.id + "_current").val()) ? parseInt(jQuery("#" + this.id + "_current").val()) : 0;
	this.subFiles = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

	for (var intCountX = 1; intCountX < intCurrent + 1; intCountX++) {
		this.subFiles.uploaded.push(jQuery("#" + this.id + "_" + intCountX).get(0));
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
		this.subFiles = {currentFiles:intCurrent, toUpload:new Array, uploaded:new Array()};

		for (var intCount = 1; intCount < intCurrent + 1; intCount++) {
			this.subFiles.uploaded.push(jQuery("#" + this.id + "_" + intCount).get(0));
			this.fileCount++;
		}
	}

	for (var intCount = 0; intCount < this.subFiles.toUpload.length; intCount++) {
		var filledElement = this.subFiles.toUpload[intCount];
		this.addUploadRow(filledElement);
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
		}
	});
}

FileField.prototype.transferField = function() {
	jQuery("#filelist_" + this.id).show();

	//*** Set the id and name of the filled file field.
	var filledElement = jQuery("#" + this.id);
	var objParent = this.parent;
	var strId = this.id;
	var __this = this;
	
	this.subFiles.toUpload.push(filledElement);
	
	filledElement.id = this.id + "_" + this.fileCount++;
	filledElement.name = this.id + "_new[]";
	
	//*** Create empty replacement.
	var objElement = document.createElement('input');
	objElement.type = 'file';
	objElement.className = 'input-file';
	objElement.id = this.id;
	objElement.name = this.id + "_new[]";
	
	objElement.onchange = function() {
		__this.transferField();
	};

	filledElement.parentNode.insertBefore(objElement, filledElement.nextSibling);

	//*** Add row to the upload list.
	this.addUploadRow(filledElement);
	
	//*** Appease Safari: display:none doesn't seem to work correctly in Safari.
	filledElement.style.position = 'absolute';
	filledElement.style.left = '-1000px';
}

FileField.prototype.addUploadRow = function(element) {
	var strId = this.id;
	var __this = this;
	
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
		__this.removeUploadField(this);
		return false;
	};
		
	objRow.appendChild(objButton);
	
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = this.shortName(element.value, this.maxChar);
	objRow.appendChild(objRowValue);

	jQuery("#filelist_" + this.id).append(objRow);
	
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
	// Sortable.create("filelist_" + this.id, {tag:"div",only:"multifile",hoverclass:"sorthover",onUpdate:function(){objContentLanguage.sort(strId)}});
}

FileField.prototype.addCurrentRow = function(element) {
	var strId = this.id;
	var __this = this;
	
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
		__this.removeCurrentField(this);
		return false;
	};	
	objRow.appendChild(objButton);

	var arrValue = element.value.split(":");
	var labelValue = arrValue.shift();
	var fileValue = arrValue.shift();
	//*** Image thumbnail.
	if (this.thumbPath != "") {
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
			objThumb.rel = 'external';
			objThumb.href = __this.thumbPath + fileValue;
			objThumb.onmouseover = function() {
				return overlib('This file will open in a new window.');
			};
			objThumb.onmouseout = function() {
				return nd();
			};
		}
		objRow.appendChild(objThumb);
	}
	
	var objRowValue = document.createElement('p');
	objRowValue.innerHTML = labelValue;
	objRow.appendChild(objRowValue);

	jQuery("#filelist_" + this.id).append(objRow);
	
	//*** Check max files.
	if ((this.subFiles.toUpload.length + 1) + this.subFiles.currentFiles > this.maxFiles) {
		jQuery("#" + this.id + "_widget div.required").hide();
	}
}

FileField.prototype.removeUploadField = function(objTrigger) {	
	objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);
	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles.toUpload.length; intCount++) {
		if (this.subFiles.toUpload[intCount].value != objTrigger.parentNode.element.value) {
			arrTemp.push(this.subFiles.toUpload[intCount]);
		}
	}
	this.subFiles.toUpload = arrTemp;
	
	jQuery("#" + this.id + "_widget div.required").show();
	if (this.subFiles.toUpload.length == 0) {
		jQuery("#filelist_" + this.id).hide();
	}
}

FileField.prototype.removeCurrentField = function(objTrigger) {	
	var arrTemp = new Array();
	for (var intCount = 0; intCount < this.subFiles.uploaded.length; intCount++) {
		if (this.subFiles.uploaded[intCount].value != objTrigger.parentNode.element.value) {
			arrTemp.push(this.subFiles.uploaded[intCount]);
		}
	}
	this.subFiles.uploaded = arrTemp;
	this.subFiles.currentFiles--;
	
	objTrigger.parentNode.element.parentNode.removeChild(objTrigger.parentNode.element);
	objTrigger.parentNode.parentNode.removeChild(objTrigger.parentNode);
	
	if (this.subFiles.uploaded.length == 0) {
		jQuery("#filelist_" + this.id).hide();
	}
	jQuery("#" + this.id + "_widget div.required").show();
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

FileField.prototype.isImage = function(fileName) {
	var blnReturn = false;
	var extension = fileName.split(".").pop();
	var arrImages = ['jpg', 'jpeg', 'gif', 'png'];
	for (var count = 0; count < arrImages.length; count++) {
		if (arrImages[count] == extension) {
			blnReturn = true;
			break;
		}
	}
	
	return blnReturn;
}

FileField.prototype.sort = function() {
	var arrFields = Sortable.serialize('filelist_' + this.id).split("&");
	var objParent = jQuery("#" + this.id + "_widget");
	for (var intCount = 0; intCount < arrFields.length; intCount++) {
		var strTemp = arrFields[intCount].replace("filelist_" + this.id + "[]=", "");
		var objTemp = jQuery("#" + this.id + "_" + strTemp);
		if (objTemp) {
			objTemp.remove();
			objParent.append(objTemp);
		}
	}
}