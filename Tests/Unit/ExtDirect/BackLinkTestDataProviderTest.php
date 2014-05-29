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

use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;

/**
 * Class BackLinkTestDataProviderTest
 */
class BackLinkTestDataProviderTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\ExtDirect\BackLinkTestDataProvider|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\ExtDirect\BackLinkTestDataProvider';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
		$this->fixture->_set('extBaseConnector', $this->extBaseConnector);
	}

	/**
	 * @return array
	 */
	public function readCallsExtBaseControllerWithParametersDataProvider() {
		return array(
			'default' => array(
				(object) array('start' => 0, 'limit' => 200, 'sort' => 'testResult', 'dir' => 'DESC'),
			),
			'non-normalized input' => array(
				(object) array('start' => '0', 'limit' => '200', 'sort' => 'testResult', 'dir' => 'desc'),
			),
		);
	}

	/**
	 * @dataProvider readCallsExtBaseControllerWithParametersDataProvider
	 * @test
	 *
	 * @param \stdClass $input
	 * @return void
	 */
	public function readCallsExtBaseControllerWithParameters($input) {
		$parameters = array(
			'offset' => 0,
			'limit' => 200,
			'sortingField' => 'testResult',
			'sortAscending' => FALSE
		);
		$this->addMockedExtBaseConnector('BackLinkTest', 'read', $parameters);
		$this->fixture->read($input);
	}

	/**
	 * @return array
	 */
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider() {
		return array(
			'simple update call #1' => array(
				array(
					'__trustedProperties' => 'hmac',
					'backLinkTest' => array(
						'__identity' => 1,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
						'comment' => 'fooBar',
					)
				), array(
					'__trustedProperties' => 'hmac',
					'__identity' => '1',
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
					'comment' => 'fooBar',
				)
			),

			'simple update call #2' => array(
				array(
					'__trustedProperties' => 'hmac',
					'backLinkTest' => array(
						'__identity' => 2,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
						'comment' => 'fooBar',
					)
				), array(
					'__trustedProperties' => 'hmac',
					'__identity' => 2,
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
					'comment' => 'fooBar',
				)
			),

			'simple update call with quoted expected url' => array(
				array(
					'__trustedProperties' => 'hmac',
					'backLinkTest' => array(
						'__identity' => 2,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'http:\\\/\\\/foo.bar',
						'comment' => 'fooBar',
					)
				), array(
					'__trustedProperties' => 'hmac',
					'__identity' => 2,
					'testUrl' => 'fooBar',
					'expectedUrl' => 'http:\/\/foo.bar',
					'comment' => 'fooBar',
				)
			),
		);
	}

	/**
	 * @dataProvider updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider
	 * @test
	 *
	 * @param array $parameters
	 * @param array $record
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
			'__trustedProperties' => '__trustedProperties',
			'newBackLinkTest' => array(
				'testUrl' => 'FooBar',
				'expectedUrl' => 'http:\\\/\\\/foo.bar',
			)
		);
		$this->addMockedExtBaseConnector('BackLinkTest', 'create', $parameters);

		/** @noinspection PhpUndefinedFieldInspection */
		$record = new \stdClass();
		$record->records = new \stdClass();
		$record->records->__trustedProperties = '__trustedProperties';
		$record->records->__identity = 0;
		$record->records->testUrl = 'FooBar';
		$record->records->expectedUrl = 'http:\/\/foo.bar';

		$this->fixture->create($record);
	}
}

?>