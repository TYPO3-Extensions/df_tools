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
 * ExtDirect Data Provider For The Content Comparison Tests
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtDirect_ContentComparisonTestDataProvider extends Tx_DfTools_ExtDirect_AbstractDataProvider {
	/**
	 * Returns all redirect tests
	 *
	 * @return array
	 */
	public function read() {
		return $this->extBaseConnector->runControllerAction('ContentComparisonTest', 'read');
	}

	/**
	 * Creates a single record
	 *
	 * @param array $newRecord
	 * @return array
	 */
	protected function createRecord(array $newRecord) {
		$parameters = array(
			'__hmac' => $newRecord['__hmac'],
			'newContentComparisonTest' => array(
				'testUrl' => $newRecord['testUrl'],
				'compareUrl' => $newRecord['compareUrl']
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('ContentComparisonTest', 'create');
	}

	/**
	 * Updates a single record
	 *
	 * @param array $updatedRecord
	 * @return array
	 */
	protected function updateRecord(array $updatedRecord) {
		$parameters = array(
			'__hmac' => $updatedRecord['__hmac'],
			'contentComparisonTest' => array(
				'__identity' => intval($updatedRecord['__identity']),
				'testUrl' => $updatedRecord['testUrl'],
				'compareUrl' => $updatedRecord['compareUrl'],
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('ContentComparisonTest', 'update');
	}

	/**
	 * Destroys records based on their identifiers
	 *
	 * @param array $identifiers
	 * @return array
	 */
	protected function destroyRecords(array $identifiers) {
		$this->extBaseConnector->setParameters(array('identifiers' => $identifiers));
		$this->extBaseConnector->runControllerAction('ContentComparisonTest', 'destroy');
	}

	/**
	 * Runs the test for a single record
	 *
	 * @param int $identity
	 * @return array
	 */
	protected function runTestForRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity));
		$result = $this->extBaseConnector->runControllerAction('ContentComparisonTest', 'runTest');
		return $result['records'][0];
	}

	/**
	 * Calls the test content action with the given identifier and returns the
	 * updated content comparison test
	 *
	 * @param int $identity
	 * @return array
	 */
	public function updateTestContent($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity));
		$result = $this->extBaseConnector->runControllerAction('ContentComparisonTest', 'updateTestContent');
		return array(
			'success' => TRUE,
			'data' => $result['records'][0]
		);
	}
}

?>