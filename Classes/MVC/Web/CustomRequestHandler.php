<?php

namespace SGalinski\DfTools\MVC\Web;

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

use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Exception;
use TYPO3\CMS\Extbase\Mvc\Response;
use TYPO3\CMS\Extbase\Mvc\Web\AbstractRequestHandler;
use TYPO3\CMS\Extbase\Security\Channel\RequestHashService;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Custom request handler that circumvents the cli mode issues
 */
class CustomRequestHandler extends AbstractRequestHandler {

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Service\ExtensionService
	 */
	protected $extensionService;

	/**
	 * Handles the web request. The response will automatically be sent to the client.
	 *
	 * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
	 * @return Response
	 */
	public function handleRequest() {
		$request = $this->requestBuilder->build();

		/** @var $requestHashService RequestHashService */
		$requestHashService = $this->objectManager->get('TYPO3\CMS\Extbase\Security\Channel\RequestHashService');
		$requestHashService->verifyRequest($request);

		if ($this->environmentService->isEnvironmentInFrontendMode()) {
			$isCachable = $this->extensionService->isActionCacheable(
				NULL, NULL, $request->getControllerName(), $request->getControllerActionName()
			);
			if ($isCachable) {
				$request->setIsCached(TRUE);
			} else {
				$contentObject = $this->configurationManager->getContentObject();
				if ($contentObject->getUserObjectType() === ContentObjectRenderer::OBJECTTYPE_USER) {
					$contentObject->convertToUserIntObject();
					return NULL;
				}
				$request->setIsCached(FALSE);
			}
		}

		/** @var $response Response */
		$response = $this->objectManager->get('SGalinski\DfTools\Response\CustomResponse');
		try {
			$this->dispatcher->dispatch($request, $response);
		} catch (Exception $exception) {
			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['displayErrors'] <= 0) {
				/** @var $tsfe TypoScriptFrontendController */
				$tsfe = $GLOBALS['TSFE'];
				$tsfe->pageNotFoundAndExit($exception->getMessage());
			} else {
				throw $exception;
			}
		}

		return $response;
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