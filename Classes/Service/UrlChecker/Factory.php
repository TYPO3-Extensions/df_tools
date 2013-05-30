<?php

namespace SGalinski\DfTools\Service\UrlChecker;

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

use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Factory For The Creation Of An Url Checker Service
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Factory {
	/**
	 * @var ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * Injects the object manager
	 *
	 * @param ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Returns an instance of the url checker depending on your extension configuration
	 *
	 * @return AbstractService
	 */
	public function get() {
		/** @var $instance AbstractService */
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse']) {
			$instance = $this->objectManager->get('SGalinski\DfTools\Service\UrlChecker\CurlService');
		} else {
			$instance = $this->objectManager->get('SGalinski\DfTools\Service\UrlChecker\StreamService');
		}

		$instance->init();
		return $instance;
	}
}

?>