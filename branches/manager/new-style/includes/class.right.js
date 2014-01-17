
/************************
* Right Class.
*
* Note:
*   Requires the "prototype" library.
**/

function Right() {	
	//*** Construct the Right class;
}

Right.remove = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Right::remove&right_id=" + intId;

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Right.refresh, "xml");
}

Right.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Right::load&right_id=" + intId;

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Right.show, "xml");
}

Right.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Right::add";

	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.post(strUrl, strPost, Right.refresh, "xml");
}

Right.show = function(objResponse, strHeader) {
	jQuery("#userProgress").fadeOut("show", function(){
		Forms.parseAjaxResponse(objResponse);
	});
}

Right.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Right.clearForm = function(strForm) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Right::clearForm";

	Forms.clear(strForm, ['area_id']);
	jQuery("#userProgress").fadeIn("show");
	var request = jQuery.get(strUrl, strPost, Right.show, "xml");
}
