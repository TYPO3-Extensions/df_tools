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

use SGalinski\DfTools\Domain\Model\ContentComparisonTest;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Command controller for the content comparison test functionality
 */
class ContentComparisonTestCommandController extends AbstractCommandController {
	/**
	 * @inject
	 * @var \SGalinski\DfTools\Domain\Repository\ContentComparisonTestRepository
	 */
	protected $contentComparisonTestRepository;

	/**
	 * Executes all content comparison tests
	 *
	 * @param string $notificationEmailAddresses
	 * @return void
	 */
	public function runAllTestsCommand($notificationEmailAddresses) {
		$contentComparisonTests = $this->contentComparisonTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		/** @var $contentComparisonTest ContentComparisonTest */
		foreach ($contentComparisonTests as $contentComparisonTest) {
			$contentComparisonTest->test($urlCheckerService);
			$this->contentComparisonTestRepository->update($contentComparisonTest);
		}

		$failedRecords = $this->checkTestResults($contentComparisonTests);
		if (count($failedRecords)) {
			/** @var LanguageService $language */
			$language = $GLOBALS['LANG'];
			$sLPrefix = 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:';
			$subject = $language->sL($sLPrefix . 'tx_dftools_domain_model_contentcomparisontest.scheduler.mailSubject');
			$message = $language->sL($sLPrefix . 'tx_dftools_domain_model_contentcomparisontest.scheduler.mailBody') .
				PHP_EOL . PHP_EOL;

			/** @var $failedRecord ContentComparisonTest */
			foreach ($failedRecords as $failedRecord) {
				$message .= $failedRecord->getTestUrl() . PHP_EOL;
				$message .= "\t" . $failedRecord->getCompareUrl() . PHP_EOL . PHP_EOL;
			}

			$this->sendMail($subject, $message, $notificationEmailAddresses);
		}
	}

	/**
	 * Synchronizes the contents of the content comparison test urls
	 *
	 * @return void
	 */
	public function synchronizeCommand() {
		$contentComparisonTests = $this->contentComparisonTestRepository->findAll();
		$urlCheckerService = $this->getUrlCheckerService();
		/** @var $contentComparisonTest ContentComparisonTest */
		foreach ($contentComparisonTests as $contentComparisonTest) {
			$contentComparisonTest->updateTestContent($urlCheckerService);
			$this->contentComparisonTestRepository->update($contentComparisonTest);
		}
	}
}

?>