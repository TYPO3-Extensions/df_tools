<?php

namespace SGalinski\DfTools\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Dbal\Database\DatabaseConnection;

/**
 * Collection of smaller page utility functions
 */
final class PageUtility {
	/**
	 * Fetches the page id given by the table name and id pair and calculates the
	 * resulting view link that is returned afterwards.
	 *
	 * @param string $tableName
	 * @param int $identifier
	 * @return string
	 */
	public static function getViewLinkFromTableNameAndIdPair($tableName, $identifier) {
		$pageId = $identifier;
		if ($tableName !== 'pages') {
			/** @var $dbConnection DatabaseConnection */
			$dbConnection = $GLOBALS['TYPO3_DB'];
			$record = $dbConnection->exec_SELECTgetSingleRow(
				'pid',
				$tableName,
				'uid = ' . intval($identifier)
			);

			if ($record !== NULL) {
				$pageId = $record['pid'];
			}
		}

		$javascriptLink = BackendUtility::viewOnClick($pageId);
		preg_match('/window\.open\(\'([^\']+)\'/i', $javascriptLink, $match);

		return $match[1];
	}
}

?>