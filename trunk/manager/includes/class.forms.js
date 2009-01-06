
/************************
* Forms Class.
*
* Note:
*   Requires the "prototype" library.
**/

function Forms() {
	//*** Construct the Form class;
}

Forms.serialize = function (strFormId) {
	var strReturn = "";
	var objForm = $(strFormId);
	var objLists = objForm.getElementsByTagName("ul");
	
	for (var i in objLists) {
		try {
			strReturn += Sortable.serialize(objLists[i].id) + "&";
		} catch (e) {
			//alert(e.message);
		}
	}

	strReturn += Form.serialize(strFormId);

	return strReturn;
}

Forms.selectAll = function(objSelect, blnSelect) {
	for (var n = 0; n < objSelect.length; n++) {
		switch (blnSelect) {
			case undefined:
			case true:
				objSelect[n].selected = true;
				break;

			case false:
				objSelect[n].selected = false;
				break;
		}
	}
}

Forms.clear = function(strFormId, arrExclude) {
	if (typeof arrExclude == 'undefined') {
		arrExclude = new Array();
	}
	
	var objForm = $(strFormId);
	if (objForm) {
		var objElements = objForm.elements;

		for (var n = 0; n < objElements.length; n++) {
			var objElement = objElements[n];

			if (!inObject(arrExclude, objElement.name, "")) {
				switch (objElement.type) {
					// Text fields, hidden form elements
					case 'text':
					case 'hidden':
					case 'password':
					case 'textarea':
						objElement.value = "";
						break;

					// Radio buttons, checkboxes
					case 'radio':
					case 'checkbox':
						objElement.checked = false;
						break;

					// Select lists
					case 'select-one':
					case 'select-multiple':
						for (var i = (objElement.length - 1); i >= 0; i--) {
							objElement.remove(i);
						}

						var objOptgroups = objElement.getElementsByTagName("optgroup");
						for (var i = (objOptgroups.length - 1); i >= 0; i--) {
							objElement.removeChild(objOptgroups[i]);
						}
						break;

				}
			}
		}
		
		var objElements = document.getElementsByClassName("widget", objForm);
		
		for (var n = 0; n < objElements.length; n++) {
			var objElement = objElements[n];

			if (!inObject(arrExclude, objElement.id, "")) {
				objElement.innerHTML = "";
			}
		}
	}
}

