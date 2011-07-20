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
 * Test case for class Tx_DfTools_Domain_Model_BackLinkTest.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_BackLinkTestTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Domain_Model_BackLinkTest
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new Tx_DfTools_Domain_Model_BackLinkTest();
		$this->fixture->setTestUrl('FooBar');
		$this->fixture->setExpectedUrl('http://foo.bar/test/');
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
	public function transformToArrayWorksWithEmptyCategory() {
		$expected = array(
			'__identity' => NULL,
			'testUrl' => 'FooBar',
			'expectedUrl' => 'http://foo.bar/test/',
			'testResult' => 1,
			'testMessage' => '',
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
	 * @return array
	 */
	public function testHandlesAllCasesDataProvider() {
		return array(
			'url in content' => array(
				array('content' => 'Anything is fine: <a href="http://foo.bar/test/">test</a> or not?'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK,
				'http://foo.bar/test/'
			),
			'url not in content' => array(
				array('content' => 'Anything is fine or not?'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR,
				'http://foo.bar/test/'
			),
			'regular expression' => array(
				array('content' => 'Anything is fine: <a href="https://foo.bar/test/">test</a> or not?'),
				Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK,
				'https?:\/\/foo.bar/.+'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider testHandlesAllCasesDataProvider
	 *
	 * @param array $resolveUrlOutput
	 * @param int $testResult
	 * @param string $expectedUrl
	 * @return void
	 */
	public function testHandlesAllCases(array $resolveUrlOutput, $testResult, $expectedUrl) {
		$urlCheckerService = $this->getUrlCheckerService($resolveUrlOutput);
		$this->fixture->setExpectedUrl($expectedUrl);
		$this->fixture->test($urlCheckerService);
		$this->assertSame($testResult, $this->fixture->getTestResult());
	}
}

?>