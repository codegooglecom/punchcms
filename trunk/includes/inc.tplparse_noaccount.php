<?php

function parsePage($intElmntId, $strCommand) {
	global $_PATHS,
			$_CONF,
			$objLang,
			$objLiveAdmin;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("noaccount.tpl.htm");

	$objTpl->setVariable("CLIENT_LINK", $_CONF['cust']['link']);
	$objTpl->setVariable("CLIENT_LOGO", "images/splash.jpg");
	$objTpl->setVariable("CLIENT_NAME", $_CONF['cust']['alttext']);
	$objTpl->setVariable("MESSAGE", $objLang->get("noAccount", "alert"));

	return $objTpl->get();
}

?>