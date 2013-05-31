<?php

namespace SGalinski\DfTools\Tests\Unit\Service\UrlChecker;

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

use SGalinski\DfTools\Domain\Model\BackLinkTest;
use SGalinski\DfTools\Domain\Repository\AbstractRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestRepository;
use SGalinski\DfTools\Exception\GenericException;
use SGalinski\DfTools\Service\UrlChecker\AbstractService;
use SGalinski\DfTools\Service\UrlChecker\CurlService;
use SGalinski\DfTools\Service\UrlChecker\Factory;
use SGalinski\DfTools\Utility\HtmlUtility;
use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\Utility\LocalizationUtility;
use SGalinski\DfTools\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class FactoryTest
 */
class FactoryTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\Service\UrlChecker\Factory
	 */
	protected $fixture;

	/**
	 * @var boolean
	 */
	protected $backupCurlUse;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->backupCurlUse = $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'];

		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('SGalinski\DfTools\Service\UrlChecker\Factory');
		$this->fixture = $this->getMockBuilder($proxyClass)
			->setMethods(array('dummy'))
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = $this->backupCurlUse;
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function injectObjectManagerSetsObjectManager() {
		/** @var $objectManager ObjectManager */
		$objectManager = $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('dummy'));
		$this->fixture->injectObjectManager($objectManager);

		/** @noinspection PhpUndefinedMethodInspection */
		$objectManager = $this->fixture->_get('objectManager');
		$this->assertInstanceOf('TYPO3\CMS\Extbase\Object\ObjectManager', $objectManager);
	}

	/**
	 * @return array
	 */
	public function getReturnsUrlCheckerServiceDataProvider() {
		return array(
			'no type' => array(
				'', 'SGalinski\DfTools\Service\UrlChecker\StreamService'
			),
			'native type' => array(
				FALSE, 'SGalinski\DfTools\Service\UrlChecker\StreamService'
			),
			'curl type' => array(
				TRUE, 'SGalinski\DfTools\Service\UrlChecker\CurlService'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getReturnsUrlCheckerServiceDataProvider
	 *
	 * @param boolean $type
	 * @param string $expectedClass
	 * @return void
	 */
	public function getReturnsUrlCheckerService($type, $expectedClass) {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] = $type;

		/** @var $objectManager ObjectManager */
		$objectManager = $this->getMock('TYPO3\CMS\Extbase\Object\ObjectManager', array('get'));
		$this->fixture->injectObjectManager($objectManager);

		$proxyClass = $this->buildAccessibleProxy('SGalinski\DfTools\Service\UrlChecker\AbstractService');
		$service = $this->getMock($proxyClass, array('init', 'resolveUrl'));

		/** @noinspection PhpUndefinedMethodInspection */
		$objectManager->expects($this->once())->method('get')
			->with($expectedClass)->will($this->returnValue($service));

		$service->expects($this->once())->method('init');

		$this->fixture->get($type);
	}
}

?>