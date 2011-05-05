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

$schedulerPath = t3lib_extMgm::extPath('scheduler');
require_once($schedulerPath . 'class.tx_scheduler_task.php');

/**
 * Test case for class Tx_DfTools_Task_RedirectTestTask.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Task_RedirectTestTaskTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Task_RedirectTestTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
			// only solution to test the scheduler stuff, because they include mod1/index.php
			// that is directly executed
		t3lib_autoloader::unregisterAutoloader();

		$proxy = $this->buildAccessibleProxy('Tx_DfTools_Task_RedirectTestTask');
		$this->fixture = $this->getMockBuilder($proxy)
			->setMethods(array('sendMail', 'getExtBaseConnector'))->disableOriginalConstructor()->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		t3lib_autoloader::registerAutoloader();
		unset($this->fixture);
	}

	/**
	 * Adds the call to the mocked extbase connector
	 *
	 * @param string $controller
	 * @param string $action
	 * @param mixed $returnValue
	 * @return void
	 */
	protected function addMockedExtBaseConnector($controller, $action, $returnValue = array()) {
		/** @noinspection PhpUndefinedMethodInspection */
		$class = 'Tx_DfTools_Service_ExtBaseConnectorService';
		$mockExtBaseConnector = $this->getMock($class, array('runControllerAction', 'setParameters'));
		$mockExtBaseConnector->expects($this->once())->method('runControllerAction')
			->with($controller, $action)->will($this->returnValue($returnValue));
		$this->fixture->expects($this->once())->method('getExtBaseConnector')
			->will($this->returnValue($mockExtBaseConnector));
	}

	/**
	 * @test
	 * @return void
	 */
	public function executeCallsTheExtBaseController() {
		$this->addMockedExtBaseConnector('RedirectTest', 'runAllTests', array('records' => array()));
		$this->fixture->execute();
	}
}

?>