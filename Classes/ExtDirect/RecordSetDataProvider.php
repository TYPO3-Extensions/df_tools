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
 * ExtDirect Data Provider For The Record Sets
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtDirect_RecordSetDataProvider extends Tx_DfTools_ExtDirect_AbstractDataProvider {
	/**
	 * Returns all record sets that are related to the given link check identity
	 *
	 * @param stdClass $data
	 * @return array
	 */
	public function read($data) {
		/** @noinspection PhpUndefinedFieldInspection */
		$parameters = array(
			'identity' => intval($data->identity),
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('LinkCheck', 'readRecordSets');
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