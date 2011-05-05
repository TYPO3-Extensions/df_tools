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
 * Repository for Tx_DfTools_Domain_Model_RedirectTestCategory
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository extends Tx_DfTools_Domain_Repository_AbstractRepository {
	/**
	 * Default settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();

		$this->setDefaultOrderings(
			array('category' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);
	}

	/**
	 * Returns a collection of categories filtered by the given filter
	 *
	 * @param string $filterString
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function findByStartingCategory($filterString) {
		$query = $this->createQuery();
		$query->matching($query->like('category', $filterString . '%'));
		return $query->execute();
	}

	/**
	 * Returns a collection of categories that are currently unused
	 *
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function findAllUnusedCategories() {
		/** @var $pageSelect t3lib_pageSelect */
		$pageSelect = $this->getPageSelectInstance();
		$categoryEnableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttestcategory');
		$enableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttest');

		/** @var $query Tx_Extbase_Persistence_Query */
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
	 * @throws Exception if the category already exists
	 * @param string $category
	 * @return bool
	 */
	protected function checkIfCategoryNameIsAlreadyAssigned($category) {
		/** @var $result Tx_Extbase_Persistence_QueryResult */
		/** @noinspection PhpUndefinedMethodInspection */
		$result = $this->findByCategory($category);
		if ($result !== NULL && $result->count()) {
			$label = 'tx_dftools_domain_model_redirecttestcategory.categoryExists';
			$errorMessage = Tx_Extbase_Utility_Localization::translate($label, 'df_tools', array($category));
			throw new Exception($errorMessage);
		}
	}

	/**
	 * Wrapper for the add repository method to do some validity checks
	 *
	 * @param Tx_DfTools_Domain_Model_RedirectTestCategory $redirectTestCategory
	 * @return void
	 */
	public function add($redirectTestCategory) {
		$this->checkIfCategoryNameIsAlreadyAssigned($redirectTestCategory->getCategory());
		parent::add($redirectTestCategory);
	}

	/**
	 * Wrapper for the update repository method to do some validity checks
	 *
	 * @param Tx_DfTools_Domain_Model_RedirectTestCategory $redirectTestCategory
	 * @return void
	 */
	public function update($redirectTestCategory) {
		$this->checkIfCategoryNameIsAlreadyAssigned($redirectTestCategory->getCategory());
		parent::update($redirectTestCategory);
	}
}

?>