<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <sgalinski@df.eu>, domainfactory GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Collection of smaller tca utility functions
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
final class Tx_DfTools_Utility_TcaUtility {
	/**
	 * Callback filter for the Tca parser with special filter logic
	 *
	 * @param array $configuration
	 * @return bool
	 */
	public static function filterCallback($configuration) {
		$excluded = FALSE;
		if (isset($configuration['config']['max']) && $configuration['config']['max'] <= 50) {
			$excluded = TRUE;
		}

		return $excluded;
	}

	/**
	 * Returns a list of available plain text table fields without special meanings
	 *
	 * @param Tx_DfTools_Service_TcaParserService $tcaParser
	 * @param array $excludedTables
	 * @param array $excludedTableFields
	 * @return array
	 */
	public static function getTextFields(Tx_DfTools_Service_TcaParserService $tcaParser, array $excludedTables = array(), array $excludedTableFields = array()) {
		$tcaParser->setExcludedTables($excludedTables);
		$tcaParser->setAllowedTypes(array('input', 'text'));
		$tcaParser->setExcludedFields(array_merge($excludedTableFields, array('t3ver_label')));
		$tcaParser->setExcludedEvals(array(
			'date', 'datetime', 'time', 'timesec', 'year',
			'int', 'num', 'double2', 'alpha', 'alphanum', 'alphanum_x',
			'md5', 'password'
		));

		return $tcaParser->findFields(array('Tx_DfTools_Utility_TcaUtility', 'filterCallback'));
	}

	/**
	 * Strips the prefixed table names from the group db (tca type) values and returns an
	 * array of pure ids
	 *
	 * @param string
	 * @return array
	 */
	public static function stripTablePrefixFromGroupDBValues($values) {
		$ids = array();
		$values = explode(',', $values);
		foreach ($values as $value) {
			$lastUnderscorePosition = strrpos($value, '_');
			$value = ($lastUnderscorePosition ? substr($value, $lastUnderscorePosition + 1) : $value);
			$ids[] = trim($value);
		}

		return array_map('intval', $ids);
	}
}

?>