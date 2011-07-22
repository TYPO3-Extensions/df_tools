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
 * Abstract ExtDirect Data Provider
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_ExtDirect_AbstractDataProvider {
	/**
	 * BootStrap Instance
	 *
	 * @var Tx_DfTools_Service_ExtBaseConnectorService
	 */
	protected $extBaseConnector = NULL;

	/**
	 * Constructor
	 */
	public function __construct() {
		if (TYPO3_MODE === 'FE' && !is_object($GLOBALS['TSFE'])) {
			$pageId = intval(t3lib_div::_GP('pageId'));
			$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageId, 0);
			$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
			$GLOBALS['TSFE']->getPageAndRootline();
			$GLOBALS['TSFE']->initTemplate();
			$GLOBALS['TSFE']->forceTemplateParsing = TRUE;
			$GLOBALS['TSFE']->initFEuser();
			$GLOBALS['TSFE']->initUserGroups();
			$GLOBALS['TSFE']->getCompressedTCarray();

			$GLOBALS['TSFE']->no_cache = TRUE;
			$GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);
			$GLOBALS['TSFE']->no_cache = FALSE;

			$GLOBALS['TSFE']->config = array();
			$GLOBALS['TSFE']->config['config'] = array(
				'sys_language_uid' => intval(t3lib_div::_GP('L')),
				'sys_language_mode' => 'content_fallback;0',
				'sys_language_overlay' => 'hideNonTranslated',
				'sys_language_softMergeIfNotBlank' => '',
				'sys_language_softExclude' => '',
			);
			$GLOBALS['TSFE']->settingLanguage();
		}

		$this->extBaseConnector = t3lib_div::makeInstance('Tx_DfTools_Service_ExtBaseConnectorService');
		$this->extBaseConnector->setExtensionKey('DfTools');
		$this->extBaseConnector->setModuleOrPluginKey('tools_DfToolsTools');
	}

	/**
	 * Handles incoming multiple update requests
	 *
	 * Note: ExtJS transfers a single data object or multiple
	 * ones based on the amount of updated records.
	 *
	 * Note2: An exception causes the whole process to stop. You don't
	 * know on the client side which records are written successfully. Always
	 * update a record once by once!
	 *
	 * @param stdClass $updatedRecords
	 * @return array
	 */
	public function update($updatedRecords) {
		/** @noinspection PhpUndefinedFieldInspection */
		$updatedRecords = $updatedRecords->records;
		if (!is_array($updatedRecords)) {
			$updatedRecords = array($updatedRecords);
		}

		try {
			$data = array(
				'records' => array(),
			);

			foreach ($updatedRecords as $updatedRecord) {
				$record = $this->updateRecord((array)$updatedRecord);
				if (is_array($record['records'])) {
					$data['records'] = array_merge_recursive($data['records'], $record['records']);
				}
			}
		} catch (Exception $exception) {
			$data = array(
				'success' => FALSE,
				'message' => $exception->getMessage(),
				'records' => array(),
			);
		}

		return $data;
	}

	/**
	 * Handles the incoming create record calls
	 *
	 * @param stdClass $newRecord
	 * @return array
	 */
	public function create($newRecord) {
		/** @noinspection PhpUndefinedFieldInspection */
		return $this->createRecord((array)$newRecord->records);
	}

	/**
	 * Destroys records based on their identifiers
	 *
	 * Note: ExtJS transfers a single identifier object or multiple
	 * ones based on the amount of deleted records.
	 *
	 * @param stdClass $identifiers
	 * @return array
	 */
	public function destroy($identifiers) {
		/** @noinspection PhpUndefinedFieldInspection */
		$identifiers = $identifiers->records;
		if (!is_array($identifiers)) {
			$identifiers = array($identifiers);
		}

		$this->destroyRecords($identifiers);

		return array(
			'success' => TRUE,
			'records' => array()
		);
	}

	/**
	 * Handles an incoming test request for a record
	 *
	 * @param int $identity
	 * @return array
	 */
	public function runTest($identity) {
		try {
			$data = $this->runTestForRecord($identity);
			$result = array(
				'success' => TRUE,
				'data' => $data,
			);

		} catch (Exception $exception) {
			$result = array(
				'success' => FALSE,
				'data' => array(
					'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION,
					'testMessage' => htmlspecialchars($exception->getMessage()),
				),
			);
		}

		return $result;
	}

	/**
	 * @abstract
	 * @param int $identity
	 * @return array
	 */
	abstract protected function runTestForRecord($identity);

	/**
	 * @abstract
	 * @param array $updatedRecord
	 * @return void
	 */
	abstract protected function updateRecord(array $updatedRecord);

	/**
	 * @abstract
	 * @param array $newRecord
	 * @return void
	 */
	abstract protected function createRecord(array $newRecord);

	/**
	 * @abstract
	 * @param array $identifiers
	 * @return void
	 */
	abstract protected function destroyRecords(array $identifiers);
}

?>