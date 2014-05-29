<?php

namespace SGalinski\DfTools\Controller;

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

use SGalinski\DfTools\Domain\Model\TestableInterface;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\UrlChecker\Factory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Abstract Controller
 */
abstract class AbstractController extends ActionController {
	/**
	 * @var array
	 */
	protected $extensionConfiguration = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$serializedConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['df_tools'];
		$this->extensionConfiguration = unserialize($serializedConfiguration);

		parent::__construct();
	}

	/**
	 * Error Handler
	 *
	 * @throws \RuntimeException
	 * @return void
	 */
	public function errorAction() {
		$message = parent::errorAction();
		throw new \RuntimeException($message);
	}

	/**
	 * Saves the the last called controller/action pair into the backend user
	 * configuration if available
	 *
	 * @return void
	 */
	public function setLastCalledControllerActionPair() {
		/** @var BackendUserAuthentication $beUser */
		$beUser = $GLOBALS['BE_USER'];
		if (!$beUser) {
			return;
		}

		$beUser->uc['DfToolsState']['LastActionControllerPair'] = array(
			$this->request->getControllerActionName(),
			$this->request->getControllerName()
		);
		$beUser->writeUC($beUser->uc);
	}

	/**
	 * Resets the last called controller/action pair combination from the
	 * backend user session
	 *
	 * @return void
	 */
	public function resetLastCalledControllerActionPair() {
		/** @var BackendUserAuthentication $beUser */
		$beUser = $GLOBALS['BE_USER'];

		if (!$beUser) {
			return;
		}

		$beUser->uc['DfToolsState']['LastActionControllerPair'] = array();
		$beUser->writeUC($GLOBALS['BE_USER']->uc);
	}

	/**
	 * Returns the last called controller/action pair from the backend user session
	 *
	 * @return array
	 */
	public function getLastCalledControllerActionPair() {
		if (!$GLOBALS['BE_USER']) {
			return array();
		}

		$state = $GLOBALS['BE_USER']->uc['DfToolsState']['LastActionControllerPair'];
		if (!is_array($state)) {
			$state = array();
		}

		return $state;
	}

	/**
	 * Redirects to the last called controller/action pair saved inside the
	 * backend user session
	 *
	 * @return void
	 */
	protected function redirectToLastCalledControllerActionPair() {
		$state = $this->getLastCalledControllerActionPair();
		if (is_array($state) && count($state) === 2) {
			list($action, $controller) = $state;
			$this->redirect($action, $controller);
		}
	}

	/**
	 * Checks the test results and throws different kinds of exceptions if required.
	 *
	 * @throws \RuntimeException if an exceptional error happened while the test
	 * @param TestableInterface $record
	 * @return void
	 */
	protected function handleExceptionalTest(TestableInterface $record) {
		if ($record->getTestResult() === AbstractService::SEVERITY_EXCEPTION) {
			/** @var $persistenceManager PersistenceManager */
			$persistenceManager = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager');
			$persistenceManager->persistAll();

			$message = $record->getTestMessage();
			throw new \RuntimeException($message, AbstractService::SEVERITY_EXCEPTION);
		}
	}

	/**
	 * Returns an url checker instance
	 *
	 * @return AbstractService
	 */
	protected function getUrlCheckerService() {
		/** @var $factory Factory */
		$factory = $this->objectManager->get('SGalinski\DfTools\UrlChecker\Factory');
		return $factory->get();
	}
}

?>