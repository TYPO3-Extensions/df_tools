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
 * Test case for class Tx_DfTools_Controller_ContentComparisonTestController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_ContentComparisonTestControllerTest extends Tx_DfTools_Tests_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_ContentComparisonTestController
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_ContentComparisonTestRepository
	 */
	protected $repository;

	/**
	 * @var Tx_DfTools_View_ContentComparisonTest_ArrayView
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_ContentComparisonTestController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->injectObjectManager($this->objectManager);

		/** @var $repository Tx_DfTools_Domain_Repository_ContentComparisonTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_ContentComparisonTestRepository';
		$this->repository = $this->getMock($class, array('findAll', 'findByUid', 'update', 'add', 'remove'));
		$this->fixture->injectContentComparisonTestRepository($this->repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('Tx_DfTools_View_ContentComparisonTest_ArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * @return Tx_DfTools_Domain_Model_ContentComparisonTest
	 */
	protected function getContentComparisonTest() {
		/** @var $contentComparisonTest Tx_DfTools_Domain_Model_ContentComparisonTest */
		$contentComparisonTest = $this->getMockBuilder('Tx_DfTools_Domain_Model_ContentComparisonTest')
			->setMethods(array('test', 'updateTestContent'))
			->disableOriginalClone()->getMock();
		$contentComparisonTest->setTestUrl('FooBar');
		$contentComparisonTest->setCompareUrl('FooBar');

		return $contentComparisonTest;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRedirectTestCategoryRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_ContentComparisonTestRepository */
		$repository = new Tx_DfTools_Domain_Repository_ContentComparisonTestRepository;
		$this->fixture->injectContentComparisonTestRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('contentComparisonTestRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function readActionFindAllEntries() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findAll');
		$this->fixture->readAction();
	}

	/**
	 * @test
	 * @return void
	 */
	public function createActionCreatedNewRecord() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->addMockedCallToPersistAll();
		$contentComparisonTest = $this->getContentComparisonTest();
		$this->repository->expects($this->once())->method('add')->with($contentComparisonTest);
		$this->fixture->createAction($contentComparisonTest);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateActionUpdatesData() {
		/** @noinspection PhpUndefinedMethodInspection */
		$contentComparisonTest = $this->getContentComparisonTest();
		$this->repository->expects($this->once())->method('update')->with($contentComparisonTest);
		$this->fixture->updateAction($contentComparisonTest);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyActionRemovesRedirectTests() {
		/** @noinspection PhpUndefinedMethodInspection */
		$contentComparisonTest = $this->getContentComparisonTest();
		$this->repository->expects($this->exactly(2))->method('remove')->with($contentComparisonTest);
		$this->repository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($contentComparisonTest))
			->with($this->isType('integer'));
		$this->fixture->destroyAction(array(10, 20));
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateTestContentWorks() {
		/** @var $urlCheckerService Tx_DfTools_Service_UrlChecker_AbstractService */
		$class = 'Tx_DfTools_Service_UrlChecker_AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$contentComparisonTest = $this->getContentComparisonTest();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($contentComparisonTest))->with(1);

		$contentComparisonTest->expects($this->once())->method('updateTestContent')->with($urlCheckerService);
		$this->view->expects($this->once())->method('assign')->with('records', $this->isType('array'));
		$this->fixture->expects($this->once())->method('getUrlCheckerService')
			->will($this->returnValue($urlCheckerService));

		$this->fixture->updateTestContentAction(1);
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
		$contentComparisonTest = $this->getContentComparisonTest();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($contentComparisonTest))->with(1);

		$contentComparisonTest->expects($this->once())->method('test')->with($urlCheckerService);
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
		$contentComparisonTest1 = $this->getContentComparisonTest();
		$contentComparisonTest2 = $this->getContentComparisonTest();

		$testCollection = new Tx_Extbase_Persistence_ObjectStorage();
		$testCollection->attach($contentComparisonTest1);
		$testCollection->attach($contentComparisonTest2);

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
		$contentComparisonTest1->expects($this->once())->method('test')->with($urlCheckerService);
		$contentComparisonTest2->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->runAllTestsAction();
	}
}

?>