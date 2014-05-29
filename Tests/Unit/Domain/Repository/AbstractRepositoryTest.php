<?php

namespace SGalinski\DfTools\Tests\Unit\Domain\Repository;

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
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class AbstractRepositoryTest
 */
class AbstractRepositoryTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Repository\AbstractRepository|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'SGalinski\DfTools\Domain\Repository\AbstractRepository',
			array('createQuery'), array($this->objectManager), '', FALSE
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
		$this->assertInstanceOf('TYPO3\CMS\Frontend\Page\PageRepository', $this->fixture->getPageSelectInstance());
	}

	/**
	 * @return Query|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function prepareFindSortedInRangeTests() {
		$mockQuery = $this->getMockBuilder('TYPO3\CMS\Extbase\Persistence\Generic\Query')
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
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings');
		$this->fixture->findSortedAndInRange('10', '20', array('field1' => TRUE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithEmptySortingInformation() {
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->never())->method('setOrderings');
		$this->fixture->findSortedAndInRange(0, 200, array());
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithValidValues() {
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(array('field1' => QueryInterface::ORDER_ASCENDING));
		$this->fixture->findSortedAndInRange(0, 200, array('field1' => TRUE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithDescendingSortingDirection() {
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(array('field1' => QueryInterface::ORDER_DESCENDING));
		$this->fixture->findSortedAndInRange(0, 200, array('field1' => FALSE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeWithMultipleSorters() {
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('setOrderings')
			->with(
				array(
					'field1' => QueryInterface::ORDER_ASCENDING,
					'field2' => QueryInterface::ORDER_DESCENDING
				)
			);
		$this->fixture->findSortedAndInRange('10', '20', array('field1' => TRUE, 'field2' => FALSE));
	}
}

?>