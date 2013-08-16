/**
 * Javascript Cookie handling library
 *
 * @author Robin van Baalen
 * @link http://felix-it.com
 * 
 * @version 1.0
 */

var Cookie = function () {
	var self = this
	
	this.set = function (name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}
	
	this.get = function (name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	
	this.unset = function (name) {
		self.set(name, "", -1)
	}
}
Cookie.prototype;

(function () {
	window._cookie = window._cookie || new Cookie()
})()
