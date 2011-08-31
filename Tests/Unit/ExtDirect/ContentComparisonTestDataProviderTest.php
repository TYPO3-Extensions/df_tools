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
 * Test case for class Tx_DfTools_ExtDirect_ContentComparisonTestDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtBaseConnector_ContentComparisonTestDataProviderTest extends Tx_DfTools_ExtBaseConnectorTestCase {
	/**
	 * @var Tx_DfTools_ExtDirect_ContentComparisonTestDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_ExtDirect_ContentComparisonTestDataProvider';
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
	 * @param stdClass $input
	 * @return void
	 */
	public function readCallsExtBaseControllerWithParameters($input) {
		$parameters = array(
			'offset' => 0,
			'limit' => 200,
			'sortingField' => 'testResult',
			'sortAscending' => FALSE
		);
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'read', $parameters);
		$this->fixture->read($input);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createCallsCreateAction() {
		$parameters = array(
			'__hmac' => 'hmac',
			'newContentComparisonTest' => array(
				'testUrl' => 'FooBar',
				'compareUrl' => 'FooBar'
			),
		);
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'create', $parameters);

		/** @noinspection PhpUndefinedFieldInspection */
		$record = array(
			'records' => (object)array(
				'__hmac' => 'hmac',
				'__identity' => 0,
				'testUrl' => 'FooBar',
				'compareUrl' => 'FooBar',
			),
		);

		$this->fixture->create((object)$record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateCallsUpdateAction() {
		$parameters = array(
			'__hmac' => 'hmac',
			'contentComparisonTest' => array(
				'__identity' => 2,
				'testUrl' => 'FooBar',
				'compareUrl' => 'FooBar'
			),
		);
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'update', $parameters);

		/** @noinspection PhpUndefinedFieldInspection */
		$record = (object)array(
			'records' => (object)array(
				'__hmac' => 'hmac',
				'__identity' => 2,
				'testUrl' => 'FooBar',
				'compareUrl' => 'FooBar',
			),
		);

		$this->fixture->update($record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyCallsDestrayAction() {
		$parameters = array('identifiers' => array(10, 20));
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'destroy', $parameters);
		$this->fixture->destroy((object)array('records' => array(10, 20)));
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestCallsRunAction() {
		$parameters = array('identity' => 5);
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'runTest', $parameters);
		$this->fixture->runTest(5);
	}

	/**
	 * @test
	 * @return void
	 */
	public function updateTestContentCallsUpdateTestContentAction() {
		$parameters = array('identity' => 5);
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'updateTestContent', $parameters);
		$this->fixture->updateTestContent(5);
	}
}

?>