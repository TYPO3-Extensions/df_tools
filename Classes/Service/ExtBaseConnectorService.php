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
 * Utility class to simplify the execution of extbase actions from external sources (e.g. from Ext.Direct)
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_ExtBaseConnectorService implements t3lib_Singleton {
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
	 * @var Tx_Extbase_Core_Bootstrap
	 */
	protected $bootStrap = NULL;

	/**
	 * Initializes the instance
	 *
	 * @return void
	 */
	public function __construct() {
		/** @var $bootStrap Tx_Extbase_Core_Bootstrap */
		$bootStrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
		$this->injectBootstrap($bootStrap);
	}

	/**
	 * Initialize the bootstrap
	 *
	 * @param Tx_Extbase_Core_Bootstrap $bootStrap
	 * @return void
	 */
	public function injectBootStrap(Tx_Extbase_Core_Bootstrap $bootStrap) {
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
		$parameterNamespace = Tx_Extbase_Utility_Extension::getPluginNamespace(
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
	 * @throws InvalidArgumentException
	 * @return array
	 */
	public function runControllerAction($controller, $action) {
		if ($controller === '' || $action === '') {
			throw new InvalidArgumentException('ExtDirect (Tx_DfTools): Invalid Controller/Action Combination!');
		}

		$response = $this->bootStrap->run('', array(
			'extensionName' => $this->extensionKey,
			'pluginName' => $this->moduleOrPluginKey,
			'switchableControllerActions' => array(
				$controller => array($action)
			),
		));

		return $response;
	}
}

?>