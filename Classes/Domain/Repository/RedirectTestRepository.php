<?php

namespace SGalinski\DfTools\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 domainfactory GmbH (Stefan Galinski <stefan@sgalinski.de>)
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

use TYPO3\CMS\Dbal\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\DatabaseUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Repository for Tx_DfTools_Domain_Model_RedirectTest
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @package df_tools
 */
class RedirectTestRepository extends AbstractRepository {
	/**
	 * @var DataMapper
	 */
	protected $dataMapper;

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
	 * Injects the DataMapper to map nodes to objects
	 *
	 * @param DataMapper $dataMapper
	 * @return void
	 */
	public function injectDataMapper(DataMapper $dataMapper) {
		$this->dataMapper = $dataMapper;
	}

	/**
	 * Finds a range of records sorted by the given information's
	 *
	 * Note: The sortingInformation array consists of an undefined amount of
	 * additional sorters that are defined as key/value pairs. Each sorter consists
	 * of a field as key and a direction as boolean value there TRUE means ascending.
	 *
	 * Example:
	 * array('sorter1' => TRUE, 'sorter2' => FALSE);
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param array $sortingInformation
	 * @return QueryResult
	 */
	public function findSortedAndInRangeByCategory($offset, $limit, array $sortingInformation) {
		/** @var $pageSelect PageRepository */
		$pageSelect = $this->getPageSelectInstance();
		/** @var $dbConnection DatabaseConnection */
		$dbConnection = $GLOBALS['TYPO3_DB'];
		$categoryEnableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttestcategory');
		$enableFields = $pageSelect->enableFields('tx_dftools_domain_model_redirecttest');

		$orderings = array('tx_dftools_domain_model_redirecttestcategory.category ASC');
		foreach ($sortingInformation as $field => $direction) {
			$direction = ($direction ? 'ASC' : 'DESC');
			if ($field === 'categoryId') {
				$orderings[0] = 'tx_dftools_domain_model_redirecttestcategory.category ' . $direction;
			} else {
				$class = 'SGalinski\DfTools\Domain\Model\RedirectTest';
				$field = $this->dataMapper->convertPropertyNameToColumnName($field, $class);
				$field = $dbConnection->fullQuoteStr($field, 'SGalinski\DfTools\Domain\Model\RedirectTest');
				$orderings[] = trim($field, '\'') . ' ' . $direction;
			}
		}

		/** @var $query Query */
		$query = $this->createQuery();
		$query->statement(
			'SELECT tx_dftools_domain_model_redirecttest.* ' .
			'FROM tx_dftools_domain_model_redirecttest ' .
			'LEFT JOIN tx_dftools_domain_model_redirecttestcategory ' .
			'ON tx_dftools_domain_model_redirecttest.category = ' .
			'tx_dftools_domain_model_redirecttestcategory.uid' .
			$categoryEnableFields .
			' WHERE 1=1' . $enableFields .
			' ORDER BY ' . implode(', ', $orderings) .
			' LIMIT ' . intval($offset) . ', ' . intval($limit)
		);

		return $query->execute();
	}
}

?>