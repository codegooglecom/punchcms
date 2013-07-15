<?php

require_once('includes/init.php');

$objFeeds = Feed::selectActive();
foreach ($objFeeds as $objFeed) {
	if ($objFeed->getId() != 1) {
		$objFeed->updateElements();
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Insert title here</title>
</head>
<body>

<p>Feeds rendered.</p>

</body>
</html>