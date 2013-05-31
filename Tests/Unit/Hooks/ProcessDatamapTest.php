<?php

namespace SGalinski\DfTools\Tests\Unit\Hooks;

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

use SGalinski\DfTools\Hooks\ProcessDatamap;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class ProcessDatamapTest
 */
class ProcessDatamapTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\Hooks\ProcessDatamap
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'SGalinski\DfTools\Hooks\ProcessDatamap';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));
		$this->fixture->_set('extBaseConnector', $this->extBaseConnector);
	}

	/**
	 * @test
	 * @return void
	 */
	public function processDataMapAfterAllOperationsWorksWithUpdatedElement() {
		/** @var $tceMain DataHandler */
		$tceMain = $this->getMockBuilder('TYPO3\CMS\Core\DataHandling\DataHandler')->disableOriginalConstructor(
		)->getMock();
		$tceMain->datamap = array(
			'tt_content' => array(
				12 => array(),
			),
		);

		$parameters = array('table' => 'tt_content', 'identity' => 12);
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronizeUrlsFromASingleRecord', $parameters);
		$this->fixture->processDatamap_afterAllOperations($tceMain);
	}

	/**
	 * @test
	 * @return void
	 */
	public function processDataMapAfterAllOperationsWorksWithNewElement() {
		/** @var $tceMain DataHandler */
		$tceMain = $this->getMockBuilder('TYPO3\CMS\Core\DataHandling\DataHandler')->disableOriginalConstructor(
		)->getMock();
		$tceMain->substNEWwithIDs = array('NEW123' => 25);
		$tceMain->datamap = array(
			'tt_content' => array(
				'NEW123' => array(),
			),
		);

		$parameters = array('table' => 'tt_content', 'identity' => 25);
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronizeUrlsFromASingleRecord', $parameters);
		$this->fixture->processDatamap_afterAllOperations($tceMain);
	}

	/**
	 * @test
	 * @return void
	 */
	public function processCommandMapPostProcessWorksWithADeleteOperation() {
		$parameters = array('table' => 'tt_content', 'identity' => 12);
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronizeUrlsFromASingleRecord', $parameters);
		$this->fixture->processCmdmap_postProcess('delete', 'tt_content', 12);
	}

	/**
	 * @test
	 * @return void
	 */
	public function processCommandMapPostProcessWorksWithAUndeleteOperation() {
		$parameters = array('table' => 'tt_content', 'identity' => 12);
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronizeUrlsFromASingleRecord', $parameters);
		$this->fixture->processCmdmap_postProcess('undelete', 'tt_content', 12);
	}

	/**
	 * @test
	 * @return void
	 */
	public function processCommandMapPostProcessWorksNotWithAUnknownOperation() {
		$class = 'SGalinski\DfTools\Connector\ExtBaseConnectorService';
		$mockExtBaseConnector = $this->getMock($class, array('runControllerAction'));
		$mockExtBaseConnector->expects($this->never())->method('runControllerAction');

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->processCmdmap_postProcess('foobar', 'tt_content', 12);
	}
}

?>