
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
	var strPost = "cmd=Group::remove&group_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Group.refresh
			});
}

Group.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Group::load&group_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Group.show
			});
}

Group.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Group::add";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'post', 
				parameters: strPost, 
				onComplete: Group.refresh
			});

}

Group.show = function(objResponse, strHeader) {
	Element.hide('userProgress');
	Forms.parseAjaxResponse(objResponse.responseXML);
	
	//alert(objResponse.responseText);
}

Group.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Group.clearForm = function(strForm) {
	Forms.clear(strForm, ['right_level']);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=Group::clearForm";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Group.show
			});
}
