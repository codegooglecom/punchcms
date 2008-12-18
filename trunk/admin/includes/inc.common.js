
$(document).ready(function() {
	$("#login").corner("14px");
	
	$("#handle").focus();
	
	$("#frm_import_keep_settings").change(function(){
			if (this.checked) {
				$("#frm_import_overwrite").attr("checked", "checked");
			}
		}
	);
	
	$("#frm_import_overwrite").change(function(){
			if (!this.checked) {
				$("#frm_import_keep_settings").removeAttr("checked");
			}
		}
	);
});
