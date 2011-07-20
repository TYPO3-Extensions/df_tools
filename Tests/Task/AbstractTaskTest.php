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

$schedulerPath = t3lib_extMgm::extPath('scheduler');
require_once($schedulerPath . 'class.tx_scheduler_task.php');

/**
 * Test case for class Tx_DfTools_Task_AbstractTask.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Task_AbstractTaskTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Task_AbstractTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
			// only solution to test the scheduler stuff, because they include mod1/index.php
			// that is directly executed
		t3lib_autoloader::unregisterAutoloader();

		$proxy = $this->buildAccessibleProxy('Tx_DfTools_Task_AbstractTask');
		$this->fixture = $this->getMockBuilder($proxy)
			->setMethods(array('execute', 'sendNotificationEmail'))
			->disableOriginalConstructor()->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		t3lib_autoloader::registerAutoloader();
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
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
				),
			),
			'succeeded tests' => array(
				TRUE, array(
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
				),
			),
			'succeeded tests with an ignore' => array(
				TRUE, array(
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_IGNORE),
					array('testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK),
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
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setNotificationEmailAddress($input);
		$result = $this->fixture->_call('getNotificationEmailAddressesForSwift');
		$this->assertSame($expected, $result);
	}
}

?>