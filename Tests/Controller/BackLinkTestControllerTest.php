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
 * Test case for class Tx_DfTools_Controller_BackLinkTestController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_BackLinkTestControllerTest extends Tx_DfTools_Tests_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_BackLinkTestController
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_BackLinkTestRepository
	 */
	protected $repository;

	/**
	 * @var Tx_DfTools_View_BackLinkTest_ArrayView
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_BackLinkTestController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->injectObjectManager($this->objectManager);

		/** @var $repository Tx_DfTools_Domain_Repository_BackLinkTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_BackLinkTestRepository';
		$this->repository = $this->getMock($class, array('findAll', 'findByUid', 'update', 'add', 'remove'));
		$this->fixture->injectBackLinkTestRepository($this->repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('Tx_DfTools_View_BackLinkTest_ArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns a back link test instance
	 *
	 * @return Tx_DfTools_Domain_Model_BackLinkTest
	 */
	protected function getBackLinkTest() {
		/** @var $backLinkTest Tx_DfTools_Domain_Model_BackLinkTest */
		$backLinkTest = $this->getMockBuilder('Tx_DfTools_Domain_Model_BackLinkTest')
			->setMethods(array('test'))
			->disableOriginalClone()->getMock();

		$backLinkTest->setExpectedUrl('FooBar');
		$backLinkTest->setTestUrl('FooBar');

		return $backLinkTest;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectBackLinkTestRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_BackLinkTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_BackLinkTestRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectBackLinkTestRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('backLinkTestRepository'));
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
	public function updateActionUpdatesData() {
		/** @var $category Tx_DfTools_Domain_Model_BackLinkTestCategory */
		$backLinkTest = $this->getBackLinkTest();
		$category = $this->getMockBuilder('Tx_DfTools_Domain_Model_BackLinkTestCategory')
			->disableOriginalClone()->getMock();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('update')->with($backLinkTest);
		$this->fixture->updateAction($backLinkTest, $category);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createActionAddsNewBackLinkTest() {
		/** @noinspection PhpUndefinedMethodInspection */
		$backLinkTest = $this->getBackLinkTest();
		$this->repository->expects($this->once())->method('add')->with($backLinkTest);
		$this->addMockedCallToPersistAll();
		$this->fixture->createAction($backLinkTest);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyActionRemovesBackLinkTests() {
		/** @noinspection PhpUndefinedMethodInspection */
		$backLinkTest = $this->getBackLinkTest();
		$this->repository->expects($this->exactly(2))->method('remove')->with($backLinkTest);
		$this->repository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($backLinkTest))
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
		$backLinkTest = $this->getBackLinkTest();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($backLinkTest))->with(1);

		$backLinkTest->expects($this->once())->method('test')->with($urlCheckerService);
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
		$backLinkTest1 = $this->getBackLinkTest();
		$backLinkTest2 = $this->getBackLinkTest();

		$testCollection = new Tx_Extbase_Persistence_ObjectStorage();
		$testCollection->attach($backLinkTest1);
		$testCollection->attach($backLinkTest2);

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
		$backLinkTest1->expects($this->once())->method('test')->with($urlCheckerService);
		$backLinkTest2->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->runAllTestsAction();
	}
}

?>