<?php

namespace SGalinski\DfTools\Domain\Repository;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Abstract Repository
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class AbstractRepository extends Repository {
	/**
	 * @var \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected $pageSelect = NULL;

	/**
	 * Initializes the repository default settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		/** @var $querySettings Typo3QuerySettings */
		$querySettings = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * Returns an instance of t3lib_pageSelect to call the enableFields method
	 * for self-made queries.
	 *
	 * @return PageRepository
	 */
	public function getPageSelectInstance() {
		if ($this->pageSelect === NULL) {
			$this->pageSelect = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		}

		return $this->pageSelect;
	}

	/**
	 * Adds a range limiter and a sorting information to the given query
	 *
	 * @param Query $query
	 * @param int $offset
	 * @param int $limit
	 * @param array $sortingInformation
	 * @return Query
	 */
	protected function addSortedAndRangeToQuery($query, $offset, $limit, array $sortingInformation) {
		$query->setOffset(intval($offset));
		$query->setLimit(intval($limit));

		if (count($sortingInformation)) {
			foreach ($sortingInformation as $field => $direction) {
				$sortingInformation[$field] = ($direction ?
					QueryInterface::ORDER_ASCENDING :
					QueryInterface::ORDER_DESCENDING);
			}
			$query->setOrderings($sortingInformation);
		}

		return $query;
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
	public function findSortedAndInRange($offset, $limit, array $sortingInformation) {
		/** @var $query Query */
		$query = $this->createQuery();
		return $this->addSortedAndRangeToQuery($query, $offset, $limit, $sortingInformation)->execute();
	}
}

?>