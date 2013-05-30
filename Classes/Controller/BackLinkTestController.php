<?php

namespace SGalinski\DfTools\Controller;

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

use SGalinski\DfTools\Domain\Model\BackLinkTest;
use SGalinski\DfTools\Domain\Repository\BackLinkTestRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Controller for the BackLinkTest domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class BackLinkTestController extends AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'SGalinski\DfTools\View\BackLinkTest\ArrayView';

	/**
	 * Instance of the back link test repository
	 *
	 * @var \SGalinski\DfTools\Domain\Repository\BackLinkTestRepository
	 */
	protected $backLinkTestRepository;

	/**
	 * Injects the back link test repository
	 *
	 * @param BackLinkTestRepository $backLinkTestRepository
	 * @return void
	 */
	public function injectBackLinkTestRepository(BackLinkTestRepository $backLinkTestRepository) {
		$this->backLinkTestRepository = $backLinkTestRepository;
	}

	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->defaultViewObjectName = 'TYPO3\CMS\Fluid\View\TemplateView';
	}

	/**
	 * Displays all redirect tests
	 *
	 * @return string
	 */
	public function indexAction() {
		$this->setLastCalledControllerActionPair();
	}

	/**
	 * Returns all back link test records
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $sortingField
	 * @param boolean $sortAscending
	 * @return void
	 */
	public function readAction($offset, $limit, $sortingField, $sortAscending) {
		/** @var $linkChecks ObjectStorage */
		$records = $this->backLinkTestRepository->findSortedAndInRange(
			$offset, $limit, array($sortingField => $sortAscending)
		);

		$this->view->assign('records', $records);
		$this->view->assign('totalRecords', $this->backLinkTestRepository->countAll());
	}

	/**
	 * Updates an existing back link test
	 *
	 * @param BackLinkTest $backLinkTest
	 * @return void
	 */
	public function updateAction(BackLinkTest $backLinkTest) {
		$this->backLinkTestRepository->update($backLinkTest);
		$this->view->assign('records', array($backLinkTest));
	}

	/**
	 * Creates a back link test
	 *
	 * @param BackLinkTest $newBackLinkTest
	 * @return void
	 */
	public function createAction(BackLinkTest $newBackLinkTest) {
		$this->backLinkTestRepository->add($newBackLinkTest);

		/** @var $persistenceManager PersistenceManager */
		$persistenceManager = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
		$persistenceManager->persistAll();

		$this->view->assign('records', array($newBackLinkTest));
	}

	/**
	 * Removes all back link tests that can be found with the given identifiers
	 *
	 * @param array $identifiers
	 * @return void
	 */
	public function destroyAction($identifiers) {
		$this->view = NULL;

		foreach ($identifiers as $identifier) {
			$redirectTest = $this->backLinkTestRepository->findByUid(intval($identifier));
			$this->backLinkTestRepository->remove($redirectTest);
		}
	}

	/**
	 * Runs all available tests
	 *
	 * @return void
	 */
	public function runAllTestsAction() {
		/** @var $backLinkTest BackLinkTest */
		$backLinkTests = $this->backLinkTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		foreach ($backLinkTests as $backLinkTest) {
			$backLinkTest->test($urlCheckerService);
			$this->backLinkTestRepository->update($backLinkTest);
		}
		$this->view->assign('records', $backLinkTests);
	}

	/**
	 * Runs a single test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function runTestAction($identity) {
		/** @var $backLinkTest BackLinkTest */
		$backLinkTest = $this->backLinkTestRepository->findByUid($identity);
		$backLinkTest->test($this->getUrlCheckerService());
		$this->forward('saveTest', NULL, NULL, array('backLinkTest' => $backLinkTest));
	}

	/**
	 * Saves a back link test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param BackLinkTest $backLinkTest
	 * @return void
	 */
	protected function saveTestAction(BackLinkTest $backLinkTest) {
		$this->backLinkTestRepository->update($backLinkTest);
		$this->handleExceptionalTest($backLinkTest);
		$this->view->assign('records', array($backLinkTest));
	}
}

?>