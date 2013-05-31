<?php

namespace SGalinski\DfTools\Tests\Unit\Domain\Model;

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

use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Model\RecordSet;
use SGalinski\DfTools\UrlChecker\AbstractService;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;

/**
 * Class LinkCheckTest
 */
class LinkCheckTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Model\LinkCheck
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new LinkCheck();
		$this->fixture->setTestUrl('FooBar');
		$this->fixture->setResultUrl('FooBar');
		$this->fixture->setHttpStatusCode(200);
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
	public function setTestUrlWorks() {
		$this->fixture->setTestUrl('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTestUrl());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setResultUrlWorks() {
		$this->fixture->setResultUrl('FooBar');
		$this->assertSame('FooBar', $this->fixture->getResultUrl());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setHttpStatusCodeWorks() {
		$this->fixture->setHttpStatusCode(12);
		$this->assertSame(12, $this->fixture->getHttpStatusCode());

		$this->fixture->setHttpStatusCode('12');
		$this->assertSame(12, $this->fixture->getHttpStatusCode());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setTestResultWorks() {
		$this->fixture->setTestResult(0);
		$this->assertSame(0, $this->fixture->getTestResult());

		$this->fixture->setTestResult('1');
		$this->assertSame(1, $this->fixture->getTestResult());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setTestMessageWorks() {
		$this->fixture->setTestMessage('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTestMessage());
	}

	/**
	 * @test
	 * @return void
	 */
	public function recordSetsCanBeSet() {
		$objectStorage = new ObjectStorage();
		$objectStorage->attach((new RecordSet()));
		$this->fixture->setRecordSets($objectStorage);

		$this->assertSame($objectStorage, $this->fixture->getRecordSets());
	}

	/**
	 * @test
	 * @return void
	 */
	public function recordSetCanBeAdded() {
		$recordSet = new RecordSet();
		$objectStorage = $this->fixture->getRecordSets();

		$this->fixture->addRecordSet($recordSet);
		$objectStorage->attach($recordSet);

		$this->assertEquals($objectStorage, $this->fixture->getRecordSets());
	}

	/**
	 * @test
	 * @return void
	 */
	public function recordSetCanBeRemoved() {
		$recordSet = new RecordSet();
		$objectStorage = $this->fixture->getRecordSets();

		$objectStorage->attach($recordSet);
		$objectStorage->detach($recordSet);

		$this->assertEquals($objectStorage, $this->fixture->getRecordSets());
	}

	/**
	 * @test
	 * @return void
	 */
	public function transformToArrayWorks() {
		$expected = array(
			'__identity' => NULL,
			'testUrl' => 'FooBar',
			'resultUrl' => 'FooBar',
			'httpStatusCode' => 200,
			'testResult' => AbstractService::SEVERITY_UNTESTED,
			'testMessage' => ''
		);

		$this->assertSame($expected, $this->fixture->toArray());
	}

	/**
	 * @param mixed $resolveUrlOutput
	 * @param \PHPUnit_Framework_MockObject_Matcher_InvokedCount $amountOfCalls
	 * @return AbstractService
	 */
	protected function getUrlCheckerService(
		$resolveUrlOutput, \PHPUnit_Framework_MockObject_Matcher_InvokedCount $amountOfCalls
	) {
		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$urlCheckerService->expects($amountOfCalls)->method('resolveURL')
			->will($this->returnValue($resolveUrlOutput));

		return $urlCheckerService;
	}

	/**
	 * @return array
	 */
	public function testHandlesAllCasesDataProvider() {
		return array(
			'url mismatch' => array(
				array('http_code' => 200, 'url' => 'UnknownUrl'),
				AbstractService::SEVERITY_WARNING,
			),
			'http code mismatch with 404' => array(
				array('http_code' => 404, 'url' => 'FooBar'),
				AbstractService::SEVERITY_ERROR,
			),
			'test works with http code 200' => array(
				array('http_code' => 200, 'url' => 'FooBar'),
				AbstractService::SEVERITY_OK,
			),
			'test works with http code 301' => array(
				array('http_code' => 301, 'url' => 'FooBar'),
				AbstractService::SEVERITY_OK,
			),
			'test works with http code 302' => array(
				array('http_code' => 302, 'url' => 'FooBar'),
				AbstractService::SEVERITY_OK,
			),
		);
	}

	/**
	 * @test
	 * @dataProvider testHandlesAllCasesDataProvider
	 *
	 * @param array $resolveUrlOutput
	 * @param boolean $testResult
	 * @return void
	 */
	public function testHandlesAllCases(array $resolveUrlOutput, $testResult) {
		$urlCheckerService = $this->getUrlCheckerService($resolveUrlOutput, $this->once());
		$this->fixture->test($urlCheckerService);
		$this->assertSame($testResult, $this->fixture->getTestResult());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testIsNotExecutedBecauseItHasAnIgnoreState() {
		$this->fixture->setTestResult(AbstractService::SEVERITY_IGNORE);
		$urlCheckerService = $this->getUrlCheckerService(array(), $this->never());
		$this->fixture->test($urlCheckerService);
		$this->assertSame(
			AbstractService::SEVERITY_IGNORE,
			$this->fixture->getTestResult()
		);
	}

	/**
	 * @test
	 * @return void
	 */
	public function testIsExecutedButTheRecordIsInFalsePositiveStateAndDoesNotUpdateItsState() {
		$this->fixture->setTestResult(AbstractService::SEVERITY_INFO);
		$urlCheckerService = $this->getUrlCheckerService(array(), $this->once());
		$this->fixture->test($urlCheckerService);
		$this->assertSame(
			AbstractService::SEVERITY_INFO,
			$this->fixture->getTestResult()
		);
	}
}

?>