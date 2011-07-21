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
 * RealUrl Import Service
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_RealUrlImportService implements t3lib_Singleton {
	/**
	 * Instance of the redirect test repository
	 *
	 * @var Tx_DfTools_Domain_Repository_RedirectTestRepository
	 */
	protected $redirectTestRepository = NULL;

	/**
	 * Instance of the redirect test category repository
	 *
	 * @var Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository
	 */
	protected $redirectTestCategoryRepository = NULL;

	/**
	 * Instance of the object manager
	 *
	 * @var Tx_ExtBase_Object_ObjectManager
	 */
	protected $objectManager = NULL;

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
	 * Injects the object manager
	 *
	 * @param Tx_ExtBase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_ExtBase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Returns all realUrl redirects
	 *
	 * @return array
	 */
	protected function getRealUrlRedirects() {
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('url, destination', 'tx_realurl_redirects');
	}

	/**
	 * Returns the category instance if one exists for the given category field value
	 *
	 * @param $category
	 * @return Tx_DfTools_Domain_Model_RedirectTestCategory|NULL
	 */
	protected function getCategoryByCategoryField($category) {
		/** @noinspection PhpUndefinedMethodInspection */
		return $this->redirectTestCategoryRepository->findOneByCategory($category);
	}

	/**
	 * Returns the import category for the redirect tests with the name "RealUrl"
	 *
	 * Note: If a category with the same name already exists, then the method
	 * doesn't creates a new one.
	 *
	 * @return Tx_DfTools_Domain_Model_RedirectTestCategory
	 */
	protected function getRedirectTestCategory() {
		$category = $this->getCategoryByCategoryField('RealUrl');
		if ($category === NULL) {
			/** @var $category Tx_DfTools_Domain_Model_RedirectTestCategory */
			$category = $this->objectManager->create('Tx_DfTools_Domain_Model_RedirectTestCategory');
			$category->setCategory('RealUrl');
			$this->redirectTestCategoryRepository->add($category);
		}

		return $category;
	}

	/**
	 * Returns the given url with an appended slash if it's a relative one
	 *
	 * @param string $url
	 * @return string
	 */
	protected function prepareUrl($url) {
		if (!preg_match('/(http|ftp)/i', $url) && strpos($url, '/') !== 0) {
			$url = '/' . $url;
		}

		return $url;
	}

	/**
	 * Returns true if the redirect test with the given url already exists
	 *
	 * @param string $url
	 * @return boolean
	 */
	protected function doesRedirectTestWithUrlAlreadyExists($url) {
		/** @noinspection PhpUndefinedMethodInspection */
		return $this->redirectTestRepository->countByTestUrl($url);
	}

	/**
	 * Creates redirect tests from realUrl redirect entries
	 *
	 * @return void
	 */
	public function importFromRealUrl() {
		$records = $this->getRealUrlRedirects();
		if (!count($records)) {
			return;
		}

		$category = $this->getRedirectTestCategory();
		foreach ($records as $record) {
			$url = $this->prepareUrl($record['url']);
			if (!$this->doesRedirectTestWithUrlAlreadyExists($url)) {
				/** @var $redirectTest Tx_DfTools_Domain_Model_RedirectTest */
				$redirectTest = $this->objectManager->create('Tx_DfTools_Domain_Model_RedirectTest');
				$redirectTest->setCategory($category);
				$redirectTest->setTestUrl($url);
				$redirectTest->setExpectedUrl($this->prepareUrl($record['destination']));
				$this->redirectTestRepository->add($redirectTest);
			}
		}
	}
}

?>