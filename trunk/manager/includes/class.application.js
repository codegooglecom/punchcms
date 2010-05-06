
/************************
* Application Class
*
* Note:
*   Requires the "jQuery" library.
**/

function Application() {	
	//*** Construct the Application class;
}

Application.remove = function(intId) {
	var strUrl = "ajax.php";
	var strPost = { cmd: "Application::remove",	application_id: intId };

	jQuery("#userProgress").fadeIn("fast");
	jQuery.get(strUrl, strPost,	Application.refresh, "xml");
}

Application.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = {	cmd: "Application::load", application_id: intId	};
	
	jQuery("#userProgress").fadeIn("fast");
	jQuery.get(strUrl, strPost,	Application.show, "xml");
}

Application.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Application::add";
	jQuery.debug({title: "Application.write strPost", content: strPost});
	
	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.post(strUrl, strPost, Application.refresh, "xml");
}

Application.show = function(objResponse) {
	jQuery("#userProgress").fadeOut("fast", function(){
		Forms.parseAjaxResponse(objResponse);
	});
}

Application.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Application.clearForm = function(strForm) {
	var strUrl = "ajax.php";
	var strPost = { cmd: "Application::clearForm" };

	Forms.clear(strForm);
	jQuery("#userProgress").fadeIn("fast");
	var request = jQuery.get(strUrl, strPost, Application.show, "xml");
}