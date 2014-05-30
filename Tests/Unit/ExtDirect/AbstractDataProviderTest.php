<?php

namespace SGalinski\DfTools\Tests\Unit\ExtDirect;

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
use SGalinski\DfTools\UrlChecker\AbstractService;

/**
 * Class AbstractDataProviderTest
 */
class AbstractDataProviderTest extends ControllerTestCase {

	/**
	 * @var \SGalinski\DfTools\ExtDirect\AbstractDataProvider|object
	 */
	protected $fixture;

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $backupTSFE;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->backupTSFE = $GLOBALS['TSFE'];
		$GLOBALS['TSFE'] = $this->getMock(
			'TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', [], [], '', FALSE
		);

		$methods = array('updateRecord', 'createRecord', 'destroyRecords', 'isInFrontendMode', 'runTestForRecord');
		$proxy = $this->buildAccessibleProxy('SGalinski\DfTools\ExtDirect\AbstractDataProvider');
		$this->fixture = $this->getMockBuilder($proxy)
			->setMethods($methods)
			->disableOriginalConstructor()->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TSFE'] = $this->backupTSFE;
		unset($this->fixture);
	}

	/**
	 * @test
	 * @expectedException \SGalinski\DfTools\Exception\GenericException
	 * @return void
	 */
	public function accessCheckFailsIfNoFrontendUserIsLoggedInIfCalledInFrontendMode() {
		$this->fixture->expects($this->once())->method('isInFrontendMode')->will($this->returnValue(TRUE));
		$this->fixture->hasAccess();
	}

	/**
	 * @test
	 * @return void
	 */
	public function accessCheckSucceedsIfFrontendUserIsLoggedInIfCalledInFrontendMode() {
		$this->fixture->expects($this->once())->method('isInFrontendMode')->will($this->returnValue(TRUE));
		$GLOBALS['TSFE']->fe_user->user['uid'] = 1;
		$this->fixture->hasAccess();
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateCanHandleASingleRecord() {
		/** @var \stdClass $record */
		$record = (object) array(
			'records' => (object) array(
				'__trustedProperties' => 'hmac',
				'__identity' => 1
			)
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('updateRecord')
			->with(array('__trustedProperties' => 'hmac', '__identity' => 1))
			->will($this->returnValue(array('records' => array())));

		$this->fixture->update($record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateCanHandleMultipleRecords() {
		$record = (object) array(
			'records' => array(
				(object) array(
					'__trustedProperties' => 'hmac',
					'__identity' => 1
				), (object) array(
					'__trustedProperties' => 'hmac',
					'__identity' => 2
				),
			)
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->exactly(2))->method('updateRecord')
			->with($this->isType('array'))
			->will($this->returnValue(array('records' => array())));

		$this->fixture->update($record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createAddsANewRecord() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('createRecord')->with(array('FooBar'));
		$this->fixture->create((object) array('records' => 'FooBar'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyCallsActionExpectableWithOnlyOneIdentifier() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('destroyRecords')->with(array(2));
		/** @var /stdClass $records */
		$records = (object) array('records' => array(2));
		$this->fixture->destroy($records);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyCallsActionExpectableWithMultipleIdentifiers() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('destroyRecords')->with(array(2, 5, 10));
		/** @var /stdClass $records */
		$records = (object) array('records' => array(2, 5, 10));
		$this->fixture->destroy($records);
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestCallWithoutException() {
		$identity = 12;
		$expectedResult = array(
			'success' => TRUE,
			'data' => array(
				'testResult' => AbstractService::SEVERITY_OK,
				'testMessage' => '',
			),
		);

		$data = array(
			'testResult' => AbstractService::SEVERITY_OK,
			'testMessage' => '',
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('runTestForRecord')
			->will($this->returnValue($data));
		$this->assertSame($expectedResult, $this->fixture->runTest($identity));
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestCallHandlesAGenericException() {
		$identity = 12;
		$exception = new \Exception('FooBar');
		$expectedResult = array(
			'success' => FALSE,
			'data' => array(
				'testResult' => AbstractService::SEVERITY_EXCEPTION,
				'testMessage' => 'FooBar',
			),
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('runTestForRecord')
			->will($this->throwException($exception));
		$this->assertSame($expectedResult, $this->fixture->runTest($identity));
	}
}

?>