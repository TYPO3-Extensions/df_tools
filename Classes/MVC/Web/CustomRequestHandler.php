<?php

namespace SGalinski\DfTools\MVC\Web;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Stefan Galinski <sgalinski@df.eu>
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

use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Response;
use TYPO3\CMS\Extbase\Mvc\Web\AbstractRequestHandler;
use TYPO3\CMS\Extbase\Security\Channel\RequestHashService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Custom request handler that circumvents the cli mode issues
 */
class CustomRequestHandler extends AbstractRequestHandler {

	/**
	 * @var ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @param ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Handles the web request. The response will automatically be sent to the client.
	 *
	 * @return Response
	 */
	public function handleRequest() {
		$request = $this->requestBuilder->build();

		/** @var $requestHashService RequestHashService */
		$requestHashService = $this->objectManager->get('TYPO3\CMS\Extbase\Security\Channel\RequestHashService');
		$requestHashService->verifyRequest($request);

		if (TYPO3_MODE === 'FE') {
			if ($this->isCacheable($request->getControllerName(), $request->getControllerActionName())) {
				$request->setIsCached(TRUE);
			} else {
				$contentObject = $this->configurationManager->getContentObject();
				if ($contentObject->getUserObjectType() === ContentObjectRenderer::OBJECTTYPE_USER) {
					$contentObject->convertToUserIntObject();

					// @TODO implement the integration of adding cached header data in a nice way (see EXT:rs_fetsy)

					// tslib_cObj::convertToUserIntObject() will recreate the object, so we have to stop the request here
					return NULL;
				}
				$request->setIsCached(FALSE);
			}
		}

		/** @var $response Response */
		$response = $this->objectManager->get('SGalinski\DfTools\Response\CustomResponse');
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
		if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 4006000) {
			/** @var $extensionService ExtensionService */
			$extensionService = $this->objectManager->get('TYPO3\CMS\Extbase\Service\ExtensionService');
			$isCachable = $extensionService->isActionCacheable(NULL, NULL, $controllerName, $actionName);

		} else {
			$frameworkConfiguration = $this->configurationManager->getConfiguration(
				ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
			);
			if (isset($frameworkConfiguration['controllerConfiguration'][$controllerName]['nonCacheableActions'])
				&& in_array(
					$actionName,
					$frameworkConfiguration['controllerConfiguration'][$controllerName]['nonCacheableActions']
				)
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
		$configuration = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
		);
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