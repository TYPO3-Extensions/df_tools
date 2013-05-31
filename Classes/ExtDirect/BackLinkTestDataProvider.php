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
 * ExtDirect Data Provider For The Back Link Tests
 */
class BackLinkTestDataProvider extends AbstractDataProvider {
	/**
	 * Returns all back link tests
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
		return $this->extBaseConnector->runControllerAction('BackLinkTest', 'read');
	}

	/**
	 * Updates a single back link test
	 *
	 * @param array $updatedRecord
	 * @return array
	 */
	protected function updateRecord(array $updatedRecord) {
		$parameters = array(
			'__trustedProperties' => $updatedRecord['__trustedProperties'],
			'backLinkTest' => array(
				'__identity' => intval($updatedRecord['__identity']),
				'testUrl' => $updatedRecord['testUrl'],
				'expectedUrl' => str_replace('\\', '\\\\', $updatedRecord['expectedUrl']),
				'comment' => $updatedRecord['comment'],
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('BackLinkTest', 'update');
	}

	/**
	 * Creates a single back link test
	 *
	 * @param array $newRecord
	 * @return array
	 */
	public function createRecord(array $newRecord) {
		$parameters = array(
			'__trustedProperties' => $newRecord['__trustedProperties'],
			'newBackLinkTest' => array(
				'testUrl' => $newRecord['testUrl'],
				'expectedUrl' => str_replace('\\', '\\\\', $newRecord['expectedUrl']),
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('BackLinkTest', 'create');
	}

	/**
	 * Destroys back link tests based on their identifiers
	 *
	 * @param array $identifiers
	 * @return array
	 */
	public function destroyRecords(array $identifiers) {
		$this->extBaseConnector->setParameters(array('identifiers' => $identifiers));
		$this->extBaseConnector->runControllerAction('BackLinkTest', 'destroy');
	}

	/**
	 * Runs the test for a single back link test
	 *
	 * @param int $identity
	 * @return array
	 */
	protected function runTestForRecord($identity) {
		$this->extBaseConnector->setParameters(array('identity' => $identity));
		$result = $this->extBaseConnector->runControllerAction('BackLinkTest', 'runTest');
		return $result['records'][0];
	}
}

?>