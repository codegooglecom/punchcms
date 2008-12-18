
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

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Area.refresh
			});
}

Area.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Area::load&area_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Area.show
			});
}

Area.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Area::add";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'post', 
				parameters: strPost, 
				onComplete: Area.refresh
			});

}

Area.show = function(objResponse, strHeader) {
	Element.hide('userProgress');
	Forms.parseAjaxResponse(objResponse.responseXML);
	
	//alert(objResponse.responseText);
}

Area.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Area.clearForm = function(strForm) {
	Forms.clear(strForm, ['application_id']);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=Area::clearForm";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Area.show
			});
}
