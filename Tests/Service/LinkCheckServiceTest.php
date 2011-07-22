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
 * Test case for class Tx_DfTools_Service_LinkCheckService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_LinkCheckServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_LinkCheckService
	 */
	protected $fixture;

	/**
	 * @var Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected $testRepository;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$proxy = $this->buildAccessibleProxy('Tx_DfTools_Service_LinkCheckService');
		$this->fixture = $this->getMock($proxy, array('dummy'));

		/** @var $repository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$this->testRepository = $this->getMock('Tx_DfTools_Domain_Repository_LinkCheckRepository', array('dummy'));
		$this->fixture->injectLinkCheckRepository($this->testRepository);

		/** @var $repository Tx_Extbase_Object_ObjectManager */
		$this->objectManager = $this->getMock('Tx_Extbase_Object_ObjectManager', array('create'));
		$this->fixture->injectObjectManager($this->objectManager);
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
	public function testInjectLinkCheckRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$class = 'Tx_DfTools_Domain_Repository_LinkCheckRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectLinkCheckRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('linkCheckRepository'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function testInjectObjectManager() {
		/** @var $repository Tx_Extbase_Object_ObjectManager */
		$class = 'Tx_Extbase_Object_ObjectManager';
		$objectManager = $this->getMock($class, array('dummy'));
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($objectManager, $this->fixture->_get('objectManager'));
	}

	// @todo missing unit tests!
}

?>