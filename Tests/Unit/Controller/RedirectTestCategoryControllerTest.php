<?php

namespace SGalinski\DfTools\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use SGalinski\DfTools\Controller\RedirectTestCategoryController;
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class RedirectTestCategoryControllerTest
 */
class RedirectTestCategoryControllerTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Controller\RedirectTestCategoryController|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\Controller\RedirectTestCategoryController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @var $objectManager ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->fixture->_set('objectManager', $objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_View_RedirectTestCategory_ArrayView';
		$mockView = $this->getMock($class, array('assign'));
		$this->fixture->_set('view', $mockView);
	}

	/**
	 * @test
	 * @return void
	 */
	public function readWithoutFilterFindAllResults() {
		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$mockRepository = $this->getMock($class, array('findAll'), array($this->objectManager));
		$mockRepository->expects($this->once())->method('findAll');

		$this->fixture->_set('redirectTestCategoryRepository', $mockRepository);
		$this->fixture->readAction();
	}

	/**
	 * @test
	 * @return void
	 */
	public function readWithFilterStringFindsCategoriesStartingWithTheFilterString() {
		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$mockRepository = $this->getMock($class, array('findByStartingCategory'), array($this->objectManager));
		$mockRepository->expects($this->once())->method('findByStartingCategory')->with('FooBar');

		$this->fixture->_set('redirectTestCategoryRepository', $mockRepository);
		$this->fixture->readAction('FooBar');
	}

	/**
	 * @test
	 * @return void
	 */
	public function deleteAllUnusedCategoriesMethodRemovesCategories() {
		$objectCollection = new ObjectStorage();
		$objectCollection->attach(new RedirectTestCategory());
		$objectCollection->attach(new RedirectTestCategory());

		/** @noinspection PhpUndefinedMethodInspection */
		$mockRepository = $this->getMock(
			'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository',
			array('findAllUnusedCategories', 'remove'),
			array($this->objectManager)
		);
		$mockRepository->expects($this->once())->method('findAllUnusedCategories')
			->will($this->returnValue($objectCollection));
		$mockRepository->expects($this->exactly(2))->method('remove')
			->with($this->isInstanceOf('Tx_DfTools_Domain_Model_RedirectTestCategory'));

		$this->fixture->_set('redirectTestCategoryRepository', $mockRepository);
		$this->fixture->deleteUnusedCategoriesAction();
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateActionUpdatesCategory() {
		/** @var $category RedirectTestCategory */
		$category = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTestCategory')
			->disableOriginalClone()->getMock();
		$category->setCategory('FooBar');

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$mockRepository = $this->getMock($class, array('update'), array($this->objectManager));
		$mockRepository->expects($this->once())->method('update')->with($category);
		$this->fixture->_set('redirectTestCategoryRepository', $mockRepository);

		$this->fixture->updateAction($category);
	}
}

?>