<?php
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

/**
 * Factory For The Creation Of An Url Checker Service
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_Factory {
	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * Injects the object manager
	 *
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Returns an instance of the url checker depending on your extension configuration
	 *
	 * @param string $type native (default) or curl
	 * @return Tx_DfTools_Service_UrlChecker_AbstractService
	 */
	public function get($type = 'native') {
		/** @var $instance Tx_DfTools_Service_UrlChecker_AbstractService */
		if ($type === 'curl') {
			$instance = $this->objectManager->get('Tx_DfTools_Service_UrlChecker_CurlService');
		} else {
			$instance = $this->objectManager->get('Tx_DfTools_Service_UrlChecker_StreamService');
		}

		$instance->init();
		return $instance;
	}
}

?>