<?php

namespace SGalinski\DfTools\View;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Mvc\View\AbstractView;
use TYPO3\CMS\Extbase\Service\ExtensionService;

/**
 * Abstract View For The Rendering of Plain Records (array types of records)
 */
abstract class AbstractArrayView extends AbstractView {
	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfigurationService
	 */
	protected $mvcPropertyMappingConfigurationService;

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

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
		return $this->mvcPropertyMappingConfigurationService->generateTrustedPropertiesToken(
			$fieldNames, $this->getNamespace()
		);
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
			'__trustedProperties' => array(
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