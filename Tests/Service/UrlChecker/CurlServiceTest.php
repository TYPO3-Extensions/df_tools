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
 * Test case for class Tx_DfTools_Service_UrlChecker_CurlService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_CurlServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_UrlChecker_CurlService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		if (!in_array('curl', get_loaded_extensions())) {
			$this->markTestSkipped('curl extension not loaded!');
		}

		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_UrlChecker_CurlService');
		$this->fixture = $this->getMockBuilder($proxyClass)
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
	 * @test
	 * @return void
	 */
	public function initCreatesValidCurlHandle() {
		$this->fixture->init();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInternalType('resource', $this->fixture->_get('cUrlHandle'));
	}
}

?>