Forms.parseAjaxResponse = function(objResponse) {
	var blnReturn = true;
	
	//*** Fields.
	var objFields = objResponse.getElementsByTagName("field");
	
	for (var i = 0; i < objFields.length; i++) {
		var blnClear = true;
		
		if (objFields[i].attributes[0].name == "name") {
			var strField = objFields[i].attributes[0].value;
		} else {
			var blnClear = objFields[i].attributes[0].value;
			var strField = objFields[i].attributes[1].value;
		}

		try {
			var objField = $(strField);
			if (eval(blnClear)) {
				Forms.clearElementValue(objField);
			}
			
			if (objFields[i].childNodes.length > 1) {
				for (var j = 0; j < objFields[i].childNodes.length; j++) {
					try {
						var strSelected = objFields[i].childNodes[j].attributes[1].value;
					} catch (e) {
						var strSelected = false;
					}
					
					var strId = objFields[i].childNodes[j].attributes[0].value;
					var strValue = objFields[i].childNodes[j].firstChild.nodeValue;
					Forms.setElementValue(objField, strValue, strId, false, strSelected);
				}
			} else if (objFields[i].childNodes.length > 0) {
				switch (objFields[i].childNodes[0].nodeName) {
					case "value":
						try {
							var strId = objFields[i].childNodes[0].attributes[0].value;
						} catch(e) {
							var strId = "";
						}
						
						var strValue = objFields[i].childNodes[0].firstChild.nodeValue;
						Forms.setElementValue(objField, strValue, strId);
						break;
						
					case "fields":
						var objSubFields = objFields[i].childNodes[0].childNodes;

						for (var j = 0; j < objSubFields.length; j++) {
							var strValue = objSubFields[j].attributes[0].value;
							Forms.setElementValue(objField, strValue, "", true);

							for (var k = 0; k < objSubFields[j].childNodes.length; k++) {
								try {
									var strSelected = objFields[i].childNodes[j].attributes[1].value;
								} catch (e) {
									var strSelected = false;
								}
					
								var strId = objSubFields[j].childNodes[k].attributes[0].value;
								var strValue = objSubFields[j].childNodes[k].firstChild.nodeValue;
								Forms.setElementValue(objField, strValue, strId, false, strSelected);
							}
						}
						break;
						
				}
			}
		} catch (e) {
			//*** Could not find the element.
			//alert(e.message);
			blnReturn = false;
		}
	}
	
	//*** Widgets.
	var objWidgets = objResponse.getElementsByTagName("widget");
	
	for (var i = 0; i < objWidgets.length; i++) {
		var strWidget = objWidgets[i].getAttribute("name");
		var strContain = objWidgets[i].getAttribute("contain");

		try {
			var objWidget = $(strWidget);
			
			//*** Empty the widget.
			objWidget.innerHTML = "";
			
			//*** Fill the widget with values.
			var objValues = objWidgets[i].getElementsByTagName("value");
			
			for (var j = 0; j < objValues.length; j++) {
				var strValueName = objValues[j].getAttribute("name");
				var strValueText = objValues[j].firstChild.nodeValue;
				
				var objValue = document.createElement("li");
				objValue.setAttribute("id", strValueName);
				objValue.onmouseover = function() { Element.addClassName(this, 'hover'); };
				objValue.onmouseout = function() { Element.removeClassName(this, 'hover'); };
				var objText = document.createTextNode(strValueText);
				objValue.appendChild(objText);
				objWidget.appendChild(objValue);
			}
			
			//*** Refresh the Drag'n'Drop logic.
			if (strContain == null) {
				Sortable.create(strWidget, {tag:"li"});
			} else {
				Sortable.create(strWidget, {dropOnEmpty:true,hoverclass:'sorthover',containment:[strWidget, strContain],constraint:false});
			}
		} catch (e) {
			//*** Could not find the element.
			alert(e.message);
			blnReturn = false;
		}
	}
	
	return blnReturn;
}

Forms.clearElementValue = function(objField) {
	switch (objField.nodeName) {
		case "INPUT":
			switch (objField.type) {
				case "text":
				case "password":
				case "button":
				case "submit":
					objField.value = "";
					break;
					
				case "checkbox":
				case "radio":
					objField.checked = null;
					break;
			}
			break;
		
		case "SELECT":
			objField.innerHTML = null
			break;
		
		case "TEXTAREA":
			objField.value = "";
			break;

	}
	
	return null;
}

Forms.setElementValue = function(objField, strValue, strId, blnOpt, blnSelected) {
	switch (objField.nodeName) {
		case "INPUT":
			switch (objField.type) {
				case "text":
				case "password":
				case "button":
				case "submit":
				case "hidden":
					objField.value = strValue;
					break;
					
				case "checkbox":
				case "radio":
					if (strValue == "1") {
						objField.checked = "checked";
					} else {
						objField.checked = null;
					}
					break;
			}
			break;
		
		case "SELECT":
			if (blnOpt) {
				var objOptgroup = document.createElement("optgroup");
				objOptgroup.label = strValue;
				objField.appendChild(objOptgroup);
			} else {
				if (strId == "") {
					Forms.selectOption(objField, strValue);
				} else {
					var objOption = document.createElement("option");
					objOption.value = strId;
					objOption.innerHTML = strValue;
					if (blnSelected) {
						objOption.selected = "selected";
					}
					objField.appendChild(objOption);
				}
			}
			break;
		
		case "TEXTAREA":
			break;

	}
	
	return null;
}

Forms.selectOption = function(objSelect, strValue) {
	for (var n = 0; n < objSelect.length; n++) {
		if (objSelect[n].value == strValue) {
			objSelect[n].selected = true;
			break;
		}
	}
	
	return null;
}