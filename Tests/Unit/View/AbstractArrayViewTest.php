<?php

namespace SGalinski\DfTools\Tests\Unit\View;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinsk@gmail.com>)
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

use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\View\AbstractArrayView;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Security\Channel\RequestHashService;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class AbstractArrayViewTest
 */
class AbstractArrayViewTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\View\AbstractArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('SGalinski\DfTools\View\AbstractArrayView');
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
		/** @var $mock RequestHashService */
		$mock = $this->getMock('TYPO3\CMS\Extbase\Security\Channel\RequestHashService', array('dummy'));
		$this->fixture->injectRequestHashService($mock);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($mock, $this->fixture->_get('requestHashService'));
	}

	/**
	 * @return array plain record
	 */
	protected function prepareTestRenderProcess() {
		/** @var $requestHashService RequestHashService */
		$class = 'TYPO3\CMS\Extbase\Security\Channel\RequestHashService';
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