var intSortId;
var intSortedId;
var strSortElement;

Event.observe(window, 'load', function() { 
	init(); 
});

function init() {
	//*** Initiate page.
	externalLinks();
	
	//*** Hide the progress animation.
	if ($('userProgress')) Element.hide('userProgress');
	
	try {
		obtrudeItemBox();
	} catch(e) {
		//alert(e.message);
	}

	try {
		obtrudeForm();
	} catch(e) {
		//alert(e.message);
	}

	try {
		focusLogin();
	} catch(e) {
		//alert(e.message);
	}

	try {
		loadTree();
	} catch(e) {
		//alert(e.message);
	}
	
	try {
		loadAnnouncement();
	} catch(e) {
		//alert(e.message);
	}
	
	//*** Show page load duration.
	//var intNow = new Date();
	//alert((intNow - intTime) / 1000);
}

function externalLinks() {
	var objCurrent;
	var objReplacement;

	if (document.getElementsByTagName) {
		var objAnchors = document.getElementsByTagName("a");
		for (var iCounter=0; iCounter<objAnchors.length; iCounter++) {
			//*** Check for internal links and correct them
			if (objAnchors[iCounter].getAttribute("href")) {
				var strHref = objAnchors[iCounter].getAttribute("href");
				
				//*** fix anchors
				if (strHref.indexOf("#") > -1 && strHref.length > 1) {
					//*** add the "rel" attribute if not already available
					if (!objAnchors[iCounter].getAttribute("rel")) {
						objAnchors[iCounter].setAttribute("rel", "internal");
					}
				
					var strPageUri = document.location.href.split("#")[0];
					var arrHref = strHref.split("/");
					strHref = arrHref[arrHref.length - 1];
					objAnchors[iCounter].setAttribute("href", strPageUri + strHref);
				}
				
				//*** fix hrefs who point to local files
				if (strHref.indexOf("://") > -1 && strHref.length > 1) {
					//*** add the "rel" attribute if not already available
					if (!objAnchors[iCounter].getAttribute("rel")) {
						objAnchors[iCounter].setAttribute("rel", "internal");
					}
				}
			}
			
			//*** Create external links
			if (objAnchors[iCounter].getAttribute("href") && objAnchors[iCounter].getAttribute("rel") != "internal") {
				objAnchors[iCounter].onclick = function(event){return launchWindow(this, event);}
				objAnchors[iCounter].onkeypress = function(event){return launchWindow(this, event);}
				if (document.replaceChild) {
					objCurrent = objAnchors[iCounter].firstChild;
					if (objCurrent.nodeType == 3) { // Text node
						objAnchors[iCounter].title = (objAnchors[iCounter].title != "") ? objAnchors[iCounter].title + " (opent in een nieuw venster)" : objCurrent.data + " opent in een nieuw venster";
					} else if (objCurrent.alt) { // Current element is an image
						objReplacement = objCurrent;
						objReplacement.alt = objCurrent.alt + " (opent in een nieuw venster)";
						try {
							objAnchors[iCounter].replaceChild(objReplacement, objCurrent);
						} catch(e){}
					}
				}
			}
		}
	}
}

function launchWindow(objAnchor, objEvent) {
	var iKeyCode;

	if (objEvent && objEvent.type == "keypress") {
		if (objEvent.keyCode) {
			iKeyCode = objEvent.keyCode;
		} else if (objEvent.which) {
			iKeyCode = objEvent.which;
		}
		
		if (iKeyCode != 13 && iKeyCode != 32) {
			return true;
		}
	}

	return !window.open(objAnchor);
}

function inObject(objArray, strValue, strProperty) {
	for (var n = 0; n < objArray.length; n++) {
		if (eval("objArray[n]" + strProperty) == strValue) {
			return true;
			break;
		}
	}

	return false;
}

function focusLogin() {
	var objLogin = document.getElementById("login");
	if (objLogin.tagName == "BODY") {
		document.getElementById("handle").focus();
	}
}

