/************************
* User Class.
*
* Note:
*   Requires the "prototype" library.
**/

function User() {	
	//*** Construct the User class;
}

User.remove = function(intId) {
	var strUrl = "ajax.php";
	var objPost = { cmd: "User::remove", perm_user_id: intId };

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, objPost, User.refresh, "xml");
}

User.load = function(intId) {
	var strUrl = "ajax.php";
	var objPost = {cmd: "User::load", perm_user_id: intId };

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, objPost, User.show, "xml");
}

User.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=User::add";

	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.post(strUrl, strPost, User.refresh, "xml");
}

User.show = function(objResponse, strHeader) {
	jQuery("#userProgress").fadeOut("fast", function(){
		Forms.parseAjaxResponse(objResponse);
	});
	User.sortable();
}

/*
 * Test function to refresh the sortable functionality
 */
User.sortable = function(){
	jQuery("#rights, #allrights, #groups, #allgroups").sortable("refresh");
}

User.refresh = function(objResponse, strHeader) {
	history.go(0);
}

User.clearForm = function(strForm) {
	var strUrl = "ajax.php";
	var objPost = { cmd: "User::clearForm" };

	Forms.clear(strForm, ['perm_type']);
	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.post(strUrl, objPost, User.show, "xml");
}