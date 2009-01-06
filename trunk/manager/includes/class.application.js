
/************************
* Application Class.
*
* Note:
*   Requires the "prototype" and "scriptacoulus" library.
**/

function Application() {	
	//*** Construct the Application class;
}

Application.remove = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Application::remove&application_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Application.refresh
			});
}

Application.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Application::load&application_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Application.show
			});
}

Application.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Application::add";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'post', 
				parameters: strPost, 
				onComplete: Application.refresh
			});

}

Application.show = function(objResponse, strHeader) {
	Element.hide('userProgress');
	Forms.parseAjaxResponse(objResponse.responseXML);
	
	//alert(objResponse.responseText);
}

Application.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Application.clearForm = function(strForm) {
	Forms.clear(strForm);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=Application::clearForm";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Application.show
			});
}
