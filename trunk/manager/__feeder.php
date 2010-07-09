<?php

require_once('includes/init.php');

function structureNodes($objElement) {
		$arrReturn = array();
		
		foreach ($objElement->childNodes as $objSubElement) {
			if ($objSubElement instanceof domElement && !isset($arrReturn[$objSubElement->tagName])) {
				$arrReturn[$objSubElement->tagName] = $objSubElement;
			}
		}
		
		return $arrReturn;
}

function getXpath($objElement) {
	$strReturn = "/" . $objElement->tagName;

	$objParent = $objElement->parentNode;
	$strReturn = ($objParent) ? getXpath($objParent) . $strReturn : "";

	return $strReturn;
}

function showElement($objElement) {
	$strReturn = "";
	
	$objList = $objElement->attributes;
	if (count($objList) > 0) {
		foreach ($objList as $name => $value) {		
			$strReturn .= "<li title=\"{$value->nodeValue}\">@{$value->nodeName}</li>\n";
		}
	}
	
	$objList = structureNodes($objElement);
	if (count($objList) > 0) {
		foreach ($objList as $objSubElement) {
			$strReturn .= "<li title=\"" . htmlentities(str_replace("<br />", "\n", $objSubElement->nodeValue)) . "\" class=\"" . getXpath($objSubElement) . "\">" . $objSubElement->tagName;
			$strReturn .= showElement($objSubElement);
			$strReturn .= "</li>\n";
		}
	}
	
	if (!empty($strReturn)) {
		$strReturn = "<ul>\n{$strReturn}</ul>\n";
	}
			
	return $strReturn;
}

$objDoc = new DOMDocument();
//$objDoc->load($_PATHS['upload'] . "bluebay-koop-test.xml");
$objDoc->load("http://www.nu.nl/feeds/rss/internet.rss");
//$strOutput = showElement($objDoc);

//*** Load from path.
$objXml = simplexml_load_file($_PATHS['upload'] . "bluebay-koop-test.xml");
$objNodes = $objXml->xpath("/bluebay/item");
foreach ($objNodes as $objNode) {
	echo "Value: " . $objNode . "<br />";
	$objSubNodes = $objNode->xpath("foto/foto1/full");
	foreach ($objSubNodes as $objSubNode) {
		echo "SubValue: " . $objSubNode . "<br />";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Insert title here</title>
<style type="text/css">

#items {
	width: 300px;
	position: absolute;
	left: 0;
	top: 0;
}

#drop-wrapper {
	margin: 20px 0 0 350px;
}

</style>
<script type="text/javascript" src="/libraries/jquery.js"></script>
<script type="text/javascript">

$(function(){

	$("#items li").bind("click", function(){
		$("#dropper").val($(this).attr("class"));
		return false;
	});
	
});

</script>
</head>
<body>

<div id="items">
	<?php echo $strOutput; ?>
</div>

<div id="drop-wrapper">
	<input id="dropper" name="dropper" />
</div>

</body>
</html>