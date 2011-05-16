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
 * Special Test Case For ExtDirect
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Tests_ExtBaseConnectorTestCase extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * Adds the call to the mocked extbase connector
	 *
	 * @param string $controller
	 * @param string $action
	 * @param array $parameters
	 * @param mixed $returnValue
	 * @param Exception $exception optional
	 * @return void
	 */
	protected function addMockedExtBaseConnector($controller, $action, array $parameters = array(), $returnValue = NULL, $exception = NULL) {
		$mockExtBaseConnector = $this->getMock(
			'Tx_DfTools_Service_ExtBaseConnectorService',
			array('runControllerAction', 'setParameters')
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$mockedMethod = $mockExtBaseConnector->expects($this->once())->method('runControllerAction');
		$mockedMethod->with($controller, $action);

		if ($returnValue !== NULL) {
			/** @noinspection PhpUndefinedMethodInspection */
			$mockedMethod->will($this->returnValue($returnValue));
		} elseif ($exception !== NULL) {
			/** @noinspection PhpUndefinedMethodInspection */
			$mockedMethod->will($this->throwException($exception));
		}

		if (count($parameters)) {
			/** @noinspection PhpUndefinedMethodInspection */
			$mockExtBaseConnector->expects($this->once())->method('setParameters')->with($parameters);
		}

		$this->fixture->_set('extBaseConnector', $mockExtBaseConnector);
	}
}

?>