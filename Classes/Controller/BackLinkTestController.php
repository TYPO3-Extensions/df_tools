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
 * Controller for the BackLinkTest domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_BackLinkTestController extends Tx_DfTools_Controller_AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_DfTools_View_BackLinkTest_ArrayView';

	/**
	 * Instance of the back link test repository
	 *
	 * @var Tx_DfTools_Domain_Repository_BackLinkTestRepository
	 */
	protected $backLinkTestRepository;

	/**
	 * Injects the back link test repository
	 *
	 * @param Tx_DfTools_Domain_Repository_BackLinkTestRepository $backLinkTestRepository
	 * @return void
	 */
	public function injectBackLinkTestRepository(Tx_DfTools_Domain_Repository_BackLinkTestRepository $backLinkTestRepository) {
		$this->backLinkTestRepository = $backLinkTestRepository;
	}

	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->defaultViewObjectName = 'Tx_Fluid_View_TemplateView';
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
	 * @return void
	 */
	public function readAction() {
		$records = $this->backLinkTestRepository->findAll();
		$this->view->assign('records', $records);
	}

	/**
	 * Updates an existing back link test
	 *
	 * @param Tx_DfTools_Domain_Model_BackLinkTest $backLinkTest
	 * @return void
	 */
	public function updateAction(Tx_DfTools_Domain_Model_BackLinkTest $backLinkTest) {
		$this->backLinkTestRepository->update($backLinkTest);
		$this->view->assign('records', array($backLinkTest));
	}

	/**
	 * Creates a back link test
	 *
	 * @param Tx_DfTools_Domain_Model_BackLinkTest $newBackLinkTest
	 * @return void
	 */
	public function createAction(Tx_DfTools_Domain_Model_BackLinkTest $newBackLinkTest) {
		$this->backLinkTestRepository->add($newBackLinkTest);

		/** @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();

		$this->view->assign('records', array($newBackLinkTest));
	}

	/**
	 * Removes all back link tests that can be found with the given identifiers
	 *
	 * @param array[int] $identifiers
	 * @return void
	 */
	public function destroyAction($identifiers) {
		$this->view = null;

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
		/** @var $backLinkTest Tx_DfTools_Domain_Model_BackLinkTest */
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
		/** @var $backLinkTest Tx_DfTools_Domain_Model_BackLinkTest */
		$backLinkTest = $this->backLinkTestRepository->findByUid($identity);
		$backLinkTest->test($this->getUrlCheckerService());
		$this->forward('saveTest', NULL, NULL, array('backLinkTest' => $backLinkTest->toArray()));
	}

	/**
	 * Saves a back link test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param Tx_DfTools_Domain_Model_BackLinkTest $backLinkTest
	 * @return void
	 */
	protected function saveTestAction(Tx_DfTools_Domain_Model_BackLinkTest $backLinkTest) {
		$this->backLinkTestRepository->update($backLinkTest);
		$this->handleExceptionalTest($backLinkTest);
		$this->view->assign('records', array($backLinkTest));
	}
}

?>