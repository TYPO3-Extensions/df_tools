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
 * Test case for class Tx_DfTools_Domain_Model_RedirectTest.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_RedirectTestTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Domain_Model_RedirectTest
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new Tx_DfTools_Domain_Model_RedirectTest();
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
		$testData = new Tx_DfTools_Domain_Model_RedirectTestCategory();
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
		/** @var $category Tx_DfTools_Domain_Model_RedirectTestCategory */
		$category = $this->getAccessibleMock('Tx_DfTools_Domain_Model_RedirectTestCategory', array('dummy'));

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
	 * @return Tx_DfTools_Service_UrlChecker_AbstractService
	 */
	protected function getUrlCheckerService($resolveUrlOutput) {
		/** @var $urlCheckerService Tx_DfTools_Service_UrlChecker_AbstractService */
		$class = 'Tx_DfTools_Service_UrlChecker_AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$urlCheckerService->expects($this->once())->method('resolveURL')
			->will($this->returnValue($resolveUrlOutput));

		return $urlCheckerService;
	}

	/**
	 * @return void
	 */
	public function testHandlesAllCasesDataProvider() {
		return array(
			'url mismatch' => array(
				array('http_code' => 200, 'url' => 'UnknownUrl'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR,
			),
			'http code mismatch' => array(
				array('http_code' => 999, 'url' => 'FooBar'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_WARNING,
			),
			'test works' => array(
				array('http_code' => 200, 'url' => 'FooBar'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK,
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