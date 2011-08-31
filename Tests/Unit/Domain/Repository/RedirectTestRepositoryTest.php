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
 * Test case for class Tx_DfTools_Domain_Repository_RedirectTestRepository.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Repository_RedirectTestRepositoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Domain_Repository_RedirectTestRepository
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'Tx_DfTools_Domain_Repository_RedirectTestRepository',
			array('createQuery', 'getPageSelectInstance'),
			array($this->objectManager)
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
	public function dataMapperCanBeInjected() {
		/** @var $dataMapper Tx_Extbase_Persistence_Mapper_DataMapper */
		$dataMapper = $this->getMock('Tx_Extbase_Persistence_Mapper_DataMapper', array('dummy'));
		$this->fixture->injectDataMapper($dataMapper);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($dataMapper, $this->fixture->_get('dataMapper'));
	}

	/**
	 * @return Tx_Extbase_Persistence_Query
	 */
	protected function prepareFindSortedInRangeTests() {
		/** @var $pageSelectInstance t3lib_pageSelect */
		$pageSelectInstance = $this->getMock('t3lib_pageSelect', array('enableFields'));

		/** @var $dataMapper Tx_Extbase_Persistence_Mapper_DataMapper */
		$class = 'Tx_Extbase_Persistence_Mapper_DataMapper';
		$dataMapper = $this->getMock($class, array('convertPropertyNameToColumnName'));

		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->getMockBuilder('Tx_Extbase_Persistence_Query')
			->setMethods(array('execute', 'statement'))
			->disableOriginalConstructor()
			->getMock();
		$mockQuery->expects($this->once())->method('execute');
		$this->fixture->expects($this->once())->method('createQuery')->will($this->returnValue($mockQuery));

		$pageSelectInstance->expects($this->exactly(2))->method('enableFields')
			->with($this->isType('string'))->will($this->returnValue(' AND foo = bar'));
		$this->fixture->expects($this->once())->method('getPageSelectInstance')
			->will($this->returnValue($pageSelectInstance));

		$dataMapper->expects($this->any())->method('convertPropertyNameToColumnName')
			->with($this->isType('string'), 'Tx_DfTools_Domain_Model_RedirectTest')
			->will($this->returnValue('field'));
		$this->fixture->injectDataMapper($dataMapper);

		return $mockQuery;
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeByCategoryWithSingleSorter() {
		$statement = 'SELECT tx_dftools_domain_model_redirecttest.* ' .
			'FROM tx_dftools_domain_model_redirecttest ' .
				'LEFT JOIN tx_dftools_domain_model_redirecttestcategory ' .
					'ON tx_dftools_domain_model_redirecttest.category = ' .
						'tx_dftools_domain_model_redirecttestcategory.uid AND foo = bar ' .
			'WHERE 1=1 AND foo = bar ' .
			'ORDER BY tx_dftools_domain_model_redirecttestcategory.category ASC, field ASC ' .
			'LIMIT 10, 20';

		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('statement')->with($statement);
		$this->fixture->findSortedAndInRangeByCategory(10, 20, array('field1' => TRUE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeByCategoryWithMultipleSorters() {
		$statement = 'SELECT tx_dftools_domain_model_redirecttest.* ' .
			'FROM tx_dftools_domain_model_redirecttest ' .
				'LEFT JOIN tx_dftools_domain_model_redirecttestcategory ' .
					'ON tx_dftools_domain_model_redirecttest.category = ' .
						'tx_dftools_domain_model_redirecttestcategory.uid AND foo = bar ' .
			'WHERE 1=1 AND foo = bar ' .
			'ORDER BY tx_dftools_domain_model_redirecttestcategory.category ASC, field ASC, field DESC ' .
			'LIMIT 10, 20';

		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('statement')->with($statement);
		$this->fixture->findSortedAndInRangeByCategory(10, 20, array('field1' => TRUE, 'field2' => FALSE));
	}

	/**
	 * @test
	 * @return void
	 */
	public function findSortedInRangeByCategoryWithCategoryIdSorterAndAnother() {
		$statement = 'SELECT tx_dftools_domain_model_redirecttest.* ' .
			'FROM tx_dftools_domain_model_redirecttest ' .
				'LEFT JOIN tx_dftools_domain_model_redirecttestcategory ' .
					'ON tx_dftools_domain_model_redirecttest.category = ' .
						'tx_dftools_domain_model_redirecttestcategory.uid AND foo = bar ' .
			'WHERE 1=1 AND foo = bar ' .
			'ORDER BY tx_dftools_domain_model_redirecttestcategory.category DESC, field ASC ' .
			'LIMIT 10, 20';

		/** @noinspection PhpUndefinedMethodInspection */
		$mockQuery = $this->prepareFindSortedInRangeTests();
		$mockQuery->expects($this->once())->method('statement')->with($statement);
		$this->fixture->findSortedAndInRangeByCategory(10, 20, array('categoryId' => FALSE, 'field' => TRUE));
	}
}

?>