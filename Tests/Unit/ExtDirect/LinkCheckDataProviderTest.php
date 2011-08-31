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
 * Test case for class Tx_DfTools_ExtDirect_LinkCheckDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtDirect_LinkCheckDataProviderTest extends Tx_DfTools_ExtBaseConnectorTestCase {
	/**
	 * @var Tx_DfTools_ExtDirect_LinkCheckDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_ExtDirect_LinkCheckDataProvider';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
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
	public function synchronizeRunsWithoutException() {
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronize');
		$this->assertSame(array('success' => TRUE), $this->fixture->synchronize());
	}

	/**
	 * @test
	 * @expectException Exception
	 * @return void
	 */
	public function synchronizeMustHandleAnException() {
		$exception = new Exception('FooBar');
		$expected = array(
			'success' => FALSE,
			'message' => 'FooBar',
		);
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronize', array(), NULL, $exception);
		$this->assertSame($expected, $this->fixture->synchronize());
	}

	/**
	 * @return array
	 */
	public function readHandlesParametersDataProvider() {
		return array(
			'ascending sort' => array(
				array('start' => 0, 'limit' => 200, 'sort' => 'field1', 'dir' => 'ASC'),
				array('offset' => 0, 'limit' => 200, 'sortingField' => 'field1', 'sortAscending' => TRUE),
			),
			'descending sort' => array(
				array('start' => 0, 'limit' => 200, 'sort' => 'field1', 'dir' => 'DESC'),
				array('offset' => 0, 'limit' => 200, 'sortingField' => 'field1', 'sortAscending' => FALSE),
			),
			'strange values' => array(
				array('start' => 'abc', 'limit' => '200', 'sort' => 'field1', 'dir' => 'FOOBAR'),
				array('offset' => 0, 'limit' => 200, 'sortingField' => 'field1', 'sortAscending' => FALSE),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider readHandlesParametersDataProvider
	 *
	 * @param array $givenParameters
	 * @param array $parameters
	 * @return void
	 */
	public function readHandlesParameters(array $givenParameters, array $parameters) {
		$this->addMockedExtBaseConnector('LinkCheck', 'read', $parameters);
		$this->fixture->read((object) $givenParameters);
	}

	/**
	 * @test
	 * @return void
	 */
	public function ignoreRecordWorks() {
		$parameters = array(
			'identity' => 7,
			'doIgnoreRecord' => TRUE,
		);

		$this->addMockedExtBaseConnector('LinkCheck', 'resetRecord', $parameters);
		$this->fixture->ignoreRecord(7);
	}

	/**
	 * @test
	 * @return void
	 */
	public function observeRecordWorks() {
		$parameters = array(
			'identity' => 7,
			'doIgnoreRecord' => FALSE,
		);

		$this->addMockedExtBaseConnector('LinkCheck', 'resetRecord', $parameters);
		$this->fixture->observeRecord(7);
	}

	/**
	 * @test
	 * @return void
	 */
	public function setAsFalsePositiveWorks() {
		$parameters = array(
			'identity' => 7,
			'isFalsePositive' => TRUE,
		);

		$this->addMockedExtBaseConnector('LinkCheck', 'setFalsePositiveState', $parameters);
		$this->fixture->setAsFalsePositive(7);
	}

	/**
	 * @test
	 * @return void
	 */
	public function resetAsFalsePositiveWorks() {
		$parameters = array(
			'identity' => 7,
			'isFalsePositive' => FALSE,
		);
		
		$this->addMockedExtBaseConnector('LinkCheck', 'setFalsePositiveState', $parameters);
		$this->fixture->resetAsFalsePositive(7);
	}
}

?>