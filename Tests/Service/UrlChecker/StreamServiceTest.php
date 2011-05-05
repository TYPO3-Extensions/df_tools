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
 * Test case for class Tx_DfTools_Service_UrlChecker_StreamService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_StreamServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_UrlChecker_StreamService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_UrlChecker_StreamService');
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
	public function initCreatesValidContext() {
		$this->fixture->init();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInternalType('resource', $this->fixture->_get('context'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function getHttpCodeParsesTheCodeFromTheHeaders() {
		$headers = array(
			'HTTP/1.0 200 Ok',
			'FooBar',
			'HTTP/1.2 Ooops',
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$httpCode = $this->fixture->_call('getHttpCode', $headers);
		$this->assertSame(200, $httpCode);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getHttpCodeParsesTheCodeFromTheHeadersWithRedirect() {
		$headers = array(
			'HTTP/1.0 301 Ok',
			'Location: FooBar',
			'HTTP/1.1 200 Ok',
			'HTTP/1.2 Ooops',
			'FooBar'
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$httpCode = $this->fixture->_call('getHttpCode', $headers);
		$this->assertSame(200, $httpCode);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getLastUrlReturnsAnEmptyStringIfNoLocationHeaderWasSet() {
		$headers = array(
			'HTTP/1.1 200 Ok',
			'FooBar'
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$url = $this->fixture->_call('getLastUrl', $headers);
		$this->assertSame('', $url);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getLastUrlReturnsTheLocationUrl() {
		$headers = array(
			'HTTP/1.0 301 Ok',
			'Location: http://example.org/FooBar/bla',
			'HTTP/1.1 200 Ok',
			'FooBar'
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$url = $this->fixture->_call('getLastUrl', $headers);
		$this->assertSame('http://example.org/FooBar/bla', $url);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getLastUrlReturnsTheAbsoluteLocationUrlIfWithoutAuthorityInHeader() {
		$headers = array(
			'HTTP/1.0 301 Ok',
			'Location: /FooBar/bla',
			'HTTP/1.1 200 Ok',
			'FooBar'
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setUrl('http://example.org');
		$url = $this->fixture->_call('getLastUrl', $headers);
		$this->assertSame('http://example.org/FooBar/bla', $url);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getLastUrlReturnsTheLastLocationUrl() {
		$headers = array(
			'HTTP/1.0 301 Ok',
			'Location: http://example.org/FooBar/1',
			'HTTP/1.1 301 Ok',
			'Location: http://example.org/FooBar/2',
			'HTTP/1.1 200 Ok',
			'FooBar'
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$url = $this->fixture->_call('getLastUrl', $headers);
		$this->assertSame('http://example.org/FooBar/2', $url);
	}
}

?>