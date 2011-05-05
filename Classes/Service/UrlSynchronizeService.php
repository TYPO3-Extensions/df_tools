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
 * Synchronization Service For The Link Check Aggregate
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlSynchronizeService implements t3lib_Singleton {
	/**
	 * @var Tx_DfTools_Domain_Repository_RecordSetRepository
	 */
	protected $recordSetRepository;

	/**
	 * @var Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * Injects the link check test repository
	 *
	 * @param Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository
	 * @return void
	 */
	public function injectLinkCheckRepository(Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository) {
		$this->linkCheckRepository = $linkCheckRepository;
	}

	/**
	 * Injects the record set repository
	 *
	 * @param Tx_DfTools_Domain_Repository_RecordSetRepository $recordSetRepository
	 * @return void
	 */
	public function injectRecordSetRepository(Tx_DfTools_Domain_Repository_RecordSetRepository $recordSetRepository) {
		$this->recordSetRepository = $recordSetRepository;
	}

	/**
	 * Injects the object manager
	 *
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Returns the persistence manager instance
	 *
	 * @return Tx_Extbase_Persistence_Manager
	 */
	public function getPersistenceManager() {
		return $this->objectManager->get('Tx_Extbase_Persistence_Manager');
	}

	/**
	 * Returns the existing raw url data without the record sets!
	 *
	 * @return array $array[<test_url>] = <urlData>
	 */
	protected function fetchExistingRawUrls() {
		$enableFields = $this->linkCheckRepository->getPageSelectInstance()->enableFields(
			'tx_dftools_domain_model_linkcheck', 1,
			array('starttime' => TRUE, 'endtime' => TRUE, 'fe_group' => TRUE)
		);

		$urls = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, test_url',
			'tx_dftools_domain_model_linkcheck',
			'1=1' . $enableFields,
			'', '', '', 'test_url'
		);

		return $urls;
	}

	/**
	 * Returns the existing raw url data without the record sets!
	 *
	 * @return array $array[<table><identifier>] = <recordSetData>
	 */
	protected function fetchExistingRawRecordSets() {
		$enableFields = $this->linkCheckRepository->getPageSelectInstance()->enableFields(
			'tx_dftools_domain_model_recordset', 1,
			array('starttime' => TRUE, 'endtime' => TRUE, 'fe_group' => TRUE)
		);

		$resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, table_name, identifier',
			'tx_dftools_domain_model_recordset',
			'1=1' . $enableFields
		);

		$recordSets = array();
		if (!$resource) {
			return $recordSets;
		}

		while (($recordSet = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resource))) {
			$identifier = $recordSet['table_name'] . $recordSet['identifier'];
			$recordSets[$identifier] = $recordSet;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($resource);

		return $recordSets;
	}

	/**
	 * Returns a record set based on the given identifier
	 *
	 * Note: The record set will be created if it doesn't exists!
	 *
	 * @param string $table
	 * @param string $identifier
	 * @param array $existingRawRecordSets by reference
	 * @return Tx_DfTools_Domain_Model_RecordSet
	 */
	protected function getValidRecordSet($table, $identifier, array &$existingRawRecordSets) {
		$index = $table . $identifier;
		if (!isset($existingRawRecordSets[$index])) {
			/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */
			$recordSet = $this->objectManager->create('Tx_DfTools_Domain_Model_RecordSet');
			$recordSet->setTableName($table);
			$recordSet->setIdentifier($identifier);

			$aggregateRootObjects = new Tx_Extbase_Persistence_ObjectStorage();
			$aggregateRootObjects->attach($recordSet);

			$persistenceManager = $this->getPersistenceManager();
			$backend = $persistenceManager->getBackend();
			$backend->setAggregateRootObjects($aggregateRootObjects);
			$backend->commit();

			$existingRawRecordSets[$index] = array(
				'uid' => $recordSet->getUid(),
				'table_name' => $table,
				'identifier' => $identifier,
			);
		} else {
			$recordSet = $this->recordSetRepository->findByUid($existingRawRecordSets[$index]['uid']);
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
	 * @param Tx_DfTools_Domain_Model_LinkCheck $record
	 * @return bool
	 */
	protected function removeUnknownRecordSetsFromUrlRecord(array &$rawUrls, Tx_DfTools_Domain_Model_LinkCheck $record) {
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */
		$recordWasEdited = FALSE;
		$recordSets = $record->getRecordSets();
		foreach ($recordSets as $recordSet) {
			$index = $recordSet->getTableName() . $recordSet->getIdentifier();
			if (!isset($rawUrls[$index])) {
				$recordWasEdited = TRUE;
				$record->removeRecordSet($recordSet);
			}
			unset($rawUrls[$index]);
		}

		return $recordWasEdited;
	}

	/**
	 * Adds missing record sets to an url record based on the given raw url information's
	 *
	 * @param array $rawUrls by reference
	 * @param Tx_DfTools_Domain_Model_LinkCheck $record
	 * @param array $existingRawRecordSets by reference
	 * @return void
	 */
	protected function addMissingRecordSetsToUrlRecord(array &$rawUrls, Tx_DfTools_Domain_Model_LinkCheck $record, array &$existingRawRecordSets) {
		foreach ($rawUrls as $rawRecordSet) {
			list($table, $identifier) = $rawRecordSet;

			$recordSet = $this->getValidRecordSet($table, $identifier, $existingRawRecordSets);
			$record->addRecordSet($recordSet);
		}
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
			/** @var $record Tx_DfTools_Domain_Model_LinkCheck */
			$record = $this->objectManager->create('Tx_DfTools_Domain_Model_LinkCheck');
			$record->setTestUrl($url);

			foreach ($rawRecordSets as $rawRecordSet) {
				list($table, $identifier) = $rawRecordSet;

				$recordSet = $this->getValidRecordSet($table, $identifier, $existingRawRecordSets);
				$record->addRecordSet($recordSet);
			}

			$this->linkCheckRepository->add($record);
		}
	}

	/**
	 * Synchronizes the link check and record set repositories
	 * with the given raw data.
	 *
	 * This means:
	 * - adding new urls with their related record sets
	 * - edit the record sets of existing urls
	 * - remove non-existing urls with their related records sets
	 *
	 * @param array $rawUrls
	 * @return void
	 */
	public function synchronize(array $rawUrls) {
		$existingRawUrls = $this->fetchExistingRawUrls();
		$existingRawRecordSets = $this->fetchExistingRawRecordSets();

		foreach ($existingRawUrls as $url => $urlData) {
			/** @var $record Tx_DfTools_Domain_Model_LinkCheck */
			$record = $this->linkCheckRepository->findByUid($urlData['uid']);

			if (!isset($rawUrls[$url])) {
				$this->linkCheckRepository->remove($record);
				continue;
			}

			$recordWasEdited = $this->removeUnknownRecordSetsFromUrlRecord($rawUrls[$url], $record);
			if (count($rawUrls[$url])) {
				$recordWasEdited = TRUE;
				$this->addMissingRecordSetsToUrlRecord($rawUrls[$url], $record, $existingRawRecordSets);
			}

			if ($recordWasEdited) {
				$this->linkCheckRepository->update($record);
			}

			unset($rawUrls[$url]);
		}

		$this->addUrlRecords($rawUrls, $existingRawRecordSets);
	}
}

?>