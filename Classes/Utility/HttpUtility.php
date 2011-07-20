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
 * Collection of smaller http utility functions
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
final class Tx_DfTools_Utility_HttpUtility {
	/**
	 * Prefixes a string with the current host if it starts with a slash!
	 *
	 * Note:
	 * The method t3lib_div::locationHeaderUrl does the same, but it's much slower and
	 * doesn't work with installation in sub-directories and if we must handle AJAX or Scheduler
	 * requests.
	 *
	 * @static
	 * @param string $string
	 * @return string
	 */
	public static function prefixStringWithCurrentHost($string) {
		if ($string{0} === '/') {
			if (trim(t3lib_div::getIndpEnv('HTTP_HOST')) !== '') {
				$locationUrl = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
			} else {
				if (!is_object($GLOBALS['TSFE']) || !is_array($GLOBALS['TSFE']->config)) {
					self::initTSFE();
				}
				
				if (trim($GLOBALS['TSFE']->baseUrl) !== '') {
					$locationUrl = $GLOBALS['TSFE']->baseUrl;
				} elseif (trim($GLOBALS['TSFE']->absRefPrefix) !== '') {
					$locationUrl = $GLOBALS['TSFE']->absRefPrefix;
				} else {
					throw new RuntimeException('The current site url could not be determined!');
				}
			}

			$string = trim($locationUrl, ' /') . $string;
		}

		return $string;
	}

	/**
	 * Initializes the TSFE object to fetch the configured location url
	 *
	 * Note: Only useful in AJAX or Scheduler environments!
	 *
	 * @static
	 * @return void
	 */
	protected static function initTSFE() {
		$GLOBALS['TT'] = new t3lib_timeTrackNull;
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
		TSpagegen::pagegenInit();
	}
}

?>