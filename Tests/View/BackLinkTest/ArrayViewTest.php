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
 * Test case for class Tx_DfTools_View_BackLinkTest_ArrayView
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_View_BackLinkTest_ArrayViewTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_View_BackLinkTest_ArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('Tx_DfTools_View_BackLinkTest_ArrayView');
		$this->fixture = $this->getMockBuilder($class)
			->setMethods(array('dummy'))
			->disableOriginalConstructor()
			->getMock();
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
	public function recordsCanBeRenderedDataProvider() {
		$backLinkTestNormal = new Tx_DfTools_Domain_Model_BackLinkTest();
		$backLinkTestNormal->setTestUrl('FooBar');
		$backLinkTestNormal->setExpectedUrl('FooBar');

		$backLinkTestWithoutCategory = new Tx_DfTools_Domain_Model_BackLinkTest();
		$backLinkTestWithoutCategory->setTestUrl('FooBar');
		$backLinkTestWithoutCategory->setExpectedUrl('FooBar');
		$backLinkTestWithoutCategory->setTestResult(1);
		$backLinkTestWithoutCategory->setTestMessage('FooBar');

		$backLinkTestWithXSS = new Tx_DfTools_Domain_Model_BackLinkTest();
		$backLinkTestWithXSS->setTestUrl('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$backLinkTestWithXSS->setExpectedUrl('<script>alert("Ooops!!!");</script>');
		$backLinkTestWithXSS->setTestMessage('<script>alert("Ooops!!!");</script>');

		return array(
			'normal back link test with category' => array(
				$backLinkTestNormal,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED,
					'testMessage' => '',
				),
			),
			'back link test without category' => array(
				$backLinkTestWithoutCategory,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'testResult' => 1,
					'testMessage' => 'FooBar',
				),
			),
			'XSS attack' => array(
				$backLinkTestWithXSS,
				array(
					'__identity' => 0,
					'testUrl' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
					'expectedUrl' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED,
					'testMessage' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
				),
			)
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 * @param Tx_DfTools_Domain_Model_BackLinkTest $backLinkTest
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($backLinkTest, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $backLinkTest));
	}
}

?>