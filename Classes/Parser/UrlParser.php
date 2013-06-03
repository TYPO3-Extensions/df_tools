<?php

namespace SGalinski\DfTools\Parser;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan.galinski@gmail.com>
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

use SGalinski\DfTools\Utility\TcaUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Url Parser
 *
 * Fetches all urls defined inside the database. Needs an instance of the tca parser
 * to work correctly.
 */
class UrlParser implements SingletonInterface {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Parser\TcaParser
	 */
	protected $tcaParser = NULL;

	/**
	 * @var \TYPO3\CMS\Frontend\Page\PageRepository
	 */
	protected $pageSelect = NULL;

	/**
	 * Returns an instance of t3lib_pageSelect to call the enableFields method
	 * for self-made queries.
	 *
	 * @return PageRepository
	 */
	protected function getPageSelectInstance() {
		if ($this->pageSelect === NULL) {
			$this->pageSelect = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		}

		return $this->pageSelect;
	}

	/**
	 * Fetches all URLs from the database
	 *
	 * @param array $excludedTables
	 * @param array $excludedTableFields
	 * @return array
	 */
	public function fetchUrls(array $excludedTables = array(), array $excludedTableFields = array()) {
		$tablesWithFields = TcaUtility::getTextFields($this->tcaParser, $excludedTables, $excludedTableFields);

		$urls = array();
		foreach ((array) $tablesWithFields as $table => $fields) {
			$fetchedUrls = $this->fetchUrlsFromDatabase($table, $fields);
			foreach ($fetchedUrls as $url => $data) {
				$urls[$url] = array_merge((array) $urls[$url], $data);
			}
		}

		if (isset($tablesWithFields['pages'])) {
			$fetchedUrls = $this->fetchLinkCheckLinkType();
			foreach ($fetchedUrls as $url => $data) {
				$urls[$url] = array_merge((array) $urls[$url], $data);
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
			$whereClause[] = $field . ' REGEXP \'(https|http|ftp)://\'';
		}

		$enableFields = $this->getPageSelectInstance()->enableFields(
			$table, 1, array('starttime' => TRUE, 'fe_group' => TRUE)
		);
		/** @var $dbConnection DatabaseConnection */
		$dbConnection = $GLOBALS['TYPO3_DB'];

		$rows = $dbConnection->exec_SELECTgetRows(
			'uid, ' . implode(', ', $fields),
			$table,
			'(' . implode(' OR ', $whereClause) . ')' . $enableFields
		);

		return $this->parseRows($rows, $table);
	}

	/**
	 * Parses the database rows to retrieve the urls
	 *
	 * @param array $rows
	 * @param string $table
	 * @return array $array[<url>][<table><uid>] = array(<table>, <uid>)
	 */
	public function parseRows(array $rows, $table) {
		$urls = array();
		foreach ($rows as $row) {
			$foundUrls = array();
			$regularExpression =
				'#((https?|ftp)://[\w\d:\#\!@%/;\$\(\)~\_\?\+\-=&]+\.[\w\d:\#\!@%/;\$\(\)~\_\?\+\-=&\.]+)#is';
			foreach ($row as $field => $value) {
				if ($field === 'uid') {
					continue;
				}

				$matches = array();
				$value = htmlentities($value, ENT_NOQUOTES, 'UTF-8', FALSE);
				$value = str_replace(array('&lt;', '&gt;'), array('<', '>'), $value);
				preg_match_all($regularExpression, $value, $matches);
				$foundUrls[$field] = $matches;
			}

			foreach ($foundUrls as $field => $matches) {
				$length = count($matches[1]);
				for ($i = 0; $i < $length; ++$i) {
					$url = html_entity_decode($matches[1][$i], ENT_NOQUOTES, 'UTF-8');

					$trimList = '.';
					if (strpos($url, ')') !== FALSE) {
						$characterMap = count_chars($url);
						if ($characterMap[ord('(')] !== $characterMap[ord(')')]) {
							$trimList .= ')';
						}
					}
					$url = trim($url, $trimList);

					list($url, $anchor) = explode('#', $url, 2);
					if ($anchor{0} === '!') {
						$url .= '#' . $anchor;
					}

					$urls[$url][$table . $field . $row['uid']] = array($table, $field, $row['uid']);
				}
			}
		}

		return $urls;
	}

	/**
	 * Merges the "urltype" and "url" fields into a single url and returns the valid ones afterwards
	 *
	 * @param array $rows
	 * @return array
	 */
	protected function prepareUrlsFromPagesRows(array $rows) {
		$urls = array();
		foreach ($rows as $row) {
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

			$urls[$row['url']]['pages' . 'url' . $row['uid']] = array('pages', 'url', $row['uid']);
		}

		return $urls;
	}

	/**
	 * Returns all urls from the link check type of the table pages if they are
	 * configured to be prefixed by the urlType field.
	 *
	 * @param array|NULL $identities
	 * @return array $array[<url>][<table><uid>] = array(<table>, <uid>)
	 */
	public function fetchLinkCheckLinkType($identities = NULL) {
		$enableFields = $this->getPageSelectInstance()->enableFields(
			'pages', 1, array('starttime' => TRUE, 'fe_group' => TRUE)
		);

		$pageFilter = '';
		if ($identities !== NULL) {
			$pageFilter = ' AND uid IN (' . implode(', ', $identities) . ')';
		}

		/** @var $dbConnection DatabaseConnection */
		$dbConnection = $GLOBALS['TYPO3_DB'];
		$rows = $dbConnection->exec_SELECTgetRows(
			'uid, url, urltype',
			'pages',
			'doktype = 3 && urltype != 3 && urltype != 0' . $enableFields . $pageFilter
		);

		return $this->prepareUrlsFromPagesRows($rows);
	}
}

?>