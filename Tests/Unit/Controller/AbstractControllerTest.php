<?php

namespace SGalinski\DfTools\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

use SGalinski\DfTools\Controller\AbstractController;
use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\UrlChecker\AbstractService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class AbstractControllerTest
 */
class AbstractControllerTest extends ControllerTestCase {
	/**
	 * @var \SGalinski\DfTools\Controller\AbstractController|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\Controller\AbstractController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @var $objectManager ObjectManager */
		$objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->fixture->_set('objectManager', $objectManager);
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
		$class = 'SGalinski\DfTools\Controller\AbstractController';
		$this->fixture = $this->getAccessibleMock($class, array('dummy'));

		/** @noinspection PhpUndefinedMethodInspection */
		$extensionConfiguration = $this->fixture->_get('extensionConfiguration');
		$this->assertSame($expected, $extensionConfiguration);
	}

	/**
	 * @expectedException \RuntimeException
	 * @test
	 * @return void
	 */
	public function errorActionThrowsRuntimeException() {
		$mockMappingResults = $this->getMock('TYPO3\CMS\Extbase\Property\MappingResults', array('dummy'));
		$this->fixture->_set('argumentsMappingResults', $mockMappingResults);

		$arguments = $this->getMock('TYPO3\CMS\Extbase\Mvc\Controller\Arguments', array('dummy'));
		$this->fixture->_set('arguments', $arguments);
		$configuratioManager = $this->getMock('TYPO3\CMS\Extbase\Configuration\ConfigurationManager');
		$this->fixture->_set('configurationManager', $configuratioManager);

		$this->fixture->errorAction();
	}

	/**
	 *
	 * @return void
	 */
	protected function initStateTest() {
		/** @noinspection PhpUndefinedMethodInspection */
		$request = $this->getMock(
			'TYPO3\CMS\Extbase\Mvc\Request', array('getControllerActionName', 'getControllerName')
		);
		$request->expects($this->once())->method('getControllerActionName')
			->will($this->returnValue('Foo'));
		$request->expects($this->once())->method('getControllerName')
			->will($this->returnValue('Bar'));

		$GLOBALS['BE_USER'] = new BackendUserAuthentication();
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
	 * @return RedirectTest
	 */
	protected function getTestableDomainObject() {
		/** @var $redirectTest RedirectTest */
		$redirectTest = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RedirectTest')
			->setMethods(array('dummy'))
			->disableOriginalClone()->getMock();

		$redirectTest->setExpectedUrl('FooBar');
		$redirectTest->setTestUrl('FooBar');
		$redirectTest->setHttpStatusCode(200);

		return $redirectTest;
	}

	/**
	 * @test
	 * @expectedException \RuntimeException
	 * @return void
	 */
	public function handleActionThrowsRuntimeException() {
		$this->addMockedCallToPersistAll();
		$testableObject = $this->getTestableDomainObject();
		$testableObject->setTestResult(AbstractService::SEVERITY_EXCEPTION);

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
		$testableObject->setTestResult(AbstractService::SEVERITY_OK);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(AbstractService::SEVERITY_INFO);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(AbstractService::SEVERITY_WARNING);
		$this->fixture->_call('handleExceptionalTest', $testableObject);

		$testableObject->setTestResult(AbstractService::SEVERITY_ERROR);
		$this->fixture->_call('handleExceptionalTest', $testableObject);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getUrlCheckerServiceReturnsService() {
		/** @noinspection PhpUndefinedMethodInspection */
		$service = $this->fixture->_call('getUrlCheckerService');
		$this->assertInstanceOf('SGalinski\DfTools\UrlChecker\AbstractService', $service);
	}
}

?>