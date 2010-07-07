<!--

/*************************************************************************/
/* Application specific functions
/*************************************************************************/

function validateForm(strFormId) {
	/*************************/
	/* validateForm Function *********************************************/
	/* 
	/* Uses the ValidForms, ValidForm, ValidElement and FormAlerter objects
	/* to validate form elements.
	/*********************************************************************/
	
	var blnReturn 			= true;
	var arrMultyElements 	= new Array();
	var objAlerter 			= new FormAlerter(strFormId);

	//*** Set the form object.
	try {
		var $objForm = jQuery("#" + strFormId);
	} catch(e) {
		alert("Er ging iets mis bij het aanroepen van het formulier.\nFoutmelding: " + e.message);
	}
	
	if ($objForm.length > 0) {
		/*** Loop through the elements of the form and look for elements 
			  that need validation. */
		var formElements = objForm.elements;
		var intIndex;
		
		//*** Reset main error notifications.
		objAlerter.mainPop();
		
		//*** Get the ValidForm object
		var objValidForm = objValidForms.form(strFormId);
			
		if (objValidForm) {
			objAlerter.mainAlert = objValidForm.alerts.mainAlert;
	
			//*** Element loop.
			for (intIndex = 0; intIndex < formElements.length; intIndex++) {
				var objElement = formElements[intIndex];

				//*** Check for elements with the same name
				if (!arrMultyElements.inArray(objElement.name)) {
				
					//*** Check for radio and checkboxes
					if (objElement.type == "radio") {
						arrMultyElements.push(objElement.name);
					}

					//*** Reset error notifications.
					objAlerter.pop(objElement.name);

					var objValidElement = objValidForm.element(objElement.name);

					//*** Finally let's validate the input.	
					if (objValidElement) {
						if (!objValidElement.validate()) {
							blnReturn = false;
							objAlerter.push(objElement.name, objValidForm.alerts[objElement.name]);
						}	
					}
				}
			}
		}
				
	} else {
		alert("Het formulier werd niet gevonden.");
	}
	
	return blnReturn;
}

function FormAlerter(strFormId) {
	/*********************/
	/* FormAlerter Class *************************************************/
	/* 
	/* Display class used to push alerts/errors regarding form validation 
	/* to the browser.
	/*********************************************************************/
	
	this.id 				= strFormId;
	this.mainAlert			= "";
}

FormAlerter.prototype.push = function(strElementName, strAlert) {
	//*** Add alert to the element.

	//*** Create the main error notification.
	this.mainPush(this.mainAlert);

	//*** Find the parent div with the right class
	try {
		var objElement = document.getElementById(strElementName).parentNode;
		objParent = getParentByClass(objElement, "required");
		if (!objParent) {
			objParent = getParentByClass(objElement, "optional");
		}
	} catch(e) {
		//*** Checkbox or radio button.
		var objElement = document.getElementById(this.id)[strElementName];
		if (objElement) {
			var objElement = objElement[objElement.length - 1];
			var objParent = getParentByClass(objElement, "required");
			if (!objParent) {
				objParent = getParentByClass(objElement, "optional");
			}
		}
	}

	if (objParent) {
		//*** Add the "error" class to the current class.
		if (objParent.tagName.toLowerCase() == "div") {
			var strClass = objParent.className + " error";
			objParent.className = strClass;
		}

		//*** Add error description to the element.
		var objErrorElmnt = this.constructError(strAlert);
		var objFirstElmnt = objParent.firstChild;
		objParent.insertBefore(objErrorElmnt, objFirstElmnt);
	}
};

