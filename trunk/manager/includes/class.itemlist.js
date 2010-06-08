
/************************
* Itemlist Class.
*
* Note:
*   Requires the "prototype" library.
**/

function Itemlist() {	
	//*** Construct the Itemlist class;
}

Itemlist.init = function() {
//	Position.includeScrollOffsets = true;
	Itemlist.createSortable();
}

Itemlist.createSortable = function() {
	jQuery("#itemlist").sortable({
		update: initUpdateSort,
		axis: "y",
		forceHelperSize: true,
		containment: ".wrap"
	});
	var items = jQuery( "#itemlist" ).sortable( "option", "items" );

	jQuery.debug({title: "items", content: items});
	jQuery("#itemlist").disableSelection();
}

Itemlist.getChecked = function() {
	var arrChecked = new Array();
	
	//*** Get all checkbox fields.
	arrCheckbox = document.getElementsByClassName("multiitem");

	//*** Loop through the fields to find the checked ones.
	for (var i = 0; i < arrCheckbox.length; i++) {
		if (arrCheckbox[i].checked) {
			var strId = arrCheckbox[i].id.substr(5);
			arrChecked.push(strId);
		}
	}
	
	return arrChecked;
}