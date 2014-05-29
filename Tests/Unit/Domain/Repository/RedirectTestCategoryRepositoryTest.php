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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class RedirectTestCategoryRepositoryTest
 */
class RedirectTestCategoryRepositoryTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository',
			array('createQuery', 'findByCategory', 'getPageSelectInstance'),
			array($this->objectManager), '', FALSE
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
	public function categoriesBeginningWithAWordAreReturned() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->getMockBuilder('TYPO3\CMS\Extbase\Persistence\Generic\Query')
			->setMethods(array('like', 'execute'))
			->disableOriginalConstructor()
			->getMock();

		$mockQuery->expects($this->once())->method('execute')->will($this->returnValue('FooBar'));
		$mockQuery->expects($this->once())->method('like')->with('category', 'FooBar%');

		$this->fixture->expects($this->once())->method('createQuery')
			->will($this->returnValue($mockQuery));

		$this->assertSame('FooBar', $this->fixture->findByStartingCategory('FooBar'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findUnusedCategoriesCallsADefinedSqlQuery() {
		$sqlQuery =
			'SELECT tx_dftools_domain_model_redirecttestcategory.* ' .
			'FROM tx_dftools_domain_model_redirecttestcategory ' .
			'LEFT JOIN tx_dftools_domain_model_redirecttest ' .
			'ON tx_dftools_domain_model_redirecttest.category = ' .
			'tx_dftools_domain_model_redirecttestcategory.uid AND FooBar ' .
			'WHERE tx_dftools_domain_model_redirecttest.uid IS NULL AND FooBar ';

		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->getMockBuilder('TYPO3\CMS\Extbase\Persistence\Generic\Query')
			->setMethods(array('statement', 'execute'))
			->disableOriginalConstructor()
			->getMock();

		$mockQuery->expects($this->once())->method('execute')->will($this->returnValue('FooBar'));
		$mockQuery->expects($this->once())->method('statement')->with($sqlQuery);

		$mockPageSelect = $this->getMock('t3lib_pageSelect', array('enableFields'));
		$mockPageSelect->expects($this->exactly(2))->method('enableFields')->will($this->returnValue('AND FooBar '));

		$this->fixture->expects($this->once())->method('createQuery')->will($this->returnValue($mockQuery));
		$this->fixture->expects($this->once())->method('getPageSelectInstance')
			->will($this->returnValue($mockPageSelect));

		$this->assertSame('FooBar', $this->fixture->findAllUnusedCategories());
	}

	/**
	 * @expectedException \SGalinski\DfTools\Exception\GenericException
	 * @test
	 * @return void
	 */
	public function uniqueCategoryNameCheckThrowsExceptionIfTheGivenCategoryAlreadyExists() {
		$objectCollection = new ObjectStorage();
		$objectCollection->attach(new \stdClass(''));

		$this->fixture->expects($this->once())->method('findByCategory')
			->will($this->returnValue($objectCollection));

		$this->fixture->_call('checkIfCategoryNameIsAlreadyAssigned', 'FooBar');
	}

	/**
	 * @test
	 * @return void
	 */
	public function uniqueCategoryNameCheckReturnsTrueIfTheGivenCategoryNameDoesNotExists() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('findByCategory')->will($this->returnValue(NULL));
		$this->fixture->_call('checkIfCategoryNameIsAlreadyAssigned', 'FooBar');
	}
}

?>