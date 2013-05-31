<?php

namespace SGalinski\DfTools\ExtDirect;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ExtDirect Data Provider For The Record Sets
 */
class RecordSetDataProvider extends AbstractDataProvider {
	/**
	 * Returns all record sets that are related to the given link check identity
	 *
	 * @param \stdClass $data
	 * @return array
	 */
	public function read($data) {
		$parameters = array(
			'identity' => intval($data->identity),
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('LinkCheck', 'readRecordSets');
	}

	/**
	 * Returns the view link to the frontend of a given table/id pair
	 *
	 * @param string $tableName
	 * @param int $identifier
	 * @return string
	 */
	public function getViewLink($tableName, $identifier) {
		$parameters = array(
			'tableName' => $tableName,
			'identifier' => $identifier,
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('LinkCheck', 'getViewLink');
	}

	/**
	 * Not implemented!
	 *
	 * @param array $updatedRecord
	 * @return void
	 */
	protected function updateRecord(array $updatedRecord) {
	}

	/**
	 * Not implemented!
	 *
	 * @param array $identifiers
	 * @return void
	 */
	protected function destroyRecords(array $identifiers) {
	}

	/**
	 * Not implemented!
	 *
	 * @param array $newRecord
	 * @return void
	 */
	protected function createRecord(array $newRecord) {
	}

	/**
	 * Not implemented!
	 *
	 * @param int $identity
	 * @return void
	 */
	protected function runTestForRecord($identity) {
	}
}

?>