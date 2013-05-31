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

use SGalinski\DfTools\Domain\Model\BackLinkTest;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\View\BackLinkTestArrayView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class BackLinkTestArrayViewTest
 */
class BackLinkTestArrayViewTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\View\BackLinkTestArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('SGalinski\DfTools\View\BackLinkTestArrayView');
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
		$backLinkTestNormal = new BackLinkTest();
		$backLinkTestNormal->setTestUrl('FooBar');
		$backLinkTestNormal->setExpectedUrl('FooBar');
		$backLinkTestNormal->setComment('FooBar');

		$backLinkTestWithTestResult = new BackLinkTest();
		$backLinkTestWithTestResult->setTestUrl('FooBar');
		$backLinkTestWithTestResult->setExpectedUrl('FooBar');
		$backLinkTestWithTestResult->setTestResult(1);
		$backLinkTestWithTestResult->setTestMessage('FooBar');
		$backLinkTestWithTestResult->setComment('FooBar');

		$backLinkTestWithXSS = new BackLinkTest();
		$backLinkTestWithXSS->setTestUrl('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$backLinkTestWithXSS->setExpectedUrl('<script>alert("Ooops!!!");</script>');
		$backLinkTestWithXSS->setTestMessage('<script>alert("Ooops!!!");</script>');
		$backLinkTestWithXSS->setComment('<script>alert("Ooops!!!");</script>');

		return array(
			'normal back link test' => array(
				$backLinkTestNormal,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'testResult' => AbstractService::SEVERITY_UNTESTED,
					'testMessage' => '',
					'comment' => 'FooBar',
				),
			),
			'back link test with test result' => array(
				$backLinkTestWithTestResult,
				array(
					'__identity' => 0,
					'testUrl' => 'FooBar',
					'expectedUrl' => 'FooBar',
					'testResult' => 1,
					'testMessage' => 'FooBar',
					'comment' => 'FooBar',
				),
			),
			'XSS attack' => array(
				$backLinkTestWithXSS,
				array(
					'__identity' => 0,
					'testUrl' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
					'expectedUrl' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'testResult' => AbstractService::SEVERITY_UNTESTED,
					'testMessage' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
					'comment' => htmlspecialchars('<script>alert("Ooops!!!");</script>'),
				),
			)
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 * @param BackLinkTest $backLinkTest
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($backLinkTest, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $backLinkTest));
	}
}

?>