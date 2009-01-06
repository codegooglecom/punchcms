<?php

function parsePage($intElmntId, $strCommand) {
	global $_PATHS,
			$_CONF,
			$objLang,
			$objLiveAdmin;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("noaccount.tpl.htm");

	$objTpl->setVariable("CLIENT_NAME", $objLang->get("invalidAccount", "login"));
	$objTpl->setVariable("POWERED_BY", $objLang->get("poweredBy", "label"));
	$objTpl->setVariable("MESSAGE", $objLang->get("noAccount", "alert"));

	return $objTpl->get();
}

?>