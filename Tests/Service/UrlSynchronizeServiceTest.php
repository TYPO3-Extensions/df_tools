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
		$existingRawRecordSets = array(
			'pagessubtitle1' => array(
				'uid' => 1,
				'table_name' => 'pages',
				'field' => 'subtitle',
				'identifier' => 1,
			),
			'tt_contentbodytext2' => array(
				'uid' => 2,
				'table_name' => 'tt_content',
				'field' => 'bodytext',
				'identifier' => 2,
			),
			'pagesfoo2' => array(
				'uid' => 3,
				'table_name' => 'pages',
				'field' => 'foo',
				'identifier' => 2,
			),
			'pagesurl3' => array(
				'uid' => 4,
				'table_name' => 'pages',
				'field' => 'url',
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
	 * @return Tx_DfTools_Domain_Repository_LinkCheckRepository
	 */
	protected function prepareLinkCheckRepository() {
		/** @var $linkCheckRepository Tx_DfTools_Domain_Repository_LinkCheckRepository */
		$class = 'Tx_DfTools_Domain_Repository_LinkCheckRepository';
		$linkCheckRepository = $this->getMock($class, array('add', 'update', 'remove', 'findAll'));
		$this->fixture->injectLinkCheckRepository($linkCheckRepository);

		return $linkCheckRepository;
	}

	/**
	 * @param Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository
	 * @return Tx_Extbase_Persistence_ObjectStorage
	 */
	protected function prepareFindAllLinkCheckRepository(Tx_DfTools_Domain_Repository_LinkCheckRepository $linkCheckRepository) {
		$relatedRecordSet = new Tx_DfTools_Domain_Model_RecordSet();
		$relatedRecordSet->setTableName('pages');
		$relatedRecordSet->setField('url');
		$relatedRecordSet->setIdentifier(3);

		/** @var $testRecord1 Tx_DfTools_Domain_Model_LinkCheck */
		$testRecord1 = $this->getAccessibleMock('Tx_DfTools_Domain_Model_LinkCheck', array('dummy'));
		$testRecord1->setTestUrl('http://foo.bar');
		$testRecord1->addRecordSet($relatedRecordSet);

		/** @var $testRecord2 Tx_DfTools_Domain_Model_LinkCheck */
		$testRecord2 = $this->getAccessibleMock('Tx_DfTools_Domain_Model_LinkCheck', array('dummy'));
		$testRecord2->setTestUrl('http://bar.foo');
		$testRecord2->addRecordSet($relatedRecordSet);

		$storage = new Tx_Extbase_Persistence_ObjectStorage();
		$storage->attach($testRecord1);
		$storage->attach($testRecord2);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->once())->method('findAll')
			->will($this->returnValue($storage));

		return $storage;
	}

	/**
	 * @test
	 * @return void
	 */
	public function synchronizationLogicRemovesTwoUrls() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();
		$this->prepareFindAllLinkCheckRepository($linkCheckRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->never())->method('add');
		$linkCheckRepository->expects($this->never())->method('update');
		$linkCheckRepository->expects($this->exactly(2))->method('remove');

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid');

		$this->fixture->synchronize(array());
	}

	/**
	 * @test
	 * @return void
	 */
	public function synchronizationLogicRemovesOneUrlAndAddsAnotherWithOneNewRecordSetAndOneExisting() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();
		$storage = $this->prepareFindAllLinkCheckRepository($linkCheckRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->once())->method('add');
		$linkCheckRepository->expects($this->never())->method('update');
		$linkCheckRepository->expects($this->once())->method('remove');

		$recordSetRepository->expects($this->once())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->once())->method('findByUid')
			->will($this->returnValue(new Tx_DfTools_Domain_Model_RecordSet()));

		$this->fixture->synchronize(array(
			'http://bar.foo' => array(
				'pagesurl3' => array('pages', 'url', 3),
			),
			'http://ying.yang' => array(
				'pagessubtitle1' => array('pages', 'subtitle', 1),
				'be_userstext1' => array('be_users', 'text', 1),
			),
		));

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

		$storage->rewind();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());

		$storage->next();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function synchronizationLogicEditsAnUrlByRemovingARecordSetAndAddingAnother() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();
		$storage = $this->prepareFindAllLinkCheckRepository($linkCheckRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->never())->method('add');
		$linkCheckRepository->expects($this->once())->method('update');
		$linkCheckRepository->expects($this->never())->method('remove');

		$recordSetRepository->expects($this->once())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid')
			->will($this->returnValue(new Tx_DfTools_Domain_Model_RecordSet()));

		$this->fixture->synchronize(array(
			'http://foo.bar' => array(
				'pagesurl3' => array('pages', 'url', 3),
			),
			'http://bar.foo' => array(
				'pagessubtitle4' => array('pages', 'subtitle', 4),
			),
		));

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

		$storage->rewind();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());

		$storage->next();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('subtitle', $recordSet->getField());
		$this->assertSame(4, $recordSet->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function synchronizationLogicAddsARecordSetForAnUrlAndAddsAnotherUrlWithTheSimilarRecordSet() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();
		$storage = $this->prepareFindAllLinkCheckRepository($linkCheckRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->once())->method('add');
		$linkCheckRepository->expects($this->once())->method('update');
		$linkCheckRepository->expects($this->never())->method('remove');

		$recordSetRepository->expects($this->once())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid');

		$this->fixture->synchronize(array(
			'http://foo.bar' => array(
				'pagesurl3' => array('pages', 'url', 3),
			),
			'http://bar.foo' => array(
				'pagesurl3' => array('pages', 'url', 3),
				'be_userstext1' => array('be_users', 'text', 1),
			),
			'http://ying.yang' => array(
				'be_userstext1' => array('be_users', 'text', 1),
			),
		));

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

		$storage->rewind();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());

		$storage->next();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(2, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());

		$recordSets->next();
		$recordSet = $recordSets->current();
		$this->assertSame('be_users', $recordSet->getTableName());
		$this->assertSame('text', $recordSet->getField());
		$this->assertSame(1, $recordSet->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function synchronizationLogicRemovesARecordSetWithEmptyField() {
		$this->prepareTestSynchronizationLogic();
		$recordSetRepository = $this->prepareRecordSetRepository();
		$linkCheckRepository = $this->prepareLinkCheckRepository();
		$storage = $this->prepareFindAllLinkCheckRepository($linkCheckRepository);

		/** @noinspection PhpUndefinedMethodInspection */
		$linkCheckRepository->expects($this->never())->method('add');
		$linkCheckRepository->expects($this->once())->method('update');
		$linkCheckRepository->expects($this->never())->method('remove');

		$recordSetRepository->expects($this->never())->method('add');
		$recordSetRepository->expects($this->never())->method('remove');
		$recordSetRepository->expects($this->never())->method('findByUid');

		$this->fixture->synchronize(array(
			'http://foo.bar' => array(
				'pagesurl3' => array('pages', 'url', 3),
			),
			'http://bar.foo' => array(
				'pages3' => array('pages', '', 3),
			),
		));

		/** @var $linkCheck Tx_DfTools_Domain_Model_LinkCheck */
		/** @var $recordSet Tx_DfTools_Domain_Model_RecordSet */

		$storage->rewind();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(1, $recordSets->count());

		$recordSets->rewind();
		$recordSet = $recordSets->current();
		$this->assertSame('pages', $recordSet->getTableName());
		$this->assertSame('url', $recordSet->getField());
		$this->assertSame(3, $recordSet->getIdentifier());

		$storage->next();
		$linkCheck = $storage->current();
		$recordSets = $linkCheck->getRecordSets();
		$this->assertSame(0, $recordSets->count());
	}
}

?>