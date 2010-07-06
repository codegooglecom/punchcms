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

/**************************************************************************
 * FeedLanguage Class.
 *************************************************************************/

/*** 
 * FeedLanguage object.
 */
var FeedLanguage = function() {
	this.version = '2.0';
	this.currentLanguage = 0;
	this.hover = false;
	this.defaultLanguage = 0;
	this.cascades = {};
	this.fields = {};
	this.fieldsWrapper = "#feedfields-wrapper";
	this.feedsWrapper = "#feedfields li";
	this.svgWrapper = "#svgbasics";
}

FeedLanguage.require = function(libraryName) {
	var $objScript = jQuery("<script></script>");
	
	objScript.attr({
		type: "text/javascript",
		src: libraryName
	});
	jQuery("head").append($objScript);

}

FeedLanguage.load = function() {
	if(typeof jQuery == "undefined")
		throw("FeedLanguage class requires the jQuery library >= 1.4.2");
}

FeedLanguage.prototype.init = function() {
	$(this.svgWrapper)
		.css( "height", $(this.fieldsWrapper).height())
		.css( "width", $(this.fieldsWrapper).width());
								
	$(this.feedsWrapper)
		.draggable({ revert: true, revertDuration: 0, containment: this.fieldsWrapper, opacity: 0.50 })
		.hover(
			function(){ $(this).addClass("hover"); }, 
			function(){ $(this).removeClass("hover"); }
		);
}

FeedLanguage.prototype.swap = function(languageId) {
	var $objImage 		= jQuery("#language_cascade"),
		$objButton		= $objImage.parent();
		
	this.toTemp();
	this.currentLanguage = languageId;
	
	//*** Check is current and default language is equal.
	if (this.currentLanguage == this.defaultLanguage) {
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
					objReturn = objFeedLanguage.buttonOver("cascadeElement", this);
					break;
				case "mouseout":
					objReturn = objFeedLanguage.buttonOut("cascadeElement", this);
					break;
				case "click":
					objReturn = objFeedLanguage.toggleCascadeElement();
					break;
			}
			
			return objReturn;
		});
	}
		
	$(this.svgWrapper).empty();
	
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.fields[count].cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

FeedLanguage.prototype.addField = function(fieldId, strCascades) {
	//*** Create and store the field object in the global fields array.
	var objField = new TemplateFeedField(fieldId, this, strCascades);
		
	this.fields[fieldId] = objField;
	this.toScreen(fieldId);
}

FeedLanguage.prototype.toScreen = function(fieldId) {
	this.fields[fieldId].toScreen();
}

FeedLanguage.prototype.toTemp = function(fieldId) {
	if (fieldId == undefined) {
		for (var intCount in this.fields) {
			this.fields[intCount].toTemp();
		}
	} else {
		this.fields[fieldId].toTemp();
	}
}

FeedLanguage.prototype.buttonOver = function(strButtonType, objImage, fieldId) {
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
	}
}

FeedLanguage.prototype.buttonOut = function(strButtonType, objImage, fieldId) {
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
	}
}

FeedLanguage.prototype.toggleCascadeElement = function() {
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
		
		$(this.feedsWrapper).draggable("enable");
	} else {
		if (this.hover) overlib("<?php echo $objLang->get("langElementCascade", "tip") ?>");
		jQuery("#language_cascade").attr("src", "images/lang_locked.gif");
		
		$(this.feedsWrapper).draggable("disable");
	}

	$(this.svgWrapper).empty();
	
	//*** Take action according to the state.
	for (var count in this.fields) {
		this.toggleCascadeState(this.fields[count].id, this.cascades[this.currentLanguage]);
		this.toScreen(this.fields[count].id);
	}
}

