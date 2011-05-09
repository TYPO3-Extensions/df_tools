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
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Url Parser Service
 *
 * Fetches all urls defined inside the database. Needs an instance of the tca parser
 * to work correctly.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlParserService implements t3lib_Singleton {
	/**
	 * @var Tx_DfTools_Service_TcaParserService
	 */
	protected $tcaParser = NULL;

	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect = NULL;

	/**
	 * Returns an instance of t3lib_pageSelect to call the enableFields method
	 * for self-made queries.
	 *
	 * @return t3lib_pageSelect
	 */
	protected function getPageSelectInstance() {
		if ($this->pageSelect === NULL) {
			$this->pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		}

		return $this->pageSelect;
	}

	/**
	 * @param Tx_DfTools_Service_TcaParserService $tcaParser
	 * @return void
	 */
	public function injectTcaParser(Tx_DfTools_Service_TcaParserService $tcaParser) {
		$this->tcaParser = $tcaParser;
	}

	/**
	 * Fetches all URLs from the database
	 *
	 * @param array $excludedTables
	 * @param array $excludedTableFields
	 * @return array
	 */
	public function fetchUrls(array $excludedTables = array(), array $excludedTableFields = array()) {
		$tablesWithFields = Tx_DfTools_Utility_TcaUtility::getTextFields(
			$this->tcaParser,
			$excludedTables,
			$excludedTableFields
		);

		$urls = array();
		foreach ((array)$tablesWithFields as $table => $fields) {
			$fetchedUrls = $this->fetchUrlsFromDatabase($table, $fields);
			foreach ($fetchedUrls as $url => $data) {
				$urls[$url] = array_merge((array)$urls[$url], $data);
			}
		}

		if (isset($tablesWithFields['pages'])) {
			$fetchedUrls = $this->fetchLinkCheckLinkType();
			foreach ($fetchedUrls as $url => $data) {
				$urls[$url] = array_merge((array)$urls[$url], $data);
			}
		}

		return $urls;
	}

	/**
	 * Returns all used urls inside the given table and fields.
	 *
	 * Note: We don't exclude domains of the same origin as the current site,
	 * because all hardcoded links should be checked!
	 *
	 * @param string $table
	 * @param array $fields
	 * @return array $array[<url>][<table><uid>] = array(<table>, <uid>)
	 */
	protected function fetchUrlsFromDatabase($table, array $fields) {
		$whereClause = array();
		foreach ($fields as $field) {
			$whereClause[] = $field . ' REGEXP \'(https|http|ftps|ftp)://\'';
		}

		$enableFields = $this->getPageSelectInstance()->enableFields(
			$table, 1,
			array('starttime' => TRUE, 'endtime' => TRUE, 'fe_group' => TRUE)
		);

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, ' . implode(', ', $fields),
			$table,
			'(' . implode(') OR (', $whereClause) . ')' . $enableFields
		);

		return $this->parseRows($rows, $table);
	}

	/**
	 * Parses the database rows to retrieve the urls
	 *
	 * @param array $rows
	 * @param string $table
	 * @return array
	 */
	protected function parseRows(array $rows, $table) {
		$urls = array();
		foreach ((array) $rows as $row) {
			$foundUrls = array();
			$searchString = implode(' ', $row);
			$regularExpression = '/((?:https|http|ftps|ftp):\/\/[^\s<>\)\]|"\']+)/is';
			preg_match_all($regularExpression, $searchString, $foundUrls);

			$length = count($foundUrls[1]);
			for ($i = 0; $i < $length; ++$i) {
				$url = trim($foundUrls[1][$i], '.');
				$url = html_entity_decode($url, ENT_COMPAT, 'UTF-8');

				list($url, $anchor) = explode('#', $url, 2);
				if ($anchor{0} === '!') {
					$url .= '#' . $anchor;
				}

				$urls[$url][$table . $row['uid']] = array($table, $row['uid']);
			}
		}

		return $urls;
	}

	/**
	 * Returns all urls from the link check type of the table pages if they are
	 * configured to be prefixed by the urlType field.
	 *
	 * @return array $array[<url>][<table><uid>] = array(<table>, <uid>)
	 */
	protected function fetchLinkCheckLinkType() {
		$enableFields = $this->getPageSelectInstance()->enableFields(
			'pages', 1,
			array('starttime' => TRUE, 'endtime' => TRUE, 'fe_group' => TRUE)
		);

		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, url, urltype',
			'pages',
			'doktype = 3 && urltype != 3 && urltype != 0' . $enableFields
		);

		$urls = array();
		foreach ((array) $rows as $row) {
			$row['urltype'] = intval($row['urltype']);
			if ($row['urltype'] === 1) {
				$row['url'] = 'http://' . $row['url'];
			} elseif ($row['urltype'] === 2) {
				$row['url'] = 'ftp://' . $row['url'];
			} elseif ($row['urltype'] === 4) {
				$row['url'] = 'https://' . $row['url'];
			} else {
				continue;
			}

			$urls[$row['url']]['pages' . $row['uid']] = array('pages', $row['uid']);
		}

		return $urls;
	}
}

?>