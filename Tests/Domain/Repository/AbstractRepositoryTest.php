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
 * Test case for class Tx_DfTools_Domain_Repository_AbstractRepository.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Repository_AbstractRepositoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Domain_Repository_AbstractRepository
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'Tx_DfTools_Domain_Repository_AbstractRepository',
			array('createQuery')
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
	public function getPageSelectInstanceReturnsAt3libPageSelectInstance() {
		$this->assertInstanceOf('t3lib_pageSelect', $this->fixture->getPageSelectInstance());
	}

	/**
	 * @return Tx_Extbase_Persistence_Query
	 */
	protected function prepareFindSortedInRangeTests() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->getMockBuilder('Tx_Extbase_Persistence_Query')
			->setMethods(array('execute', 'setOrderings'))
			->disableOriginalConstructor()
			->getMock();
		$this->fixture->expects($this->once())->method('createQuery')->will($this->returnValue($mockQuery));
		return $mockQuery;
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithNonIntegerRangeThrowsNoException() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings');
		$this->fixture->findSortedAndInRange('10', '20', array('field1' => TRUE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithEmptySortingInformation() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->never())->method('setOrderings');
		$this->fixture->findSortedAndInRange(0, 200, array());
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithValidValues() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(array('field1' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING));
		$this->fixture->findSortedAndInRange(0, 200, array('field1' => TRUE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithDescendingSortingDirection() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(array('field1' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING));
		$this->fixture->findSortedAndInRange(0, 200, array('field1' => FALSE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithMultipleSorters() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(array(
				'field1' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING,
				'field2' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
			));
		$this->fixture->findSortedAndInRange('10', '20', array('field1' => TRUE, 'field2' => FALSE));
	}
}

?>