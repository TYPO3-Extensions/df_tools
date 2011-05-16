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
 * Test case for class Tx_DfTools_ExtDirect_BackLinkTestDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtBaseConnector_BackLinkTestDataProviderTest extends Tx_DfTools_Tests_ExtBaseConnectorTestCase {
	/**
	 * @var Tx_DfTools_ExtDirect_BackLinkTestDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_ExtDirect_BackLinkTestDataProvider';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
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
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider() {
		return array(
			'simple update call #1' => array(
				array(
					'__hmac' => 'hmac',
					'backLinkTest' => array(
						'__identity' => 1,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
					)
				), array(
					'__hmac' => 'hmac',
					'__identity' => '1',
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
				)
			),

			'simple update call #2' => array(
				array(
					'__hmac' => 'hmac',
					'backLinkTest' => array(
						'__identity' => 2,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
					)
				), array(
					'__hmac' => 'hmac',
					'__identity' => 2,
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
				)
			),
		);
	}

	/**
	 * @dataProvider updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider
	 * @test
	 * @return void
	 */
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBase($parameters, $record) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->addMockedExtBaseConnector('BackLinkTest', 'update', $parameters);
		$this->fixture->_call('updateRecord', $record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createCallsTheExtBaseControllerWithExpectedParameters() {
		$parameters = array(
			'__hmac' => '__hmac',
			'newBackLinkTest' => array(
				'testUrl' => 'FooBar',
				'expectedUrl' => 'FooBar',
			)
		);
		$this->addMockedExtBaseConnector('BackLinkTest', 'create', $parameters);

		/** @noinspection PhpUndefinedFieldInspection */
		$record = new stdClass;
		$record->records = new stdClass;
		$record->records->__hmac = '__hmac';
		$record->records->__identity = 0;
		$record->records->testUrl = 'FooBar';
		$record->records->expectedUrl = 'FooBar';

		$this->fixture->create($record);
	}
}

?>