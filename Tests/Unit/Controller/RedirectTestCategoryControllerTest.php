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

/**
 * Test case for class Tx_DfTools_Controller_RedirectTestCategoryController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_RedirectTestCategoryControllerTest extends Tx_DfTools_Controller_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_RedirectTestCategoryController
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_RedirectTestCategoryController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_View_RedirectTestCategory_ArrayView';
		$mockView = $this->getMock($class, array('assign'));
		$this->fixture->_set('view', $mockView);
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRedirectTestCategoryRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository */
		$repository = new Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository($this->objectManager);
		$this->fixture->injectRedirectTestCategoryRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('redirectTestCategoryRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function readWithoutFilterFindAllResults() {
		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
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
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
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
		$objectCollection = new Tx_Extbase_Persistence_ObjectStorage();
		$objectCollection->attach(new Tx_DfTools_Domain_Model_RedirectTestCategory());
		$objectCollection->attach(new Tx_DfTools_Domain_Model_RedirectTestCategory());

		/** @noinspection PhpUndefinedMethodInspection */
		$mockRepository = $this->getMock(
			'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository',
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
		/** @var $category Tx_DfTools_Domain_Model_RedirectTestCategory */
		$category = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTestCategory')
			->disableOriginalClone()->getMock();
		$category->setCategory('FooBar');

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
		$mockRepository = $this->getMock($class, array('update'), array($this->objectManager));
		$mockRepository->expects($this->once())->method('update')->with($category);
		$this->fixture->_set('redirectTestCategoryRepository', $mockRepository);

		$this->fixture->updateAction($category);
	}
}

?>