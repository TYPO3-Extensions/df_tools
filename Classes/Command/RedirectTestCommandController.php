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

use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\Utility\LocalizationUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Command controller for the redirect test functionality
 */
class RedirectTestCommandController extends AbstractCommandController {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Service\RealUrlImportService
	 */
	protected $realUrlImportService;

	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\RedirectTestRepository
	 */
	protected $redirectTestRepository;

	/**
	 * Executes all redirect tests
	 *
	 * @param string $notificationEmailAddresses
	 * @return void
	 */
	public function runAllTestsCommand($notificationEmailAddresses) {
		$redirectTests = $this->redirectTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		/** @var $redirectTest RedirectTest */
		foreach ($redirectTests as $redirectTest) {
			$redirectTest->test($urlCheckerService);
			$this->redirectTestRepository->update($redirectTest);
		}

		$failedRecords = $this->checkTestResults($redirectTests);
		if (count($failedRecords)) {
			/** @var LanguageService $language */
			$language = $GLOBALS['LANG'];
			$sLPrefix = 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:';
			$subject = $language->sL($sLPrefix . 'tx_dftools_domain_model_redirecttest.scheduler.mailSubject');
			$message = $language->sL($sLPrefix . 'tx_dftools_domain_model_redirecttest.scheduler.mailBody') .
				PHP_EOL . PHP_EOL;

			/** @var $failedRecord RedirectTest */
			foreach ($failedRecords as $failedRecord) {
				$testMessage = LocalizationUtility::localizeParameterDrivenString(
					$failedRecord->getTestMessage(), 'df_tools'
				);

				$message .= $failedRecord->getTestUrl() . PHP_EOL . PHP_EOL;
				$message .= "\t" . $this->br2nl($testMessage, "\t");
				$message .= PHP_EOL . PHP_EOL;
			}

			$this->sendMail($subject, $message, $notificationEmailAddresses);
		}
	}

	/**
	 * Imports the existing redirects from EXT:realurl
	 *
	 * @return void
	 */
	public function importFromRealUrlCommand() {
		$this->realUrlImportService->importFromRealUrl();
	}
}

?>