/*
 * @package: jQuery debugging library.
 * @version: 0.3.4
 * @author: Robin van Baalen
 * 
 * Note: 
 *   This library is dependant on the "jQuery" library.
 *   
 * Changelog: 
 * 	07/07	- Faster debugging for jQuery objects.
 *  24/06	- For faster debugging, no more need for an object of arguments. Also introduced multiline debug messages. 
 *  22/06	- Both "warn" and "warning" are allowed debug types now.
 * 	16/06	- Implemented all Firebug's logging methods: info, error, warn, debug and log.
 * 	16/06	- Replaced jQuery.browser.mozilla for check if console is object
 *  09/06 	- Added listHandlers function for event handler debugging
 *  18/05 	- Added basic multi browser suppport
 */

//*** Enable the debugger:
window.debug = false;

/*
 * Debug function
 */
jQuery.extend({
	debug: function() {
		if(typeof arguments[0] == "object" && !(arguments[0] instanceof jQuery)){
			var args 	= arguments[0] || {};
			var content = args.content;
		}
		else {
			var args 	= {content: arguments[0]};
			var content = args.content;
		}
		
		var title 	= args.title || "";
		var type 	= args.type || "log";
	    
    	if(window.debug){
			if(typeof console == "object"){
				var strContent = (title !== "") ? title + ":\n" + content : content;
				
				switch(type){
					case "info":
						console.info(strContent);
						break;
					case "error":
						console.error(strContent);
						break;
					case "warn":
					case "warning":
						console.warn(strContent);
						break;
					case "debug":
						console.debug(strContent);
						break;
					default:
						console.log(strContent);
						break;
				}
			}
			else { // Most basic debugging
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