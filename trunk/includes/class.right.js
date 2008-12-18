
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

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Right.refresh
			});
}

Right.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=Right::load&right_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Right.show
			});
}

Right.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=Right::add";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'post', 
				parameters: strPost, 
				onComplete: Right.refresh
			});

}

Right.show = function(objResponse, strHeader) {
	Element.hide('userProgress');
	Forms.parseAjaxResponse(objResponse.responseXML);
	
	//alert(objResponse.responseText);
}

Right.refresh = function(objResponse, strHeader) {
	history.go(0);
}

Right.clearForm = function(strForm) {
	Forms.clear(strForm, ['area_id']);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=Right::clearForm";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: Right.show
			});
}
