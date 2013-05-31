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
use SGalinski\DfTools\ExtDirect\RedirectTestDataProvider;
use SGalinski\DfTools\Parser\TcaParserService;
use SGalinski\DfTools\Parser\UrlParserService;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Test case for class Tx_DfTools_ExtDirect_RedirectTestDataProvider.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class RedirectTestDataProviderTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\ExtDirect\RedirectTestDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\ExtDirect\RedirectTestDataProvider';
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
		$this->addMockedExtBaseConnector('RedirectTest', 'read', $parameters);
		$this->fixture->read($input);
	}

	/**
	 * @return array
	 */
	public function updateRecordTransformRecordInformationAsCorrectParametersForExtBaseDataProvider() {
		return array(
			'simple update call #1' => array(
				array(
					'__hmac' => 'hmac',
					'redirectTest' => array(
						'__identity' => 1,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
						'httpStatusCode' => 200,
						'category' => array(
							'__identity' => 1
						)
					)
				), array(
					'__hmac' => 'hmac',
					'__identity' => '1',
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
					'httpStatusCode' => 200,
					'categoryId' => 1
				)
			),

			'simple update call #2' => array(
				array(
					'__hmac' => 'hmac',
					'redirectTest' => array(
						'__identity' => 2,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
						'httpStatusCode' => 404,
						'category' => array(
							'__identity' => 2
						)
					)
				), array(
					'__hmac' => 'hmac',
					'__identity' => 2,
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
					'httpStatusCode' => '404',
					'categoryId' => '2'
				)
			),

			'update call with new category' => array(
				array(
					'__hmac' => 'hmac',
					'redirectTest' => array(
						'__identity' => 2,
						'testUrl' => 'fooBar',
						'expectedUrl' => 'fooBar',
						'httpStatusCode' => 500,
					),
					'newCategory' => array(
						'category' => 'fooBar',
					)
				), array(
					'__hmac' => 'hmac',
					'__identity' => 2,
					'testUrl' => 'fooBar',
					'expectedUrl' => 'fooBar',
					'httpStatusCode' => 500,
					'categoryId' => 'fooBar'
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
		$this->addMockedExtBaseConnector('RedirectTest', 'update', $parameters);
		$this->fixture->_call('updateRecord', $record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function createCallsTheExtBaseControllerWithExpectedParameters() {
		$parameters = array(
			'__hmac' => '__hmac',
			'newRedirectTest' => array(
				'testUrl' => 'FooBar',
				'expectedUrl' => 'FooBar',
				'httpStatusCode' => 404,
			)
		);
		$this->addMockedExtBaseConnector('RedirectTest', 'create', $parameters);

		/** @noinspection PhpUndefinedFieldInspection */
		$record = new \stdClass;
		$record->records = new \stdClass;
		$record->records->__hmac = '__hmac';
		$record->records->__identity = 0;
		$record->records->categoryId = 0;
		$record->records->testUrl = 'FooBar';
		$record->records->expectedUrl = 'FooBar';
		$record->records->httpStatusCode = 404;

		$this->fixture->create($record);
	}

	/**
	 * @test
	 * @return void
	 */
	public function destroyRecordsCallsExtBaseController() {
		$parameters = array(
			'identifiers' => array(1, 2, 3),
		);
		$this->addMockedExtBaseConnector('RedirectTest', 'destroy', $parameters);
		$this->fixture->destroyRecords(array(1, 2, 3));
	}

	/**
	 * @test
	 * @return void
	 */
	public function runTestCallsExtBaseController() {
		/** @noinspection PhpUndefinedMethodInspection */
		$parameters = array(
			'identity' => 2,
		);
		$this->addMockedExtBaseConnector('RedirectTest', 'runTest', $parameters);
		$this->fixture->_call('runTestForRecord', 2);
	}
}

?>