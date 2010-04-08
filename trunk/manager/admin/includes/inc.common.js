/*
 * Initializing all the javascripts
 */

jQuery(function(){
	
	//*** Login page
	jQuery("#login").corner("14px");
	jQuery("#handle").focus();
	
	//*** PunchCMS Super Admin stuff
	jQuery("#frm_import_keep_settings").change(function(){
		if (jQuery(this).is(":checked")) {
			jQuery("#frm_import_overwrite").attr("checked", "checked");
		}
	});
	jQuery("#frm_import_overwrite").change(function(){
		if (!jQuery(this).is(":checked")) {
			$("#frm_import_keep_settings").removeAttr("checked");
		}
	});
	
});