function obtrudeItemBox() {
	if (document.getElementsByTagName) {
		var objElmnts = document.getElementsByClassName("itembox");
		for (var intCount = 0; intCount < objElmnts.length; intCount++) {
			objElmnts[intCount].onclick = function(event){return toggleItemBox(this, event);};
			objElmnts[intCount].onmousedown = pauseUpdateSort;
			objElmnts[intCount].onmouseup = restartUpdateSort;
		}
	}
}

function obtrudeForm() {
	//*** Set onsubmit event for all forms.
	if (objValidForms) {
		for (var i = 0; i < document.forms.length; i++) {
			document.forms[i].onsubmit = function() {
				if (typeof objContentLanguage != "undefined") objContentLanguage.toTemp();
				return objValidForms.validate(this.id);
			};
		}
	}

	//*** Set onchange event for the template field type list.
	objTarget = document.getElementById("frm_field_type");
	if (objTarget) {
		objTarget.onchange = function() {
			PTemplate.fieldTypeChange(this);
		};
		objTarget.onblur = function() {
			PTemplate.fieldTypeChange(this);
			this.onblur = null;
		};
		objTarget.focus();
		objTarget.blur();
	}
}

function toggleItemBox(objItem, objEvent) {
	var intId;
	var objCheckBox;
	var blnDragged = true;

	//*** Are we being dragged?
	if (typeof(objItem._revert) == 'undefined' || objItem._revert == null) {
		blnDragged = false;
	} else {
		if (typeof(objItem._revert) == 'object') {
			if (objItem._revert.finishOn - objItem._revert.startOn <= 40) {
				blnDragged = false;
			}
		}
	}

	if (blnDragged == false) {
		objCheckBox = objItem.getElementsByTagName("input");
		if (objCheckBox && objCheckBox.length > 0) {
			if (objCheckBox[0].checked) {
				objCheckBox[0].checked = false;
				objCheckBox[0].defaultChecked = false;
				var objElmnts = document.getElementsByClassName("on", objItem);
				for (var intCount = 0; intCount < objElmnts.length; intCount++) {
					objElmnts[intCount].className = "off";
				}
			} else {
				objCheckBox[0].checked = true;
				objCheckBox[0].defaultChecked = true;
				var objElmnts = document.getElementsByClassName("off", objItem);
				for (var intCount = 0; intCount < objElmnts.length; intCount++) {
					objElmnts[intCount].className = "on";
				}
			}
		}
	}
}

function initUpdateSort() {
	//*** Clear any submition of item sorting.
	clearTimeout(intSortId);

	//*** Set the timeout for a delayed save of the item sorting.
	strSortElement = this.element.id;
	intSortId = setTimeout("submitUpdateSort()", 500);
}

function submitUpdateSort() {
	//*** Submit the sorting of the items via Ajax.
	intSortedId = intSortId;
	var strPost = document.location.href;
	
	/* Sanitize the request string by filtering out any previously
	 * sorted items.
	 */
	strPost = strPost.replace(/\&itemlist\[]=.*\&/ig, "&");
	strPost = strPost.replace(/\&itemlist\[]=.*/ig, "");
		
	objPost = strPost.toQueryParams();
		
	if (strPost.indexOf("?") > -1) {
		strPost += "&";
	} else {
		strPost += "?";
	}
	strPost += "cmd=12&" + Sortable.serialize(strSortElement);

	new Ajax.Request(strPost, {
		method: 'get',
		onSuccess: function(transport) {
			if (typeof objTree == "object") {
				objTree.refreshItem(objPost.eid);
			}
		}
	});
	
	//document.location.href = strPost;
}

function pauseUpdateSort() {
	//*** Pause any submition of item sorting.
	clearTimeout(intSortId);
}

function restartUpdateSort() {
	//*** Restart any submition of item sorting.
	if (intSortId != intSortedId) {
		intSortId = setTimeout("submitUpdateSort()", 500);
	}
}

function debugObject(strName) {
	obj = eval(strName);
	var temp = "";
	for (x in obj)
	temp += x + ": " + obj[x] + "\n";

	var objDebug = document.getElementById('holddebug');
	if (objDebug) {
		objDebug.innerHTML = temp;
		//objCopied = objDebug.createTextRange();
		//objCopied.execCommand("copy");
	}

	alert (temp);
}