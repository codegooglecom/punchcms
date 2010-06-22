/*
 * @package: jQuery debugging library.
 * @version: 0.3.2
 * @author: Robin van Baalen
 * 
 * Note: 
 *   This library is dependant on the "jQuery" library.
 *   
 * Changelog: 
 *  22/06	- Both "warn" and "warning" are allowed debug types now.
 * 	16/06	- Implemented all Firebug's logging methods: info, error, warn, debug and log.
 * 	16/06	- Replaced jQuery.browser.mozilla for check if console is object
 *  09/06 	- Added listHandlers function for event handler debugging
 *  18/05 	- Added basic multi browser suppport
 */

//*** Enable the debugger:
window.debug = true;

/*
 * Debug function
 */
jQuery.extend( {
	debug: function() {
		var args 	= arguments[0] || {};
	    var title 	= args.title || "";
	    var content = args.content;
	    var type 	= args.type || "log";
	    
    	if(window.debug){
			if(typeof console == "object"){
				switch(type){
					case "info":
						if(title !== "") console.info(title + ":");
						console.info(content);
						break;
					case "error":
						if(title !== "") console.error(title + ":");
						console.error(content);
						break;
					case "warn":
					case "warning":
						if(title !== "") console.warn(title + ":");
						console.warn(content);
						break;
					case "debug":
						if(title !== "") console.debug(title + ":");
						console.debug(content);
						break;
					default:
						if(title !== "") console.log(title + ":");
						console.log(content);
						break;
				}
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

/*
 * List event handlers function
 */
jQuery.fn.listHandlers = function(events, outputFunction) {
    return this.each(function(i){
        var elem 	= this,
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