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
 * Test case for class tx_DfTools_Hooks_ProcessDatamap.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Hooks_ProcessDatamapTest extends Tx_DfTools_Tests_ExtBaseConnectorTestCase {
	/**
	 * @var tx_DfTools_Hooks_ProcessDatamap
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'tx_DfTools_Hooks_ProcessDatamap';
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
	public function processDataMapAfterAllOperationsWorksWithUpdatedElement() {
		/** @var $tceMain t3lib_TCEmain */
		$tceMain = $this->getMockBuilder('t3lib_TCEmain')->disableOriginalConstructor()->getMock();
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
		/** @var $tceMain t3lib_TCEmain */
		$tceMain = $this->getMockBuilder('t3lib_TCEmain')->disableOriginalConstructor()->getMock();
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
		$class = 'Tx_DfTools_Service_ExtBaseConnectorService';
		$mockExtBaseConnector = $this->getMock($class, array('runControllerAction'));
		$mockExtBaseConnector->expects($this->never())->method('runControllerAction');

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->processCmdmap_postProcess('foobar', 'tt_content', 12);
	}
}

?>