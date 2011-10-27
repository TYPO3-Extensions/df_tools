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
 * Test case for class Tx_DfTools_Controller_AbstractController.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Controller_AbstractControllerTest extends Tx_DfTools_Controller_ControllerTestCase {
	/**
	 * @var Tx_DfTools_Controller_AbstractController
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_Controller_AbstractController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fixture->injectObjectManager($objectManager);
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
	public function constructorSetsExtensionConfiguration() {
		$expected = array(
			'foo' => 'bar',
			'bar' => 'foo',
		);
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['df_tools'] = serialize($expected);

		unset($this->fixture);
		$class = 'Tx_DfTools_Controller_AbstractController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @noinspection PhpUndefinedMethodInspection */
		$extensionConfiguration = $this->fixture->_get('extensionConfiguration');
		$this->assertSame($expected, $extensionConfiguration);
	}

	/**
	 * @expectedException RuntimeException
	 * @test
	 * @return void
	 */
	public function errorActionThrowsRuntimeException() {
		/** @noinspection PhpUndefinedMethodInspection */
		$mockMappingResults = $this->getMock('Tx_Extbase_Property_MappingResults', array('dummy'));
		$this->fixture->_set('argumentsMappingResults', $mockMappingResults);
		
		$arguments = $this->getMock('Tx_Extbase_MVC_Controller_Arguments', array('dummy'));
		$this->fixture->_set('arguments', $arguments);
		$configuratioManager = $this->getMock('Tx_Extbase_Configuration_ConfigurationManager');
		$this->fixture->_set('configurationManager', $configuratioManager);
		
		$this->fixture->errorAction();
	}

	/**
	 *
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
	public function controllerActionPairCanBeSaved() {
		$this->initStateTest();
		$this->assertSame(array('Foo', 'Bar'), $this->fixture->getLastCalledControllerActionPair());
	}

	/**
	 * @test
	 * @return void
	 */
	public function controllerActionPairCanBeResetted() {
		$this->initStateTest();
		$this->fixture->resetLastCalledControllerActionPair();
		$this->assertSame(array(), $this->fixture->getLastCalledControllerActionPair());
	}

	/**
	 * Returns a redirect test instance
	 *
	 * @return Tx_DfTools_Domain_Model_RedirectTest
	 */
	protected function getTestableDomainObject() {
		/** @var $redirectTest Tx_DfTools_Domain_Model_RedirectTest */
		$redirectTest = $this->getMockBuilder('Tx_DfTools_Domain_Model_RedirectTest')
			->setMethods(array('dummy'))
			->disableOriginalClone()->getMock();

		$redirectTest->setExpectedUrl('FooBar');
		$redirectTest->setTestUrl('FooBar');
		$redirectTest->setHttpStatusCode(200);

		return $redirectTest;
	}

	/**
	 * @test
	 * @expectedException RuntimeException
	 * @return void
	 */
	public function handleActionThrowsRuntimeException() {
		$this->addMockedCallToPersistAll();
		$testableObject = $this->getTestableDomainObject();
		$testableObject->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->_call('handleExceptionalTest', $testableObject);
	}

	/**
	 * @test
	 * @return void
	 */
	public function handleActionThrowsNoExceptionButForAnRealError() {
		/** @noinspection PhpUndefinedMethodInspection */
		$testableObject = $this->getTestableDomainObject();
		$testableObject->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_WARNING);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR);
		$this->fixture->_call('handleExceptionalTest', $testableObject);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getUrlCheckerServiceReturnsService() {
		/** @noinspection PhpUndefinedMethodInspection */
		$service = $this->fixture->_call('getUrlCheckerService');
		$this->assertInstanceOf('Tx_DfTools_Service_UrlChecker_AbstractService', $service);
	}
}

?>