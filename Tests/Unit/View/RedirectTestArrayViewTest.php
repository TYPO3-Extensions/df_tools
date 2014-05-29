<?php

namespace SGalinski\DfTools\Tests\Unit\View;

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
use SGalinski\DfTools\UrlChecker\AbstractService;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class RedirectTestArrayViewTest
 */
class RedirectTestArrayViewTest extends UnitTestCase {
	/**
	 * @var \SGalinski\DfTools\View\RedirectTestArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('SGalinski\DfTools\View\RedirectTestArrayView');
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
		$category = new RedirectTestCategory();
		$category->setCategory('FooBar');

		$redirectTestNormal = new RedirectTest();
		$redirectTestNormal->setTestUrl('FooBar');
		$redirectTestNormal->setExpectedUrl('FooBar');
		$redirectTestNormal->setHttpStatusCode(200);
		$redirectTestNormal->setCategory($category);

		$redirectTestWithoutCategory = new RedirectTest();
		$redirectTestWithoutCategory->setTestUrl('FooBar');
		$redirectTestWithoutCategory->setExpectedUrl('FooBar');
		$redirectTestWithoutCategory->setHttpStatusCode(404);
		$redirectTestWithoutCategory->setTestResult(1);
		$redirectTestWithoutCategory->setTestMessage('FooBar');

		$redirectTestWithXSS = new RedirectTest();
		$redirectTestWithXSS->setTestUrl('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$redirectTestWithXSS->setExpectedUrl('<script>alert("Ooops!!!");</script>');
		$redirectTestWithXSS->setHttpStatusCode(500);
		$redirectTestWithXSS->setTestMessage('<script>alert("Ooops!!!");</script>');

		return array(
			'normal redirect test with category' => array(
				$redirectTestNormal,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'httpStatusCode' => 200,
					'testResult' => AbstractService::SEVERITY_UNTESTED,
					'testMessage' => '',
					'categoryId' => NULL,
				),
			),
			'redirect test without category' => array(
				$redirectTestWithoutCategory,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'httpStatusCode' => 404,
					'testResult' => 1,
					'testMessage' => 'FooBar',
					'categoryId' => '',
				),
			),
			'XSS attack' => array(
				$redirectTestWithXSS,
				array(
					'__identity' => 0,
					'testUrl' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
					'expectedUrl' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'httpStatusCode' => 500,
					'testResult' => AbstractService::SEVERITY_UNTESTED,
					'testMessage' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'categoryId' => '',
				),
			)
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 * @param RedirectTest $redirectTest
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($redirectTest, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $redirectTest));
	}
}

?>