
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
	var strPost = "cmd=User::remove&perm_user_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: User.refresh
			});
}

User.load = function(intId) {
	var strUrl = "ajax.php";
	var strPost = "cmd=User::load&perm_user_id=" + intId;

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: User.show
			});
}

User.write = function(strFormId) {
	var strUrl = "ajax.php";
	var strPost = Forms.serialize(strFormId) + "&cmd=User::add";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'post', 
				parameters: strPost, 
				onComplete: User.refresh
			});

}

User.show = function(objResponse, strHeader) {
	Element.hide('userProgress');
	Forms.parseAjaxResponse(objResponse.responseXML);
	
	//alert(objResponse.responseText);
}

User.refresh = function(objResponse, strHeader) {
	history.go(0);
}

User.clearForm = function(strForm) {
	Forms.clear(strForm, ['perm_type']);
	
	var strUrl = "ajax.php";
	var strPost = "cmd=User::clearForm";

	Element.show('userProgress');
	var myAjax = new Ajax.Request(
			strUrl, 
			{
				method: 'get', 
				parameters: strPost, 
				onComplete: User.show
			});
}