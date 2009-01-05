<?php

ini_set("include_path", dirname(__FILE__) . "/../");
require_once('init.php');
require_once("../libraries/ValidForm/class.validform.php");

$strOutput = "";

$objInstaller = new PCMS_Installer();
$strRequirements = $objInstaller->checkRequirements();

if (!empty($strRequirements)) {
	$strOutput = $strRequirements;
} else {
	$objForm = $objInstaller->getForm();
	
	if ($objForm->isSubmitted() && $objForm->isValid()) {
		//*** Execute SQL file.
		$strReturn = $objInstaller->installDb($objForm->getValidField("db_server")->getValue(), 
			$objForm->getValidField("db_name")->getValue(), 
			$objForm->getValidField("db_username")->getValue(), 
			$objForm->getValidField("db_passwd")->getValue());
		
		if (empty($strReturn)) {
			//*** Write config file.
			$strReturn = $objInstaller->writeConfig($objForm->getValidField("username")->getValue(),
				$objForm->getValidField("passwd")->getValue(),
				$objForm->getValidField("email")->getValue());
			
			if (empty($strReturn)) {
				$strOutput = "<p class=\"success\"><b>Congratulations</b>, PunchCMS is almost ready!<br /></p>";
				$strOutput .= "<p>Three more steps to go:</p>";
				$strOutput .= "<ol><li>Copy or move the &quot;<b>config.php</b>&quot; file from the <b>install</b> folder to the root folder of PunchCMS.</li>";
				$strOutput .= "<li>Delete the <b>install</b> folder for security reasons.</li>";
				$strOutput .= "<li>Login to the <a href=\"../admin\">admin area</a> to create a website and start building it.</li>";
			} else {
				$strOutput = "<p class=\"error\">Error while writing to the configuration file. Check the folder permissions and try again.</p>";
				$strOutput .= "<p><b>Details:</b><br />{$strReturn}</p>";
			}
		} else {
			$strOutput = "<p class=\"error\">Error during database creation. Check the settings and try again.</p>";
			$strOutput .= "<p><b>Details:</b><br />{$strReturn}</p>";
		}
	} else {
		$strOutput = "<p>Welcome to the PunchCMS installer.<br />Fill in the required fields below and you should be up and running in no time.</p>";
		$strOutput .= $objForm->toHtml();
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Install / Upgrade PunchCMS</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/validform.css" />
<link rel="stylesheet" type="text/css" href="css/common.css" />
<script type="text/javascript" src="../libraries/jquery.js"></script>
<script type="text/javascript" src="../libraries/validform.js"></script>
</head>
<body>

<h1>PunchCMS Installation</h1>
<?php echo $strOutput ?>

</body>
</html>