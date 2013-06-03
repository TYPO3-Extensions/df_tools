<?php

namespace SGalinski\DfTools\Tests\Command;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan.galinski@gmail.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\UrlChecker\AbstractService;

/**
 * Class AbstractCommandControllerTest
 */
class AbstractCommandControllerTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\Command\AbstractCommandController|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\Command\AbstractCommandController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
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
		$this->assertSame($expected, $this->fixture->_call('br2nl', $input, $additional));
	}

	/**
	 * @return array
	 */
	public function checkTestResultsSendsFailedRecordsDataProvider() {
		$errorEntry = new RedirectTest();
		$errorEntry->setTestResult(AbstractService::SEVERITY_ERROR);

		$okEntry = new RedirectTest();
		$okEntry->setTestResult(AbstractService::SEVERITY_OK);

		$infoEntry = new RedirectTest();
		$infoEntry->setTestResult(AbstractService::SEVERITY_INFO);

		$warningEntry = new RedirectTest();
		$warningEntry->setTestResult(AbstractService::SEVERITY_WARNING);

		$ignoreEntry = new RedirectTest();
		$ignoreEntry->setTestResult(AbstractService::SEVERITY_IGNORE);

		return array(
			'failed tests combined with successful ones' => array(
				array($errorEntry, $warningEntry),
				array($okEntry, $errorEntry, $okEntry, $ignoreEntry, $infoEntry, $warningEntry)
			),
			'succeeded tests' => array(
				array(),
				array($okEntry, $okEntry),
			),
			'succeeded tests with an ignore' => array(
				array(),
				array($okEntry, $ignoreEntry, $okEntry),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider checkTestResultsSendsFailedRecordsDataProvider
	 *
	 * @param boolean $expected
	 * @param array $testResults
	 * @return void
	 */
	public function checkTestResultsSendsFailedRecords($expected, array $testResults) {
		$returnValue = $this->fixture->_call('checkTestResults', $testResults);
		$this->assertSame($expected, $returnValue);
	}
}

?>