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
		$this->fixture = $this->getMock(
			$this->buildAccessibleProxy('Tx_DfTools_Service_LinkCheckService'),
			array('findExistingRawUrlsByTableAndUid', 'getRecordByTableAndId', 'findExistingRawUrlsByTestUrls')
		);

		/** @var $repository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$this->testRepository = $this->getMock(
			'Tx_DfTools_Domain_Repository_LinkCheckRepository',
			array('dummy'),
			array($this->objectManager)
		);
		$this->fixture->injectLinkCheckRepository($this->testRepository);

		/** @var $repository Tx_Extbase_Object_ObjectManager */
		$this->objectManager = $this->getMock('Tx_Extbase_Object_ObjectManager', array('create', 'get'));
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
		$repository = $this->getMock($class, array('dummy'), array($this->objectManager));
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

	/**
	 * @test
	 * @return void
	 */
	public function allRawUrlsFromTheDatabaseCanBeFetched() {
		$excludedTablesString = 'tt_content,pages';
		$excludedTableFieldsString = 'field1,field2,field3';
		$preparedExcludedTablesString = array('tt_content', 'pages');
		$preparedExcludedTableFieldsString = array('field1', 'field2', 'field3');

		$urlParserService = $this->getMock('Tx_DfTools_Service_UrlParserService');
		$urlParserService->expects($this->once())->method('fetchUrls')
			->with($preparedExcludedTablesString, $preparedExcludedTableFieldsString);
		/** @noinspection PhpUndefinedMethodInspection */
		$this->objectManager->expects($this->once())->method('get')
			->will($this->returnValue($urlParserService));

		$this->fixture->fetchAllRawUrlsFromTheDatabase($excludedTablesString, $excludedTableFieldsString);
	}

	/**
	 * @return array
	 */
	public function urlsFromASingleRecordCanBeFetchedDataProvider() {
		return array(
			'with table tt_content' => array(
				'tt_content', 12, $this->never()
			),
			'with table pages' => array(
				'pages', 16, $this->once()
			),
		);
	}

	/**
	 * @dataProvider urlsFromASingleRecordCanBeFetchedDataProvider
	 * @test
	 *
	 * @param string $table
	 * @param int $identitiy
	 * @param PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $fetchLinkCheckTypeCallAmounts
	 * @return void
	 */
	public function urlsFromASingleRecordCanBeFetched($table, $identitiy, $fetchLinkCheckTypeCallAmounts) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('getRecordByTableAndId')
			->will($this->returnValue(array()));

		$foundUrls = array(
			'http://foo.bar' => array(
				'tt_contentbodytext12' => array('tt_content', 'bodytext', 12),
			),
			'http://ying.yang' => array(
				'tt_contentbodytext12' => array('tt_content', 'bodytext', 12),
			),
		);

		$urlParserService = $this->getMock('Tx_DfTools_Service_UrlParserService');
		$urlParserService->expects($fetchLinkCheckTypeCallAmounts)->method('fetchLinkCheckLinkType')
			->will($this->returnValue(array()));
		$urlParserService->expects($this->once())->method('parseRows')
			->with($this->isType('array'))->will($this->returnValue($foundUrls));
		$this->objectManager->expects($this->once())->method('get')
			->will($this->returnValue($urlParserService));

		$existingRawUrls = array(
			'http://bar.foo' => array(
				$table . 'bodytext' . $identitiy => array($table, 'bodytext', $identitiy),
				'pagesheader25' => array('pages', 'header', 25),
			),
		);
		$this->fixture->expects($this->once())->method('findExistingRawUrlsByTableAndUid')
			->will($this->returnValue($existingRawUrls));

		$existingFoundRawUrls = array(
			'http://ying.yang' => array(
				'pagesheader25' => array('pages', 'header', 25),
			),
		);

		$this->fixture->expects($this->once())->method('findExistingRawUrlsByTestUrls')
			->will($this->returnValue($existingFoundRawUrls));

		$expectedRawUrls = array(
			'http://foo.bar' => array(
				'tt_contentbodytext12' => array('tt_content', 'bodytext', 12),
			),
			'http://ying.yang' => array(
				'pagesheader25' => array('pages', 'header', 25),
				'tt_contentbodytext12' => array('tt_content', 'bodytext', 12),
			),
			'http://bar.foo' => array(
				'pagesheader25' => array('pages', 'header', 25),
			),
		);

		$rawUrls = $this->fixture->getUrlsFromSingleRecord($table, $identitiy);
		$this->assertSame($expectedRawUrls, $rawUrls);
	}

	/**
	 * @test
	 * @return void
	 */
	public function recordSetsOfALinkCheckCanBeReturnedInAPlainStructure() {
		/** @noinspection PhpUndefinedMethodInspection */
		$recordSet1 = new Tx_DfTools_Domain_Model_RecordSet();
		$recordSet1->setTableName('tt_content');
		$recordSet1->setField('bodytext');
		$recordSet1->setIdentifier(12);

		$recordSet2 = new Tx_DfTools_Domain_Model_RecordSet();
		$recordSet2->setTableName('pages');
		$recordSet2->setField('header');
		$recordSet2->setIdentifier(25);

		$storage = new Tx_Extbase_Persistence_ObjectStorage();
		$storage->attach($recordSet1);
		$storage->attach($recordSet2);

		$linkCheck = new Tx_DfTools_Domain_Model_LinkCheck();
		$linkCheck->setRecordSets($storage);

		$expectedPlainRecordSets = array(
			'tt_contentbodytext12' => array(
				'tt_content', 'bodytext', 12
			),
			'pagesheader25' => array(
				'pages', 'header', 25
			),
		);

		$plainRecordSets = $this->fixture->_call('getRecordSetsAsPlainArray', $linkCheck);
		$this->assertSame($expectedPlainRecordSets, $plainRecordSets);
	}
}

?>