<?php

/* Form Class v0.1.0
 * Handles Form properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class Form extends DBA_Form {
	private $objFormCollection;

	public static function selectByPK($varValue, $arrFields = array()) {
		global $_CONF;
		DBA__Object::$__object = "Form";
		DBA__Object::$__table = "pcms_form";

		return parent::selectByPK($varValue, $arrFields, $_CONF['app']['account']->getId());
	}

	public static function selectByAccountId() {
		global $_CONF;
		DBA__Object::$__object = "Form";
		DBA__Object::$__table = "pcms_form";
		
		$strSql = "SELECT * FROM pcms_form WHERE accountId = '{$_CONF['app']['account']->getId()}' ORDER BY sort";
		$objReturn = parent::select($strSql);

		return $objReturn;
	}

	public function getFields() {
		$strSql = "SELECT * FROM pcms_template_field WHERE formId = '{$this->id}' ORDER BY sort";
		$objReturn = TemplateField::select($strSql);

		return $objReturn;
	}

}

?>