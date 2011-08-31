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
 * Test case for class Tx_DfTools_View_AbstractArrayView.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_View_AbstractArrayViewTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_View_AbstractArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('Tx_DfTools_View_AbstractArrayView');
		$this->fixture = $this->getMockBuilder($class)
			->setMethods(array('getPlainRecord', 'getHmacFieldConfiguration', 'getNamespace'))
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
	public function testInjectRequestHash() {
		/** @var $mock Tx_Extbase_Security_Channel_RequestHashService */
		$mock = $this->getMock('Tx_Extbase_Security_Channel_RequestHashService', array('dummy'));
		$this->fixture->injectRequestHashService($mock);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($mock, $this->fixture->_get('requestHashService'));
	}

	/**
	 * @return array plain record
	 */
	protected function prepareTestRenderProcess() {
		/** @var $requestHashService Tx_Extbase_Security_Channel_RequestHashService */
		$class = 'Tx_Extbase_Security_Channel_RequestHashService';
		$requestHashService = $this->getMock($class, array('generateRequestHash'));

		$plainRecord = array(
			'__identity' => 202,
			'testUrl' => 'FooBar',
			'expectedUrl' => 'FooBar',
			'httpStatusCode' => 404,
			'categoryId' => 5,
		);

		$namespace = 'tx_dftools_tools_dftoolstools';
		$hmacFieldConfiguration = array(
			'update' => array(
				$namespace . '[redirectTest][__identity]',
				$namespace . '[redirectTest][testUrl]',
				$namespace . '[redirectTest][expectedUrl]',
				$namespace . '[redirectTest][httpStatusCode]',
				$namespace . '[redirectTest][category][__identity]',
			),
			'create' => array(
				$namespace . '[newRedirectTest][testUrl]',
				$namespace . '[newRedirectTest][expectedUrl]',
				$namespace . '[newRedirectTest][httpStatusCode]',
			),
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('getPlainRecord')
			->with(array('record1'))
			->will($this->returnValue($plainRecord));

		$this->fixture->expects($this->once())->method('getHmacFieldConfiguration')
			->will($this->returnValue($hmacFieldConfiguration));

		$this->fixture->expects($this->exactly(2))->method('getNamespace')
			->will($this->returnValue($namespace));

		$requestHashService->expects($this->exactly(2))->method('generateRequestHash')
			->with($this->anything(), $namespace)
			->will($this->returnValue('hmac'));
		$this->fixture->injectRequestHashService($requestHashService);

		return $plainRecord;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testRenderProcess() {
		$plainRecord = $this->prepareTestRenderProcess();
		$expectedData = array(
			'__hmac' => array(
				'update' => 'hmac',
				'create' => 'hmac'
			),
			'records' => array($plainRecord),
			'total' => 1
		);

		$this->fixture->assign('records', array(array('record1')));
		$this->assertSame($expectedData, $this->fixture->render());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testRenderProcessWithAssignedTotalRecords() {
		$plainRecord = $this->prepareTestRenderProcess();
		$expectedData = array(
			'__hmac' => array(
				'update' => 'hmac',
				'create' => 'hmac'
			),
			'records' => array($plainRecord),
			'total' => 199
		);

		$this->fixture->assign('records', array(array('record1')));
		$this->fixture->assign('totalRecords', 199);
		$this->assertSame($expectedData, $this->fixture->render());
	}
}

?>