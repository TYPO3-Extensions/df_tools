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
 * Abstract Controller
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Controller_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {
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
	 * @throws RuntimeException
	 * @return void
	 */
	public function errorAction() {
		$message = Tx_Extbase_Utility_Localization::translate(
			'tx_dftools_common.generic',
			'df_tools',
			array(get_class($this), $this->actionMethodName)
		);

		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= 4006000  &&
			$this->configurationManager->isFeatureEnabled('rewrittenPropertyMapper')
		) {
			foreach ($this->arguments->getValidationResults()->getFlattenedErrors() as $errors) {
				/** @var $error Tx_Extbase_Error_Error */
				foreach ($errors as $error) {
					$message .= '<strong>' .
						Tx_Extbase_Utility_Localization::translate('tx_dftools_common.error', 'df_tools') .
						':</strong>   ' . $error->getMessage() . '<br />';
				}
			}

		} else {
			/** @var $error Tx_Extbase_Error_Error */
			foreach ((array) $this->argumentsMappingResults->getErrors() as $error) {
				$message .= '<strong>' .
					Tx_Extbase_Utility_Localization::translate('tx_dftools_common.error', 'df_tools') .
					':</strong>   ' . $error->getMessage() . '<br />';
			}

			/** @var $warning Tx_Extbase_Error_Error */
			foreach ((array) $this->argumentsMappingResults->getWarnings() as $warning) {
				$message .= '<strong>' .
					Tx_Extbase_Utility_Localization::translate('tx_dftools_common.warning', 'df_tools') .
					':</strong> ' . $warning . '<br />';
			}
		}

		throw new RuntimeException($message);
	}

	/**
	 * Saves the the last called controller/action pair into the backend user
	 * configuration if available
	 *
	 * @return void
	 */
	public function setLastCalledControllerActionPair() {
		if (!$GLOBALS['BE_USER']) {
			return;
		}

		$GLOBALS['BE_USER']->uc['DfToolsState']['LastActionControllerPair'] = array(
			$this->request->getControllerActionName(),
			$this->request->getControllerName()
		);
		$GLOBALS['BE_USER']->writeUC($GLOBALS['BE_USER']->uc);
	}

	/**
	 * Resets the last called controller/action pair combination from the
	 * backend user session
	 *
	 * @return void
	 */
	public function resetLastCalledControllerActionPair() {
		if (!$GLOBALS['BE_USER']) {
			return;
		}

		$GLOBALS['BE_USER']->uc['DfToolsState']['LastActionControllerPair'] = array();
		$GLOBALS['BE_USER']->writeUC($GLOBALS['BE_USER']->uc);
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
	 * @throws RuntimeException if an exceptional error happened while the test
	 * @param Tx_DfTools_Domain_Model_TestableInterface $record
	 * @return void
	 */
	protected function handleExceptionalTest(Tx_DfTools_Domain_Model_TestableInterface $record) {
		if ($record->getTestResult() === Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION) {
			/** @var $persistenceManager Tx_Extbase_Persistence_Manager */
			$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();

			$message = $record->getTestMessage();
			throw new RuntimeException($message, Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);
		}
	}

	/**
	 * Returns an url checker instance
	 *
	 * @return Tx_DfTools_Service_UrlChecker_AbstractService
	 */
	protected function getUrlCheckerService() {
		/** @var $factory Tx_DfTools_Service_UrlChecker_Factory */
		$factory = $this->objectManager->get('Tx_DfTools_Service_UrlChecker_Factory');
		return $factory->get();
	}
}

?>