FormAlerter.prototype.pop = function(strElementName) {
	//*** Remove any alerts from the element.

	//*** Find the parent div with the right class
	if (strElementName) {
		try {
			var objElement = document.getElementById(strElementName).parentNode;
			objParent = getParentByClass(objElement, "required error");
			if (!objParent) {
				objParent = getParentByClass(objElement, "optional error");
			}
		} catch(e) {
			//*** Checkbox or radio button.
			var objElement = document.getElementById(this.id)[strElementName];
			if (objElement) {
				var objElement = objElement[objElement.length - 1];
				var objParent = getParentByClass(objElement, "required error");
				if (!objParent) {
					objParent = getParentByClass(objElement, "optional error");
				}
			}
		}

		if (objParent) {
			var objErrorElmnt = objParent.firstChild;

			//*** Remove the "error" class from the current class.
			if (objParent.tagName.toLowerCase() == "div") {
				var strClass = objParent.className;
				strClass = strClass.split("error").join(" ").trim();
				objParent.className = strClass;
			}

			//*** Remove error description from the element.
			if (objErrorElmnt.attr("class").toLowerCase() == "error") {
				objErrorElmnt.remove();
			}
		}
	}
};

FormAlerter.prototype.mainPush = function(strError) {
	//*** Add main alert to the document.

	var $objForm = jQuery("#" + this.id);
	var blnFound = false;
	
	try {
		blnFound = jQuery($objForm.next()).hasClass("error-main");
	} catch(e) {
		//alert(e.message);
	}
	
	if (!blnFound) {
		var objErrorElmnt = this.constructMainError(strError);
	
		objForm.insertBefore(objErrorElmnt, objForm.firstChild);
	}
	scroll(0,0);
};

FormAlerter.prototype.mainPop = function() {
	//*** Remove main alert from the document.

	var objForm = document.getElementById(this.id);
	var objElements = getElementsByClass("error-tr", objForm.parentNode, "div");

	try {
		var objErrorElmnt = objElements[objElements.length - 1];
		objForm.parentNode.removeChild(objErrorElmnt);
	} catch(e) {
		//*** Could not find the error element.
	}
};

FormAlerter.prototype.constructMainError = function(strError) {
	//*** Build the MainError HTML. ***************************************
	//*
	//* <div class="ui-widget">
	//* 	<div class="ui-state-error ui-corner-all">
	//* 		<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>{ERROR_MAIN}</p>
	//* 	</div>
	//* </div>
	//* 
	//* <div class="error-main">
	//*     <p>The error string.</p>
	//* </div>
	//*********************************************************************

	var $uiWidget 		= jQuery("<div/>",{ "class": "ui-widget" });
	var $uiStateError 	= jQuery("<div/>",{ "class": "ui-state-error ui-corner-all" });
	var $paragraph		= jQuery("<p/>", {"text": strError});
	var $uiIcon			= jQuery("<span/>", {
							"css": {
								"float":"left",
								"margin-right":"0.3em"
							},
							"class":"ui-icon ui-icon-alert"
						});
	
	var $objCurrent = $uiWidget.append($uiStateError.append($paragraph.append($uiIcon)));
	
//	var objCurrent = document.createElement("p");
//	var objText = document.createTextNode(strError); 
//	objCurrent.appendChild(objText);
//
//	var objDiv = document.createElement("div");
//	objDiv.className = "error-main";
//	objDiv.appendChild(objCurrent);
//	objCurrent = objDiv;

	return $objCurrent;
};

FormAlerter.prototype.constructError = function(strError) {
	//*** Build the element error description. ****************************
	//*
	//* <p class="error">The error string.</p>
	//*********************************************************************

	var objCurrent = document.createElement("p");
	objCurrent.className = "error";
	var objText = document.createTextNode(strError); 
	objCurrent.appendChild(objText);

	return objCurrent;
};

/*************************************************************************/
/* Library functions
/*************************************************************************/

function getParentByClass(objChild, strClassName) {
	try {
		if (objChild.className == strClassName) {
			return objChild;
		} else {
			var objParent = objChild.parentNode;
			if (objParent) {
				return getParentByClass(objParent, strClassName);
			} else {
				return null;
			}
		}
	} catch(e) {
		return null;
	}
}

function getElementsByClass(searchClass, node, tag) {
	var classElements = new Array();
	if (node == null) node = document;
	if (tag == null) tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};

String.prototype.trim = function () {
	var s = this.replace(/^\s*/, "");
	return s.replace(/\s*$/, "");    
};

//-->