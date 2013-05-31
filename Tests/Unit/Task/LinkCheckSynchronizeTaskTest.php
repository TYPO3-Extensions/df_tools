<?php

namespace SGalinski\DfTools\Tests\Unit\Task;

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
use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Model\RecordSet;
use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use SGalinski\DfTools\Domain\Repository\AbstractRepository;
use SGalinski\DfTools\Domain\Repository\LinkCheckRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestRepository;
use SGalinski\DfTools\Exception\GenericException;
use SGalinski\DfTools\Hooks\ProcessDatamap;
use SGalinski\DfTools\Service\ExtBaseConnectorService;
use SGalinski\DfTools\Service\LinkCheckService;
use SGalinski\DfTools\Service\RealUrlImportService;
use SGalinski\DfTools\Service\TcaParserService;
use SGalinski\DfTools\Service\UrlChecker\AbstractService;
use SGalinski\DfTools\Service\UrlChecker\CurlService;
use SGalinski\DfTools\Service\UrlChecker\Factory;
use SGalinski\DfTools\Service\UrlParserService;
use SGalinski\DfTools\Task\AbstractFields;
use SGalinski\DfTools\Task\AbstractTask;
use SGalinski\DfTools\Task\LinkCheckSynchronizeTask;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HtmlUtility;
use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\Utility\LocalizationUtility;
use SGalinski\DfTools\Utility\TcaUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Class LinkCheckSynchronizeTaskTest
 */
class LinkCheckSynchronizeTaskTest extends ExtBaseConnectorTestCase {
	/**
	 * @var LinkCheckSynchronizeTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->fixture = $this->getMockBuilder($this->buildAccessibleProxy('SGalinski\DfTools\Task\LinkCheckSynchronizeTask'))
			->setMethods(array('getExtBaseConnector'))->disableOriginalConstructor()->getMock();
		$this->fixture->expects($this->once())->method('getExtBaseConnector')
			->will($this->returnValue($this->extBaseConnector));
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);

		parent::tearDown();
	}

	/**
	 * @test
	 * @return void
	 */
	public function executeCallsTheExtBaseController() {
		$this->addMockedExtBaseConnector('LinkCheck', 'synchronize');
		$this->fixture->execute();
	}
}

?>