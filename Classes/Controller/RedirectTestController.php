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
 * Controller for the RedirectTest domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_RedirectTestController extends Tx_DfTools_Controller_AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_DfTools_View_RedirectTest_ArrayView';

	/**
	 * Instance of the redirect test repository
	 *
	 * @var Tx_DfTools_Domain_Repository_RedirectTestRepository
	 */
	protected $redirectTestRepository;

	/**
	 * Instance of the redirect test category repository
	 *
	 * @var Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository
	 */
	protected $redirectTestCategoryRepository;

	/**
	 * Injects the redirect test repository
	 *
	 * @param Tx_DfTools_Domain_Repository_RedirectTestRepository $redirectTestRepository
	 * @return void
	 */
	public function injectRedirectTestRepository(Tx_DfTools_Domain_Repository_RedirectTestRepository $redirectTestRepository) {
		$this->redirectTestRepository = $redirectTestRepository;
	}

	/**
	 * Injects the redirect test category repository
	 *
	 * @param Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository $redirectTestCategoryRepository
	 * @return void
	 */
	public function injectRedirectTestCategoryRepository(Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository $redirectTestCategoryRepository) {
		$this->redirectTestCategoryRepository = $redirectTestCategoryRepository;
	}

	/**
	 * Returns an instance of the realUrl import service
	 *
	 * @return Tx_DfTools_Service_RealUrlImportService
	 */
	public function getRealUrlImportService() {
		return $this->objectManager->get('Tx_DfTools_Service_RealUrlImportService');
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
	 * Returns all redirect test records
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $sortingField
	 * @param boolean $sortAscending
	 * @return void
	 */
	public function readAction($offset, $limit, $sortingField, $sortAscending) {
		/** @var $linkChecks Tx_Extbase_Persistence_ObjectStorage */
		$records = $this->redirectTestRepository->findSortedAndInRangeByCategory(
			$offset, $limit, array($sortingField => $sortAscending)
		);

		$this->view->assign('records', $records);
		$this->view->assign('totalRecords', $this->redirectTestRepository->countAll());
	}

	/**
	 * Updates an existing redirect test
	 *
	 * @param Tx_DfTools_Domain_Model_RedirectTest $redirectTest
	 * @param Tx_DfTools_Domain_Model_RedirectTestCategory $newCategory optional
	 * @return void
	 */
	public function updateAction(Tx_DfTools_Domain_Model_RedirectTest $redirectTest, Tx_DfTools_Domain_Model_RedirectTestCategory $newCategory = NULL) {
		if ($newCategory !== NULL) {
			$this->redirectTestCategoryRepository->add($newCategory);
			$redirectTest->setCategory($newCategory);

			/** @var $persistenceManager Tx_Extbase_Persistence_Manager */
			$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();
		}

		$this->redirectTestRepository->update($redirectTest);
		$this->view->assign('records', array($redirectTest));
	}

	/**
	 * Creates a redirect test
	 *
	 * @param Tx_DfTools_Domain_Model_RedirectTest $newRedirectTest
	 * @return void
	 */
	public function createAction(Tx_DfTools_Domain_Model_RedirectTest $newRedirectTest) {
		$this->redirectTestRepository->add($newRedirectTest);

		/** @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();

		$this->view->assign('records', array($newRedirectTest));
	}

	/**
	 * Removes all redirect tests that can be found with the given identifiers
	 *
	 * @param array $identifiers
	 * @return void
	 */
	public function destroyAction($identifiers) {
		$this->view = null;

		foreach ($identifiers as $identifier) {
			$redirectTest = $this->redirectTestRepository->findByUid(intval($identifier));
			$this->redirectTestRepository->remove($redirectTest);
		}
	}

	/**
	 * Imports entries from the realUrl redirect table as redirect tests
	 *
	 * @return void
	 */
	public function importFromRealUrlAction() {
		$importer = $this->getRealUrlImportService();
		$importer->importFromRealUrl();
		unset($importer);
	}

	/**
	 * Runs all available tests
	 *
	 * @return void
	 */
	public function runAllTestsAction() {
		/** @var $redirectTest Tx_DfTools_Domain_Model_RedirectTest */
		$redirectTests = $this->redirectTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		foreach ($redirectTests as $redirectTest) {
			$redirectTest->test($urlCheckerService);
			$this->redirectTestRepository->update($redirectTest);
		}
		$this->view->assign('records', $redirectTests);
	}

	/**
	 * Runs a single test
	 *
	 * @param int $identity
	 * @return void
	 */
	public function runTestAction($identity) {
		/** @var $redirectTest Tx_DfTools_Domain_Model_RedirectTest */
		$redirectTest = $this->redirectTestRepository->findByUid($identity);
		$redirectTest->test($this->getUrlCheckerService());
		$this->forward('saveTest', NULL, NULL, array('redirectTest' => $redirectTest->toArray()));
	}

	/**
	 * Saves an redirect test (just exists for validation issues)
	 *
	 * @dontverifyrequesthash
	 * @param Tx_DfTools_Domain_Model_RedirectTest $redirectTest
	 * @return void
	 */
	protected function saveTestAction(Tx_DfTools_Domain_Model_RedirectTest $redirectTest) {
		$this->redirectTestRepository->update($redirectTest);
		$this->handleExceptionalTest($redirectTest);
		$this->view->assign('records', array($redirectTest));
	}
}

?>