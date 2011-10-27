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
 * Abstract View For The Rendering of Plain Records (array types of records)
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_View_AbstractArrayView extends Tx_Extbase_MVC_View_AbstractView {
	/**
	 * @var Tx_Extbase_Security_Channel_RequestHashService
	 */
	protected $requestHashService;

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initializeObject();
	}

	/**
	 * Injects the object manager
	 *
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Initializes the object
	 *
	 * @return void
	 */
	protected function initializeObject() {
		/** @var $requestHashService Tx_Extbase_Security_Channel_RequestHashService */
		$requestHashService = t3lib_div::makeInstance('Tx_Extbase_Security_Channel_RequestHashService');

		/** @var $hashService Tx_Extbase_Security_Cryptography_HashService */
		$hashService = t3lib_div::makeInstance('Tx_Extbase_Security_Cryptography_HashService');
		$requestHashService->injectHashService($hashService);

		$this->injectRequestHashService($requestHashService);
	}

	/**
	 * Inject a request hash service
	 *
	 * @param Tx_Extbase_Security_Channel_RequestHashService $requestHashService
	 * @return void
	 */
	public function injectRequestHashService(Tx_Extbase_Security_Channel_RequestHashService $requestHashService) {
		$this->requestHashService = $requestHashService;
	}

	/**
	 * Returns the current parameter namespace
	 *
	 * @return string
	 */
	protected function getNamespace() {
		$request = $this->controllerContext->getRequest();
		$extensionName = $request->getControllerExtensionName();
		$pluginName = $request->getPluginName();

		if (t3lib_div::compat_version('4.6')) {
			/** @var $extensionService Tx_Extbase_Service_ExtensionService */
			$extensionService = $this->objectManager->get('Tx_Extbase_Service_ExtensionService');
			$namespace = $extensionService->getPluginNamespace($extensionName, $pluginName);
		} else {
			$namespace = Tx_Extbase_Utility_Extension::getPluginNamespace($extensionName, $pluginName);
		}

		return $namespace;
	}

	/**
	 * Returns the request hash for the given field names
	 *
	 * @param array $fieldNames
	 * @return string
	 */
	protected function getDataHash(array $fieldNames) {
		return $this->requestHashService->generateRequestHash($fieldNames, $this->getNamespace());
	}

	/**
	 * Renders the records data as a plain array
	 *
	 * @return array
	 */
	public function render() {
		$data = array();
		foreach ($this->variables['records'] as $record) {
			$data[] = $this->getPlainRecord($record);
		}

		if (isset($this->variables['totalRecords'])) {
			$total = $this->variables['totalRecords'];
		} else {
			$total = count($data);
		}

		$fieldConfiguration = $this->getHmacFieldConfiguration();
		return array(
			'__hmac' => array(
				'update' => $this->getDataHash($fieldConfiguration['update']),
				'create' => $this->getDataHash($fieldConfiguration['create']),
			),
			'records' => $data,
			'total' => $total,
		);
	}

	/**
	 * Must return the plain array of a given domain object
	 *
	 * @abstract
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $record
	 * @return void
	 */
	abstract protected function getPlainRecord($record);

	/**
	 * Must return the field configurations for the update and create CRUD actions (HMAC support)
	 *
	 * @abstract
	 * @return array
	 */
	abstract protected function getHmacFieldConfiguration();
}

?>