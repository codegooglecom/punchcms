<?php

/***
 *
 * StructureDetails DBA Class.
 *
 */

class DBA_StructureDetails extends DBA__Object {
	protected $id = NULL;
	protected $structureid = 0;
	protected $language = "";
	protected $name = "";
	protected $description = "";

	//*** Constructor.
	public function DBA_StructureDetails() {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";
	}

	//*** Static inherited functions.
	public static function selectByPK($varValue, $arrFields = array()) {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::selectByPK($varValue, $arrFields);
	}

	public static function select($strSql = "") {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::select($strSql);
	}

	public static function doDelete($varValue) {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::doDelete($varValue);
	}

	public function save($blnSaveModifiedDate = TRUE) {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::save($blnSaveModifiedDate);
	}

	public function delete() {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::delete();
	}

	public function duplicate() {
		self::$__object = "StructureDetails";
		self::$__table = "pcms_structure_meta";

		return parent::duplicate();
	}
}

?>