<?php

namespace SGalinski\DfTools\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

use SGalinski\DfTools\Parser\TcaParser;

/**
 * Collection of smaller tca utility functions
 */
final class TcaUtility {
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
	 * @param TcaParser $tcaParser
	 * @param array $excludedTables
	 * @param array $excludedTableFields
	 * @return array
	 */
	public static function getTextFields(
		TcaParser $tcaParser, array $excludedTables = array(), array $excludedTableFields = array()
	) {
		$tcaParser->setExcludedTables($excludedTables);
		$tcaParser->setAllowedTypes(array('input', 'text'));
		$tcaParser->setExcludedFields(array_merge($excludedTableFields, array('t3ver_label')));
		$tcaParser->setExcludedEvals(
			array(
				'date', 'datetime', 'time', 'timesec', 'year',
				'int', 'num', 'double2', 'alpha', 'alphanum', 'alphanum_x',
				'md5', 'password'
			)
		);

		return $tcaParser->findFields(array('Tx_DfTools_Utility_TcaUtility', 'filterCallback'));
	}
}

?>