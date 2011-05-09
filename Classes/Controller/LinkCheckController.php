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
 * Controller for the LinkCheck domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_LinkCheckController extends Tx_DfTools_Controller_AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_DfTools_View_LinkCheck_ArrayView';

	/**
	 * @var Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * @var Tx_DfTools_Domain_Repository_RecordSetRepository
	 */
	protected $recordSetRepository;

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
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->defaultViewObjectName = 'Tx_Fluid_View_TemplateView';
	}

	/**
	 * Displays all link checks
	 *
	 * @return string
	 */
	public function indexAction() {
		$this->setLastCalledControllerActionPair();
	}

	/**
	 * Fetches all available link check tests
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $sortingField
	 * @param boolean $sortAscending
	 * @return void
	 */
	public function readAction($offset, $limit, $sortingField, $sortAscending) {
		/** @var $linkChecks Tx_Extbase_Persistence_ObjectStorage */
		$linkChecks = $this->linkCheckRepository->findSortedAndInRange(
			$offset, $limit,
			$sortingField, $sortAscending
		);

		$this->view->assign('records', $linkChecks);
		$this->view->assign('totalRecords', $this->linkCheckRepository->countAll());
	}

	/**
	 * Resets a test record
	 *
	 * @param int $identity
	 * @param boolean $doIgnoreRecord
	 * @return void
	 */
	public function resetRecordAction($identity, $doIgnoreRecord) {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$linkCheck->setTestMessage('');
		$linkCheck->setResultUrl('');
		$linkCheck->setHttpStatusCode(0);

		if ($doIgnoreRecord) {
			$linkCheck->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_IGNORE);
		} else {
			$linkCheck->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED);
		}

		$this->linkCheckRepository->update($linkCheck);
		$this->view->assign('records', array($linkCheck));
	}

	/**
	 * Sets the state of a record to false positive or resets this state depending on the parameters
	 *
	 * @param int $identity
	 * @param boolean $isFalsePositive true or false
	 * @return void
	 */
	public function setFalsePositiveStateAction($identity, $isFalsePositive) {
		/** @var $record Tx_DfTools_Domain_Model_LinkCheck */
		$record = $this->linkCheckRepository->findByUid($identity);

		if ($isFalsePositive) {
			$record->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO);
		} else {
			$record->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED);
		}

		$this->linkCheckRepository->update($record);
		$this->view->assign('records', array($record));
	}

	/**
	 * Returns the parsed urls of a site
	 *
	 * @return array
	 */
	protected function fetchRawUrls() {
		$excludedTables = array();
		$excludedTablesString = $this->extensionConfiguration['excludedTables'];
		if ($excludedTablesString !== '') {
			$excludedTables = explode(',', trim($excludedTablesString, ','));
		}

		$excludedTableFields = array();
		$excludedTableFieldsString = $this->extensionConfiguration['excludedTableFields'];
		if ($excludedTableFieldsString !== '') {
			$excludedTableFields = explode(',', trim($excludedTableFieldsString, ','));
		}

		/** @var $urlParser Tx_DfTools_Service_UrlParserService */
		$urlParser = $this->objectManager->get('Tx_DfTools_Service_UrlParserService');
		$urlParser->injectTcaParser($this->objectManager->get('Tx_DfTools_Service_TcaParserService'));
		$rawUrlData = $urlParser->fetchUrls($excludedTables, $excludedTableFields);

		return $rawUrlData;
	}

	/**
	 * Synchronizes the whole link check repository
	 *
	 * @return void
	 */
	public function synchronizeAction() {
		$this->view = NULL;

		/** @var $urlSynchronizationService Tx_DfTools_Service_UrlSynchronizeService */
		$urlSynchronizationService = $this->objectManager->get('Tx_DfTools_Service_UrlSynchronizeService');
		$urlSynchronizationService->injectRecordSetRepository($this->recordSetRepository);
		$urlSynchronizationService->injectLinkCheckRepository($this->linkCheckRepository);
		$urlSynchronizationService->injectObjectManager($this->objectManager);
		$urlSynchronizationService->synchronize($this->fetchRawUrls());
	}

	/**
	 * @return void
	 */
	public function initializeReadRecordSetsAction() {
		$this->defaultViewObjectName = 'Tx_DfTools_View_RecordSet_ArrayView';
	}

	/**
	 * Reads the record sets for a given link check test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function readRecordSetsAction($identity) {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$this->view->assign('records', $linkCheck->getRecordSets());
	}

	/**
	 * Evaluates a test and writes the results into the link check test
	 *
	 * @param array $report
	 * @param Tx_DfTools_Domain_Model_LinkCheck $linkCheck
	 * @return void
	 */
	protected function evalTestResult(array $report, Tx_DfTools_Domain_Model_LinkCheck $linkCheck) {
		$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK;
		$testUrl = $linkCheck->getTestUrl();
		$message = '';

		if (!in_array($report['http_code'], array(200, 301, 302))) {
			$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR;
			$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
				'tx_dftools_domain_model_LinkCheck.test.httpCodeMismatch',
				array('[200, 301, 302]', $report['http_code'])
			);

		} elseif ($report['url'] !== $testUrl) {
			$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_WARNING;
			$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
				'tx_dftools_domain_model_LinkCheck.test.urlMismatch',
				array($testUrl, $report['url'])
			);
		}

		$linkCheck->setTestResult($result);
		$linkCheck->setTestMessage($message);
		$linkCheck->setResultUrl($report['url']);
		$linkCheck->setHttpStatusCode($report['http_code']);
	}

	/**
	 * Runs all available tests
	 *
	 * @return void
	 */
	public function runAllTestsAction() {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$linkChecks = $this->linkCheckRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		foreach ($linkChecks as $linkCheck) {
			$linkCheck->test($urlCheckerService);
			$this->linkCheckRepository->update($linkCheck);
		}
		$this->view->assign('records', $linkChecks);
	}

	/**
	 * Runs a single test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function runTestAction($identity) {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$linkCheck->test($this->getUrlCheckerService());
		$this->forward('saveTest', NULL, NULL, array('linkCheck' => $linkCheck->toArray()));
	}

	/**
	 * Saves an link check test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param Tx_DfTools_Domain_Model_LinkCheck $linkCheck
	 * @return void
	 */
	protected function saveTestAction(Tx_DfTools_Domain_Model_LinkCheck $linkCheck) {
		$this->linkCheckRepository->update($linkCheck);
		$this->handleExceptionalTest($linkCheck);
		$this->view->assign('records', array($linkCheck));
	}
}

?>