jQuery(function ($) {
	var $objTable = $("#auditLogTable");
	var $objHeader = $("thead", $objTable);
	var $objContent = $("tbody", $objTable);
	
	$.get("/ajax.php?cmd=AuditLog::getLastRecordsJson&params=100")
		.success(function (data) {
			$objHeader.find("tr").remove();
			$objContent.find("tr").remove();
			
			var objItems = $.parseJSON($(data).find("command").text());
			
			for (var i = 0; i <= objItems.length; i++) {
				var objItem = objItems[i];
				var $objContentRow = $objContent.append("<tr />");
				
				if (i == 0) {
					var $objHeaderRow = $objHeader.append("<tr />");
				}
				
				for (var property in objItem) {
					if (objItem.hasOwnProperty(property)) {
						
						if (i == 0) {
							$objHeaderRow
								.append("<th>" + property.charAt(0).toUpperCase() + property.slice(1) + "</th>");
						}
						
						$objContentRow
							.append("<td>" + objItem[property] + "</td>");
						
					}
				}
			}
			
			$objTable.fadeIn();
		})
		.error(function () {
			var $objContentRow = $objContent.append("<tr />");
			var $objHeaderRow = $objHeader.append("<tr />");

			$objHeaderRow.append("<th>Failed to load data</th>");
			$objContentRow.append("<td>We were unable to load the audit log data.</td>");
						
			$objTable.fadeIn();
		})
});