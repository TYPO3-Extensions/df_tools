<?php

namespace SGalinski\DfTools\Connector;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <stefan.galinski@gmail.com>
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Core\Bootstrap;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\ExtensionService;

/**
 * Utility class to simplify the execution of extbase actions from external sources (e.g. from Ext.Direct)
 */
class ExtBaseConnector implements SingletonInterface {
	/**
	 * Extension Key
	 *
	 * @var string
	 */
	protected $extensionKey = '';

	/**
	 * Module Key
	 *
	 * @var string
	 */
	protected $moduleOrPluginKey = '';

	/**
	 * ExtBase Bootstrap Instance
	 *
	 * @var \TYPO3\CMS\Extbase\Core\Bootstrap
	 */
	protected $bootStrap = NULL;

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

	/**
	 * Initializes the instance
	 */
	public function __construct() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

		/** @var $bootStrap Bootstrap */
		$bootStrap = $this->objectManager->create('TYPO3\CMS\Extbase\Core\Bootstrap');
		$this->injectBootstrap($bootStrap);
	}

	/**
	 * Initialize the bootstrap
	 *
	 * @param Bootstrap $bootStrap
	 * @return void
	 */
	public function injectBootStrap(Bootstrap $bootStrap) {
		$this->bootStrap = $bootStrap;
	}

	/**
	 * Setter for the extension key
	 *
	 * @param string $extensionKey
	 * @return void
	 */
	public function setExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;
	}

	/**
	 * Getter for the extension key
	 *
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->extensionKey;
	}

	/**
	 * Setter for the module or plugin key
	 *
	 * @param string $moduleOrPluginKey
	 * @return void
	 */
	public function setModuleOrPluginKey($moduleOrPluginKey) {
		$this->moduleOrPluginKey = $moduleOrPluginKey;
	}

	/**
	 * Getter for the module or plugin key
	 *
	 * @return string
	 */
	public function getModuleOrPluginKey() {
		return $this->moduleOrPluginKey;
	}

	/**
	 * Sets the parameters for the configured module/plugin
	 *
	 * @param array $parameters
	 * @return void
	 */
	public function setParameters(array $parameters) {
		/** @var $extensionService ExtensionService */
		$extensionService = $this->objectManager->get('TYPO3\CMS\Extbase\Service\ExtensionService');
		$parameterNamespace = $extensionService->getPluginNamespace(
			$this->extensionKey,
			$this->moduleOrPluginKey
		);

		$_POST[$parameterNamespace] = $parameters;
	}

	/**
	 * Runs the given ExtBase configuration and returns the result
	 *
	 * @param string $controller
	 * @param string $action
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function runControllerAction($controller, $action) {
		if ($controller === '' || $action === '') {
			throw new \InvalidArgumentException('Invalid Controller/Action Combination!');
		}

		$response = $this->bootStrap->run(
			'', array(
				'extensionName' => $this->extensionKey,
				'pluginName' => $this->moduleOrPluginKey,
				'vendorName' => 'SGalinski',
				'switchableControllerActions' => array(
					$controller => array($action)
				),
			)
		);

		return $response;
	}
}

?>