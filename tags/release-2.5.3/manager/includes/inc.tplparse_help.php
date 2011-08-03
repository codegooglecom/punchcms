<?php

function parseHelp($intElmntId, $strCommand) {
	global $_PATHS,
			$objLang;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("help.tpl.htm");

	$objTpl->setVariable("HELP", $objLang->get("help", "label"));
	
	$objTpl->setCurrentBlock("paragraph");
	$objTpl->setVariable("HEADER", $objLang->get("docHeader", "help"));
	$objTpl->setVariable("BODY", $objLang->get("docBody", "help"));
	$objTpl->parseCurrentBlock();

	return $objTpl->get();
}

?>