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

use SGalinski\DfTools\Domain\Model\ContentComparisonTest;
use SGalinski\DfTools\Tests\Unit\Controller\ControllerTestCase;
use SGalinski\DfTools\UrlChecker\AbstractService;

/**
 * Class ContentComparisonTestTest
 */
class ContentComparisonTestTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Model\ContentComparisonTest
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['compressionLevel'] = -1;
		$this->fixture = new ContentComparisonTest();
		$this->fixture->setTestUrl('FooBar');
		$this->fixture->setCompareUrl('FooBar');
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
	public function setCompareUrlWorks() {
		$this->fixture->setCompareUrl('FooBar');
		$this->assertSame('FooBar', $this->fixture->getCompareUrl());
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
	public function setTestContentWorks() {
		$this->fixture->setTestContent('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTestContent());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setCompareContentWorks() {
		$this->fixture->setCompareContent('FooBar');
		$this->assertSame('FooBar', $this->fixture->getCompareContent());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setDifferenceWorks() {
		$this->fixture->setDifference('FooBar');
		$this->assertSame('FooBar', $this->fixture->getDifference());
	}

	/**
	 * @test
	 * @return void
	 */
	public function instanceCanBeTransformToAnArray() {
		$contentComparisonTest = new ContentComparisonTest;
		$contentComparisonTest->setTestUrl('fooBar1');
		$contentComparisonTest->setCompareUrl('fooBar2');
		$contentComparisonTest->setTestContent('fooBar3');
		$contentComparisonTest->setCompareContent('fooBar4');
		$contentComparisonTest->setDifference('fooBar5');
		$contentComparisonTest->setTestMessage('fooBar6');
		$contentComparisonTest->setTestResult(2);

		$asArray = array(
			'__identity' => NULL,
			'testUrl' => 'fooBar1',
			'compareUrl' => 'fooBar2',
			'testContent' => 'fooBar3',
			'compareContent' => 'fooBar4',
			'difference' => 'fooBar5',
			'testMessage' => 'fooBar6',
			'testResult' => 2,
		);

		$this->assertSame($asArray, $contentComparisonTest->toArray());
	}

	/**
	 * @param mixed $resolveUrlOutput
	 * @return AbstractService
	 */
	protected function getUrlCheckerService($resolveUrlOutput) {
		/** @var $urlCheckerService AbstractService */
		$class = 'SGalinski\DfTools\UrlChecker\AbstractService';
		$urlCheckerService = $this->getMock($class, array('init', 'resolveURL'));

		/** @noinspection PhpUndefinedMethodInspection */
		$urlCheckerService->expects($this->any())->method('resolveURL')
			->will($this->returnValue($resolveUrlOutput));

		return $urlCheckerService;
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithEqualUrlsAtFirstRun() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setTestContent('');
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'Foo; Bar; Narf', 'http_code' => 200));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_OK, $result);
		$this->assertSame('Foo; Bar; Narf', $this->fixture->getTestContent());
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithEqualUrlsAtSecondRunWithoutDifference() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setTestContent('Foo; Bar; Narf');
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'Foo; Bar; Narf', 'http_code' => 200));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_OK, $result);
		$this->assertSame('', $this->fixture->getDifference());
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithEqualUrlsAndMultipleSearchTags() {
		$this->fixture->setTestContent(
			'
						<!--TYPO3SEARCH_begin-->
						Foo; Bar; Narf
						<!--TYPO3SEARCH_end-->
						Some Foo Content
						<!--TYPO3SEARCH_begin-->
						Foo; Bar; Narf
						<!--TYPO3SEARCH_end-->
					'
		);

		$returns = '
			<!--TYPO3SEARCH_begin-->
			Foo; Bar; Narf
			<!--TYPO3SEARCH_end-->
			Some Foo Content
			<!--TYPO3SEARCH_begin-->
			; Narf
			<!--TYPO3SEARCH_end-->
		';

		/** @noinspection PhpUndefinedMethodInspection */
		$urlCheckerService = $this->getUrlCheckerService(array('content' => $returns, 'http_code' => 200));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_ERROR, $result);
		$this->assertContains('Foo; Bar', $this->fixture->getDifference());
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithEqualUrlsAtSecondRunWithDifference() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setTestContent('Foo; Bar;');
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'Foo; Bar; Narf', 'http_code' => 200));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_ERROR, $result);
		$this->assertContains('Narf', $this->fixture->getDifference());
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithEqualUrlsThrows404() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setTestContent('Foo; Bar;');
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'Foo; Bar; Narf', 'http_code' => 404));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_ERROR, $result);
		echo($this->fixture->getDifference());
		$this->assertSame('', $this->fixture->getDifference());
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestWithDifferentUrlsWithoutDifference() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->setCompareUrl('BarFoo');
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'Foo; Bar; Narf', 'http_code' => 200));
		$this->fixture->test($urlCheckerService);

		$result = $this->fixture->getTestResult();
		$this->assertSame(AbstractService::SEVERITY_OK, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateTestContentWorks() {
		$urlCheckerService = $this->getUrlCheckerService(array('content' => 'FooBar', 'http_code' => 200));
		$this->fixture->updateTestContent($urlCheckerService);
		$this->assertSame('FooBar', $this->fixture->getTestContent());
	}
}

?>