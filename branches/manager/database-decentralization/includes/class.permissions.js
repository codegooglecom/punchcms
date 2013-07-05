/**************************************************************************
 * PermissionList Class.
 *************************************************************************/

/*** 
 * PermissionList object.
 */
var PermissionList = new function() {
	this.version = '1.0.0';
	this.hasCtrl = false;
	this.permissions = [];
	this.PERM = {
			1: 'browse',
			2: 'read',
			3: 'write',
			4: 'create',
			5: 'change',
			6: 'fullcontrol'
		};
	this.MREP = {
			browse: 1,
			read: 2,
			write: 3,
			create: 4,
			change: 5,
			fullcontrol: 6
		};
}

PermissionList.load = function() {
	jQuery("div.selectList a").bind("click", PermissionList.toggleList);
	jQuery("div.selectList").bind("click", PermissionList.untoggleList);
	jQuery("div.permissions input").bind("click", PermissionList.updatePermissions);
	jQuery(document).live("keydown", PermissionList.setKey);
	jQuery(document).live("keydown", PermissionList.unsetKey);
}

PermissionList.setKey = function(event) {
	if (event.keyCode == 17) PermissionList.hasCtrl = true;
}

PermissionList.unsetKey = function(event) {
	if (event.keyCode == 17) PermissionList.hasCtrl = false;
}

PermissionList.untoggleList = function(event) {
	if (!PermissionList.hasCtrl) {
		jQuery("div.selectList a").each(function(){
			jQuery(this).removeClass("on");
		});
		PermissionList.clearPermissions();
	}
}

PermissionList.toggleList = function(event) {
	var blnShow 	= true;
	var objElement 	= Event.element(event);
	
	if (objElement.hasClassName('on')) blnShow = false;
	PermissionList.untoggleList(event);
	
	if (blnShow) {
		objElement.addClassName('on').blur();
		if (!PermissionList.hasCtrl) {
			PermissionList.setPermissions(objElement.id.split("_").pop());
		}
	} else {
		objElement.removeClassName('on').blur();
		if (!PermissionList.hasCtrl) {
			PermissionList.clearPermissions();
		}
	}
	
	//*** Stop further event handling.
	Event.stop(event);
}

PermissionList.setPermissions = function(intId) {
	PermissionList.clearPermissions();
	PermissionList.permissions.each(function(obj){
		if (intId == obj.id) {
			//*** Set checkboxes.
			obj.permissions.each(function(int){
				jQuery("#perm_" + PermissionList.PERM[int]).attr("checked","checked");
			});
			throw $break;
		}
	});
}

PermissionList.clearPermissions = function() {
	for (var intCount in PermissionList.PERM) {
		jQuery("#perm_" + PermissionList.PERM[intCount]).removeAttr("checked");
	};
}

PermissionList.updatePermissions = function() {
	jQuery("div.selectList a.on").each(function(){
		var intId = jQuery(this).attr("id").split("_").pop();
		var arrPermissions = [];
		
		jQuery("div.permissions input").each(function(){
			if (jQuery(this).is(":checked")) {
				arrPermissions.push(PermissionList.MREP[jQuery(this).attr("id").split("_").pop()]);
			}
		});
		
		var arrTemp = [];
		PermissionList.permissions.each(function(){
			if (intId != jQuery(this).attr("id")) {
				arrTemp.push(this);
			}
		});
		
		var objPermission = {id: intId, permissions: arrPermissions};
		arrTemp.push(objPermission);
		
		jQuery("#perm_" + intId).next().val(arrPermissions.join(","));
		
		PermissionList.permissions = arrTemp;
	});
}

PermissionList.addPermission = function(intId) {
	var arrPermissions = jQuery("#perm_" + intId).next().val().split(",");
	var objPermission = {id: intId, permissions: arrPermissions};
	PermissionList.permissions.push(objPermission);
}

/*** 
 * Init the permissions.
 */
jQuery(function(){
	PermissionList.load();
});