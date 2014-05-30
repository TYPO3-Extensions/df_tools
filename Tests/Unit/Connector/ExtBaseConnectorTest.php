<?php

namespace SGalinski\DfTools\Tests\Unit\Connector;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan.galinski@gmail.com>
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

use SGalinski\DfTools\Connector\ExtBaseConnector;
use SGalinski\DfTools\Tests\Unit\Controller\ControllerTestCase;
use TYPO3\CMS\Extbase\Core\Bootstrap;

/**
 * Class ExtBaseConnectorTest
 */
class ExtBaseConnectorTest extends ControllerTestCase {
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\SGalinski\DfTools\Connector\ExtBaseConnector
	 */
	protected $fixture;

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $backupTSFE;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->backupTSFE = $GLOBALS['TSFE'];
		$GLOBALS['TSFE'] = $this->getMock(
			'TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', [], [], '', FALSE
		);

		/** @var $fixture ExtBaseConnector */
		$proxyClass = $this->buildAccessibleProxy('SGalinski\DfTools\Connector\ExtBaseConnector');
		$fixture = $this->getMockBuilder($proxyClass)->setMethods(array('dummy'))
			->disableOriginalConstructor()->getMock();

		$fixture->setExtensionKey('Foo');
		$fixture->setModuleOrPluginKey('tools_FooTools');
		$this->fixture = $fixture;
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TSFE'] = $this->backupTSFE;
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
		/** @var $mockBootStrap \PHPUnit_Framework_MockObject_MockObject|Bootstrap */
		$mockBootStrap = $this->getMock('TYPO3\CMS\Extbase\Core\Bootstrap', array('run'));

		/** @noinspection PhpUndefinedMethodInspection */
		$mockBootStrap->expects($this->once())
			->method('run')
			->will($this->returnValue($returnValue))
			->with(
				'', array(
					'extensionName' => 'Foo',
					'pluginName' => 'tools_FooTools',
					'vendorName' => 'SGalinski',
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
		/** @var $fixture ExtBaseConnector */
		$fixture = $this->getAccessibleMock('SGalinski\DfTools\Connector\ExtBaseConnector', array('dummy'));

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInstanceOf('TYPO3\CMS\Extbase\Core\Bootstrap', $fixture->_get('bootStrap'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectBootStrap() {
		/** @var $bootStrap Bootstrap */
		$bootStrap = $this->getMock('TYPO3\CMS\Extbase\Core\Bootstrap', array('dummy'));
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
	 * @expectedException \InvalidArgumentException
	 * @test
	 *
	 * @param string $controller
	 * @param string $action
	 * @return void
	 */
	public function testExecutionOfControllerAndActionWithIncorrectParameters($controller, $action) {
		/** @var $mockBootStrap Bootstrap */
		$mockBootStrap = $this->getMock('TYPO3\CMS\Extbase\Core\Bootstrap');
		$this->fixture->injectBootStrap($mockBootStrap);
		$this->fixture->runControllerAction($controller, $action);
	}

	/**
	 * @test
	 * @return void
	 */
	public function parametersCanBeSet() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->_set('objectManager', $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('dummy')));
		$parameters = array('foo' => 'bar', 'my' => 'cat');
		unset($GLOBALS['TSFE']);
		$this->fixture->setParameters($parameters);
		$this->assertSame($_POST['tx_foo_tools_footools'], $parameters);
	}

	/**
	 * @test
	 * @return void
	 */
	public function multipleSetParametersCallUnsetThePostInformation() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->_set('objectManager', $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('dummy')));
		$parameters = array('foo' => 'bar');
		$parameters2 = array('my' => 'cat');
		unset($GLOBALS['TSFE']);
		$this->fixture->setParameters($parameters);
		$this->fixture->setParameters($parameters2);
		$this->assertSame($_POST['tx_foo_tools_footools'], $parameters2);
	}
}

?>