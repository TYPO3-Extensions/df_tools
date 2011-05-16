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
 * Test case for class Tx_DfTools_Controller_OverviewController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_OverviewControllerTest extends Tx_DfTools_Tests_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_OverviewController
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_OverviewController';
		$this->fixture = $this->getAccessibleMock($class, array('redirect'));

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->_set('view', NULL);
	}

	/**
	 * @return void
	 */
	protected function initStateTest() {
		/** @noinspection PhpUndefinedMethodInspection */
		$request = $this->getMock('Tx_Extbase_MVC_Request', array('getControllerActionName', 'getControllerName'));
		$request->expects($this->once())->method('getControllerActionName')
			->will($this->returnValue('Foo'));
		$request->expects($this->once())->method('getControllerName')
			->will($this->returnValue('Bar'));

		$GLOBALS['BE_USER'] = new t3lib_beUserAuth();
		$this->fixture->_set('request', $request);
		$this->fixture->setLastCalledControllerActionPair();
	}

	/**
	 * @test
	 * @return void
	 */
	public function indexActionRedirectsIfAllowedAndStateIsValid() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->initStateTest();
		$this->fixture->expects($this->once())->method('redirect')->with('Foo', 'Bar');
		$this->fixture->indexAction();
	}

	/**
	 * @test
	 * @return void
	 */
	public function indexActionRedirectsIfNotAllowed() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->initStateTest();
		$this->fixture->expects($this->never())->method('redirect');
		$this->fixture->indexAction(FALSE);
	}

	/**
	 * @test
	 * @return void
	 */
	public function indexActionRedirectsIfAllowedAndStateIsInvalid() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->initStateTest();
		$this->fixture->resetLastCalledControllerActionPair();
		$this->fixture->expects($this->never())->method('redirect');
		$this->fixture->indexAction();
	}
}

?>