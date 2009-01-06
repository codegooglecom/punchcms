
/*
###########################################################################
#  Copyright (c) 2006 Phixel.org (http://www.phixel.org)
#
#  Permission is hereby granted, free of charge, to any person obtaining
#  a copy of this software and associated documentation files (the
#  "Software"), to deal in the Software without restriction, including
#  without limitation the rights to use, copy, modify, merge, publish,
#  distribute, sublicense, and/or sell copies of the Software, and to
#  permit persons to whom the Software is furnished to do so, subject to
#  the following conditions:
#
#  The above copyright notice and this permission notice shall be
#  included in all copies or substantial portions of the Software.
#
#  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
#  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
#  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
#  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
#  LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
#  OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
#  WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
###########################################################################
*/

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
	$$("div.selectList a").invoke('observe', 'click', PermissionList.toggleList);
	$$("div.selectList").invoke('observe', 'click', PermissionList.untoggleList);
	$$("div.permissions input").invoke('observe', 'click', function(){ PermissionList.updatePermissions() });
	Event.observe(document, 'keydown', PermissionList.setKey);
	Event.observe(document, 'keyup', PermissionList.unsetKey);
}

PermissionList.setKey = function(event) {
	if (event.keyCode == 17) PermissionList.hasCtrl = true;
}

PermissionList.unsetKey = function(event) {
	if (event.keyCode == 17) PermissionList.hasCtrl = false;
}

PermissionList.untoggleList = function(event) {
	if (!PermissionList.hasCtrl) {
		$$('div.selectList a').each(function(obj){
			$(obj).removeClassName('on');
		});
		PermissionList.clearPermissions();
	}
}

PermissionList.toggleList = function(event) {
	var blnShow = true;
	var objElement = Event.element(event);
	
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
				$("perm_" + PermissionList.PERM[int]).checked = true;
			});
			throw $break;
		}
	});
}

PermissionList.clearPermissions = function() {
	for (var intCount in PermissionList.PERM) {
		$("perm_" + PermissionList.PERM[intCount]).checked = false;
	};
}

PermissionList.updatePermissions = function() {
	$$('div.selectList a.on').each(function(obj){
		var intId = obj.id.split("_").pop();
		var arrPermissions = [];
		
		$$("div.permissions input").each(function(objInput){
			if (objInput.checked) {
				arrPermissions.push(PermissionList.MREP[objInput.id.split("_").pop()]);
			}
		});
		
		var arrTemp = [];
		PermissionList.permissions.each(function(obj){
			if (intId != obj.id) {
				arrTemp.push(obj);
			}
		});
		
		var objPermission = {id: intId, permissions: arrPermissions};
		arrTemp.push(objPermission);
		
		$("perm_" + intId).nextSiblings().pop().value = arrPermissions.join(",");
		
		PermissionList.permissions = arrTemp;
	});
}

PermissionList.addPermission = function(intId) {
	var arrPermissions = $("perm_" + intId).nextSiblings().pop().value.split(",");
	var objPermission = {id: intId, permissions: arrPermissions};
	PermissionList.permissions.push(objPermission);
}

/*** 
 * Init the permissions.
 */
Event.observe(window, 'load', PermissionList.load);
