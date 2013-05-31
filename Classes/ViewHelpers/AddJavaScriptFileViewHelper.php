<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

namespace SGalinski\DfTools\ViewHelpers;

/**
 * View helper to add custom javascript files
 *
 * Example:
 * {namespace rs=Tx_DfTools_ViewHelpers}
 * <rs:addCssFile cssFile="{f:uri.resource(path: 'Scripts/dfpdm.js')}" />
 */
class AddJavaScriptFileViewHelper extends AbstractViewHelper {
	/**
	 * Adds a custom javascript file
	 *
	 * @param string $javaScriptFile
	 * @return void
	 */
	public function render($javaScriptFile) {
		$javaScriptFile = (TYPO3_MODE === 'FE' ? $this->getBaseUrl() : '') . $javaScriptFile;
		$this->getPageRenderer()->addJsFile($javaScriptFile);
	}
}

?>