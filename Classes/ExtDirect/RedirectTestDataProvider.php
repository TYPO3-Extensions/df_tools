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
 * ExtDirect Data Provider For The Redirect Tests
 */
class RedirectTestDataProvider extends AbstractDataProvider {
	/**
	 * Returns all redirect tests
	 *
	 * @param \stdClass $parameters
	 * @return array
	 */
	public function read($parameters) {
		$this->extBaseConnector->setParameters(
			array(
				'offset' => intval($parameters->start),
				'limit' => intval($parameters->limit),
				'sortingField' => $parameters->sort,
				'sortAscending' => strtoupper($parameters->dir) === 'ASC' ? TRUE : FALSE,
			)
		);
		return $this->extBaseConnector->runControllerAction('RedirectTest', 'read');
	}

	/**
	 * Updates a single redirect test
	 *
	 * @param array $updatedRecord
	 * @return array
	 */
	protected function updateRecord(array $updatedRecord) {
		$parameters = array(
			'__hmac' => $updatedRecord['__hmac'],
			'redirectTest' => array(
				'__identity' => intval($updatedRecord['__identity']),
				'testUrl' => $updatedRecord['testUrl'],
				'expectedUrl' => $updatedRecord['expectedUrl'],
				'httpStatusCode' => intval($updatedRecord['httpStatusCode']),
			)
		);

//		__trustedProperties


		// decide if we need to add a category before updating the assignment to the redirect test
		if (is_numeric($updatedRecord['categoryId'])) {
			$parameters['redirectTest']['category'] = array(
				'__identity' => intval($updatedRecord['categoryId'])
			);
		} else {
			$parameters['newCategory'] = array(
				'category' => $updatedRecord['categoryId']
			);
		}

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('RedirectTest', 'update');
	}

	/**
	 * Creates a single redirect test
	 *
	 * @param array $newRecord
	 * @return array
	 */
	public function createRecord(array $newRecord) {
		$parameters = array(
			'__hmac' => $newRecord['__hmac'],
			'newRedirectTest' => array(
				'testUrl' => $newRecord['testUrl'],
				'expectedUrl' => $newRecord['expectedUrl'],
				'httpStatusCode' => intval($newRecord['httpStatusCode']),
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('RedirectTest', 'create');
	}

	/**
	 * Destroys redirect tests based on their identifiers
	 *
	 * @param array $identifiers
	 * @return array
	 */
	public function destroyRecords(array $identifiers) {
		$this->extBaseConnector->setParameters(array('identifiers' => $identifiers));
		$this->extBaseConnector->runControllerAction('RedirectTest', 'destroy');
	}

	/**
	 * Runs the test for a single redirect test
	 *
	 * @param int $identity
	 * @return array
	 */
	protected function runTestForRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity));
		$result = $this->extBaseConnector->runControllerAction('RedirectTest', 'runTest');
		return $result['records'][0];
	}
}

?>