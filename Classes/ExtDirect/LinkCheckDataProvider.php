<?php

namespace SGalinski\DfTools\ExtDirect;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <stefan@sgalinski.de>, domainfactory GmbH
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
 * ExtDirect Data Provider For The Link Check Tests
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @package df_tools
 */
class LinkCheckDataProvider extends AbstractDataProvider {
	/**
	 * Returns all link check tests
	 *
	 * @param \stdClass $parameters
	 * @return array
	 */
	public function read($parameters) {
		/** @noinspection PhpUndefinedFieldInspection */
		$this->extBaseConnector->setParameters(
			array(
				'offset' => intval($parameters->start),
				'limit' => intval($parameters->limit),
				'sortingField' => $parameters->sort,
				'sortAscending' => strtoupper($parameters->dir) === 'ASC' ? TRUE : FALSE,
			)
		);
		return $this->extBaseConnector->runControllerAction('LinkCheck', 'read');
	}

	/**
	 * Synchronizes the link checks
	 *
	 * @return array
	 */
	public function synchronize() {
		try {
			$this->extBaseConnector->runControllerAction('LinkCheck', 'synchronize');
			$return = array(
				'success' => TRUE,
			);

		} catch (\Exception $exception) {
			$return = array(
				'success' => FALSE,
				'message' => htmlspecialchars($exception->getMessage()),
			);
		}

		return $return;
	}

	/**
	 * Runs the test for a single link check test
	 *
	 * @param int $identity
	 * @return array
	 */
	protected function runTestForRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity));
		$result = $this->extBaseConnector->runControllerAction('LinkCheck', 'runTest');
		return $result['records'][0];
	}

	/**
	 * Ignore a record
	 *
	 * @param int $identity
	 * @return array
	 */
	public function ignoreRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity, 'doIgnoreRecord' => TRUE));
		$result = $this->extBaseConnector->runControllerAction('LinkCheck', 'resetRecord');
		return array(
			'success' => TRUE,
			'data' => $result['records'][0],
		);
	}

	/**
	 * Observe a record
	 *
	 * @param int $identity
	 * @return array
	 */
	public function observeRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity, 'doIgnoreRecord' => FALSE));
		$result = $this->extBaseConnector->runControllerAction('LinkCheck', 'resetRecord');
		return array(
			'success' => TRUE,
			'data' => $result['records'][0],
		);
	}

	/**
	 * Sets a record as false positive
	 *
	 * @param int $identity
	 * @return array
	 */
	public function setAsFalsePositive($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity, 'isFalsePositive' => TRUE));
		$result = $this->extBaseConnector->runControllerAction('LinkCheck', 'setFalsePositiveState');
		return array(
			'success' => TRUE,
			'data' => $result['records'][0],
		);
	}

	/**
	 * Resets as record from false positive
	 *
	 * @param int $identity
	 * @return array
	 */
	public function resetAsFalsePositive($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity, 'isFalsePositive' => FALSE));
		$result = $this->extBaseConnector->runControllerAction('LinkCheck', 'setFalsePositiveState');
		return array(
			'success' => TRUE,
			'data' => $result['records'][0],
		);
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
}

?>