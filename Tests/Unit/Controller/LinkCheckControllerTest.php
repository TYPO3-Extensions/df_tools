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

use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Repository\LinkCheckRepository;
use SGalinski\DfTools\UrlChecker\AbstractService;

/**
 * Class LinkCheckControllerTest
 */
class LinkCheckControllerTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Controller\LinkCheckController|object
	 */
	protected $fixture;

	/**
	 * @var \SGalinski\DfTools\Domain\Repository\LinkCheckRepository|object
	 */
	protected $repository;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var \SGalinski\DfTools\View\LinkCheckArrayView|object
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'SGalinski\DfTools\Controller\LinkCheckController',
			array('forward', 'getUrlCheckerService', 'fetchRawUrls', 'getUrlsFromSingleRecord')
		);
		$this->fixture->_set('objectManager', $this->objectManager);

		/** @var $repository LinkCheckRepository */
		$this->repository = $this->getMock(
			'SGalinski\DfTools\Domain\Repository\LinkCheckRepository', [], [], '', FALSE
		);
		$this->fixture->_set('linkCheckRepository', $this->repository);

		$this->objectManager = $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->fixture->_set('objectManager', $this->objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('SGalinski\DfTools\View\LinkCheckArrayView');
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns an link check test instance
	 *
	 * @return LinkCheck|object
	 */
	protected function getLinkCheck() {
		/** @var $linkCheck LinkCheck */
		$linkCheck = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\LinkCheck')
			->setMethods(array('test'))
			->disableOriginalClone()->getMock();
		$linkCheck->setTestUrl('FooBar');

		return $linkCheck;
	}

	/**
	 * @test
	 * @return void
	 */
	public function readFetchesSortedRange() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findSortedAndInRange')
			->with(1, 2, array('test' => TRUE));
		$this->repository->expects($this->once())->method('countAll');
		$this->view->expects($this->exactly(2))->method('assign');
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
		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
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
	 * @return array
	 */
	public function resetActionResetsAnRecordDataProvider() {
		return array(
			'reset as ignore' => array(
				TRUE, AbstractService::SEVERITY_IGNORE,
			),
			'reset as untested' => array(
				FALSE, AbstractService::SEVERITY_UNTESTED,
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
		$this->assertSame(AbstractService::SEVERITY_INFO, $state);

		$this->fixture->setFalsePositiveStateAction(7, FALSE);
		$state = $record->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_UNTESTED, $state);
	}
}

?>