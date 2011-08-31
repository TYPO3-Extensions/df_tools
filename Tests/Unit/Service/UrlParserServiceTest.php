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
 * Test case for class Tx_DfTools_Service_UrlParserService.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlParserServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Service_UrlParserService
	 */
	protected $fixture;

	/**
	 * @var t3lib_db
	 */
	protected $savedDB;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->savedDB = $GLOBALS['TYPO3_DB'];

		/** @noinspection PhpUndefinedMethodInspection */
		$proxyClass = $this->buildAccessibleProxy('Tx_DfTools_Service_UrlParserService');
		$this->fixture = $this->getMock($proxyClass, array('dummy'));
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TYPO3_DB'] = $this->savedDB;
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getPageSelectInstanceReturnsAValidObject() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInstanceOf('t3lib_pageSelect', $this->fixture->_call('getPageSelectInstance'));
		$this->assertInstanceOf('t3lib_pageSelect', $this->fixture->_call('getPageSelectInstance'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function tcaParserCanBeInjected() {
		$tcaParser = new Tx_DfTools_Service_TcaParserService();
		$this->fixture->injectTcaParser($tcaParser);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertInstanceOf('Tx_DfTools_Service_TcaParserService', $this->fixture->_get('tcaParser'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function fetchUrlsFromDatabaseBuildsItsWhereClause() {
		$dbMock = $this->getMock('t3lib_db', array('exec_SELECTgetRows'));
		$GLOBALS['TYPO3_DB'] = $dbMock;

		$table = 'pages';
		$fields = array('field1', 'field2');
		$whereClause = '(field1 REGEXP \'(https|http|ftp)://\' OR ' .
			'field2 REGEXP \'(https|http|ftp)://\') AND ' .
			'pages.deleted=0 AND pages.t3ver_state<=0 AND pages.pid!=-1 ' .
			'AND (pages.endtime=0 OR pages.endtime>' . $GLOBALS['SIM_ACCESS_TIME'] . ')';

		/** @noinspection PhpUndefinedMethodInspection */
		$dbMock->expects($this->once())->method('exec_SELECTgetRows')
			->will($this->returnValue(array()))
			->with('uid, field1, field2', $table, $whereClause);

		$this->fixture->_call('fetchUrlsFromDatabase', $table, $fields);
	}

	/**
	 * @return array
	 */
	public function parseRowsCanFetchAllUrlsDataProvider() {
		return array(
			'one url in a single field' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar')),
			),
			'multiple urls in a single field' => array(
				array(
					'http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1)),
					'http://foo.bar2' => array('pagestitle1' => array('pages', 'title', 1)),
				),
				array(array('uid' => 1, 'title' => 'http://foo.bar blablabla http://foo.bar2')),
			),
			'one url in multiple fields' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'field2' => 'FooBar', 'title' => 'http://foo.bar')),
			),
			'multiple urls in multiple fields' => array(
				array(
					'http://foo.bar2' => array('pagesfield21' => array('pages', 'field2', 1)),
					'http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1)),
				),
				array(array('uid' => 1, 'field2' => 'http://foo.bar2', 'title' => 'http://foo.bar')),
			),
			'multiple urls in multiple fields with multiple rows' => array(
				array(
					'http://foo.bar2' => array(
						'pagesfield21' => array('pages', 'field2', 1),
						'pagestitle3' => array('pages', 'title', 3)
					),
					'http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1)),
					'http://foo.bar3' => array('pagestitle2' => array('pages', 'title', 2)),
				),
				array(
					array('uid' => 1, 'field2' => 'http://foo.bar2', 'title' => 'http://foo.bar'),
					array('uid' => 2, 'title' => 'http://foo.bar3'),
					array('uid' => 3, 'title' => 'http://foo.bar2'),
				),
			),
			'multiple urls in multiple fields without multiple rows' => array(
				array(
					'http://foo.bar' => array(
						'pagesfield11' => array('pages', 'field1', 1),
						'pagesfield21' => array('pages', 'field2', 1),
					),
				),
				array(
					array('uid' => 1, 'field1' => 'http://foo.bar', 'field2' => 'http://foo.bar'),
				),
			),
			'ftp url ending with quote' => array(
				array('ftp://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'ftp://foo.bar"foo')),
			),
			'ftp url ending with single quote' => array(
				array('ftp://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'ftp://foo.bar\'foo')),
			),
			'http url with anchor' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar#bla foo')),
			),
			'http url ending with space' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar foo')),
			),
			'http url ending with comma' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar, foo')),
			),
			'http url ending with dot' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar. foo')),
			),
			'http url ending with a closing brace' => array(
				array('http://foo.bar/foo_(bar)' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar/foo_(bar). foo')),
			),
			'http url ending with file extension' => array(
				array('http://foo.bar/bla.php' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar/bla.php foo')),
			),
			'https url ending with angle bracket' => array(
				array('https://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'https://foo.bar<foo')),
			),
			'http url with ending brace' => array(
				array('http://foo.bar' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => '(http://foo.bar)')),
			),
			'http url with braces inside the url' => array(
				array('http://foo.bar/foo_(bar)' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'http://foo.bar/foo_(bar)')),
			),
			'http url with html entities' => array(
				array('https://foo.bar?a=1&b=2' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'https://foo.bar?a=1&amp;b=2<foo')),
			),
			'https url from twitter (/#!/ segment)' => array(
				array('https://twitter.com/#!/sgalinski' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'https://twitter.com/#!/sgalinski')),
			),
			'https url from facebook defined in typoscript within an iframe' => array(
				array('https://www.facebook.com/plugins/like.php?href=www.facebook.com%2Fdomainfactory.GmbH&send=false&layout=button_count&width=115&show_faces=false&action=like&colorscheme=light&font=arial&height=21' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => ' value = <iframe src="https://www.facebook.com/plugins/like.php?href=www.facebook.com%2Fdomainfactory.GmbH&amp;send=false&amp;layout=button_count&amp;width=115&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:115px; height:21px;"></iframe>')),
			),
			'https url with umlauts' => array(
				array('https://kundenmenü.de' => array('pagestitle1' => array('pages', 'title', 1))),
				array(array('uid' => 1, 'title' => 'https://kundenmenü.de')),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider parseRowsCanFetchAllUrlsDataProvider
	 *
	 * @param array $expected
	 * @param array $rows
	 * @return void
	 */
	public function parseRowsCanFetchAllUrls(array $expected, array $rows) {
		$this->assertSame($expected, $this->fixture->parseRows($rows, 'pages'));
	}

	/**
	 * @test
	 * @return void
	 */
	public function fetchLinkCheckLinkTypeBuildsUrlsCorrectly() {
		$rows = array(
			array(
				'uid' => 1,
				'urltype' => 1,
				'url' => 'foo.bar'
			),
			array(
				'uid' => 2,
				'urltype' => 2,
				'url' => 'foo.bar'
			),
			array(
				'uid' => 3,
				'urltype' => 5,
				'url' => 'foo.bar'
			),
			array(
				'uid' => 4,
				'urltype' => 4,
				'url' => 'foo.bar'
			),
		);

		$whereClause = 'doktype = 3 && urltype != 3 && urltype != 0 ' .
			'AND pages.deleted=0 AND pages.t3ver_state<=0 AND pages.pid!=-1 ' .
			'AND (pages.endtime=0 OR pages.endtime>' . $GLOBALS['SIM_ACCESS_TIME'] . ')';

		/** @noinspection PhpUndefinedMethodInspection */
		$dbMock = $this->getMock('t3lib_db', array('exec_SELECTgetRows'));
		$GLOBALS['TYPO3_DB'] = $dbMock;

		$dbMock->expects($this->once())->method('exec_SELECTgetRows')
			->will($this->returnValue($rows))
			->with('uid, url, urltype', 'pages', $whereClause);

		$expectedUrls = array(
			'http://foo.bar' => array('pagesurl1' => array('pages', 'url', 1)),
			'ftp://foo.bar' => array('pagesurl2' => array('pages', 'url', 2)),
			'https://foo.bar' => array('pagesurl4' => array('pages', 'url', 4)),
		);

		$this->assertSame($expectedUrls, $this->fixture->fetchLinkCheckLinkType());
	}

	/**
	 * @test
	 * @return void
	 */
	public function fetchLinkCheckLinkTypeBuildsUrlsCorrectlyWithRestrictedPageFilter() {
		$rows = array(
			array(
				'uid' => 1,
				'urltype' => 1,
				'url' => 'foo.bar'
			),
			array(
				'uid' => 2,
				'urltype' => 2,
				'url' => 'foo.bar'
			),
		);

		$whereClause = 'doktype = 3 && urltype != 3 && urltype != 0 ' .
			'AND pages.deleted=0 AND pages.t3ver_state<=0 AND pages.pid!=-1 ' .
			'AND (pages.endtime=0 OR pages.endtime>' . $GLOBALS['SIM_ACCESS_TIME'] . ') ' .
			'AND uid IN (1, 2)';

		/** @noinspection PhpUndefinedMethodInspection */
		$dbMock = $this->getMock('t3lib_db', array('exec_SELECTgetRows'));
		$GLOBALS['TYPO3_DB'] = $dbMock;

		$dbMock->expects($this->once())->method('exec_SELECTgetRows')
			->will($this->returnValue($rows))
			->with('uid, url, urltype', 'pages', $whereClause);

		$expectedUrls = array(
			'http://foo.bar' => array('pagesurl1' => array('pages', 'url', 1)),
			'ftp://foo.bar' => array('pagesurl2' => array('pages', 'url', 2)),
		);

		$this->assertSame($expectedUrls, $this->fixture->fetchLinkCheckLinkType(array(1, 2)));
	}

	/**
	 * @test
	 * @return void
	 */
	public function fetchUrlMergesUrlsFromSources() {
		$this->fixture = $this->getMock(
			'Tx_DfTools_Service_UrlParserService',
			array('fetchLinkCheckLinkType', 'fetchUrlsFromDatabase')
		);

		/** @var $tcaParser Tx_DfTools_Service_TcaParserService */
		$tcaParser = $this->getMock('Tx_DfTools_Service_TcaParserService', array('findFields'));
		$this->fixture->injectTcaParser($tcaParser);

		/** @noinspection PhpUndefinedMethodInspection */
		$tableFields = array('pages' => array('field1', 'field2'), 'tt_content' => array('field1'));
		$tcaParser->expects($this->once())->method('findFields')->will($this->returnValue($tableFields));

		$urls = array('http://foo.bar' => array(
			'pages1' => array('pages', 1),
			'pages3' => array('pages', 3),
			'tt_content1' => array('tt_content', 1)),
		);
		$this->fixture->expects($this->exactly(2))->method('fetchUrlsFromDatabase')
			->will($this->returnValue($urls));

		$urls = array(
			'http://foo.bar' => array('pages3' => array('pages', 3)),
			'http://foo.bar2' => array('pages2' => array('pages', 2))
		);
		$this->fixture->expects($this->once())->method('fetchLinkCheckLinkType')
			->will($this->returnValue($urls));

		$expectedUrls = array(
			'http://foo.bar' => array(
				'pages1' => array('pages', 1),
				'pages3' => array('pages', 3),
				'tt_content1' => array('tt_content', 1),
			),
			'http://foo.bar2' => array(
				'pages2' => array('pages', 2),
			),
		);
		$this->assertSame($expectedUrls, $this->fixture->fetchUrls());
	}
}

?>