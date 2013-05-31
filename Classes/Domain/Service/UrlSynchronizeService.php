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
use SGalinski\DfTools\Domain\Repository\RecordSetRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Synchronization Service For The Link Check Aggregate
 */
class UrlSynchronizeService implements SingletonInterface {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\RecordSetRepository
	 */
	protected $recordSetRepository;

	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Returns the existing raw url data without the record sets!
	 *
	 * @return array $array[<table><field><identifier>] = <recordSetData>
	 */
	protected function fetchExistingRawRecordSets() {
		$enableFields = $this->linkCheckRepository->getPageSelectInstance()->enableFields(
			'tx_dftools_domain_model_recordset', 1,
			array('starttime' => TRUE, 'endtime' => TRUE, 'fe_group' => TRUE)
		);
		/** @var $dbConnection DatabaseConnection */
		$dbConnection = $GLOBALS['TYPO3_DB'];
		$resource = $dbConnection->exec_SELECTquery(
			'uid, table_name, field, identifier',
			'tx_dftools_domain_model_recordset',
			'1=1' . $enableFields
		);

		$recordSets = array();
		if (!$resource) {
			return $recordSets;
		}

		while (($recordSet = $dbConnection->sql_fetch_assoc($resource))) {
			$identifier = $recordSet['table_name'] . $recordSet['field'] . $recordSet['identifier'];
			$recordSets[$identifier] = $recordSet;
		}
		$dbConnection->sql_free_result($resource);

		return $recordSets;
	}

	/**
	 * Returns a record set based on the given identifier
	 *
	 * Note: The record set will be created if it does not exists!
	 *
	 * @param string $table
	 * @param string $field
	 * @param string $identifier
	 * @param array $existingRawRecordSets by reference
	 * @return RecordSet
	 */
	protected function getValidRecordSet($table, $field, $identifier, array &$existingRawRecordSets) {
		$index = $table . $field . $identifier;
		if (!isset($existingRawRecordSets[$index])) {
			/** @var $recordSet RecordSet */
			$recordSet = $this->objectManager->get('SGalinski\DfTools\Domain\Model\RecordSet');
			$recordSet->setTableName($table);
			$recordSet->setIdentifier($identifier);
			$recordSet->setField($field);
			$existingRawRecordSets[$index] = $recordSet;
			$this->recordSetRepository->add($recordSet);

		} elseif ($existingRawRecordSets[$index] instanceof RecordSet) {
			$recordSet = $existingRawRecordSets[$index];

		} else {
			$recordSet = $this->recordSetRepository->findByUid($existingRawRecordSets[$index]['uid']);
			$existingRawRecordSets[$index] = $recordSet;
		}

		return $recordSet;
	}

	/**
	 * Removes unknown record sets from the given url information's array
	 *
	 * The method returns a boolean that indicates if a record set was
	 * removed from the url record.
	 *
	 * @param array $rawUrls by reference
	 * @param LinkCheck $linkTest
	 * @return bool
	 */
	protected function removeUnknownRecordSetsFromUrlRecord(array &$rawUrls, LinkCheck $linkTest) {
		/** @var $recordSet RecordSet */
		$recordWasEdited = FALSE;
		$recordSets = $linkTest->getRecordSets();
		foreach ($recordSets as $recordSet) {
			$index = $recordSet->getTableName() . $recordSet->getField() . $recordSet->getIdentifier();
			if (!isset($rawUrls[$index]) || $recordSet->getField() === '') {
				$recordWasEdited = TRUE;
				$linkTest->removeRecordSet($recordSet);
			}
			unset($rawUrls[$index]);
		}

		return $recordWasEdited;
	}

	/**
	 * Adds missing record sets to an url record based on the given raw url information's. Returns
	 * a boolean TRUE if a record set was added, otherwise FALSE.
	 *
	 * @param array $rawUrls by reference
	 * @param LinkCheck $linkTest
	 * @param array $existingRawRecordSets by reference
	 * @return boolean
	 */
	protected function addMissingRecordSetsToUrlRecord(
		array &$rawUrls, LinkCheck $linkTest, array &$existingRawRecordSets
	) {
		$recordWasEdited = FALSE;
		foreach ($rawUrls as $rawRecordSet) {
			list($table, $field, $identifier) = $rawRecordSet;
			if ($field === '' || $table === '' || $identifier === '') {
				continue;
			}

			$recordWasEdited = TRUE;
			$recordSet = $this->getValidRecordSet($table, $field, $identifier, $existingRawRecordSets);
			$linkTest->addRecordSet($recordSet);
		}

		return $recordWasEdited;
	}

