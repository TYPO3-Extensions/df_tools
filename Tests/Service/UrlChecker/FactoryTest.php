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
 * Test case for class Tx_DfTools_Service_UrlChecker_Factory.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_FactoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_UrlChecker_Factory
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_UrlChecker_Factory');
		$this->fixture = $this->getMockBuilder($proxyClass)
			->setMethods(array('dummy'))
			->disableOriginalConstructor()
			->getMock();
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
	public function injectObjectManagerSetsObjectManager() {
		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$objectManager = $this->getMock('Tx_Extbase_Object_ObjectManager', array('dummy'));
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$objectManager = $this->fixture->_get('objectManager');
		$this->assertInstanceOf('Tx_Extbase_Object_ObjectManager', $objectManager);
	}

	/**
	 * @return array
	 */
	public function getReturnsUrlCheckerServiceDataProvider() {
		return array(
			'no type' => array(
				'', 'Tx_DfTools_Service_UrlChecker_StreamService'
			),
			'native type' => array(
				'native', 'Tx_DfTools_Service_UrlChecker_StreamService'
			),
			'curl type' => array(
				'curl', 'Tx_DfTools_Service_UrlChecker_CurlService'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getReturnsUrlCheckerServiceDataProvider
	 *
	 * @param string $type
	 * @param string $expectedClass
	 * @return void
	 */
	public function getReturnsUrlCheckerService($type, $expectedClass) {
		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$objectManager = $this->getMock('Tx_Extbase_Object_ObjectManager', array('get'));
		$this->fixture->injectObjectManager($objectManager);

		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_UrlChecker_AbstractService');
		$service = $this->getMock($proxyClass, array('init', 'resolveUrl'));

		/** @noinspection PhpUndefinedMethodInspection */
		$objectManager->expects($this->once())->method('get')
			->with($expectedClass)->will($this->returnValue($service));

		$service->expects($this->once())->method('init');

		$this->fixture->get($type);
	}
}

?>