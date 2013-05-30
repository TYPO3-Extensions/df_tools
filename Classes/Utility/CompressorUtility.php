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
 * Compressor
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
final class CompressorUtility {
	/**
	 * Compresses the given content if possible
	 *
	 * @param string $content
	 * @return string
	 */
	public static function compressContent($content) {
		$compressionLevel = intval($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['compressionLevel']);
		if ($content !== '' && extension_loaded('zlib') && $compressionLevel) {
			$content = gzcompress($content, $compressionLevel);
		}

		return $content;
	}

	/**
	 * Decompresses the given content if possible
	 *
	 * @param string $content
	 * @return string
	 */
	public static function decompressContent($content) {
		if ($content !== '' && extension_loaded('zlib')) {
			$content = gzuncompress($content);
		}

		return $content;
	}
}

?>