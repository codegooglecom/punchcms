<?php

/* DBA ParseDatabase methods.
 * Parse the database.xml file and create PHP5 classes from it.
 *
 * CHANGELOG
 * version 0.1.0, 08 Mei 2006
 *   NEW: Created file.
 */

//*** Configuration options.
$_options = array(
	'databaseFile' => 'database.xml',
	'templateFile' => 'classTemplate.php',
	'outputPre' => 'class.dba_',
	'outputPost' => '.php',
	'outputPath' => '../includes/',
);

//*** Load the database.xml file.
$objDoc = new DOMDocument();
$objDoc->load($_options['databaseFile']);

//*** Select the "table" nodes.
$objTables = $objDoc->getElementsByTagName('table');

//*** Loop through the list of tables.
if (count($objTables) > 0) {
	//*** Load the template file.
	$strTemplate = file_get_contents($_options['templateFile']);

	foreach ($objTables as $objTable) {
		$strClass = $strTemplate;
		$strTableName = $objTable->getAttribute('name');
		$strClassName = $objTable->getAttribute('phpName');

		//*** Render class name.
		$strClass = str_replace('{className}', $strClassName, $strClass);

		//*** Render table name.
		$strClass = str_replace('{classTable}', $strTableName, $strClass);

		//*** Render properties.
		$objProperties = $objTable->getElementsByTagName('column');
		$strProperties = "";
		foreach ($objProperties as $objProperty) {
			$strProperties .= "\tprotected $" . strtolower($objProperty->getAttribute('name')) . " = ";

			if ($objProperty->getAttribute('primaryKey') == TRUE) {
				//*** Primary key column.
				$strProperties .= "NULL;\n";
			} else {
				switch ($objProperty->getAttribute('type')) {
					case "integer":
						if ($objProperty->getAttribute('default') == "") {
							$strProperties .= "0;\n";
						} else {
							$strProperties .= $objProperty->getAttribute('default') . ";\n";
						}
						break;

					case "varchar":
						if ($objProperty->getAttribute('default') == "") {
							$strProperties .= "\"\";\n";
						} else {
							$strProperties .= "\"" . $objProperty->getAttribute('default') . "\";\n";
						}
						break;

					case "timestamp":
						if ($objProperty->getAttribute('default') == "") {
							$strProperties .= "NULL;\n";
						} else {
							$strProperties .= $objProperty->getAttribute('default') . ";\n";
						}
						break;

					case "datetime":
						if ($objProperty->getAttribute('default') == "") {
							$strProperties .= "\"0000-00-00 00:00:00\";\n";
						} else {
							$strProperties .= $objProperty->getAttribute('default') . ";\n";
						}
						break;

				}
			}
		}
		$strClass = str_replace('{classProperties}', $strProperties, $strClass);

		//*** Write class file.
		file_put_contents($_options['outputPath'] . $_options['outputPre'] . strtolower($strClassName) . $_options['outputPost'], $strClass);
	}

	echo "DBA Classes generated successfully from <b>database.xml</b>.";
} else {
	echo "No tables found in <b>database.xml</b>.";
}

?>