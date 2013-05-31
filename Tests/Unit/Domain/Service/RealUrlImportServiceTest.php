<?php

namespace SGalinski\DfTools\Tests\Unit\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinsk@gmail.com>)
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

use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestRepository;
use SGalinski\DfTools\Domain\Service\RealUrlImportService;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class RealUrlImportServiceTest
 */
class RealUrlImportServiceTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Service\RealUrlImportService|object
	 */
	protected $fixture;

	/**
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestRepository|object
	 */
	protected $testRepository;

	/**
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository|object
	 */
	protected $categoryRepository;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager|object
	 */
	protected $objectManager;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture = $this->getMock(
			$this->buildAccessibleProxy('SGalinski\DfTools\Domain\Service\RealUrlImportService'),
			array('getRealUrlRedirects', 'doesRedirectTestWithUrlAlreadyExists', 'getCategoryByCategoryField')
		);

		/** @var $repository RedirectTestRepository */
		$this->testRepository = $this->getMock(
			'SGalinski\DfTools\Domain\Repository\RedirectTestRepository',
			array('add'),
			array($this->objectManager)
		);
		$this->fixture->_set('testRepository', $this->testRepository);

		/** @var $repository RedirectTestCategoryRepository */
		$class = 'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository';
		$this->categoryRepository = $this->getMock($class, array('add'), array($this->objectManager));
		$this->fixture->_set('categoryRepository', $this->categoryRepository);

		/** @var $repository ObjectManager */
		$this->objectManager = $this->getMock(
			'TYPO3\CMS\Extbase\Object\ObjectManager',
			array('create')
		);
		$this->fixture->_set('objectManager', $this->objectManager);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @return array
	 */
	public function prepareUrlAppendsSlashCorrectlyDataProvider() {
		return array(
			'url with appended slash' => array(
				'/foo/bar', '/foo/bar',
			),
			'relative url without slash' => array(
				'foo/bar', '/foo/bar',
			),
			'http url' => array(
				'http://test.de/foo/bar', 'http://test.de/foo/bar',
			),
		);
	}

	/**
	 * @dataProvider prepareUrlAppendsSlashCorrectlyDataProvider
	 * @test
	 *
	 * @param string $url
	 * @param string $expected
	 * @return void
	 */
	public function prepareUrlAppendsSlashCorrectly($url, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$result = $this->fixture->_call('prepareUrl', $url);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function importFromRealUrlWithoutAnExistingCategoryAndAlreadySyncedRedirects() {
		/** @var $category RedirectTestCategory */
		$category = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTestCategory')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$category->setCategory('RealUrl');
		$this->objectManager->expects($this->once())->method('create')
			->will($this->returnValue($category));

		$this->fixture->expects($this->once())->method('getCategoryByCategoryField')
			->will($this->returnValue(NULL));
		$this->categoryRepository->expects($this->once())->method('add')->with($category);

		$this->fixture->expects($this->any())->method('doesRedirectTestWithUrlAlreadyExists')
			->will($this->returnValue(TRUE));
		$this->testRepository->expects($this->never())->method('add');

		$redirects = array(
			array(
				'url' => 'FooBar',
				'destination' => 'BarFoo',
			),
			array(
				'url' => 'http://test.de/foo',
				'destination' => 'http://test.de/bar',
			),
		);

		$this->fixture->expects($this->once())->method('getRealUrlRedirects')->will($this->returnValue($redirects));

		$this->fixture->importFromRealUrl();
	}

	/**
	 * @test
	 * @return void
	 */
	public function importFromRealUrlWithAnExistingCategoryAndNonSyncedRedirects() {
		/** @noinspection PhpUndefinedMethodInspection */
		$category = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTestCategory')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$testRecord = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTest')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$this->objectManager->expects($this->any())->method('create')->will($this->returnValue($testRecord));

		$this->fixture->expects($this->once())->method('getCategoryByCategoryField')
			->will($this->returnValue($category));
		$this->categoryRepository->expects($this->never())->method('add');

		$this->fixture->expects($this->any())->method('doesRedirectTestWithUrlAlreadyExists')
			->will($this->returnValue(FALSE));
		$this->testRepository->expects($this->any())->method('add')->with($testRecord);

		$redirects = array(
			array(
				'url' => 'FooBar',
				'destination' => 'BarFoo',
			),
			array(
				'url' => 'http://test.de/foo',
				'destination' => 'http://test.de/bar',
			),
		);

		$this->fixture->expects($this->once())->method('getRealUrlRedirects')->will($this->returnValue($redirects));

		$this->fixture->importFromRealUrl();
	}
}

?>