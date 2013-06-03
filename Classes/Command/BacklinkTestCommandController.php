<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stefan Galinski <stefan.galinski@gmail.com>
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

namespace SGalinski\DfTools\Command;

use SGalinski\DfTools\Domain\Model\BackLinkTest;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Command controller for the backlink test functionality
 */
class BacklinkTestCommandController extends AbstractCommandController {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\BackLinkTestRepository
	 */
	protected $backlinkTestRepository;

	/**
	 * Executes all backlink tests
	 *
	 * @param string $notificationEmailAddresses
	 * @return void
	 */
	public function runAllTestsCommand($notificationEmailAddresses) {
		$backlinkTests = $this->backlinkTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		/** @var $backlinkTest BackLinkTest */
		foreach ($backlinkTests as $backlinkTest) {
			$backlinkTest->test($urlCheckerService);
			$this->backlinkTestRepository->update($backlinkTest);
		}

		$failedRecords = $this->checkTestResults($backlinkTests);
		if (count($failedRecords)) {
			/** @var LanguageService $language */
			$language = $GLOBALS['LANG'];
			$sLPrefix = 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:';
			$subject = $language->sL($sLPrefix . 'tx_dftools_domain_model_backlinktest.scheduler.mailSubject');
			$message = $language->sL($sLPrefix . 'tx_dftools_domain_model_backlinktest.scheduler.mailBody') .
				PHP_EOL . PHP_EOL;

			/** @var $failedRecord BackLinkTest */
			foreach ($failedRecords as $failedRecord) {
				$testMessage = \SGalinski\DfTools\Utility\LocalizationUtility::localizeParameterDrivenString(
					$failedRecord->getTestMessage(), 'df_tools'
				);

				$message .= $failedRecord->getTestUrl() . PHP_EOL . PHP_EOL;
				$message .= "\t" . $this->br2nl($testMessage, "\t");
				$message .= PHP_EOL . PHP_EOL;
			}

			$this->sendMail($subject, $message, $notificationEmailAddresses);
		}
	}
}

?>