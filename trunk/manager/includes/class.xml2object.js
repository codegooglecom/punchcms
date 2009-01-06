
var XML2Object = {
	parse: function(objResponse) {
		var objReturn = {fields:new Array(),command:''};
		var objFields = objResponse.getElementsByTagName("field");
		
		for (var i = 0; i < objFields.length; i++) {
			if (objFields[i].attributes[0].name) {
				var objValues = objFields[i].getElementsByTagName("value");
		
				for (var j = 0; j < objValues.length; j++) {
					objReturn.fields[objFields[i].attributes[0].value] = objValues[j].firstChild.nodeValue;
				}
			}
		}
		
		var objCommand = objResponse.getElementsByTagName("command");
		if (objCommand && objCommand.length > 0) {
			if (objCommand[0].attributes[0].name) {
				objReturn.command = objCommand[0].attributes[0].value;
			}			
		}
		
		return objReturn;
	}
}
