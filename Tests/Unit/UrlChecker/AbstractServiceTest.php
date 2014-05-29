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

use SGalinski\DfTools\Tests\Unit\Controller\ControllerTestCase;

/**
 * Class AbstractServiceTest
 */
class AbstractServiceTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\UrlChecker\AbstractService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('SGalinski\DfTools\UrlChecker\AbstractService');
		$this->fixture = $this->getMockBuilder($proxyClass)
			->setMethods(array('init', 'resolveUrl'))
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
	public function settingTheTimeoutValueWorks() {
		$this->fixture->setTimeout(10);
		$this->assertSame(10, $this->fixture->getTimeout());

		$this->fixture->setTimeout('10');
		$this->assertSame(10, $this->fixture->getTimeout());
	}

	/**
	 * @test
	 * @return void
	 */
	public function settingTheUserAgentWorks() {
		$this->fixture->setUserAgent('FooBar');
		$this->assertSame('FooBar', $this->fixture->getUserAgent());
	}

	/**
	 * @return array
	 */
	public function settingTheUrlWorksDataProvider() {
		return array(
			'simple http url' => array(
				'http://foo.bar/narf/',
				'http', 'foo.bar', 'foo.bar',
				'http://foo.bar/narf/',
			),
			'simple https url' => array(
				'https://foo.bar/narf/',
				'https', 'foo.bar', 'foo.bar',
				'https://foo.bar/narf/',
			),
			'simple ftp url' => array(
				'ftp://foo.bar/narf/',
				'ftp', 'foo.bar', 'foo.bar',
				'ftp://foo.bar/narf/',
			),
			'simple url without path' => array(
				'http://foo.bar/',
				'http', 'foo.bar', 'foo.bar',
				'http://foo.bar/',
			),
			'simple url without path and ending slash' => array(
				'http://foo.bar',
				'http', 'foo.bar', 'foo.bar',
				'http://foo.bar',
			),
			'complex authority' => array(
				'ftp://user:password@foo.bar:8080/narf/',
				'ftp', 'user:password@foo.bar:8080', 'foo.bar',
				'ftp://user:password@foo.bar:8080/narf/',
			),
			'complex authority without password' => array(
				'ftp://user@foo.bar:8080/narf/',
				'ftp', 'user@foo.bar:8080', 'foo.bar',
				'ftp://user@foo.bar:8080/narf/',
			),
		);
	}

	/**
	 * @test
	 * @dataProvider settingTheUrlWorksDataProvider
	 *
	 * @param string $url
	 * @param string $protocol
	 * @param string $authority
	 * @param string $host
	 * @param string $expectedUrl
	 * @return void
	 */
	public function settingTheUrlWorks($url, $protocol, $authority, $host, $expectedUrl) {
		$this->fixture->setUrl($url);
		$this->assertSame($expectedUrl, $this->fixture->getUrl());
		$this->assertSame($protocol, $this->fixture->getProtocol());
		$this->assertSame($host, $this->fixture->getHost());
		$this->assertSame($authority, $this->fixture->getAuthority());
	}
}

?>