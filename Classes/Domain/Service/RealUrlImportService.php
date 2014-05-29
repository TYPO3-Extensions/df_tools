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

use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * RealUrl Import Service
 */
class RealUrlImportService implements SingletonInterface {
	/**
	 * Instance of the redirect test repository
	 *
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestRepository
	 */
	protected $redirectTestRepository;

	/**
	 * Instance of the redirect test category repository
	 *
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository
	 */
	protected $redirectTestCategoryRepository;

	/**
	 * Instance of the object manager
	 *
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Returns all realUrl redirects
	 *
	 * @return array
	 */
	protected function getRealUrlRedirects() {
		/** @var DatabaseConnection $db */
		$db = $GLOBALS['TYPO3_DB'];
		return $db->exec_SELECTgetRows('url, destination', 'tx_realurl_redirects', '');
	}

	/**
	 * Returns the category instance if one exists for the given category field value
	 *
	 * @param $category
	 * @return RedirectTestCategory|NULL
	 */
	protected function getCategoryByCategoryField($category) {
		/** @noinspection PhpUndefinedMethodInspection */
		return $this->redirectTestCategoryRepository->findOneByCategory($category);
	}

	/**
	 * Returns the import category for the redirect tests with the name "RealUrl"
	 *
	 * Note: If a category with the same name already exists, then the method
	 * does not creates a new one.
	 *
	 * @return RedirectTestCategory
	 */
	protected function getRedirectTestCategory() {
		$category = $this->getCategoryByCategoryField('RealUrl');
		if ($category === NULL) {
			/** @var $category RedirectTestCategory */
			$category = $this->objectManager->get('SGalinski\DfTools\Domain\Model\RedirectTestCategory');
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
				/** @var $redirectTest RedirectTest */
				$redirectTest = $this->objectManager->get('SGalinski\DfTools\Domain\Model\RedirectTest');
				$redirectTest->setCategory($category);
				$redirectTest->setTestUrl($url);
				$redirectTest->setExpectedUrl($this->prepareUrl($record['destination']));
				$this->redirectTestRepository->add($redirectTest);
			}
		}
	}
}

?>