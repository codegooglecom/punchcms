<?php

function parseAnnouncment() {
	global $_PATHS,
			$objLang;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("announcement.tpl.htm");

	$objTpl->setVariable("BUTTON_CLOSE", $objLang->get("close", "button"));

	$objMessages = AnnounceMessage::getMessages();

	foreach ($objMessages as $objMessage) {
		$objTpl->setVariable("HEADER", $objMessage->getHeader());
		$objTpl->setVariable("BODY", $objMessage->getMessage());
		break;
	}

	return $objTpl->get();
}

?>