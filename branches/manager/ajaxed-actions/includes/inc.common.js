var intSortId;
var intSortedId;
var strSortElement;

jQuery(function(){
	init(); 
	jQuery(".ui-state-highlight, .ui-state-error")
		.css("cursor", "pointer")
		.live("click", function(){
			jQuery(this).fadeOut("slow");
		});
    
    $('.input-ckeditor').each(function(){
        CKEDITOR.replace($(this).attr('id'), {
            toolbar: [
                ['Source'],
                ['Cut','Copy','Paste','PasteText','-','Table'],
                ['Undo','Redo','-','Find','Replace','-','Link','Unlink','Anchor','-','SpecialChar'],
                ['Bold','Italic','-','Subscript','Superscript','-','NumberedList','BulletedList','-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','TextColor','FontSize','Maximize']
            ],
            width: 500,
            language: 'en',
            filebrowserWindowWidth: '335',
            filebrowserWindowHeight: '480'

        });
        
        var ckeditorinst = CKEDITOR.instances[$(this).attr('id')];
        
        ckeditorinst.on('blur', function(){
            ckeditorinst.updateElement();
        });
    });
    
    $('a.select').live("click", function(e){
        e.preventDefault();
        
        window.opener.CKEDITOR.tools.callFunction(funcnum, $(this).attr('href'))
        window.close();
    });
});

function init() {
	//*** Initiate page.
//	externalLinks();
	
	//*** Hide the progress animation.
	if (jQuery('#userProgress').length > 0) jQuery('#userProgress').hide();
	
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
	//*** This is needed by the updated PElement library which dynamically reloads the #itemlist element.
	$("#itemlist")
		.on("click", ".itembox", function (event) {
			return toggleItemBox(this, event);
		})
		.on("mousedown", ".itembox", pauseUpdateSort)
		.on("mouseup", ".itembox", restartUpdateSort);
}

function obtrudeForm() {
	//*** Set onsubmit event for all forms.
	if (objValidForms) {
		for (var i = 0; i < document.forms.length; i++) {
			document.forms[i].onsubmit = function() {
				if (typeof objContentLanguage != "undefined") objContentLanguage.toTemp();
				if (typeof objMetaLanguage != "undefined") objMetaLanguage.toTemp();
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
				var objElmnts = $(".on", objItem).get();
				for (var intCount = 0; intCount < objElmnts.length; intCount++) {
					objElmnts[intCount].className = "off";
				}
			} else {
				objCheckBox[0].checked = true;
				objCheckBox[0].defaultChecked = true;
				var objElmnts = $(".off", objItem).get();
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
	strSortElement = this.id;
	intSortId = setTimeout("submitUpdateSort()", 500);
}

function submitUpdateSort() {
	//*** Submit the sorting of the items via Ajax.
	intSortedId = intSortId;
	var strPage = document.location.href;
	var strData = "cmd=12&" + jQuery("#" + strSortElement).sortable("serialize", {key: "itemlist[]"});

	jQuery.get(strPage, strData, function(data){
		if (typeof objTree == "object") {
			objTree.refreshItem(jQuery.query.get("eid"));
		}
	});
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