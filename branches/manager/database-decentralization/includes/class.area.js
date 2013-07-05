
/************************
* Area Class.
*
* Note:
*   Requires the "prototype" library.
**/

function Area() {	
	//*** Construct the User class;
}

Area.remove = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Area::remove&area_id=" + intId;

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Area.refresh, "xml");
}

Area.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Area::load&area_id=" + intId;

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Area.show, "xml");
}

Area.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Area::add";

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.post(strUrl, strPost, Area.refresh, "xml");
}

Area.show = function(objResponse, strHeader) {
	jQuery("#userProgress").fadeOut("fast", function(){
		Forms.parseAjaxResponse(objResponse);
	});
}

Area.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Area.clearForm = function(strForm) {
	Forms.clear(strForm, ['application_id']);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=Area::clearForm";

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Area.show, "xml");
}