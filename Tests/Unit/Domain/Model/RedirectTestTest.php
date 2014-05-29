<?php

namespace SGalinski\DfTools\Tests\Unit\Domain\Model;

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

use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Tests\Unit\Controller\ControllerTestCase;
use SGalinski\DfTools\UrlChecker\AbstractService;

/**
 * Test case for class Tx_DfTools_Domain_Model_RedirectTest.
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @package df_tools
 */
class RedirectTestTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Model\RedirectTest
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new RedirectTest();
		$this->fixture->setTestUrl('FooBar');
		$this->fixture->setExpectedUrl('FooBar');
		$this->fixture->setHttpStatusCode(200);
		$this->fixture->setTestResult(1);
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
	public function setTestUrlWorks() {
		$this->fixture->setTestUrl('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTestUrl());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setExpectedUrlWorks() {
		$this->fixture->setExpectedUrl('FooBar');
		$this->assertSame('FooBar', $this->fixture->getExpectedUrl());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHttpStatusCodeWorks() {
		$this->fixture->setHttpStatusCode(12);
		$this->assertSame(12, $this->fixture->getHttpStatusCode());

		$this->fixture->setHttpStatusCode('12');
		$this->assertSame(12, $this->fixture->getHttpStatusCode());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setTestResultWorks() {
		$this->fixture->setTestResult(0);
		$this->assertSame(0, $this->fixture->getTestResult());

		$this->fixture->setTestResult('1');
		$this->assertSame(1, $this->fixture->getTestResult());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setTestMessageWorks() {
		$this->fixture->setTestMessage('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTestMessage());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setCategoryWorks() {
		$testData = new RedirectTestCategory();
		$this->fixture->setCategory($testData);
		$this->assertSame($testData, $this->fixture->getCategory());
	}

	/**
	 * @test
	 * @return void
	 */
	public function transformToArrayWorksWithEmptyCategory() {
		$expected = array(
			'__identity' => NULL,
			'testUrl' => 'FooBar',
			'expectedUrl' => 'FooBar',
			'httpStatusCode' => 200,
			'testResult' => 1,
			'testMessage' => '',
			'category' => NULL,
		);

		$this->assertSame($expected, $this->fixture->toArray());
	}

	/**
	 * @test
	 * @return void
	 */
	public function transformToArrayWorksWithCategory() {
		/** @var $category RedirectTestCategory */
		$category = $this->getAccessibleMock('SGalinski\DfTools\Domain\Model\RedirectTestCategory', array('dummy'));

		/** @noinspection PhpUndefinedMethodInspection */
		$category->_set('uid', 1);
		$this->fixture->setCategory($category);

		$expected = array(
			'__identity' => NULL,
			'testUrl' => 'FooBar',
			'expectedUrl' => 'FooBar',
			'httpStatusCode' => 200,
			'testResult' => 1,
			'testMessage' => '',
			'category' => 1,
		);

		$this->assertSame($expected, $this->fixture->toArray());
	}

	/**
	 * @param mixed $resolveUrlOutput
	 * @return AbstractService
	 */
	protected function getUrlCheckerService($resolveUrlOutput) {
		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$urlCheckerService->expects($this->once())->method('resolveURL')
			->will($this->returnValue($resolveUrlOutput));

		return $urlCheckerService;
	}

	/**
	 * @return array
	 */
	public function testHandlesAllCasesDataProvider() {
		return array(
			'url mismatch' => array(
				array('http_code' => 200, 'url' => 'UnknownUrl'),
				AbstractService::SEVERITY_ERROR,
			),
			'http code mismatch' => array(
				array('http_code' => 999, 'url' => 'FooBar'),
				AbstractService::SEVERITY_WARNING,
			),
			'test works' => array(
				array('http_code' => 200, 'url' => 'FooBar'),
				AbstractService::SEVERITY_OK,
			),
		);
	}

	/**
	 * @test
	 * @dataProvider testHandlesAllCasesDataProvider
	 *
	 * @param array $resolveUrlOutput
	 * @param int $testResult
	 * @return void
	 */
	public function testHandlesAllCases(array $resolveUrlOutput, $testResult) {
		$urlCheckerService = $this->getUrlCheckerService($resolveUrlOutput);
		$this->fixture->test($urlCheckerService);
		$this->assertSame($testResult, $this->fixture->getTestResult());
	}
}

?>