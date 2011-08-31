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
 * Test case for class Tx_DfTools_Service_TcaParserService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_TcaParserServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var array
	 */
	protected $backupTCA = NULL;

	/**
	 * @var Tx_DfTools_Service_TcaParserService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->backupTCA = $GLOBALS['TCA'];
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_TcaParserService');
		$this->fixture = $this->getMock($proxyClass, array('dummy'));
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TCA'] = $this->backupTCA;
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function settingTheExcludeFieldsPropertyWorks() {
		$fields = array('table<field1>', 'field2');
		$this->fixture->setExcludedFields($fields);
		$this->assertSame($fields, $this->fixture->getExcludedFields());
	}

	/**
	 * @test
	 * @return void
	 */
	public function settingTheExcludeEvalsPropertyWorks() {
		$evals = array('date', 'datetime');
		$this->fixture->setExcludedEvals($evals);
		$this->assertSame($evals, $this->fixture->getExcludedEvals());
	}

	/**
	 * @test
	 * @return void
	 */
	public function settingTheExcludeTablesPropertyWorks() {
		$tables = array('pages', 'tt_content');
		$this->fixture->setExcludedTables($tables);
		$this->assertSame($tables, $this->fixture->getExcludedTables());
	}

	/**
	 * @test
	 * @return void
	 */
	public function settingTheAllowedTypesPropertyWorks() {
		$types = array('text', 'input');
		$this->fixture->setAllowedTypes($types);
		$this->assertSame($types, $this->fixture->getAllowedTypes());
	}

	/**
	 * Prepares the TCA configuration array as a test fixture
	 *
	 * Note: It's added as the global variable $GLOBALS['TCA'].
	 *
	 * Containing Tables:
	 * - pages
	 * - pages_language_overlay
	 * - sys_language
	 * - sys_news
	 * - be_users
	 *
	 * @return void
	 */
	protected function prepareTcaConfiguration() {
		$file = t3lib_extMgm::extPath('df_tools') . 'Tests/Fixture/serializedTcaConfiguration.txt';
		$tcaConfiguration = unserialize(file_get_contents($file));
		$GLOBALS['TCA'] = $tcaConfiguration['TCA'];
	}

	/**
	 * Returns an expected test array
	 *
	 * @return array
	 */
	protected function getExpectedFieldArray() {
		return array(
			'pages' => array(
				'TSconfig',
				'subtitle',
				'alias',
				'keywords',
				'description',
				'abstract',
				'tx_govaccesskey_accesskey',
				'tx_govaccesskey_tabindex',
			),
			'pages_language_overlay' => array(
				'keywords',
				'description',
				'abstract',
			),
			'sys_news' => array(
				'title',
				'content',
			),
			'be_users' => array(
				'username',
				'TSconfig',
			),
		);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getFieldsReturnsOnlyTextInputFieldsWithoutDateAndDatetimeAndTrimEval() {
		$this->prepareTcaConfiguration();

		$this->fixture->setAllowedTypes(array('input', 'text'));
		$this->fixture->setExcludedEvals(array('date', 'datetime', 'trim'));

		$expected = $this->getExpectedFieldArray();
		$firstField = array_shift($expected['pages']);
		array_unshift($expected['pages'], $firstField, 't3ver_label');
		$expected['pages_language_overlay'][] = 't3ver_label';

		$this->assertSame($expected, $this->fixture->findFields());
	}

	/**
	 * @test
	 * @return void
	 */
	public function getFieldsReturnsOnlyTextInputFieldsWithoutDateAndDatetimeEvalAndNoT3verLabel() {
		$this->prepareTcaConfiguration();

		$this->fixture->setAllowedTypes(array('input', 'text'));
		$this->fixture->setExcludedEvals(array('date', 'datetime', 'trim'));
		$this->fixture->setExcludedFields(array('t3ver_label'));
		$this->assertSame($this->getExpectedFieldArray(), $this->fixture->findFields());
	}

	/**
	 * @test
	 * @return void
	 */
	public function getFieldsRespectsDifferentKindsOfExcludeFields() {
		$this->prepareTcaConfiguration();

		$this->fixture->setAllowedTypes(array('input', 'text'));
		$this->fixture->setExcludedEvals(array('date', 'datetime', 'trim'));
		$this->fixture->setExcludedFields(array('t3ver_label', 'pages<keywords>'));

		$expectedFields = $this->getExpectedFieldArray();
		array_splice($expectedFields['pages'], 3, 1);

		$this->assertSame($expectedFields, $this->fixture->findFields());
	}

	/**
	 * @test
	 * @return void
	 */
	public function getFieldsReturnsOnlyTextInputFieldsWithoutDateAndDatetimeEvalAndNoT3verLabelAndNoPagesTable() {
		$this->prepareTcaConfiguration();

		$this->fixture->setAllowedTypes(array('input', 'text'));
		$this->fixture->setExcludedEvals(array('date', 'datetime', 'trim'));
		$this->fixture->setExcludedFields(array('t3ver_label'));
		$this->fixture->setExcludedTables(array('pages'));

		$expected = $this->getExpectedFieldArray();
		unset($expected['pages']);
		$this->assertSame($expected, $this->fixture->findFields());
	}

	/**
	 * Test callback filter
	 *
	 * @param array $configuration
	 * @param string $field
	 * @return boolean
	 */
	public function callbackFilter($configuration, $field) {
		if ($field !== 'abstract') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @test
	 * @return void
	 */
	public function getFieldsReturnsOnlyTextInputFieldsWithNameAbstract() {
		$this->prepareTcaConfiguration();

		$expected = array(
			'pages' => array('abstract'),
			'pages_language_overlay' => array('abstract'),
		);

		$this->fixture->setAllowedTypes(array('input', 'text'));
		$fields = $this->fixture->findFields(array($this, 'callbackFilter'));
		$this->assertSame($expected, $fields);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getAllTablesReturnsAListOfAllTables() {
		$this->prepareTcaConfiguration();
		$tables = $this->fixture->getAllTables();
		$expectedTables = array(
			'pages', 'pages_language_overlay',
			'sys_language', 'sys_news',
			'be_users',
		);

		$this->assertSame($expectedTables, $tables);
	}
}

?>