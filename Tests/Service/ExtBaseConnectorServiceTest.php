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

/**
 * Test case for class Tx_DfTools_Service_ExtBaseConnectorService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_ExtBaseConnectorServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_ExtBaseConnectorService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_ExtBaseConnectorService');
		$this->fixture = $this->getMockBuilder($proxyClass)
			->setMethods(array('dummy'))
			->disableOriginalConstructor()
			->getMock();

		$this->fixture->setExtensionKey('Foo');
		$this->fixture->setModuleOrPluginKey('tools_FooTools');
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * Returns a fake bootstrap
	 *
	 * @param string $controller
	 * @param string $action
	 * @param mixed $returnValue
	 * @return void
	 */
	protected function injectFakeBootStrap($controller, $action, $returnValue) {
		/** @var $mockBootStrap Tx_Extbase_Core_Bootstrap */
		$mockBootStrap = $this->getMock('Tx_Extbase_Core_Bootstrap', array('run'));

		/** @noinspection PhpUndefinedMethodInspection */
		$mockBootStrap->expects($this->once())
			->method('run')
			->will($this->returnValue($returnValue))
			->with('', array(
				'extensionName' => 'Foo',
				'pluginName' => 'tools_FooTools',
				'switchableControllerActions' => array(
					$controller => array($action)
				)
			)
		);

		$this->fixture->injectBootStrap($mockBootStrap);
	}

	/**
	 * @test
	 * @return void
	 */
	public function objectCanBeInitialized() {
		/** @var $fixture Tx_DfTools_Service_ExtBaseConnectorService */
		$fixture = $this->getAccessibleMock('Tx_DfTools_Service_ExtBaseConnectorService', array('dummy'));

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInstanceOf('Tx_Extbase_Core_Bootstrap', $fixture->_get('bootStrap'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectBootStrap() {
		/** @var $bootStrap Tx_Extbase_Core_Bootstrap */
		$bootStrap = $this->getMock('Tx_Extbase_Core_Bootstrap', array('dummy'));
		$this->fixture->injectBootStrap($bootStrap);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($bootStrap, $this->fixture->_get('bootStrap'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function setExtensionKeyWorks() {
		$this->fixture->setExtensionKey('FooBar');
		$this->assertSame('FooBar', $this->fixture->getExtensionKey());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setModuleOrPluginKeyWorks() {
		$this->fixture->setModuleOrPluginKey('FooBar');
		$this->assertSame('FooBar', $this->fixture->getModuleOrPluginKey());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testExecutionOfControllerAndAction() {
		$controller = 'TestController';
		$action = 'TestAction';
		$returnValue = array('foo', 'bar');

		$this->injectFakeBootStrap($controller, $action, $returnValue);
		$response = $this->fixture->runControllerAction($controller, $action);

		$this->assertSame($response, $returnValue);
	}

	/**
	 * @test
	 * @return void
	 */
	public function testExecutionOfControllerAndActionWithNonArrayResponse() {
		$controller = 'TestController';
		$action = 'TestAction';
		$returnValue = TRUE;

		$this->injectFakeBootStrap($controller, $action, $returnValue);
		$response = $this->fixture->runControllerAction($controller, $action);

		$this->assertSame($response, $returnValue);
	}

	/**
	 * @return array
	 */
	public function testExecutionOfControllerAndActionWithIncorrectParametersDataProvider() {
		return array(
			array('Foo', ''),
			array('', 'Bar'),
			array('', ''),
			array('', NULL),
		);
	}

	/**
	 * @dataProvider testExecutionOfControllerAndActionWithIncorrectParametersDataProvider
	 * @expectedException InvalidArgumentException
	 * @test
	 *
	 * @param string $controller
	 * @param string $action
	 * @return void
	 */
	public function testExecutionOfControllerAndActionWithIncorrectParameters($controller, $action) {
		/** @var $mockBootStrap Tx_Extbase_Core_Bootstrap */
		$mockBootStrap = $this->getMock('Tx_Extbase_Core_Bootstrap', array('run'));
		$this->fixture->injectBootStrap($mockBootStrap);

		$this->fixture->runControllerAction($controller, $action);
	}

	/**
	 * @test
	 * @return void
	 */
	public function parametersCanBeSet() {
		$parameters = array('foo' => 'bar', 'my' => 'cat');
		$this->fixture->setParameters($parameters);
		$this->assertSame($_POST['tx_foo_tools_footools'], $parameters);
	}

	/**
	 * @test
	 * @return void
	 */
	public function multipleSetParametersCallUnsetThePostInformation() {
		$parameters = array('foo' => 'bar');
		$parameters2 = array('my' => 'cat');
		$this->fixture->setParameters($parameters);
		$this->fixture->setParameters($parameters2);
		$this->assertSame($_POST['tx_foo_tools_footools'], $parameters2);
	}
}

?>