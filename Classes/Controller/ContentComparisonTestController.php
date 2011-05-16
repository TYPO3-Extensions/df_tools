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
 * Controller for the ContentComparisonTest domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_ContentComparisonTestController extends Tx_DfTools_Controller_AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_DfTools_View_ContentComparisonTest_ArrayView';

	/**
	 * contentComparisonTestRepository
	 *
	 * @var Tx_DfTools_Domain_Repository_ContentComparisonTestRepository
	 */
	protected $contentComparisonTestRepository;

	/**
	 * Initializes the current action
	 *
	 * @param Tx_DfTools_Domain_Repository_ContentComparisonTestRepository $contentComparisonTestRepository
	 * @return void
	 */
	public function injectContentComparisonTestRepository(Tx_DfTools_Domain_Repository_ContentComparisonTestRepository $contentComparisonTestRepository) {
		$this->contentComparisonTestRepository = $contentComparisonTestRepository;
	}

	/**
	 * @return void
	 */
	public function initializeIndexAction() {
		$this->defaultViewObjectName = 'Tx_Fluid_View_TemplateView';
	}

	/**
	 * Displays all content comparison tests
	 *
	 * @return string
	 */
	public function indexAction() {
		$this->setLastCalledControllerActionPair();
	}

	/**
	 * Reads all existing content comparison tests
	 *
	 * @return void
	 */
	public function readAction() {
		$tests = $this->contentComparisonTestRepository->findAll();
		$this->view->assign('records', $tests);
	}

	/**
	 * Creates a new content comparison test
	 *
	 * @param Tx_DfTools_Domain_Model_ContentComparisonTest $newContentComparisonTest
	 * @return void
	 */
	public function createAction(Tx_DfTools_Domain_Model_ContentComparisonTest $newContentComparisonTest) {
		$this->contentComparisonTestRepository->add($newContentComparisonTest);

		/** @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();

		$this->view->assign('records', array($newContentComparisonTest));
	}

	/**
	 * Updates an existing content comparison test
	 *
	 * @param Tx_DfTools_Domain_Model_ContentComparisonTest $contentComparisonTest
	 * @return void
	 */
	public function updateAction(Tx_DfTools_Domain_Model_ContentComparisonTest $contentComparisonTest) {
		$this->contentComparisonTestRepository->update($contentComparisonTest);
		$this->view->assign('records', array($contentComparisonTest));
	}

	/**
	 * Removes all content comparison tests that can be found with the given identifiers
	 *
	 * @param array[int] $identifiers
	 * @return void
	 */
	public function destroyAction(array $identifiers) {
		$this->view = null;

		foreach ($identifiers as $identifier) {
			$contentComparisonTest = $this->contentComparisonTestRepository->findByUid(intval($identifier));
			$this->contentComparisonTestRepository->remove($contentComparisonTest);
		}
	}

	/**
	 * Updates the test content of an action
	 *
	 * @param int $identity
	 * @return void
	 */
	public function updateTestContentAction($identity) {
		/** @var $contentComparisonTest Tx_DfTools_Domain_Model_ContentComparisonTest */
		$contentComparisonTest = $this->contentComparisonTestRepository->findByUid($identity);
		$urlCheckerService = $this->getUrlCheckerService();
		$contentComparisonTest->updateTestContent($urlCheckerService);
		$this->contentComparisonTestRepository->update($contentComparisonTest);
		$this->view->assign('records', array($contentComparisonTest));
	}

	/**
	 * Runs all available tests
	 *
	 * @return void
	 */
	public function runAllTestsAction() {
		/** @var $contentComparisonTest Tx_DfTools_Domain_Model_ContentComparisonTest */
		$contentComparisonTests = $this->contentComparisonTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		foreach ($contentComparisonTests as $contentComparisonTest) {
			$contentComparisonTest->test($urlCheckerService);
			$this->contentComparisonTestRepository->update($contentComparisonTest);
		}
		$this->view->assign('records', $contentComparisonTests);
	}

	/**
	 * Runs a single test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function runTestAction($identity) {
		/** @var $contentComparisonTest Tx_DfTools_Domain_Model_ContentComparisonTest */
		$contentComparisonTest = $this->contentComparisonTestRepository->findByUid($identity);
		$contentComparisonTest->test($this->getUrlCheckerService());
		$arguments = array('contentComparisonTest' => $contentComparisonTest->toArray());
		$this->forward('saveTest', NULL, NULL, $arguments);
	}

	/**
	 * Saves an content comparison test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param Tx_DfTools_Domain_Model_ContentComparisonTest $contentComparisonTest
	 * @return void
	 */
	protected function saveTestAction(Tx_DfTools_Domain_Model_ContentComparisonTest $contentComparisonTest) {
		$this->contentComparisonTestRepository->update($contentComparisonTest);
		$this->handleExceptionalTest($contentComparisonTest);
		$this->view->assign('records', array($contentComparisonTest));
	}
}

?>