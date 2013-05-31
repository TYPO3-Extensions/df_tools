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

use SGalinski\DfTools\Connector\ExtBaseConnectorService;
use SGalinski\DfTools\Parser\TcaParserService;
use SGalinski\DfTools\Parser\UrlParserService;
use SGalinski\DfTools\Task\AbstractFields;
use SGalinski\DfTools\Task\AbstractTask;
use SGalinski\DfTools\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Page\PageRepository;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Class AbstractFieldsTest
 */
class AbstractFieldsTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\Task\AbstractFields
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock('SGalinski\DfTools\Task\AbstractFields', array('dummy'));
		$this->fixture->setFieldPrefix('FieldPrefix');
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
	public function settingTheFieldPrefixWorks() {
		$this->fixture->setFieldPrefix('FooBar');
		$this->assertSame('FooBar', $this->fixture->getFieldPrefix());
	}

	/**
	 * @return array
	 */
	public function getFullFieldNameReturnsValidParameterNameDataProvider() {
		return array(
			'field has already an upper first character' => array(
				'FieldPrefixParameter', 'Parameter'
			),
			'field with a lower first character' => array(
				'FieldPrefixParameter', 'parameter'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider getFullFieldNameReturnsValidParameterNameDataProvider
	 *
	 * @param string $expected
	 * @param string $input
	 * @return void
	 */
	public function getFullFieldNameReturnsValidParameterName($expected, $input) {
		/** @noinspection PhpUndefinedMethodInspection */
		$returnValue = $this->fixture->_call('getFullFieldName', $input);
		$this->assertSame($expected, $returnValue);
	}

	/**
	 * @return void
	 */
	protected function addFakeSchedulerMainModule() {
		if (!class_exists('tx_scheduler_Module')) {
			eval('
				class tx_scheduler_Module {
					public $CMD = "";
					public function addMessage($message, $severity) {}
				}
			');
		}
	}

	/**
	 * @test
	 * @return void
	 */
	public function getAdditionalFieldsReturnsTheFieldConfigurationInEditState() {
		$this->addFakeSchedulerMainModule();
		$schedulerModule = new SchedulerModuleController();
		$schedulerModule->CMD = 'edit';

		$fieldName = 'FieldPrefixNotificationEmailAddress';
		$expectedFieldConfiguration = array(
			'task_' . $fieldName => array(
				'code' => '<input type="text" name="tx_scheduler[' . $fieldName . ']" '
				. 'id="task_' . $fieldName . '" value="FooBar" />',
				'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:' .
				'tx_dftools_common.scheduler.notificationEmailAddress',
				'cshKey' => '',
				'cshLabel' => 'task_' . $fieldName,
			)
		);

		/** @var $task AbstractTask */
		$task = $this->getMockBuilder('SGalinski\DfTools\Task\AbstractTask')
			->setMethods(array('execute', 'sendNotificationEmail'))->disableOriginalConstructor()->getMock();
		$task->setNotificationEmailAddress('FooBar');

		$taskInfo = array();
		$fieldConfiguration = $this->fixture->getAdditionalFields($taskInfo, $task, $schedulerModule);

		$this->assertSame($expectedFieldConfiguration, $fieldConfiguration);
		$this->assertSame('FooBar', $taskInfo[$fieldName]);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getAdditionalFieldsReturnsTheFieldConfigurationInAddState() {
		$this->addFakeSchedulerMainModule();
		$schedulerModule = new SchedulerModuleController();

		$fieldName = 'FieldPrefixNotificationEmailAddress';
		$expectedFieldConfiguration = array(
			'task_' . $fieldName => array(
				'code' => '<input type="text" name="tx_scheduler[' . $fieldName . ']" '
				. 'id="task_' . $fieldName . '" value="" />',
				'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:' .
				'tx_dftools_common.scheduler.notificationEmailAddress',
				'cshKey' => '',
				'cshLabel' => 'task_' . $fieldName,
			)
		);

		/** @var $task AbstractTask */
		$taskInfo = array();
		$task = $this->getMockBuilder('SGalinski\DfTools\Task\AbstractTask')
			->setMethods(array('execute', 'sendNotificationEmail'))->disableOriginalConstructor()->getMock();
		$fieldConfiguration = $this->fixture->getAdditionalFields($taskInfo, $task, $schedulerModule);

		$this->assertSame($expectedFieldConfiguration, $fieldConfiguration);
		$this->assertEmpty($taskInfo[$fieldName]);
	}

	/**
	 * @return array
	 */
	public function validateAdditionalFieldsDataProvider() {
		return array(
			'single valid mail address' => array(
				TRUE, 'mail@example.org'
			),
			'mulitple valid mail addresses' => array(
				TRUE, 'mail@example.org,mail2@example.org'
			),
			'single invalid mail address' => array(
				FALSE, 'mailexample.org'
			),
			'multiple invalid mail addresses' => array(
				FALSE, 'mailexample.org,mailexample.org'
			),
		);
	}

	/**
	 * @test
	 * @dataProvider validateAdditionalFieldsDataProvider
	 *
	 * @param boolean $expected
	 * @param string $input
	 * @return void
	 */
	public function validateAdditionalFields($expected, $input) {
		$this->addFakeSchedulerMainModule();
		$schedulerModule = new SchedulerModuleController();
		$submittedData = array(
			'FieldPrefixNotificationEmailAddress' => $input,
		);

		$returnValue = $this->fixture->validateAdditionalFields($submittedData, $schedulerModule);
		$this->assertSame($expected, $returnValue);
	}

	/**
	 * @test
	 * @return void
	 */
	public function saveAdditionalFields() {
		/** @var $task AbstractTask */
		$task = $this->getMockBuilder('SGalinski\DfTools\Task\AbstractTask')
			->setMethods(array('execute', 'sendNotificationEmail'))->disableOriginalConstructor()->getMock();
		$submittedData = array('FieldPrefixNotificationEmailAddress' => 'mail@example.org');
		$this->fixture->saveAdditionalFields($submittedData, $task);

		$this->assertSame('mail@example.org', $task->getNotificationEmailAddress());
	}
}

?>