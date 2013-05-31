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

use SGalinski\DfTools\Connector\ExtBaseConnectorService;
use SGalinski\DfTools\ExtDirect\RedirectTestCategoryDataProvider;
use SGalinski\DfTools\Parser\TcaParserService;
use SGalinski\DfTools\Parser\UrlParserService;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Test case for class Tx_DfTools_ExtDirect_RedirectTestCategoryDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class RedirectTestCategoryDataProviderTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\ExtDirect\RedirectTestCategoryDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\ExtDirect\RedirectTestCategoryDataProvider';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
		$this->fixture->_set('extBaseConnector', $this->extBaseConnector);
	}

	/**
	 * @test
	 * @return void
	 */
	public function readCategoriesIsCalledWithFilterQuery() {
		/** @noinspection PhpUndefinedFieldInspection */
		$data = new \stdClass();
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
	 *
	 * @param array $parameters
	 * @param array $record
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