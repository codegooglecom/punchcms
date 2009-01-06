<?php

/* TemplateFieldTypes Class v0.1.0
 * Collection class for the TemplateFieldType objects.
 *
 * CHANGELOG
 * version 0.1.0, 11 Apr 2006
 *   NEW: Created class.
 */

class TemplateFieldTypes extends DBA__Collection {

	public static function getTypes() {
		$objReturn = TemplateFieldType::select();

		return $objReturn;
	}

}

?>