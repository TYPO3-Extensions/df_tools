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
 * Abstract Task For The TYPO3 Scheduler Extension
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Task_AbstractTask extends tx_scheduler_Task {
	/**
	 * @var string
	 */
	protected $notificationEmailAddress = '';

	/**
	 * Returns an initialized extbase connector
	 *
	 * @return Tx_DfTools_Service_ExtBaseConnectorService
	 */
	protected function getExtBaseConnector() {
			// this must be set for CronJobs or ExtBase will fail
		$_SERVER['REQUEST_METHOD'] = 'GET';

		/** @var $extBaseConnector Tx_DfTools_Service_ExtBaseConnectorService */
		$extBaseConnector = t3lib_div::makeInstance('Tx_DfTools_Service_ExtBaseConnectorService');
		$extBaseConnector->setExtensionKey('DfTools');
		$extBaseConnector->setModuleOrPluginKey('tools_DfToolsTools');

		return $extBaseConnector;
	}

	/**
	 * Sets the notification email address
	 *
	 * @param string $notificationEmailAddress
	 * @return void
	 */
	public function setNotificationEmailAddress($notificationEmailAddress) {
		$this->notificationEmailAddress = $notificationEmailAddress;
	}

	/**
	 * Returns the notification email address
	 *
	 * @return string
	 */
	public function getNotificationEmailAddress() {
		return $this->notificationEmailAddress;
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
	 * Checks the test results and sends a mail with the non-working
	 *
	 * @param array $testResults
	 * @return boolean always TRUE
	 */
	protected function checkTestResults(array $testResults) {
		$notifySeverities = array(
			Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION,
			Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR,
			Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_WARNING,
		);

		$failedRecords = array();
		foreach ($testResults as $testResult) {
			if (in_array($testResult['testResult'], $notifySeverities)) {
				$failedRecords[] = $testResult;
			}
		}

		if (count($failedRecords)) {
			$this->sendNotificationEmail($failedRecords);
		}

		return TRUE;
	}

	/**
	 * Returns the notification email addresses as an array for the swift mailer.
	 * The notification email addresses string must be saved as a comma-separated
	 * list with simple email addresses.
	 *
	 * @return array
	 */
	protected function getNotificationEmailAddressesForSwift() {
		$sendTo = array();
		$emailAddresses = explode(',', $this->getNotificationEmailAddress());
		foreach ($emailAddresses as $emailAddress) {
			$sendTo[$emailAddress] = NULL;
		}

		return $sendTo;
	}

	/**
	 * Sends a notification mail with the given subject and message
	 *
	 * @param string $subject
	 * @param string $message
	 * @return void
	 */
	protected function sendMail($subject, $message) {
		/** @var $mail t3lib_mail_Message */
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');

		/** @noinspection PhpUndefinedMethodInspection */
		$mail->setFrom(t3lib_utility_Mail::getSystemFrom())
			->setTo($this->getNotificationEmailAddressesForSwift())
			->setSubject($subject)
			->setBody($message)
			->send();
	}

	/**
	 * Sends a notification email about the failed tests
	 *
	 * @param array $failedRecords
	 * @return void
	 */
	abstract protected function sendNotificationEmail(array $failedRecords);
}

?>