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
 * ExtDirect Data Provider For The Redirect Test Categories
 */
class RedirectTestCategoryDataProvider extends AbstractDataProvider {
	/**
	 * Returns all categories
	 *
	 * @param \stdClass $data
	 * @return array
	 */
	public function read($data) {
		$data = (array) $data;
		$parameters = array(
			'filterString' => $data['query'],
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('RedirectTestCategory', 'read');
	}

	/**
	 * Updates a single redirect test category
	 *
	 * @param array $updatedRecord
	 * @return array
	 */
	protected function updateRecord(array $updatedRecord) {
		$parameters = array(
			'__trustedProperties' => $updatedRecord['__trustedProperties'],
			'redirectTestCategory' => array(
				'__identity' => intval($updatedRecord['__identity']),
				'category' => $updatedRecord['category'],
			)
		);

		$this->extBaseConnector->setParameters($parameters);
		return $this->extBaseConnector->runControllerAction('RedirectTestCategory', 'update');
	}

	/**
	 * Removes unused categories from the repository!
	 *
	 * @return array
	 */
	public function deleteUnusedCategories() {
		try {
			$success = TRUE;
			$message = '';
			$this->extBaseConnector->runControllerAction('RedirectTestCategory', 'deleteUnusedCategories');
		} catch (\Exception $exception) {
			$success = FALSE;
			$message = $exception->getMessage();
		}

		return array(
			'success' => $success,
			'message' => htmlspecialchars($message),
		);
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