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
 * Test case for class Tx_DfTools_Controller_RedirectTestController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_RedirectTestControllerTest extends Tx_DfTools_Tests_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_RedirectTestController
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_RedirectTestRepository
	 */
	protected $repository;

	/**
	 * @var Tx_DfTools_View_RedirectTest_ArrayView
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_RedirectTestController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->injectObjectManager($this->objectManager);

		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestRepository';
		$this->repository = $this->getMock($class, array('findAll', 'findByUid', 'update', 'add', 'remove'));
		$this->fixture->injectRedirectTestRepository($this->repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('Tx_DfTools_View_RedirectTest_ArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns a redirect test instance
	 *
	 * @return Tx_DfTools_Domain_Model_RedirectTest
	 */
	protected function getRedirectTest() {
		/** @var $redirectTest Tx_DfTools_Domain_Model_RedirectTest */
		$redirectTest = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTest')
			->setMethods(array('test'))
			->disableOriginalClone()->getMock();

		$redirectTest->setExpectedUrl('FooBar');
		$redirectTest->setTestUrl('FooBar');
		$redirectTest->setHttpStatusCode(200);

		return $redirectTest;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRedirectTestRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectRedirectTestRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('redirectTestRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRedirectTestCategoryRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectRedirectTestCategoryRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('redirectTestCategoryRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function readCallsFindAll() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findAll');
		$this->fixture->readAction();
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateActionWithoutNewCategoryUpdatesData() {
		/** @noinspection PhpUndefinedMethodInspection */
		$redirectTest = $this->getRedirectTest();
		$this->repository->expects($this->once())->method('update')->with($redirectTest);
		$this->fixture->updateAction($redirectTest);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateActionWithNewCategoryAddsCategoryAndUpdateData() {
		/** @var $category Tx_DfTools_Domain_Model_RedirectTestCategory */
		$redirectTest = $this->getRedirectTest();
		$category = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTestCategory')
			->disableOriginalClone()->getMock();

		/** @var $categoryRepository Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
		$categoryRepository = $this->getMock($class, array('add'));
		$this->fixture->injectRedirectTestCategoryRepository($categoryRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->addMockedCallToPersistAll();
		$categoryRepository->expects($this->once())->method('add')->with($category);
		$this->repository->expects($this->once())->method('update')->with($redirectTest);
		$this->fixture->updateAction($redirectTest, $category);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createActionAddsNewRedirectTest() {
		/** @noinspection PhpUndefinedMethodInspection */
		$redirectTest = $this->getRedirectTest();
		$this->repository->expects($this->once())->method('add')->with($redirectTest);
		$this->addMockedCallToPersistAll();
		$this->fixture->createAction($redirectTest);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyActionRemovesRedirectTests() {
		/** @noinspection PhpUndefinedMethodInspection */
		$redirectTest = $this->getRedirectTest();
		$this->repository->expects($this->exactly(2))->method('remove')->with($redirectTest);
		$this->repository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($redirectTest))
			->with($this->isType('integer'));

		$this->fixture->destroyAction(array(10, 20));
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWorks() {
		/** @var $urlCheckerService Tx_DfTools_Service_UrlChecker_AbstractService */
		$class = 'Tx_DfTools_Service_UrlChecker_AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$redirectTest = $this->getRedirectTest();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($redirectTest))->with(1);

		$redirectTest->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->expects($this->once())->method('forward');
		$this->fixture->expects($this->once())->method('getUrlCheckerService')
			->will($this->returnValue($urlCheckerService));

		$this->fixture->runTestAction(1);
	}

	/**
	 * @test
	 * @return void
	 */
	public function runAllTestsWorks() {
		$redirectTest1 = $this->getRedirectTest();
		$redirectTest2 = $this->getRedirectTest();

		$testCollection = new Tx_Extbase_Persistence_ObjectStorage();
		$testCollection->attach($redirectTest1);
		$testCollection->attach($redirectTest2);

		/** @var $urlCheckerService Tx_DfTools_Service_UrlChecker_AbstractService */
		$class = 'Tx_DfTools_Service_UrlChecker_AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findAll')
			->will($this->returnValue($testCollection));
		$this->view->expects($this->once())->method('assign')
			->with('records', $this->isInstanceOf('Tx_Extbase_Persistence_ObjectStorage'));
		$this->fixture->expects($this->once())->method('getUrlCheckerService')
			->will($this->returnValue($urlCheckerService));
		$redirectTest1->expects($this->once())->method('test')->with($urlCheckerService);
		$redirectTest2->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->runAllTestsAction();
	}
}

?>