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

use SGalinski\DfTools\Domain\Model\LinkCheck;
use SGalinski\DfTools\Domain\Service\LinkCheckService;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Command controller for the link check functionality
 */
class LinkCheckCommandController extends AbstractCommandController {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\LinkCheckRepository
	 */
	protected $linkCheckRepository;

	/**
	 * Executes all link check tests
	 *
	 * @param string $notificationEmailAddresses
	 * @return void
	 */
	public function runAllTestsCommand($notificationEmailAddresses) {
		$linkChecks = $this->linkCheckRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		/** @var $linkCheck LinkCheck */
		foreach ($linkChecks as $linkCheck) {
			$linkCheck->test($urlCheckerService);
			$this->linkCheckRepository->update($linkCheck);
		}

		$failedRecords = $this->checkTestResults($linkChecks);
		if (count($failedRecords)) {
			/** @var LanguageService $language */
			$language = $GLOBALS['LANG'];
			$sLPrefix = 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:';
			$subject = $language->sL($sLPrefix . 'tx_dftools_domain_model_linkcheck.scheduler.mailSubject');
			$message = $language->sL($sLPrefix . 'tx_dftools_domain_model_linkcheck.scheduler.mailBody') .
				PHP_EOL . PHP_EOL;

			/** @var $failedRecord LinkCheck */
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

	/**
	 * Synchronizes the current data with the external urls on the page
	 *
	 * @return void
	 */
	public function synchronizeCommand() {
		/** @var $linkCheckService LinkCheckService */
		$linkCheckService = $this->objectManager->get('SGalinski\DfTools\Domain\Service\LinkCheckService');
		$rawUrls = $linkCheckService->fetchAllRawUrlsFromTheDatabase(
			$this->extensionConfiguration['excludedTables'],
			$this->extensionConfiguration['excludedTableFields']
		);

		$class = 'SGalinski\DfTools\Domain\Service\UrlSynchronizeService';
		$urlSynchronizationService = $this->objectManager->get($class);
		$urlSynchronizationService->synchronize($rawUrls, $this->linkCheckRepository->findAll());
	}
}

?>