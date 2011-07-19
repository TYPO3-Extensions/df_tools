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
 * Custom View
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_View_RecordSet_ArrayView extends Tx_DfTools_View_AbstractArrayView {
	/**
	 * Returns the hmac configuration
	 *
	 * @return array
	 */
	protected function getHmacFieldConfiguration() {
		return array('update' => array(), 'create' => array());
	}

	/**
	 * Returns the human readable table and field name of a given record set in an array
	 *
	 * Note: Access this data by using the following code:
	 * list($table, $field) = $this->getReadableTableAndFieldName($recordSet);
	 *
	 * @param Tx_DfTools_Domain_Model_RecordSet $recordSet
	 * @return array
	 */
	protected function getReadableTableAndFieldName(Tx_DfTools_Domain_Model_RecordSet $recordSet) {
		$table = $recordSet->getTableName();
		t3lib_div::loadTCA($table);

		$label = $GLOBALS['TCA'][$table]['ctrl']['title'];
		$humanReadableTable = $GLOBALS['LANG']->sL($label);

		$label = $GLOBALS['TCA'][$table]['columns'][$recordSet->getField()]['label'];
		$humanReadableField = $GLOBALS['LANG']->sL($label);

		return array($humanReadableTable, $humanReadableField);
	}

	/**
	 * Renders a redirect test into a plain array
	 *
	 * @param Tx_DfTools_Domain_Model_RecordSet $record
	 * @return array
	 */
	protected function getPlainRecord($record) {
		list($humanReadableTable, $humanReadableField) = $this->getReadableTableAndFieldName($record);

		return array(
			'__identity' => intval($record->getUid()),
			'tableName' => htmlspecialchars($record->getTableName()),
			'humanReadableTableName' => htmlspecialchars($humanReadableTable),
			'field' => htmlspecialchars($humanReadableField),
			'identifier' => intval($record->getIdentifier()),
		);
	}
}

?>