/*
 * Debug library to easily enable and disable debug functions.
 * @version 0.3
 * @author Robin van Baalen
 * 
 * Note: 
 *   This library is dependant on the "jQuery" library.
 *   
 * Changelog: 
 *   09/06 - Added listHandlers function for event handler debugging
 *   18/05 - Added basic multi browser suppport
 */

//*** Enable the debugger:
window.debug = false;

jQuery.extend( {
	debug: function() {
		var args = arguments[0] || {};
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

jQuery.fn.listHandlers = function(events, outputFunction) {
    return this.each(function(i){
        var elem = this,
            dEvents = $(this).data("events");
        
        if (!dEvents) {return;}
        $.each(dEvents, function(name, handler){
            if((new RegExp("^(" + (events === "*" ? ".+" : events.replace(",","|").replace(/^on/i,"")) + ")$" ,"i")).test(name)) {
               $.each(handler, function(i,handler){
                   outputFunction(elem, "\n" + i + ": [" + name + "] : " + handler );
               });
           }
        });
    });
};