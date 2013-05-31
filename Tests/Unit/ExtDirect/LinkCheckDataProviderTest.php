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

use SGalinski\DfTools\ExtDirect\LinkCheckDataProvider;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Class LinkCheckDataProviderTest
 */
class LinkCheckDataProviderTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\ExtDirect\LinkCheckDataProvider
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\ExtDirect\LinkCheckDataProvider';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
		$this->fixture->_set('extBaseConnector', $this->extBaseConnector);
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
		$exception = new \Exception('FooBar');
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
	 * @param \stdClass $givenParameters
	 * @param array $parameters
	 * @return void
	 */
	public function readHandlesParameters(\stdClass $givenParameters, array $parameters) {
		$this->addMockedExtBaseConnector('LinkCheck', 'read', $parameters);
		$this->fixture->read($givenParameters);
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