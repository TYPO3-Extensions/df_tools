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
 * Test case for class Tx_DfTools_Controller_LinkCheckController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_LinkCheckControllerTest extends Tx_DfTools_Tests_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_LinkCheckController
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected $repository;

	/**
	 * @var Tx_DfTools_View_LinkCheck_ArrayView
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_LinkCheckController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->injectObjectManager($this->objectManager);

		/** @var $repository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$class = 'Tx_DfTools_Domain_Repository_LinkCheckRepository';
		$this->repository = $this->getMock(
			$class,
			array('findAll', 'findByUid', 'update', 'add', 'remove', 'findSortedAndInRange', 'countAll')
		);
		$this->fixture->injectLinkCheckRepository($this->repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('Tx_DfTools_View_LinkCheck_ArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns an link check test instance
	 *
	 * @return Tx_DfTools_Domain_Model_LinkCheck
	 */
	protected function getLinkCheck() {
		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$linkCheck = $this->getMockBuilder('Tx_DfTools_Domain_Model_LinkCheck')
			->setMethods(array('test'))
			->disableOriginalClone()->getMock();
		$linkCheck->setTestUrl('FooBar');

		return $linkCheck;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectLinkCheckRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$class = 'Tx_DfTools_Domain_Repository_LinkCheckRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectLinkCheckRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('linkCheckRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRecordSetRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RecordSetRepository */
		$class = 'Tx_DfTools_Domain_Repository_RecordSetRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectRecordSetRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('recordSetRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function readFetchesSortedRange() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findSortedAndInRange');
		$this->repository->expects($this->once())->method('countAll');
		$this->fixture->readAction(1, 2, 'test', TRUE);
	}

	/**
	 * @test
	 * @return void
	 */
	public function readRecordSetsActionCallsFindByUidWithAnInteger() {
		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheck = $this->getLinkCheck();
		$this->repository->expects($this->once())->method('findByUid')
			->with($this->isType('int'))->will($this->returnValue($linkCheck));
		$this->fixture->readRecordSetsAction(7);
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
		$linkCheck = $this->getLinkCheck();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($linkCheck))->with(1);

		$linkCheck->expects($this->once())->method('test')->with($urlCheckerService);
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
		$linkCheck1 = $this->getLinkCheck();
		$linkCheck2 = $this->getLinkCheck();

		$testCollection = new Tx_Extbase_Persistence_ObjectStorage();
		$testCollection->attach($linkCheck1);
		$testCollection->attach($linkCheck2);

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
		$linkCheck1->expects($this->once())->method('test')->with($urlCheckerService);
		$linkCheck2->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->runAllTestsAction();
	}

	/**
	 * @return array
	 */
	public function resetActionResetsAnRecordDataProvider() {
		return array(
			'reset as ignore' => array(
				TRUE, Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_IGNORE,
			),
			'reset as untested' => array(
				FALSE, Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED,
			),
		);
	}

	/**
	 * @test
	 * @dataProvider resetActionResetsAnRecordDataProvider
	 *
	 * @param boolean $ignoreState
	 * @param int $state
	 * @return void
	 */
	public function resetActionResetsAnRecord($ignoreState, $state) {
		/** @noinspection PhpUndefinedMethodInspection */
		$record = $this->getLinkCheck();
		$this->repository->expects($this->once())->method('findByUid')->with(7)->will($this->returnValue($record));
		$this->repository->expects($this->once())->method('update');
		$this->view->expects($this->once())->method('assign')->with('records', $this->isType('array'));
		$this->fixture->resetRecordAction(7, $ignoreState);

		$this->assertSame('', $record->getTestMessage());
		$this->assertSame('', $record->getResultUrl());
		$this->assertSame(0, $record->getHttpStatusCode());
		$this->assertSame($state, $record->getTestResult());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setAsFalsePositiveWorks() {
		/** @noinspection PhpUndefinedMethodInspection */
		$record = $this->getLinkCheck();
		$this->repository->expects($this->exactly(2))->method('update');
		$this->view->expects($this->exactly(2))->method('assign')->with('records', $this->isType('array'));
		$this->repository->expects($this->exactly(2))->method('findByUid')->with(7)
			->will($this->returnValue($record));

		$this->fixture->setFalsePositiveStateAction(7, TRUE);
		$state = $record->getTestResult();
		$this->assertSame(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO, $state);

		$this->fixture->setFalsePositiveStateAction(7, FALSE);
		$state = $record->getTestResult();
		$this->assertSame(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED, $state);
	}
}

?>