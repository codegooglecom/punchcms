<?php

$strDispatch 	= request("dispatch");
$_CLEAN_POST 	= array();

switch ($strDispatch) {
	case "addTemplate":
		$arrWhitelist = array(
			'frm_ispage'		=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
			'frm_iscontainer'	=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
			'frm_forcecreation'	=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_apiname'		=> array(
				'type' => 'word',
				'maxlength' => 150,
			),
		  	'frm_description'	=> array(
				'type' => 'text',
				'maxlength' => 1000,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "addTemplateField":
		$arrWhitelist = array(
			'frm_required'		=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_apiname'		=> array(
				'type' => 'word',
				'maxlength' => 150,
			),
		  	'frm_description'	=> array(
				'type' => 'text',
				'maxlength' => 1000,
			),
		  	'frm_field_type'	=> array(
				'type' => 'int',
				'maxlength' => 100,
				'required' => true,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "addElement":
		$arrWhitelist = array(
			'frm_active'		=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
			'frm_ispage'		=> array(
				'type' => 'string',
				'maxlength' => 2,
			),
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_apiname'		=> array(
				'type' => 'word',
				'maxlength' => 150,
			),
		  	'frm_alias'		=> array(
				'type' => 'string',
				'maxlength' => 250,
			),
		  	'frm_template'	=> array(
				'type' => 'string',
				'required' => true,
			),
		  	'frm_feed'	=> array(
				'type' => 'string',
				'required' => false,
			),
		  	'frm_dynamic_alias_check'	=> array(
				'type' => 'string',
				'maxlength' => 2,
				'required' => false,
			),
		  	'frm_dynamic_alias'	=> array(
				'type' => 'string',
				'maxlength' => 250,
				'required' => false,
			),
		  	'frm_feedpath'	=> array(
				'type' => 'string',
				'required' => false,
			),
		  	'frm_maxitems'	=> array(
				'type' => 'int',
				'required' => false,
			),
		  	'frm_description'	=> array(
				'type' => 'text',
				'maxlength' => 250
			),
		  	'publish_start'	=> array(
				'type' => 'word',
				'maxlength' => 6,
			),
		  	'publish_end'	=> array(
				'type' => 'word',
				'maxlength' => 6,
			),
		  	'publish_start_date'	=> array(
				'type' => 'string',
				'maxlength' => 128,
			),
		  	'publish_end_date'	=> array(
				'type' => 'string',
				'maxlength' => 128,
			),
		  	'publish_start_hour'	=> array(
				'type' => 'word',
				'maxlength' => 2,
			),
		  	'publish_end_hour'	=> array(
				'type' => 'word',
				'maxlength' => 2,
			),
		  	'publish_start_minute'	=> array(
				'type' => 'word',
				'maxlength' => 2,
			),
		  	'publish_end_minute'	=> array(
				'type' => 'word',
				'maxlength' => 2,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "addStorageItem":
		$arrWhitelist = array(
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_description'	=> array(
				'type' => 'text',
				'maxlength' => 250,
			),
		  	'frm_file'	=> array(
				'type' => 'text',
				'maxlength' => 250,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;
		
	case "addForm":
		$arrWhitelist = array(
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_apiname'		=> array(
				'type' => 'word',
				'maxlength' => 150,
			),
		  	'frm_description'	=> array(
				'type' => 'text',
				'maxlength' => 1000,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editProfile":
		$arrWhitelist = array(
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 250,
				'required' => true,
			),
		  	'frm_email'		=> array(
				'type' => 'email',
				'maxlength' => 250,
				'required' => true,
			),
		  	'frm_language'	=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_timezone'	=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editPass":
		$arrWhitelist = array(
		  	'frm_currentpass' 	=> array(
				'type' => 'password',
				'maxlength' => 30,
				'required' => true,
			),
		  	'frm_newpass'		=> array(
				'type' => 'password',
				'maxlength' => 30,
				'required' => true,
			),
		  	'frm_verifypass'	=> array(
				'type' => 'password',
				'maxlength' => 30,
				'required' => true,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editSettings":
		$arrWhitelist = array(
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editLanguage":
		$arrWhitelist = array(
		  	'frm_active'		=> array(
				'type' => 'word',
				'maxlength' => 5,
			),
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		  	'frm_apiname'		=> array(
				'type' => 'word',
				'maxlength' => 150,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editAlias":
		$arrWhitelist = array(
		  	'frm_active'		=> array(
				'type' => 'word',
				'maxlength' => 5,
			),
		  	'frm_alias' 		=> array(
				'type' => 'word',
				'maxlength' => 250,
				'required' => true,
			),
		  	'frm_language' 		=> array(
				'type' => 'int',
				'maxlength' => 150,
			),
		  	'frm_element'		=> array(
				'type' => 'int',
				'maxlength' => 150,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

	case "editFeed":
		$arrWhitelist = array(
		  	'frm_active'		=> array(
				'type' => 'word',
				'maxlength' => 5,
			),
		  	'frm_name' 			=> array(
				'type' => 'string',
				'maxlength' => 250,
				'required' => true,
			),
		  	'frm_feed'		=> array(
				'type' => 'string',
				'maxlength' => 250,
				'required' => true,
			),
		  	'frm_basepath'	=> array(
				'type' => 'text',
				'maxlength' => 250,
				'required' => false,
			),
		  	'frm_refresh'	=> array(
				'type' => 'int',
				'maxlength' => 16,
				'required' => true,
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;
		
	case "addStructure":
		$arrWhitelist = array(
			'frm_structure'		=> array(
				'type' => 'string',
			),
		  	'dispatch'			=> array(
				'type' => 'string',
				'maxlength' => 100,
				'required' => true,
			),
		);

		if ($_POST) {
		  $_CLEAN_POST = filterInput($_POST, $arrWhitelist);
		}

		break;

}

?>