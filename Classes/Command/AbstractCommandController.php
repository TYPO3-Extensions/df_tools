<?php

namespace SGalinski\DfTools\Command;

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

use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\UrlChecker\Factory;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Abstract Command Controller
 */
abstract class AbstractCommandController extends CommandController {
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

	/**
	 * Converts html line breaks to newlines
	 *
	 * @param string $input
	 * @param string $additionalReplacement
	 * @return mixed
	 */
	protected function br2nl($input, $additionalReplacement = '') {
		return preg_replace('/<br\s?\/?>/is', PHP_EOL . $additionalReplacement, $input);
	}

	/**
	 * Checks the test results and sends a mail with the non-working tests
	 *
	 * @param QueryResultInterface $testResults
	 * @return array
	 */
	protected function checkTestResults(QueryResultInterface $testResults) {
		$notifySeverities = array(
			AbstractService::SEVERITY_EXCEPTION,
			AbstractService::SEVERITY_ERROR,
			AbstractService::SEVERITY_WARNING,
		);

		/** @var $testResult RedirectTest */
		$failedRecords = array();
		foreach ($testResults as $testResult) {
			if (in_array($testResult->getTestResult(), $notifySeverities)) {
				$failedRecords[] = $testResult;
			}
		}

		return $failedRecords;
	}

	/**
	 * Sends a notification mail with the given subject and message
	 *
	 * @param string $subject
	 * @param string $message
	 * @param string $emailAddresses comma-separated list of emails
	 * @return void
	 */
	protected function sendMail($subject, $message, $emailAddresses) {
		/** @var $mail MailMessage */
		$mail = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');

		$emailAddresses = GeneralUtility::trimExplode(',', $emailAddresses);
		foreach ($emailAddresses as $emailAddress) {
			$sendTo[$emailAddress] = NULL;
		}

		/** @noinspection PhpUndefinedMethodInspection */
		$mail->setFrom(MailUtility::getSystemFrom())
			->setTo($emailAddresses)
			->setSubject($subject)
			->setBody($message)
			->send();
	}
}

?>