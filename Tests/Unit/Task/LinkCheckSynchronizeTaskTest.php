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
 * Test case for class Tx_DfTools_Task_LinkCheckSynchronizeTask.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Task_LinkCheckSynchronizeTaskTest extends Tx_DfTools_ExtBaseConnectorTestCase {
	/**
	 * @var Tx_DfTools_Task_LinkCheckSynchronizeTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->fixture = $this->getMockBuilder($this->buildAccessibleProxy('Tx_DfTools_Task_LinkCheckSynchronizeTask'))
			->setMethods(array('getExtBaseConnector'))->disableOriginalConstructor()->getMock();
		$this->fixture->expects($this->once())->method('getExtBaseConnector')
			->will($this->returnValue($this->extBaseConnector));
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);

		parent::tearDown();
	}

	/**
	 * @test
	 * @return void
	 */
	public function executeCallsTheExtBaseController() {
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronize');
		$this->fixture->execute();
	}
}

?>