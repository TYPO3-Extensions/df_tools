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

use SGalinski\DfTools\Controller\BackLinkTestController;
use SGalinski\DfTools\Domain\Model\BackLinkTest;
use SGalinski\DfTools\Domain\Repository\BackLinkTestRepository;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\View\BackLinkTestArrayView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class BackLinkTestControllerTest
 */
class BackLinkTestControllerTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Controller\BackLinkTestController|object
	 */
	protected $fixture;

	/**
	 * @var \SGalinski\DfTools\Domain\Repository\BackLinkTestRepository|object
	 */
	protected $repository;

	/**
	 * @var \SGalinski\DfTools\View\BackLinkTestArrayView|object
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\Controller\BackLinkTestController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->_set('objectManager', $this->objectManager);

		$this->repository = $this->getMock('SGalinski\DfTools\Domain\Repository\BackLinkTestRepository');
		$this->fixture->_set('backLinkTestRepository', $this->repository);

		$this->view = $this->getMock('SGalinski\DfTools\View\BackLinkTestArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns a back link test instance
	 *
	 * @return BackLinkTest|object
	 */
	protected function getBackLinkTest() {
		/** @var $backLinkTest BackLinkTest */
		$backLinkTest = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\BackLinkTest')
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
	public function updateActionUpdatesData() {
		$backLinkTest = $this->getBackLinkTest();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('update')->with($backLinkTest);
		$this->fixture->updateAction($backLinkTest);
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
		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		$backLinkTest = $this->getBackLinkTest();
		$this->repository->expects($this->once())->method('findByUid')
			->will($this->returnValue($backLinkTest))->with(1);

		/** @noinspection PhpUndefinedMethodInspection */
		$backLinkTest->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->expects($this->once())->method('forward');
		$this->fixture->expects($this->once())->method('getUrlCheckerService')
			->will($this->returnValue($urlCheckerService));

		$this->fixture->runTestAction(1);
	}
}

?>