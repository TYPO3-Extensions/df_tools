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

/**
 * Test case for class Tx_DfTools_ExtDirect_AbstractDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtDirect_AbstractDataProviderTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_ExtDirect_AbstractDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_ExtDirect_AbstractDataProvider';
		$this->fixture = $this->getAccessibleMock(
			$class,
			array('updateRecord', 'createRecord', 'destroyRecords', 'runTestForRecord')
		);
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
	public function updateCanHandleASingleRecord() {
		$record = (object)array(
			'records' => (object)array(
				'__hmac' => 'hmac',
				'__identity' => 1
			)
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('updateRecord')
			->with(array('__hmac' => 'hmac', '__identity' => 1))
			->will($this->returnValue(array('records' => array())));

		$this->fixture->update($record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateCanHandleMultipleRecords() {
		$record = (object)array(
			'records' => array(
				(object)array(
					'__hmac' => 'hmac',
					'__identity' => 1
				), (object)array(
					'__hmac' => 'hmac',
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
		$this->fixture->create((object)array('records' => 'FooBar'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyCallsActionExpectableWithOnlyOneIdentifier() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('destroyRecords')->with(array(2));
		$records = (object)array('records' => array(2));
		$this->fixture->destroy($records);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyCallsActionExpectableWithMultipleIdentifiers() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('destroyRecords')->with(array(2, 5, 10));
		$records = (object)array('records' => array(2, 5, 10));
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
				'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK,
				'testMessage' => '',
			),
		);

		$data = array(
			'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK,
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
		$exception = new Exception('FooBar');
		$expectedResult = array(
			'success' => FALSE,
			'data' => array(
				'testResult' => Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION,
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