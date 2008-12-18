<?php

/* Alias Class v0.1.0
 * Handles alias properties and methods.
 *
 * CHANGELOG
 * version 0.1.0, 04 Apr 2006
 *   NEW: Created class.
 */

class Alias extends DBA_Alias {
	
	public function save($blnSaveModifiedDate = TRUE) {
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";
		
		$intId = $this->getId();
		
		$blnReturn = parent::save($blnSaveModifiedDate);
		AuditLog::addLog(AUDIT_TYPE_ALIAS, $this->getId(), $this->getAlias(), (empty($intId)) ? "create" : "edit", ($this->getActive()) ? "active" : "inactive");

		return $blnReturn;
	}

	public function delete() {
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";
		
		AuditLog::addLog(AUDIT_TYPE_ALIAS, $this->getId(), $this->getAlias(), "delete");
		return parent::delete();
	}

	public static function select($strSql = "") {
		global $_CONF;
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";

		if (empty($strSql)) {
			$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' ORDER BY sort", $_CONF['app']['account']->getId());
		}

		return parent::select($strSql);
	}

	public static function selectSorted() {
		global $_CONF;
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";

		if (empty($strSql)) {
			$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' ORDER BY alias", $_CONF['app']['account']->getId());
		}

		return parent::select($strSql);
	}

	public static function selectByUrl($strUrl) {
		global $_CONF;
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";
		$objReturn = NULL;

		if (!empty($strUrl)) {
			$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' AND url = %s ORDER BY sort", $_CONF['app']['account']->getId(), parent::quote($strUrl));
		}

		$objAliases = parent::select($strSql);
		if ($objAliases->count() > 0) $objReturn = $objAliases->current();
		
		return $objReturn;
	}

	public static function selectByAlias($strAlias) {
		global $_CONF;
		parent::$__object = "Alias";
		parent::$__table = "pcms_alias";
		$objReturn = NULL;

		if (!empty($strAlias)) {
			$strSql = sprintf("SELECT * FROM " . parent::$__table . " WHERE accountId = '%s' AND alias = %s ORDER BY sort", $_CONF['app']['account']->getId(), parent::quote($strAlias));
		}

		$objAliases = parent::select($strSql);
		if ($objAliases->count() > 0) $objReturn = $objAliases->current();
		
		return $objReturn;
	}

}

?>