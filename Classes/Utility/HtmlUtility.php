<?php

namespace SGalinski\DfTools\Utility;

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
 * Collection of smaller html utility functions
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
final class HtmlUtility {
	/**
	 * Fetches all content parts between the html comments
	 * <!--TYPO3SEARCH_begin--> and <!--TYPO3SEARCH_end-->
	 *
	 * @param string $content
	 * @return array
	 */
	public static function getTypo3SearchBlocksFromContent($content) {
		$searchParts = array();
		$parts = explode('<!--TYPO3SEARCH_begin-->', $content);
		if (count($parts) === 1) {
			return array(trim($content));
		}

		array_shift($parts);
		foreach ($parts as $part) {
			$finalParts = explode('<!--TYPO3SEARCH_end-->', $part);
			array_pop($finalParts);
			foreach ($finalParts as $innerPart) {
				$searchParts[] = trim($innerPart);
			}
		}

		return $searchParts;
	}
}

?>