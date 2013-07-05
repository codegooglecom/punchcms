
/************************
* Group Class.
*
* Note:
*   Requires the "prototype" library.
**/

function Group() {	
	//*** Construct the User class;
}

Group.remove = function(intId) {
	var strUrl = "ajax.php";
	var strPost = {cmd: "Group::remove", group_id: intId };

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, strPost, Group.refresh, "xml");
}

Group.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Group::load&group_id=" + intId;

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, strPost, Group.show, "xml");
}

Group.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Group::add";

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.post(strUrl, strPost, Group.refresh, "xml");
}

Group.show = function(objResponse, strHeader) {
	jQuery("#userProgress").fadeOut("fast", function(){
		Forms.parseAjaxResponse(objResponse);
	});
}

Group.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Group.clearForm = function(strForm) {
	var strUrl = "ajax.php";
	var strPost = { cmd: "Group::clearForm" };

	Forms.clear(strForm, ["right_level"]);
	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, strPost, Group.show, "xml");
}