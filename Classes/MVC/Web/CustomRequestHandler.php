<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Jochen Rau <jochen.rau@typoplanet.de>
 *  All rights reserved
 *
 *  This class is a backport of the corresponding class of FLOW3.
 *  All credits go to the v5 team.
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Custom request handler that circumvents the cli mode issues
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_MVC_Web_CustomRequestHandler extends Tx_Extbase_MVC_Web_AbstractRequestHandler {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Handles the web request. The response will automatically be sent to the client.
	 *
	 * @return Tx_Extbase_MVC_Web_Response
	 */
	public function handleRequest() {
		$request = $this->requestBuilder->build();

		/** @var $requestHashService Tx_Extbase_Security_Channel_RequestHashService */
		$requestHashService = $this->objectManager->get('Tx_Extbase_Security_Channel_RequestHashService');
		$requestHashService->verifyRequest($request);

		if (TYPO3_MODE === 'FE') {
			if ($this->isCacheable($request->getControllerName(), $request->getControllerActionName())) {
				$request->setIsCached(TRUE);
			} else {
				$contentObject = $this->configurationManager->getContentObject();
				if ($contentObject->getUserObjectType() === tslib_cObj::OBJECTTYPE_USER) {
					$contentObject->convertToUserIntObject();

					// @TODO implement the integration of adding cached header data in a nice way (see EXT:rs_fetsy)

					// tslib_cObj::convertToUserIntObject() will recreate the object, so we have to stop the request here
					return NULL;
				}
				$request->setIsCached(FALSE);
			}
		}

		/** @var $response Tx_Extbase_MVC_Web_Response */
		$response = $this->objectManager->create('Tx_DfTools_Response_CustomResponse');
		$this->dispatcher->dispatch($request, $response);

		return $response;
	}

	/**
	 * Determines whether the current action can be cached
	 *
	 * @param string $controllerName
	 * @param string $actionName
	 * @return boolean TRUE if the given action should be cached, otherwise FALSE
	 */
	protected function isCacheable($controllerName, $actionName) {
		$isCachable = TRUE;
		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= 4006000) {
			$extensionService = $this->objectManager->get('Tx_Extbase_Service_ExtensionService');
			$isCachable = $extensionService->isActionCacheable(NULL, NULL, $controllerName, $actionName);

		} else {
			$frameworkConfiguration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
			if (isset($frameworkConfiguration['controllerConfiguration'][$controllerName]['nonCacheableActions'])
				&& in_array($actionName, $frameworkConfiguration['controllerConfiguration'][$controllerName]['nonCacheableActions'])
			) {
				$isCachable = FALSE;
			}
		}

		return $isCachable;
	}

	/**
	 * This request handler can handle any web request.
	 *
	 * @return boolean If the request is a web request, TRUE otherwise FALSE
	 */
	public function canHandleRequest() {
		$configuration = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		return $configuration['extensionName'] === 'DfTools';
	}

	/**
	 * Returns an high priority to be the preferred for our specific case
	 *
	 * @return integer The priority of the request handler.
	 */
	public function getPriority() {
		return 1000;
	}
}

?>