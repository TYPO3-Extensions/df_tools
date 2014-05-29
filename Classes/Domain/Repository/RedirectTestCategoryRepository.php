<?php

namespace SGalinski\DfTools\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2012 domainfactory GmbH (Stefan Galinski <stefan@sgalinski.de>)
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
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Exception\GenericException;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Repository for Tx_DfTools_Domain_Model_RedirectTestCategory
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @package df_tools
 */
class RedirectTestCategoryRepository extends AbstractRepository {
	/**
	 * Default settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();

		$this->setDefaultOrderings(
			array('category' => QueryInterface::ORDER_ASCENDING)
		);
	}

	/**
	 * Returns a collection of categories filtered by the given filter
	 *
	 * @param string $filterString
	 * @return QueryResult
	 */
	public function findByStartingCategory($filterString) {
		$query = $this->createQuery();
		$query->matching($query->like('category', $filterString . '%'));
		return $query->execute();
	}

	/**
	 * Returns a collection of categories that are currently unused
	 *
	 * @return QueryResult
	 */
	public function findAllUnusedCategories() {
		/** @var $pageSelect PageRepository */
		$pageSelect = $this->getPageSelectInstance();
		$categoryEnableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttestcategory');
		$enableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttest');

		/** @var $query Query */
		$query = $this->createQuery();
		$query->statement(
			'SELECT tx_dftools_domain_model_redirecttestcategory.* ' .
			'FROM tx_dftools_domain_model_redirecttestcategory ' .
			'LEFT JOIN tx_dftools_domain_model_redirecttest ' .
			'ON tx_dftools_domain_model_redirecttest.category = ' .
			'tx_dftools_domain_model_redirecttestcategory.uid ' .
			$enableFields .
			'WHERE tx_dftools_domain_model_redirecttest.uid IS NULL ' .
			$categoryEnableFields
		);

		return $query->execute();
	}

	/**
	 * Checks if the given category name is already assigned
	 *
	 * @throws GenericException if the category already exists
	 * @param string $category
	 * @return bool
	 */
	protected function checkIfCategoryNameIsAlreadyAssigned($category) {
		/** @var $result QueryResult */
		/** @noinspection PhpUndefinedMethodInspection */
		$result = $this->findByCategory($category);
		if ($result !== NULL && $result->count()) {
			$label = 'tx_dftools_domain_model_redirecttestcategory.categoryExists';
			$errorMessage = LocalizationUtility::translate($label, 'df_tools', array($category));
			throw new GenericException($errorMessage);
		}
	}

	/**
	 * Wrapper for the add repository method to do some validity checks
	 *
	 * @param RedirectTestCategory $redirectTestCategory
	 * @return void
	 */
	public function add($redirectTestCategory) {
		$this->checkIfCategoryNameIsAlreadyAssigned($redirectTestCategory->getCategory());
		parent::add($redirectTestCategory);
	}

	/**
	 * Wrapper for the update repository method to do some validity checks
	 *
	 * @param RedirectTestCategory $redirectTestCategory
	 * @return void
	 */
	public function update($redirectTestCategory) {
		$this->checkIfCategoryNameIsAlreadyAssigned($redirectTestCategory->getCategory());
		parent::update($redirectTestCategory);
	}
}

?>