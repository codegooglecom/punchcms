/*
 * Debug library to easily enable and disable debug functions.
 * @version 0.2
 * @author Robin van Baalen
 * 
 * Note: 
 *   This library is dependant on the "jQuery" library.
 *   
 * Changelog: 
 *   18/05 - Added basic multi browser suppport
 */

//*** Enable the debugger:
window.debug = true;

jQuery.extend( {
	debug: function() {
		var args = arguments[0] || {}; // It's your object of arguments
	    var title = args.title || "";
	    var content = args.content;
	    
    	if(window.debug){
			if(jQuery.browser.mozilla){
				if(title !== "") console.log(title + ":");
					console.log(content);
			}
			else {
				var strAlert = "";
				if(title !== "") strAlert += title + ":\n\n";
				strAlert += content + "\n";
				alert(strAlert);
			}
       	}
	}
});