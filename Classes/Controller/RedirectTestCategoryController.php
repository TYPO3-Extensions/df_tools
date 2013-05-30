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

use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;

/**
 * Controller for the RedirectTestCategory domain model
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class RedirectTestCategoryController extends AbstractController {
	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'SGalinski\DfTools\View\RedirectTestCategory\ArrayView';

	/**
	 * Repository Instance
	 *
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository
	 */
	protected $redirectTestCategoryRepository;

	/**
	 * Injects the repository
	 *
	 * @param RedirectTestCategoryRepository $redirectTestCategoryRepository
	 * @return void
	 */
	public function injectRedirectTestCategoryRepository(
		RedirectTestCategoryRepository $redirectTestCategoryRepository
	) {
		$this->redirectTestCategoryRepository = $redirectTestCategoryRepository;
	}

	/**
	 * Returns all redirect test records
	 *
	 * @param string $filterString
	 * @return void
	 */
	public function readAction($filterString = '') {
		if ($filterString === '') {
			$categories = $this->redirectTestCategoryRepository->findAll();
		} else {
			/** @noinspection PhpUndefinedMethodInspection */
			$categories = $this->redirectTestCategoryRepository->findByStartingCategory($filterString);
		}
		$this->view->assign('records', $categories);
	}

	/**
	 * Updates an existing redirect test category
	 *
	 * @param RedirectTestCategory $redirectTestCategory
	 * @return void
	 */
	public function updateAction(RedirectTestCategory $redirectTestCategory) {
		$this->redirectTestCategoryRepository->update($redirectTestCategory);
		$this->view->assign('records', array($redirectTestCategory));
	}

	/**
	 * Deletes all unused categories
	 *
	 * @return void
	 */
	public function deleteUnusedCategoriesAction() {
		/** @var $category RedirectTestCategory */
		$categories = $this->redirectTestCategoryRepository->findAllUnusedCategories();
		foreach ($categories as $category) {
			$this->redirectTestCategoryRepository->remove($category);
		}
	}
}

?>