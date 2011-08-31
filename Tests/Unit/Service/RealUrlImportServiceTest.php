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
 * Test case for class Tx_DfTools_Service_RealUrlImportService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_RealUrlImportServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_RealUrlImportService
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_RedirectTestRepository
	 */
	protected $testRepository;

	/**
	 * @var Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository
	 */
	protected $categoryRepository;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture = $this->getMock(
			$this->buildAccessibleProxy('Tx_DfTools_Service_RealUrlImportService'),
			array('getRealUrlRedirects', 'doesRedirectTestWithUrlAlreadyExists', 'getCategoryByCategoryField')
		);

		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestRepository */
		$this->testRepository = $this->getMock(
			'Tx_DfTools_Domain_Repository_RedirectTestRepository',
			array('add'),
			array($this->objectManager)
		);
		$this->fixture->injectRedirectTestRepository($this->testRepository);

		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestCategoryRepository';
		$this->categoryRepository = $this->getMock($class, array('add'), array($this->objectManager));
		$this->fixture->injectRedirectTestCategoryRepository($this->categoryRepository);

		/** @var $repository Tx_Extbase_Object_ObjectManager */
		$this->objectManager = $this->getMock(
			'Tx_Extbase_Object_ObjectManager',
			array('create')
		);
		$this->fixture->injectObjectManager($this->objectManager);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectRedirectTestRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RedirectTestRepository */
		$class = 'Tx_DfTools_Domain_Repository_RedirectTestRepository';
		$repository = $this->getMock($class, array('dummy'), array($this->objectManager));
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
		$repository = $this->getMock($class, array('dummy'), array($this->objectManager));
		$this->fixture->injectRedirectTestCategoryRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('redirectTestCategoryRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectObjectManager() {
		/** @var $repository Tx_Extbase_Object_ObjectManager */
		$class = 'Tx_Extbase_Object_ObjectManager';
		$objectManager = $this->getMock($class, array('dummy'));
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($objectManager, $this->fixture->_get('objectManager'));
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
		/** @noinspection PhpUndefinedMethodInspection */
		$category = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTestCategory')
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
		$category = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTestCategory')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$testRecord = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTest')
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