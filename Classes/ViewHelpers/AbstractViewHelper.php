<?php

namespace SGalinski\DfTools\ViewHelpers;

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

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Abstract View Helper
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class AbstractViewHelper extends AbstractBackendViewHelper {
	/**
	 * Returns an instance of the page renderer
	 *
	 * @return PageRenderer
	 */
	public function getPageRenderer() {
		/** @var $tsfe TypoScriptFrontendController */
		$tsfe = $GLOBALS['TSFE'];
		if (TYPO3_MODE === 'BE') {
			$pageRenderer = $this->getDocInstance()->getPageRenderer();
		} else {
			$pageRenderer = $tsfe->getPageRenderer();
		}

		return $pageRenderer;
	}

	/**
	 * Returns the base url of the site
	 *
	 * Note: Works only in frontend mode
	 *
	 * @return string
	 */
	public function getBaseUrl() {
		if ($GLOBALS['TSFE']->absRefPrefix !== '') {
			$baseUrl = $GLOBALS['TSFE']->absRefPrefix;
		} else {
			$baseUrl = $GLOBALS['TSFE']->baseUrl;
		}

		return $baseUrl;
	}
}

?>