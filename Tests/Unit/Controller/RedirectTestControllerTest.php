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

use SGalinski\DfTools\Controller\RedirectTestController;
use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestRepository;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\View\RedirectTestArrayView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class Tx_DfTools_Controller_RedirectTestController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class RedirectTestControllerTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Controller\RedirectTestController|object
	 */
	protected $fixture;

	/**
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestRepository|object
	 */
	protected $repository;

	/**
	 * @var \SGalinski\DfTools\View\RedirectTestArrayView|object
	 */
	protected $view;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\Controller\RedirectTestController';
		$this->fixture = $this->getAccessibleMock($class, array('forward', 'getUrlCheckerService'));
		$this->fixture->_set('objectManager', $this->objectManager);

		/** @var $repository RedirectTestRepository */
		$this->repository = $this->getMock(
			'SGalinski\DfTools\Domain\Repository\RedirectTestRepository',
			array('findAll', 'findByUid', 'update', 'add', 'remove', 'findSortedAndInRangeByCategory', 'countAll'),
			array($this->objectManager)
		);
		$this->fixture->_set('repository', $this->repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->view = $this->getMock('SGalinski\DfTools\View\RedirectTestArrayView', array('assign'));
		$this->fixture->_set('view', $this->view);
	}

	/**
	 * Returns a redirect test instance
	 *
	 * @return RedirectTest
	 */
	protected function getRedirectTest() {
		/** @var $redirectTest RedirectTest */
		$redirectTest = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTest')
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
	public function testInjectRedirectTestCategoryRepository() {
		/** @var $repository RedirectTestCategoryRepository */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$repository = $this->getMock($class, array('dummy'), array($this->objectManager));
		$this->fixture->injectRedirectTestCategoryRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('redirectTestCategoryRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function realUrlImportServiceCanBeReturned() {
		$service = $this->fixture->getRealUrlImportService();
		$this->assertInstanceOf('SGalinski\DfTools\Domain\Service\RealUrlImportService', $service);
	}

	/**
	 * @test
	 * @return void
	 */
	public function readFetchesSortedRange() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findSortedAndInRangeByCategory')
			->with(1, 2, array('test' => TRUE));
		$this->repository->expects($this->once())->method('countAll');
		$this->view->expects($this->exactly(2))->method('assign');
		$this->fixture->readAction(1, 2, 'test', TRUE);
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
		/** @var $category RedirectTestCategory */
		$redirectTest = $this->getRedirectTest();
		$category = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTestCategory')
			->disableOriginalClone()->getMock();

		/** @var $categoryRepository RedirectTestCategoryRepository */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$categoryRepository = $this->getMock($class, array('add'), array($this->objectManager));
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
	public function importFromRealUrlUsesTheService() {
		/** @var $objectManager ObjectManager */
		$objectManager = $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('get'));
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$importService = $this->getMock(
			'SGalinski\DfTools\Domain\Service\RealUrlImportService', array('importFromRealUrl')
		);
		$importService->expects($this->once())->method('importFromRealUrl');
		$objectManager->expects($this->once())->method('get')->will($this->returnValue($importService));

		$this->fixture->importFromRealUrlAction();
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

		$testCollection = new ObjectStorage();
		$testCollection->attach($redirectTest1);
		$testCollection->attach($redirectTest2);

		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$this->repository->expects($this->once())->method('findAll')
			->will($this->returnValue($testCollection));
		$this->view->expects($this->once())->method('assign')
			->with('records', $this->isInstanceOf('TYPO3\CMS\Extbase\Persistence\ObjectStorage'));
		$this->fixture->expects($this->once())->method('getUrlCheckerService')
			->will($this->returnValue($urlCheckerService));
		$redirectTest1->expects($this->once())->method('test')->with($urlCheckerService);
		$redirectTest2->expects($this->once())->method('test')->with($urlCheckerService);
		$this->fixture->runAllTestsAction();
	}
}

?>