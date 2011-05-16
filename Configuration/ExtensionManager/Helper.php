<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 domainfactory GmbH (Stefan Galinski <sgalinski@df.eu>)
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
 * Helper methods for the flexform configuration in the extension manager
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class tx_DfTools_ExtensionManager_Helper implements t3lib_Singleton {
	/**
	 * Returns a list of tables in the expected TCA configuration format
	 *
	 * @param array $fieldConfiguration configuration
	 * @param object $transferData unused
	 * @return void
	 */
	public function getAllTables(&$fieldConfiguration, $transferData = NULL) {
		/** @var $tcaParser Tx_DfTools_Service_TcaParserService */
		$tcaParser = t3lib_div::makeInstance('Tx_DfTools_Service_TcaParserService');
		$tables = $tcaParser->getAllTables();

		$items = array();
		foreach ($tables as $table) {
			if ($table === 'tx_dftools_configuration') {
				continue;
			}

			$items[] = array($table, $table);
		}

		$fieldConfiguration['items'] = array_merge_recursive($items, $fieldConfiguration['items']);
	}

	/**
	 * Returns a list of all available tables and fields excluding the already excluded tables
	 *
	 * @param array $fieldConfiguration configuration
	 * @param object $transferData unused
	 * @return void
	 */
	public function getAllTableFields(&$fieldConfiguration, $transferData = NULL) {
		/** @var $tcaParser Tx_DfTools_Service_TcaParserService */
		$tcaParser = t3lib_div::makeInstance('Tx_DfTools_Service_TcaParserService');

		$items = array();
		$excludedTables = explode(',', $fieldConfiguration['row']['excludedTables']);
		$tableFields = Tx_DfTools_Utility_TcaUtility::getTextFields($tcaParser, $excludedTables);
		foreach ($tableFields as $table => $fields) {
			foreach ($fields as $field) {
				$items[] = array('[' . $table . '] ' . $field, $table . '<' . $field . '>');
			}
		}

		$fieldConfiguration['items'] = array_merge_recursive($items, $fieldConfiguration['items']);
	}
}

?>