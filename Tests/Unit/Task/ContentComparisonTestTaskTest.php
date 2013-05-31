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

use SGalinski\DfTools\Task\ContentComparisonTestTask;
use SGalinski\DfTools\Tests\Unit\ExtBaseConnectorTestCase;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Class ContentComparisonTestTaskTest
 */
class ContentComparisonTestTaskTest extends ExtBaseConnectorTestCase {
	/**
	 * @var \SGalinski\DfTools\Task\ContentComparisonTestTask
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture = $this->getMockBuilder(
			$this->buildAccessibleProxy('SGalinski\DfTools\Task\ContentComparisonTestTask')
		)
			->setMethods(array('sendMail', 'getExtBaseConnector'))->disableOriginalConstructor()->getMock();
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
		$this->addMockedExtBaseConnector('ContentComparisonTest', 'runAllTests', array(), array('records' => array()));
		$this->fixture->execute();
	}
}

?>