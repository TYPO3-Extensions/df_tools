<?php

namespace SGalinski\DfTools\View;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\View\AbstractView;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Security\Channel\RequestHashService;
use TYPO3\CMS\Extbase\Security\Cryptography\HashService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Abstract View For The Rendering of Plain Records (array types of records)
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class AbstractArrayView extends AbstractView {
	/**
	 * @var \TYPO3\CMS\Extbase\Security\Channel\RequestHashService
	 */
	protected $requestHashService;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
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
	 * @param ObjectManager $objectManager
	 * @return void
	 */
	public function injectObjectManager(ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Initializes the object
	 *
	 * @return void
	 */
	protected function initializeObject() {
		/** @var $requestHashService RequestHashService */
		$requestHashService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Security\Channel\RequestHashService');

		/** @var $hashService HashService */
		$hashService = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Security\Cryptography\HashService');
		$requestHashService->injectHashService($hashService);

		$this->injectRequestHashService($requestHashService);
	}

	/**
	 * Inject a request hash service
	 *
	 * @param RequestHashService $requestHashService
	 * @return void
	 */
	public function injectRequestHashService(RequestHashService $requestHashService) {
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

		/** @var $extensionService ExtensionService */
		$extensionService = $this->objectManager->get('TYPO3\CMS\Extbase\Service\ExtensionService');
		return $extensionService->getPluginNamespace($extensionName, $pluginName);
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
	 * @param AbstractDomainObject $record
	 * @return void|array
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