<?php

namespace SGalinski\DfTools\Controller;

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

use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Repository\LinkCheckRepository;
use SGalinski\DfTools\Domain\Service\LinkCheckService;
use SGalinski\DfTools\Domain\Service\UrlSynchronizeService;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\Utility\LocalizationUtility;
use SGalinski\DfTools\Utility\PageUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Controller for the LinkCheck domain model
 */
class LinkCheckController extends AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'SGalinski\DfTools\View\LinkCheckArrayView';

	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->defaultViewObjectName = 'TYPO3\CMS\Fluid\View\TemplateView';
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
		/** @var $linkChecks ObjectStorage */
		$linkChecks = $this->linkCheckRepository->findSortedAndInRange(
			$offset, $limit, array($sortingField => $sortAscending)
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
		/** @var $linkCheck LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$linkCheck->setTestMessage('');
		$linkCheck->setResultUrl('');
		$linkCheck->setHttpStatusCode(0);

		if ($doIgnoreRecord) {
			$linkCheck->setTestResult(AbstractService::SEVERITY_IGNORE);
		} else {
			$linkCheck->setTestResult(AbstractService::SEVERITY_UNTESTED);
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
		/** @var $record LinkCheck */
		$record = $this->linkCheckRepository->findByUid($identity);

		if ($isFalsePositive) {
			$record->setTestResult(AbstractService::SEVERITY_INFO);
		} else {
			$record->setTestResult(AbstractService::SEVERITY_UNTESTED);
		}

		$this->linkCheckRepository->update($record);
		$this->view->assign('records', array($record));
	}

	/**
	 * Synchronizes the whole link check repository
	 *
	 * @return void
	 */
	public function synchronizeAction() {
		$this->view = NULL;

		/** @var $linkCheckService LinkCheckService */
		$linkCheckService = $this->objectManager->get('SGalinski\DfTools\Domain\Service\LinkCheckService');
		$rawUrls = $linkCheckService->fetchAllRawUrlsFromTheDatabase(
			$this->extensionConfiguration['excludedTables'],
			$this->extensionConfiguration['excludedTableFields']
		);

		/** @var $urlSynchronizationService UrlSynchronizeService */
		$existingLinkChecks = $this->linkCheckRepository->findAll();
		$urlSynchronizationService = $this->objectManager->get(
			'SGalinski\DfTools\Domain\Service\UrlSynchronizeService'
		);
		$urlSynchronizationService->synchronize($rawUrls, $existingLinkChecks);
	}

	/**
	 * Synchronizes all urls that are defined in a single record
	 *
	 * @param string $table
	 * @param int $identity
	 * @return void
	 */
	public function synchronizeUrlsFromASingleRecordAction($table, $identity) {
		$this->view = NULL;

		/** @var $linkCheckService LinkCheckService */
		$linkCheckService = $this->objectManager->get('SGalinski\DfTools\Domain\Service\LinkCheckService');
		$rawUrls = $linkCheckService->getUrlsFromSingleRecord($table, $identity);

		/** @var $urlSynchronizationService UrlSynchronizeService */
		$class = 'SGalinski\DfTools\Domain\Service\UrlSynchronizeService';
		$urlSynchronizationService = $this->objectManager->get($class);
		$urlSynchronizationService->synchronizeGroupOfUrls($rawUrls);
	}

	/**
	 * @return void
	 */
	public function initializeReadRecordSetsAction() {
		$this->defaultViewObjectName = 'SGalinski\DfTools\View\RecordSetArrayView';
	}

	/**
	 * Reads the record sets for a given link check test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function readRecordSetsAction($identity) {
		/** @var $linkCheck LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$this->view->assign('records', $linkCheck->getRecordSets());
	}

	/**
	 * Evaluates a test and writes the results into the link check test
	 *
	 * @param array $report
	 * @param LinkCheck $linkCheck
	 * @return void
	 */
	protected function evalTestResult(array $report, LinkCheck $linkCheck) {
		$result = AbstractService::SEVERITY_OK;
		$testUrl = $linkCheck->getTestUrl();
		$message = '';

		if (!in_array($report['http_code'], array(200, 301, 302))) {
			$result = AbstractService::SEVERITY_ERROR;
			$message = LocalizationUtility::createLocalizableParameterDrivenString(
				'tx_dftools_domain_model_LinkCheck.test.httpCodeMismatch',
				array('[200, 301, 302]', $report['http_code'])
			);

		} elseif ($report['url'] !== $testUrl) {
			$result = AbstractService::SEVERITY_WARNING;
			$message = LocalizationUtility::createLocalizableParameterDrivenString(
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
	 * Runs a single test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function runTestAction($identity) {
		/** @var $linkCheck LinkCheck */
		$linkCheck = $this->linkCheckRepository->findByUid($identity);
		$linkCheck->test($this->getUrlCheckerService());
		$this->forward('saveTest', NULL, NULL, array('linkCheck' => $linkCheck));
	}

	/**
	 * Saves an link check test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param LinkCheck $linkCheck
	 * @return void
	 */
	protected function saveTestAction(LinkCheck $linkCheck) {
		$this->linkCheckRepository->update($linkCheck);
		$this->handleExceptionalTest($linkCheck);
		$this->view->assign('records', array($linkCheck));
	}

	/**
	 * Returns the url to the frontend page of a table/id pair
	 *
	 * @param string $tableName
	 * @param int $identifier
	 * @return string
	 */
	public function getViewLinkAction($tableName, $identifier) {
		return PageUtility::getViewLinkFromTableNameAndIdPair($tableName, $identifier);
	}
}

?>