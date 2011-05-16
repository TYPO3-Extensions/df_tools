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
 * Collection of smaller localization utility functions
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
final class Tx_DfTools_Utility_LocalizationUtility {
	/**
	 * Localizes a given string that contains parameters. The string is splitted by
	 * the following character string: |!|
	 *
	 * @param string $string
	 * @param string $extensionKey
	 * @return string
	 */
	public static function localizeParameterDrivenString($string, $extensionKey) {
		$parts = explode('|!|', $string);
		$label = array_shift($parts);
		$translation = Tx_Extbase_Utility_Localization::translate($label, $extensionKey, $parts);
		if ($translation === '') {
			$translation = $label;
		}
		return $translation;
	}

	/**
	 * Creates a localizable string with attached parameters that can be used with
	 * the static function: localizeParameterDrivenString. The parameters are splitted
	 * by the following character string: |!|
	 *
	 * @see localizeParameterDrivenString
	 * @param string $label
	 * @param array $parameters
	 * @return string
	 */
	public static function createLocalizableParameterDrivenString($label, array $parameters) {
		foreach ($parameters as $parameter) {
			$label .= '|!|' . str_replace('|!|', ' ', $parameter);
		}

		return $label;
	}
}

?>