FeedLanguage.prototype.toggleCascadeField = function(fieldId) {
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

FeedLanguage.prototype.toggleCascadeState = function(fieldId, state) {	
	//*** Toggle object property.
	this.fields[fieldId].cascades[this.currentLanguage] = state;
	
	//*** Set the cascade input field.
	var strValue = this.fields[fieldId].getCascades();
	jQuery("#" + fieldId + "_cascades").val(strValue);
}

FeedLanguage.prototype.setFieldValue = function(fieldId, strValue) {
	jQuery("#" + fieldId + "_" + this.currentLanguage).val(strValue);
}

FeedLanguage.prototype.svgDrawLine = function(eTarget, eSource) {				
	var __this = this;
					
	setTimeout(function(){							
		var $source = eSource;
		var $target = eTarget;

		// origin -> ending ... from left to right
		var originX = $source.positionAncestor(__this.fieldsWrapper).left;
		var originY = $source.position().top + 15;
		
		var endingX = $target.positionAncestor(__this.fieldsWrapper).left + $target.width() + 10;
		var endingY = $target.position().top + 15;

		// draw lines
		var svg = $(__this.svgWrapper);
		
		var space = 20;
		var color = "#2C457C";
		
		// drawLine(X1, Y1, X2, Y2);
		svg.drawLine(originX, originY, originX - space, originY, { 'color': color, 'stroke': 2 }); // beginning		
		svg.drawLine(originX - space, originY, endingX + space, endingY, { 'color': color, 'stroke': 2 }); // diagonal line	
		svg.drawLine(endingX + space, endingY, endingX, endingY, { 'color': color, 'stroke': 2 }); // ending
	}, 10);
}

FeedLanguage.prototype.svgDrawLines = function() {
	$(this.svgWrapper).empty();
	for (var count in this.fields) {
		this.toScreen(this.fields[count].id);
	}
}		

/*** 
 * TemplateFeedField object.
 */
function TemplateFeedField(strId, objParent, strCascades) {
	this.id 		= strId || 0;
	this.parent		= objParent || null;
	this.cascades 	= {};
	
	if (strCascades != undefined) this.setCascades(strCascades);
	
	var __this = this;
	jQuery("#" + this.id).droppable({
		tolerance: 'pointer',
		activeClass: 'ui-state-active',
		hoverClass: 'ui-state-highlight',
		drop: function(event, ui) {
			var arrId = $(ui.draggable).attr("id").split("_");
			jQuery("#" + this.id + "_" + __this.parent.currentLanguage).val(arrId.pop());
			__this.parent.svgDrawLines();
		}
	});
}
	
TemplateFeedField.prototype.getCascades = function() {
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
	
TemplateFeedField.prototype.setCascades = function(strCascades) {
	var arrCascades = strCascades.split(",");

	this.cascades = {};
	for (var count = 0; count < arrCascades.length; count++) {
		this.cascades[arrCascades[count]] = true;
	}
	jQuery("#" + this.id + "_cascades").val(this.getCascades());
}

TemplateFeedField.prototype.toScreen = function() {
	var __this = this;
	
	//*** Insert value into the field.
	if (this.cascades[this.parent.currentLanguage] == true) {
		//*** The field is cascading.
		jQuery("#" + this.id).droppable('disable');
		var $source =  jQuery("#ff_" + jQuery("#" + this.id + "_" + this.parent.defaultLanguage).val());
		if ($source.length > 0) {
			this.svgDrawLine($source);
		}
				
		$(this.parent.feedsWrapper).draggable("disable");
	} else {
		//*** The field needs no special treatment.
		var $source = jQuery("#ff_" + jQuery("#" + this.id + "_" + this.parent.currentLanguage).val());
		if ($source.length > 0) {
			this.svgDrawLine($source);
		}
		
		jQuery("#" + this.id).droppable('enable');
		$(this.parent.feedsWrapper).draggable("enable");
	}
	
	return true;
}

TemplateFeedField.prototype.svgDrawLine = function($source) {
	this.parent.svgDrawLine(jQuery("#" + this.id), $source);
}

TemplateFeedField.prototype.toTemp = function() {
	return true;
}