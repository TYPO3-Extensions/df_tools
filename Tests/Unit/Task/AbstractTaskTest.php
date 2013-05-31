<?php

namespace SGalinski\DfTools\Tests\Unit\Task;

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

use SGalinski\DfTools\Connector\ExtBaseConnectorService;
use SGalinski\DfTools\Parser\TcaParserService;
use SGalinski\DfTools\Parser\UrlParserService;
use SGalinski\DfTools\Task\AbstractTask;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Class AbstractTaskTest
 */
class AbstractTaskTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\Task\AbstractTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$proxy = $this->buildAccessibleProxy('SGalinski\DfTools\Task\AbstractTask');
		$this->fixture = $this->getMockBuilder($proxy)
			->setMethods(array('execute', 'sendNotificationEmail'))
			->disableOriginalConstructor()->getMock();
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
	public function settingTheNotificationEmailAddressWorks() {
		$this->fixture->setNotificationEmailAddress('mail@example.org');
		$this->assertSame('mail@example.org', $this->fixture->getNotificationEmailAddress());
	}

	/**
	 * @return array
	 */
	public function convertHtmlLineBreakToNewlineWorksDataProvider() {
		return array(
			'simple string without linebreaks' => array(
				'FooBar', 'FooBar'
			),
			'simple string with linebreaks' => array(
				'FooBar' . PHP_EOL . 'FooBar' . PHP_EOL . 'FooBar', 'FooBar<br/>FooBar<br />FooBar'
			),
			'simple string with legacy html4 linebreaks' => array(
				'FooBar' . PHP_EOL . 'FooBar', 'FooBar<br>FooBar'
			),
			'simple string with linebreak and additional replacement' => array(
				'FooBar' . PHP_EOL . '||FooBar', 'FooBar<br />FooBar', '||'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider convertHtmlLineBreakToNewlineWorksDataProvider
	 *
	 * @param string $expected
	 * @param string $input
	 * @param string $additional
	 * @return void
	 */
	public function convertHtmlLineBreakToNewlineWorks($expected, $input, $additional = '') {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('br2nl', $input, $additional));
	}

	/**
	 * @return array
	 */
	public function checkTestResultsSendsFailedRecordsByMailDataProvider() {
		return array(
			'failed tests' => array(
				TRUE, array(
					array('testResult' => AbstractService::SEVERITY_OK),
					array('testResult' => AbstractService::SEVERITY_ERROR),
					array('testResult' => AbstractService::SEVERITY_OK),
					array('testResult' => AbstractService::SEVERITY_INFO),
					array('testResult' => AbstractService::SEVERITY_OK),
				),
			),
			'succeeded tests' => array(
				TRUE, array(
					array('testResult' => AbstractService::SEVERITY_OK),
					array('testResult' => AbstractService::SEVERITY_OK),
				),
			),
			'succeeded tests with an ignore' => array(
				TRUE, array(
					array('testResult' => AbstractService::SEVERITY_OK),
					array('testResult' => AbstractService::SEVERITY_IGNORE),
					array('testResult' => AbstractService::SEVERITY_OK),
				),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider checkTestResultsSendsFailedRecordsByMailDataProvider
	 *
	 * @param boolean $expected
	 * @param array $testResults
	 * @return void
	 */
	public function checkTestResultsSendsFailedRecordsByMail($expected, array $testResults) {
		if (!$expected) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->fixture->expects($this->once())->method('sendNotificationEmail');
		}

		/** @noinspection PhpUndefinedMethodInspection */
		$returnValue = $this->fixture->_call('checkTestResults', $testResults);
		$this->assertSame($expected, $returnValue);
	}

	/**
	 * @return array
	 */
	public function getPreparedNotificationMailAddressesWorksDataProvider() {
		return array(
			'simple email address' => array(
				'foo@bar.de', array(
					'foo@bar.de' => NULL,
				)
			),
			'multiple email addresses' => array(
				'foo@bar.de,bar@foo.com', array(
					'foo@bar.de' => NULL,
					'bar@foo.com' => NULL,
				)
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getPreparedNotificationMailAddressesWorksDataProvider
	 *
	 * @param string $input
	 * @param array $expected
	 * @return void
	 */
	public function getPreparedNotificationMailAddressesWorks($input, array $expected) {
		$this->fixture->setNotificationEmailAddress($input);
		$result = $this->fixture->_call('getNotificationEmailAddressesForSwift');
		$this->assertSame($expected, $result);
	}
}

?>