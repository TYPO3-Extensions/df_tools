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
 * Scheduler task to execute the back link tests
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Task_BackLinkTestTask extends Tx_DfTools_Task_AbstractTask {
	/**
	 * Calls the ExtBase controller to execute the tests
	 *
	 * @return boolean
	 */
	public function execute() {
		$plainRecords = $this->getExtBaseConnector()->runControllerAction('BackLinkTest', 'runAllTests');
		return $this->checkTestResults($plainRecords['records']);
	}

	/**
	 * Sends a notification email about the failed tests
	 *
	 * @param array $failedRecords
	 * @return void
	 */
	protected function sendNotificationEmail(array $failedRecords) {
		$sLPrefix = 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:';
		$subject = $GLOBALS['LANG']->sL($sLPrefix . 'tx_dftools_domain_model_backlinktest.scheduler.mailSubject');
		$message = $GLOBALS['LANG']->sL($sLPrefix . 'tx_dftools_domain_model_backlinktest.scheduler.mailBody') .
			PHP_EOL . PHP_EOL;

		foreach ($failedRecords as $failedRecord) {
			$message .= $failedRecord['testUrl'] . PHP_EOL . PHP_EOL;
			$message .= "\t" . $this->br2nl(htmlspecialchars_decode($failedRecord['testMessage']), "\t");
			$message .= PHP_EOL . PHP_EOL;
		}

		$this->sendMail($subject, $message);
	}
}

?>