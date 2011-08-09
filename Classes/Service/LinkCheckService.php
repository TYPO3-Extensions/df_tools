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
 * LinkCheck Service
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_LinkCheckService implements t3lib_Singleton {
	/**
	 * Instance of the link check repository
	 *
	 * @var Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected $linkCheckRepository = NULL;

	/**
	 * Instance of the object manager
	 *
	 * @var Tx_ExtBase_Object_ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * Injects the redirect test category repository
	 *
	 * @param Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository
	 * @return void
	 */
	public function injectLinkCheckRepository(Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository) {
		$this->linkCheckRepository = $linkCheckRepository;
	}

	/**
	 * Injects the object manager
	 *
	 * @param Tx_ExtBase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_ExtBase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Returns all parsed urls in the complete database
	 *
	 * @param string $excludedTablesString
	 * @param string $excludedTableFieldsString
	 * @return array
	 */
	public function fetchAllRawUrlsFromTheDatabase($excludedTablesString, $excludedTableFieldsString) {
		$excludedTables = array();
		if ($excludedTablesString !== '') {
			$excludedTables = explode(',', trim($excludedTablesString, ','));
		}

		$excludedTableFields = array();
		if ($excludedTableFieldsString !== '') {
			$excludedTableFields = explode(',', trim($excludedTableFieldsString, ','));
		}

		/** @var $urlParser Tx_DfTools_Service_UrlParserService */
		$urlParser = $this->objectManager->get('Tx_DfTools_Service_UrlParserService');
		return $urlParser->fetchUrls($excludedTables, $excludedTableFields);
	}

	/**
	 * Returns a list of raw urls defined in the given record
	 *
	 * @param string $table
	 * @param int $identity
	 * @return array
	 */
	public function getUrlsFromSingleRecord($table, $identity) {
		$record = t3lib_BEfunc::getRecord($table, $identity);

		/** @var $urlParser Tx_DfTools_Service_UrlParserService */
		$urlParser = $this->objectManager->get('Tx_DfTools_Service_UrlParserService');

		$rawUrls = array();
		if ($table === 'pages') {
			$rawUrls = array_merge_recursive($rawUrls, $urlParser->fetchLinkCheckLinkType(array($identity)));
		}

		$record['uid'] = $identity;
		$rawUrls = array_merge_recursive($rawUrls, $urlParser->parseRows(array($record), $table));

		$existingRawUrls = $this->findExistingRawUrlsByTableAndUid($table, $identity);
		$existingAndFoundRawUrls = $this->findExistingRawUrlsByTestUrls(array_keys($rawUrls));
		$existingRawUrls = array_merge($existingRawUrls, $existingAndFoundRawUrls);
		foreach ($existingRawUrls as $url => $recordSets) {
			if (!isset($rawUrls[$url])) {
				$rawUrls[$url] = array();
				foreach ($recordSets as $index => $_) {
					if (preg_match('/^' . $table . '.+' . $identity . '$/i', $index)) {
						unset($recordSets[$index]);
					}
				}
			}

			$rawUrls[$url] = array_merge($recordSets, $rawUrls[$url]);
		}

		return $rawUrls;
	}

	/**
	 * Returns an array of raw url data with their record sets from a given bunch of test urls
	 *
	 * @param array $urls
	 * @return array
	 */
	protected function findExistingRawUrlsByTestUrls(array $urls) {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

		$rawUrls = array();
		foreach ($urls as $url) {
			/** @noinspection PhpUndefinedMethodInspection */
			$linkCheck = $this->linkCheckRepository->findOneByTestUrl($url);
			if (!$linkCheck) {
				continue;
			}

			$recordSets = array();
			foreach ($linkCheck->getRecordSets() as $recordSet) {
				$index = $recordSet->getTableName() . $recordSet->getField() . $recordSet->getIdentifier();
				$recordSets[$index] = array(
					$recordSet->getTableName(),
					$recordSet->getField(),
					$recordSet->getIdentifier(),
				);
			}

			$rawUrls[$url] = $recordSets;
		}

		return $rawUrls;
	}

	/**
	 * Returns all raw urls that have record sets with the defined table and uid
	 *
	 * @param string $table
	 * @param int $identity
	 * @return array
	 */
	protected function findExistingRawUrlsByTableAndUid($table, $identity) {
		$table = $GLOBALS['TYPO3_DB']->fullQuoteStr($table, 'tx_dftools_domain_model_linkcheck');
		$queryResult = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			'tx_dftools_domain_model_linkcheck.uid',
			'tx_dftools_domain_model_linkcheck',
			'tx_dftools_linkcheck_recordset_mm',
			'tx_dftools_domain_model_recordset',
			' AND tx_dftools_domain_model_recordset.table_name = ' . $table . ' AND ' .
				'tx_dftools_domain_model_recordset.identifier = ' . intval($identity)
		);

		$rawUrls = array();
		if (!$GLOBALS['TYPO3_DB']->sql_error()) {
			$linkCheckIds = array();
			while (($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult))) {
				$linkCheckIds[] = $record['uid'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($queryResult);

			if (count($linkCheckIds)) {
				/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
				/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

				$linkChecks = $this->linkCheckRepository->findInListByIdentity($linkCheckIds);
				foreach ($linkChecks as $linkCheck) {
					$recordSets = array();
					foreach ($linkCheck->getRecordSets() as $recordSet) {
						$index = $recordSet->getTableName() . $recordSet->getField() . $recordSet->getIdentifier();
						$recordSets[$index] = array(
							$recordSet->getTableName(),
							$recordSet->getField(),
							$recordSet->getIdentifier(),
						);
					}

					$url = $linkCheck->getTestUrl();
					$rawUrls[$url] = $recordSets;
				}
			}
		}

		return $rawUrls;
	}
}

?>