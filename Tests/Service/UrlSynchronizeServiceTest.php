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
 * Test case for class Tx_DfTools_Service_UrlSynchronizeService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlSynchronizeServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_UrlSynchronizeService
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->getAccessibleMock(
			'Tx_DfTools_Service_UrlSynchronizeService',
			array('fetchExistingRawRecordSets', 'fetchExistingRawUrls', 'getPersistenceManager')
		);

		/** @var $objectManager Tx_Extbase_Object_ObjectManager */
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->fixture->injectObjectManager($objectManager);
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
	public function testInjectRecordSetRepository() {
		/** @var $repository Tx_DfTools_Domain_Repository_RecordSetRepository */
		$class = 'Tx_DfTools_Domain_Repository_RecordSetRepository';
		$repository = $this->getMock($class, array('dummy'));
		$this->fixture->injectRecordSetRepository($repository);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($repository, $this->fixture->_get('recordSetRepository'));
	}

	/**
	 * @return void
	 */
	protected function prepareTestSynchronizationLogic() {
		/** @noinspection PhpUndefinedMethodInspection */
		$existingRawUrls = array(
			'http://foo.bar' => array(
				'uid' => 1,
				'test_url' => 'http://foo.bar',
			),
			'http://bar.foo' => array(
				'uid' => 2,
				'test_url' => 'http://bar.foo',
			),
		);

		$this->fixture->expects($this->once())->method('fetchExistingRawUrls')
			->will($this->returnValue($existingRawUrls));

		$existingRawRecordSets = array(
			'pages1' => array(
				'uid' => 1,
				'table_name' => 'pages',
				'identifier' => 1,
			),
			'tt_content2' => array(
				'uid' => 2,
				'table_name' => 'tt_content',
				'identifier' => 2,
			),
			'pages2' => array(
				'uid' => 3,
				'table_name' => 'pages',
				'identifier' => 2,
			),
			'pages3' => array(
				'uid' => 4,
				'table_name' => 'pages',
				'identifier' => 3,
			),
		);

		$this->fixture->expects($this->once())->method('fetchExistingRawRecordSets')
			->will($this->returnValue($existingRawRecordSets));

		$persistenceManager = $this->getMock('Tx_Extbase_Persistence_Manager', array('getBackend'));
		$backend = $this->getMock(
			'Tx_Extbase_Persistence_BackendInterface',
			array(
				'setAggregateRootObjects', 'setDeletedObjects', 'commit', 'isNewObject',
				'getIdentifierByObject', 'getObjectByIdentifier', 'replaceObject',
			)
		);
		$persistenceManager->expects($this->any())->method('getBackend')->will($this->returnValue($backend));
		$this->fixture->expects($this->any())->method('getPersistenceManager')
			->will($this->returnValue($persistenceManager));
	}

	/**
	 * @return Tx_DfTools_Domain_Repository_RecordSetRepository
	 */
	protected function prepareRecordSetRepository() {
		/** @var $recordSetRepository Tx_DfTools_Domain_Repository_RecordSetRepository */
		$class = 'Tx_DfTools_Domain_Repository_RecordSetRepository';
		$recordSetRepository = $this->getMock($class, array('add', 'remove', 'findByUid'));
		$this->fixture->injectRecordSetRepository($recordSetRepository);

		return $recordSetRepository;
	}

	/**
	 * @return Tx_DfTools_Domain_Repository_RecordSetRepository
	 */
	protected function prepareLinkCheckRepository() {
		/** @var $linkCheckRepository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$class = 'Tx_DfTools_Domain_Repository_LinkCheckRepository';
		$linkCheckRepository = $this->getMock($class, array('add', 'update', 'remove', 'findByUid'));
		$this->fixture->injectLinkCheckRepository($linkCheckRepository);

		return $linkCheckRepository;
	}

	/**
	 * @test
	 * @return void
	 */
	public function testSynchronizationLogicRemovesTwoUrls() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->never())->method('add');
		$linkCheckRepository->expects($this->never())->method('update');
		$linkCheckRepository->expects($this->exactly(2))->method('remove');
		$linkCheckRepository->expects($this->exactly(2))->method('findByUid');

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid');

		$this->fixture->synchronize(array());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testSynchronizationLogicRemovesOneUrlAndAddsAnotherWithOneNewRecordSetAndOneExisting() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$class = 'Tx_DfTools_Domain_Model_LinkCheck';
		$linkCheck = $this->getAccessibleMock($class, array('dummy'));
		$relatedRecordSet = new Tx_DfTools_Domain_Model_RecordSet();
		$relatedRecordSet->setTableName('pages');
		$relatedRecordSet->setIdentifier(3);
		$linkCheck->addRecordSet($relatedRecordSet);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheck->expects($this->never())->method('addRecordSet');
		$linkCheck->expects($this->never())->method('removeRecordSet');

		$linkCheckRepository->expects($this->once())->method('add');
		$linkCheckRepository->expects($this->never())->method('update');
		$linkCheckRepository->expects($this->once())->method('remove');
		$linkCheckRepository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($linkCheck));

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->once())->method('findByUid')
			->will($this->returnValue(new Tx_DfTools_Domain_Model_RecordSet()));

		$rawUrls = array(
			'http://bar.foo' => array(
				'pages3' => array('pages', 3),
			),
			'http://ying.yang' => array(
				'pages1' => array('pages', 1),
				'be_users1' => array('be_users', 1),
			),
		);

		$this->fixture->synchronize($rawUrls);

		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */
		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame(3, $recordSet->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testSynchronizationLogicEditsAnUrlByRemovingARecordSetAndAddingAnother() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$class = 'Tx_DfTools_Domain_Model_LinkCheck';
		$linkCheck = $this->getAccessibleMock($class, array('dummy'));
		$relatedRecordSet = new Tx_DfTools_Domain_Model_RecordSet();
		$relatedRecordSet->setTableName('pages');
		$relatedRecordSet->setIdentifier(3);
		$linkCheck->addRecordSet($relatedRecordSet);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->never())->method('add');
		$linkCheckRepository->expects($this->once())->method('update');
		$linkCheckRepository->expects($this->never())->method('remove');
		$linkCheckRepository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($linkCheck));

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid')
			->will($this->returnValue(new Tx_DfTools_Domain_Model_RecordSet()));

		$rawUrls = array(
			'http://foo.bar' => array(
				'pages3' => array('pages', 3),
			),
			'http://bar.foo' => array(
				'pages4' => array('pages', 4),
			),
		);

		$this->fixture->synchronize($rawUrls);

		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */
		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame(4, $recordSet->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function testSynchronizationLogicAddsARecordSetForAnUrlAndAddsAnotherUrlWithTheSimilarRecordSet() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		$class = 'Tx_DfTools_Domain_Model_LinkCheck';
		$linkCheck = $this->getAccessibleMock($class, array('dummy'));
		$relatedRecordSet = new Tx_DfTools_Domain_Model_RecordSet();
		$relatedRecordSet->setTableName('pages');
		$relatedRecordSet->setIdentifier(3);
		$linkCheck->addRecordSet($relatedRecordSet);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->once())->method('add');
		$linkCheckRepository->expects($this->once())->method('update');
		$linkCheckRepository->expects($this->never())->method('remove');
		$linkCheckRepository->expects($this->exactly(2))->method('findByUid')
			->will($this->returnValue($linkCheck));

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->once())->method('findByUid')
			->with(NULL) // newly created be_user has uid NULL in our test case
			->will($this->returnValue(new Tx_DfTools_Domain_Model_RecordSet()));

		$rawUrls = array(
			'http://foo.bar' => array(
				'pages3' => array('pages', 3),
			),
			'http://bar.foo' => array(
				'pages3' => array('pages', 3),
				'be_users1' => array('be_users', 1),
			),
			'http://ying.yang' => array(
				'be_users1' => array('be_users', 1),
			),
		);

		$this->fixture->synchronize($rawUrls);

		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(2, $recordSets->count());

		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */
		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame(3, $recordSet->getIdentifier());

		$recordSets->next();
		$recordSet = $recordSets->current();
		$this->assertSame('be_users', $recordSet->getTableName());
		$this->assertSame(1, $recordSet->getIdentifier());
	}
}

?>