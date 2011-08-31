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
 * Test case for class Tx_DfTools_View_LinkCheck_ArrayView
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_View_LinkCheck_ArrayViewTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_View_LinkCheck_ArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('Tx_DfTools_View_LinkCheck_ArrayView');
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
		$linkCheckNormal = new Tx_DfTools_Domain_Model_LinkCheck();
		$linkCheckNormal->setTestUrl('FooBar');
		$linkCheckNormal->setResultUrl('FooBar');
		$linkCheckNormal->setHttpStatusCode(200);

		$linkCheckWithXSS = new Tx_DfTools_Domain_Model_LinkCheck();
		$linkCheckWithXSS->setTestUrl('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$linkCheckWithXSS->setResultUrl('<script>alert("Ooops!!!");</script>');
		$linkCheckWithXSS->setHttpStatusCode(500);
		$linkCheckWithXSS->setTestMessage('<script>alert("Ooops!!!");</script>');

		return array(
			'normal link check test' => array(
				$linkCheckNormal,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'resultUrl' => 'FooBar',
					'httpStatusCode' => 200,
					'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED,
					'testMessage' => '',
				),
			),
			'XSS attack' => array(
				$linkCheckWithXSS,
				array(
					'__identity' => 0,
					'testUrl' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
					'resultUrl' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'httpStatusCode' => 500,
					'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED,
					'testMessage' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
				),
			)
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 *
	 * @param Tx_DfTools_Domain_Model_LinkCheck $linkCheck
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($linkCheck, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $linkCheck));
	}
}

?>