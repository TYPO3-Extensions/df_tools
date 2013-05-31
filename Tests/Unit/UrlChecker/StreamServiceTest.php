<?php

namespace SGalinski\DfTools\Tests\Unit\UrlChecker;

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

use SGalinski\DfTools\UrlChecker\StreamService;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class StreamServiceTest
 */
class StreamServiceTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\UrlChecker\StreamService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('SGalinski\DfTools\UrlChecker\StreamService');
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

		$this->fixture->setUrl('http://example.org');
		/** @noinspection PhpUndefinedMethodInspection */
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