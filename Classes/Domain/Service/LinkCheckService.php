<?php

namespace SGalinski\DfTools\Domain\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan.galinski@gmail.com>
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

use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Model\RecordSet;
use SGalinski\DfTools\Domain\Repository\LinkCheckRepository;
use SGalinski\DfTools\Parser\UrlParser;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * LinkCheck Service
 */
class LinkCheckService implements SingletonInterface {
	/**
	 * Instance of the link check repository
	 *
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * Instance of the object manager
	 *
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

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
			$excludedTables = GeneralUtility::trimExplode(',', trim($excludedTablesString, ','));
		}

		$excludedTableFields = array();
		if ($excludedTableFieldsString !== '') {
			$excludedTableFields = GeneralUtility::trimExplode(',', trim($excludedTableFieldsString, ','));
		}

		/** @var $urlParser UrlParser */
		$urlParser = $this->objectManager->get('SGalinski\DfTools\Parser\UrlParser');
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
		$record = $this->getRecordByTableAndId($table, $identity);

		/** @var $urlParser UrlParser */
		$urlParser = $this->objectManager->get('SGalinski\DfTools\Parser\UrlParser');

		$rawUrls = array();
		if ($table === 'pages') {
			$rawUrls = $urlParser->fetchLinkCheckLinkType(array($identity));
		}

		$record['uid'] = $identity;
		$rawUrls = array_merge($rawUrls, $urlParser->parseRows(array($record), $table));

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
	 * Returns a raw database record for the given table and identity
	 *
	 * @param string $table
	 * @param int $identity
	 * @return array
	 */
	protected function getRecordByTableAndId($table, $identity) {
		return BackendUtility::getRecord($table, $identity);
	}

	/**
	 * Returns the records sets of a link check in a plain array structure
	 *
	 * @param LinkCheck $linkCheck
	 * @return array
	 */
	protected function getRecordSetsAsPlainArray(LinkCheck $linkCheck) {
		/** @var $recordSet RecordSet */
		$recordSets = array();
		foreach ($linkCheck->getRecordSets() as $recordSet) {
			$index = $recordSet->getTableName() . $recordSet->getField() . $recordSet->getIdentifier();
			$recordSets[$index] = array(
				$recordSet->getTableName(),
				$recordSet->getField(),
				$recordSet->getIdentifier(),
			);
		}

		return $recordSets;
	}

	/**
	 * Returns an array of raw url data with their record sets from a given bunch of test urls
	 *
	 * @param array $urls
	 * @return array
	 */
	protected function findExistingRawUrlsByTestUrls(array $urls) {
		/** @var $linkCheck LinkCheck */

		$rawUrls = array();
		foreach ($urls as $url) {
			/** @noinspection PhpUndefinedMethodInspection */
			$linkCheck = $this->linkCheckRepository->findOneByTestUrl($url);
			if (!$linkCheck) {
				continue;
			}

			$rawUrls[$url] = $this->getRecordSetsAsPlainArray($linkCheck);
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
		/** @var $dbConnection DatabaseConnection */
		$dbConnection = $GLOBALS['TYPO3_DB'];
		$table = $dbConnection->fullQuoteStr($table, 'tx_dftools_domain_model_linkcheck');
		$queryResult = $dbConnection->exec_SELECT_mm_query(
			'tx_dftools_domain_model_linkcheck.uid',
			'tx_dftools_domain_model_linkcheck',
			'tx_dftools_linkcheck_recordset_mm',
			'tx_dftools_domain_model_recordset',
			' AND tx_dftools_domain_model_recordset.table_name = ' . $table . ' AND ' .
			'tx_dftools_domain_model_recordset.identifier = ' . intval($identity)
		);

		$rawUrls = array();
		if (!$dbConnection->sql_error()) {
			$linkCheckIds = array();
			while (($record = $dbConnection->sql_fetch_assoc($queryResult))) {
				$linkCheckIds[] = $record['uid'];
			}
			$dbConnection->sql_free_result($queryResult);

			if (count($linkCheckIds)) {
				/** @var $linkCheck LinkCheck */
				$linkChecks = $this->linkCheckRepository->findInListByIdentity($linkCheckIds);
				foreach ($linkChecks as $linkCheck) {
					$url = $linkCheck->getTestUrl();
					$rawUrls[$url] = $this->getRecordSetsAsPlainArray($linkCheck);
				}
			}
		}

		return $rawUrls;
	}
}

?>