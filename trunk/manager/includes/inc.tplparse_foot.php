<?php

function parseFooter() {
	global $_PATHS;

	$objTpl = new HTML_Template_IT($_PATHS['templates']);
	$objTpl->loadTemplatefile("footer.tpl.htm", false, false);

	return $objTpl->get();
}

?>