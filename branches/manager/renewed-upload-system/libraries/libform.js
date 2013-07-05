<!--

/*************************************************************************/
/* LibForm
/*
/* A XHTML, CSS and DOM enabled form validator.
/* In short the validator will, in the case of an error, add a CSS class
/* to the "required" or "optional" class identifier and inject the error 
/* description into the form element division.
/* 
/* version: 0.0.5
/* author: Felix Langfeldt
/* (c)Phixel.org
/*************************************************************************/

//*** Validation type constantes.
var LIBFRM_STRING = 1;
var LIBFRM_TEXT = 2;
var LIBFRM_NUMERIC = 3;
var LIBFRM_INTEGER = 4;
var LIBFRM_WORD = 5;
var LIBFRM_EMAIL = 6;
var LIBFRM_PASSWORD = 7;

function ValidForms() {
	/********************/
	/* ValidForms Class **************************************************/
	/* 
	/* Holds forms and there elements that need input validation.
	/*********************************************************************/
	
	this.forms 		= new Object();
}

function ValidForm(strFormId) {
	/*******************/
	/* ValidForm Class ***************************************************/
	/* 
	/* Holds a form and its elements.
	/*********************************************************************/
	
	this.id = strFormId;
	this.elements = new Object();
}

function ValidFormElement(strFormId, strElementName, intValidType) {
	/**************************/
	/* ValidFormElement Class ********************************************/
	/* 
	/* Holds an element that can be validated.
	/*********************************************************************/
	
	this.formId			= strFormId;
	this.id 			= strElementName;
	this.name 			= strElementName;
	this.validType 		= intValidType;
	this.required 		= false;
	
	this.VALID_WORD 	= /^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ0-9_\/]*$/i;
	this.VALID_STRING 	= /^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ0-9\.\,\'\/:&"_ ]*$/i;
	this.VALID_TEXT 	= /^[-a-zàáâãäåæçèéêëìíîïðñòóôõöøùúûüýA-ZÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝ0-9\.\,\'\/:"@_?#^*!&() \n\r]*$/i;
	this.VALID_INTEGER 	= /^[0-9]*$/i;
	this.VALID_NUMERIC 	= /^[0-9,\.]*$/i;
	this.VALID_EMAIL 	= /^[^@\s]+@([-a-z0-9]+\.)+[a-z]{2,}$/i;
	this.VALID_PASSWORD	= /^[-A-Z0-9\.-_!@#$%^(&*?|]*$/i;
	
	if (ValidFormElement.arguments.length > 3) {
		this.required = ValidFormElement.arguments[3];
	}
}

ValidForms.prototype.addForm = function(objValidForm) {
	this.forms[objValidForm.id] = objValidForm;
};

ValidForms.prototype.form = function(strFormId) {
	return this.forms[strFormId];
};	

ValidForms.prototype.validate = function(strFormId) {
//	if (this.forms[strFormId]) {
//		return validateForm(strFormId);
//	} else {
//		return true;
//	}
	return true; // TODO: ValidForm Builder javascript validation
};

ValidForm.prototype.addElement = function(strElementName, intValidType) {
	var blnRequired = false;
	if (arguments.length > 2) {
		blnRequired = arguments[2];
	}
	this.elements[strElementName] = new ValidFormElement(this.id, strElementName, intValidType, blnRequired);
};

ValidForm.prototype.element = function(strElementName) {
	return this.elements[strElementName];
};

ValidFormElement.prototype.validate = function() {
	//*** Validate the element using the validType and required

	var objElement = document.getElementById(this.name);

	try {
		var value = objElement.value;

		/*** Redirect to error handler if a checkbox or radio is found.
				This is done for cross-browser functionality. */
		switch (objElement.type) {
			case 'radio':
			case 'checkbox':
				throw "Checkbox or radio button detected.";
				break;
		}

		//*** Required, but empty is not good.
		if (this.required && value == "") {
			return false;
		} else if (!this.required && value == "") {
			return true;
		}

		//*** Check specific types using regular expression.
		switch (this.validType) {
			case LIBFRM_STRING:
				return this.VALID_STRING.test(value);
				break;

			case LIBFRM_TEXT:
				return this.VALID_TEXT.test(value);
				break;

			case LIBFRM_NUMERIC:
				return this.VALID_NUMERIC.test(value);
				break;

			case LIBFRM_INTEGER:
				return this.VALID_INTEGER.test(value);
				break;

			case LIBFRM_WORD:
				return this.VALID_WORD.test(value);
				break;

			case LIBFRM_EMAIL:
				return this.VALID_EMAIL.test(value);
				break;

			case LIBFRM_PASSWORD:
				return this.VALID_PASSWORD.test(value);
				break;

		}
	} catch(e) {
		//*** Checkbox or radio button.
		var objForm = document.getElementById(this.formId);
		var blnChecked = false;

		for (var intIndex = 0; intIndex < objForm.elements.length; intIndex++) {
			var objElement = objForm.elements[intIndex];

			if (objElement.name == this.name) {
				if (objElement.checked) {
					blnChecked = true;
					break;
				} else if (!this.required) {
					blnChecked = true;
					break;
				}
			}
		}

		return blnChecked;
	}
};

//-->
