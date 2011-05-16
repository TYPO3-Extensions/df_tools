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
 * Abstract View Helper
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_ViewHelpers_AbstractViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {
	/**
	 * Page Renderer
	 *
	 * @var t3lib_PageRenderer
	 */
	protected $pageRenderer = NULL;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		if (TYPO3_MODE === 'BE') {
			$this->injectPageRenderer($this->getDocInstance()->getPageRenderer());
		} else {
			$this->injectPageRenderer($GLOBALS['TSFE']->getPageRenderer());
		}
	}

	/**
	 * Sets the page renderer
	 *
	 * @param t3lib_PageRenderer $pageRenderer
	 * @return void
	 */
	public function injectPageRenderer(t3lib_PageRenderer $pageRenderer) {
		$this->pageRenderer = $pageRenderer;
	}
}

?>