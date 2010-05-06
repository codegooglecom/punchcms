/*
 * Debug library to easily enable and disable debug functions.
 * @version 0.1
 * @author Robin van Baalen
 * 
 * Note: 
 *   This library is dependant on the "jQuery" library.
 */

//*** Enable the debugger:
window.debug = true;

jQuery.extend( {
	debug: function() {
		var args = arguments[0] || {}; // It's your object of arguments
	    var title = args.title || 0;
	    var content = args.content;
	    
    	if(window.debug){
    		if(title !== 0)	console.log(title + ":");
	    	console.log(content);
    	}
	}
});