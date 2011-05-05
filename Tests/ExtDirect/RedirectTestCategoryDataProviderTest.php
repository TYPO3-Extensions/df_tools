<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <sgalinski@df.eu>, domainfactory GmbH
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
 * Test case for class Tx_DfTools_ExtDirect_RedirectTestCategoryDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ExtBaseConnector_RedirectTestCategoryDataProviderTest extends Tx_DfTools_Tests_ExtBaseConnectorTestCase {
	/**
	 * @var Tx_DfTools_ExtDirect_RedirectTestCategoryDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_ExtDirect_RedirectTestCategoryDataProvider';
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
	public function readCategoriesIsCalledWithFilterQuery() {
		/** @noinspection PhpUndefinedFieldInspection */
		$data = new stdClass();
		$data->query = 'FooBar';

		$parameters = array('filterString' => 'FooBar');
		$this->addMockedExtBaseConnector('RedirectTestCategory', 'read', $parameters);

		$this->fixture->read($data);
	}

	/**
	 * @return array
	 */
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider() {
		return array(
			'simple update call #1' => array(
				array(
					'__hmac' => 'hmac',
					'redirectTestCategory' => array(
						 '__identity' => 1,
						 'category' => 'fooBar',
					 )
				), array(
					'__hmac' => 'hmac',
					'__identity' => '1',
					'category' => 'fooBar',
				)
			)
		);
	}

	/**
	 * @dataProvider updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider
	 * @test
	 * @return void
	 */
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBase($parameters, $record) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->addMockedExtBaseConnector('RedirectTestCategory', 'update', $parameters);
		$this->fixture->_call('updateRecord', $record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function deleteUnusedCategoriesReturnsValidResponse() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->addMockedExtBaseConnector('RedirectTestCategory', 'deleteUnusedCategories');
		$this->fixture->deleteUnusedCategories();
	}
}

?>