	/**
	 * Adds new url records with their related record sets based on the
	 * given raw url information's.
	 *
	 * @param array $rawUrls
	 * @param array $existingRawRecordSets by reference
	 * @return void
	 */
	protected function addUrlRecords(array $rawUrls, array &$existingRawRecordSets) {
		foreach ($rawUrls as $url => $rawRecordSets) {
			/** @var $record LinkCheck */
			$record = $this->objectManager->get('SGalinski\DfTools\Domain\Model\LinkCheck');
			$record->setTestUrl($url);

			foreach ($rawRecordSets as $rawRecordSet) {
				list($table, $field, $identifier) = $rawRecordSet;

				$recordSet = $this->getValidRecordSet($table, $field, $identifier, $existingRawRecordSets);
				$record->addRecordSet($recordSet);
			}

			$this->linkCheckRepository->add($record);
		}
	}

	/**
	 * Compares the records sets of the url with the raw data of the second parameter.
	 * Missing ones are added and others are removed.
	 *
	 * @param LinkCheck $linkTest
	 * @param array $rawUrlData
	 * @param array $existingRawRecordSets
	 * @return void
	 */
	protected function evaluateRecordSetDataOfUrl(
		LinkCheck $linkTest,
		array &$rawUrlData,
		array &$existingRawRecordSets
	) {
		$recordHasMissingRecordSets = FALSE;
		$recordHasUnknownRecordSets = $this->removeUnknownRecordSetsFromUrlRecord($rawUrlData, $linkTest);
		if (count($rawUrlData)) {
			$recordHasMissingRecordSets = $this->addMissingRecordSetsToUrlRecord(
				$rawUrlData, $linkTest, $existingRawRecordSets
			);
		}

		if ($recordHasUnknownRecordSets || $recordHasMissingRecordSets) {
			$this->linkCheckRepository->update($linkTest);
		}
	}

	/**
	 * Synchronizes the link check and record set repositories with the given raw data.
	 * In opposite to the synchronizeGroupOfUrls method this one traversals above all
	 * existing data.
	 *
	 * This means:
	 * - adding new urls with their related record sets
	 * - edit the record sets of existing urls
	 * - remove non-existing urls with their related records sets
	 *
	 * @param array $rawUrls
	 * @param QueryResultInterface $existingLinkTests
	 * @return void
	 */
	public function synchronize(array $rawUrls, $existingLinkTests) {
		$existingRawRecordSets = $this->fetchExistingRawRecordSets();

		/** @var $linkTest LinkCheck */
		foreach ($existingLinkTests as $linkTest) {
			$url = $linkTest->getTestUrl();
			if (!isset($rawUrls[$url])) {
				$this->linkCheckRepository->remove($linkTest);
				continue;
			}

			$this->evaluateRecordSetDataOfUrl($linkTest, $rawUrls[$url], $existingRawRecordSets);
			unset($rawUrls[$url]);
		}

		$this->addUrlRecords($rawUrls, $existingRawRecordSets);
	}

	/**
	 * Synchronizes the record set repositories of the given links. An url is
	 * removed if no further record sets are assigned to it.
	 *
	 * Note: Any link checks with no record sets in their rawUrl counterpart are removed!
	 *
	 * @param array $rawUrls
	 * @return void
	 */
	public function synchronizeGroupOfUrls(array $rawUrls) {
		/** @var $linkTest LinkCheck */
		$existingLinkTests = $this->linkCheckRepository->findInListByTestUrl(array_keys($rawUrls));
		foreach ($existingLinkTests as $index => $linkTest) {
			$url = $linkTest->getTestUrl();
			if (!count($rawUrls[$url])) {
				$this->linkCheckRepository->remove($linkTest);
				$existingLinkTests->offsetUnset($index);
				unset($rawUrls[$url]);
			}
		}

		if (count($existingLinkTests) || count($rawUrls)) {
			$this->synchronize($rawUrls, $existingLinkTests);
		}
	}
}

?>