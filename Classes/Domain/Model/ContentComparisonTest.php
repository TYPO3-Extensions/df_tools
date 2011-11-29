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
 * URL Set For A Content Comparison
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_ContentComparisonTest extends Tx_Extbase_DomainObject_AbstractEntity implements Tx_DfTools_Domain_Model_TestableInterface {
	/**
	 * Test url
	 *
	 * @validate NotEmpty
	 * @var string
	 */
	protected $testUrl = '';

	/**
	 * Compare url
	 *
	 * @validate NotEmpty
	 * @var string
	 */
	protected $compareUrl = '';

	/**
	 * Last saved content of the test url
	 *
	 * @var string
	 */
	protected $testContent = '';

	/**
	 * Saved content that is used for the comparison
	 *
	 * @var string
	 */
	protected $compareContent = '';

	/**
	 * Content difference
	 *
	 * @var string
	 */
	protected $difference = '';

	/**
	 * Test Result
	 *
	 * @validate NumberRange(startRange = 0, endRange = 9)
	 * @var int
	 */
	protected $testResult = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_UNTESTED;

	/**
	 * Test Message
	 *
	 * @validate StringLength(maximum = 300)
	 * @var string
	 */
	protected $testMessage = '';

	/**
	 * Just an empty constructor that is needed by the property mapper
	 */
	public function __construct() {
		if (is_callable('parent::__construct')) {
			parent::__construct();
		}
	}

	/**
	 * Setter for testUrl
	 *
	 * @param string $testUrl
	 * @return void
	 */
	public function setTestUrl($testUrl) {
		$this->testUrl = $testUrl;
	}

	/**
	 * Getter for testUrl
	 *
	 * @return string
	 */
	public function getTestUrl() {
		return $this->testUrl;
	}

	/**
	 * Setter for compareUrl
	 *
	 * @param string $compareUrl
	 * @return void
	 */
	public function setCompareUrl($compareUrl) {
		$this->compareUrl = $compareUrl;
	}

	/**
	 * Getter for compareUrl
	 *
	 * @return string
	 */
	public function getCompareUrl() {
		return $this->compareUrl;
	}

	/**
	 * Setter for compareContent
	 *
	 * @param string $compareContent
	 * @return void
	 */
	public function setCompareContent($compareContent) {
		$this->compareContent = Tx_DfTools_Utility_CompressorUtility::compressContent($compareContent);
	}

	/**
	 * Getter for compareContent
	 *
	 * @return string
	 */
	public function getCompareContent() {
		return Tx_DfTools_Utility_CompressorUtility::decompressContent($this->compareContent);
	}

	/**
	 * Setter for testContent
	 *
	 * @param string $testContent
	 * @return void
	 */
	public function setTestContent($testContent) {
		$this->testContent = Tx_DfTools_Utility_CompressorUtility::compressContent($testContent);
	}

	/**
	 * Getter for testContent
	 *
	 * @return string
	 */
	public function getTestContent() {
		return Tx_DfTools_Utility_CompressorUtility::decompressContent($this->testContent);
	}

	/**
	 * Setter for difference
	 *
	 * @param string $difference
	 * @return void
	 */
	public function setDifference($difference) {
		$this->difference = Tx_DfTools_Utility_CompressorUtility::compressContent($difference);
	}

	/**
	 * Getter for difference
	 *
	 * @return string
	 */
	public function getDifference() {
		return Tx_DfTools_Utility_CompressorUtility::decompressContent($this->difference);
	}

	/**
	 * Setter for testResult
	 *
	 * @param int $testResult
	 * @return void
	 */
	public function setTestResult($testResult) {
		$this->testResult = intval($testResult);
	}

	/**
	 * Getter for testResult
	 *
	 * @return int
	 */
	public function getTestResult() {
		return $this->testResult;
	}

	/**
	 * Setter for testMessage
	 *
	 * @param string $testMessage
	 * @return void
	 */
	public function setTestMessage($testMessage) {
		$this->testMessage = $testMessage;
	}

	/**
	 * Getter for testMessage
	 *
	 * @return string
	 */
	public function getTestMessage() {
		return $this->testMessage;
	}

	/**
	 * Returns this instance as an array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'__identity' => $this->getUid(),
			'testUrl' => $this->getTestUrl(),
			'compareUrl' => $this->getCompareUrl(),
			'testContent' => $this->getTestContent(),
			'compareContent' => $this->getCompareContent(),
			'difference' => $this->getDifference(),
			'testMessage' => $this->getTestMessage(),
			'testResult' => $this->getTestResult(),
		);
	}


	/**
	 * Checks the differences of the saved contents inside the content comparison test
	 * and updates the instance accordingly
	 *
	 * @param string $compareContent
	 * @param string $testContent
	 * @return void
	 */
	protected function checkDifferences($compareContent, $testContent) {
		/** @var $diffRenderer t3lib_diff */
		$diffRenderer = t3lib_div::makeInstance('t3lib_diff');

		$testContentParts = Tx_DfTools_Utility_HtmlUtility::getTypo3SearchBlocksFromContent($testContent);
		$compareContentParts = Tx_DfTools_Utility_HtmlUtility::getTypo3SearchBlocksFromContent($compareContent);

		$differences = array();
		$count = count($testContentParts);
		for ($i = 0; $count > $i; ++$i) {
			if ($testContentParts[$i] === $compareContentParts[$i]) {
				continue;
			}

			$differences[] = $diffRenderer->makeDiffDisplay($testContentParts[$i], $compareContentParts[$i]);
		}

		if (count($differences)) {
			$message = 'tx_dftools_domain_model_contentcomparisontest.test.contentMismatch';
			$diff = '<p class="diff">' . implode(' </p><p class="diff">', $differences) . '</p>';
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR);
		} else {
			$message = $diff = '';
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK);
		}

		$this->setTestMessage($message);
		$this->setDifference(nl2br($diff));
	}

	/**
	 * Returns the url test report or NULL if the http code was invalid (not in [200, 301 or 302])
	 *
	 * @param string $url
	 * @param Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService
	 * @return array|NULL
	 */
	protected function resolveUrl($url, Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService) {
		$report = $urlCheckerService->setUrl($url)->resolveURL();
		if (!in_array($report['http_code'], array(200, 301, 302))) {
			$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
				'tx_dftools_domain_model_contentcomparisontest.test.httpCodeMismatch',
				array('[200, 301, 302]', $report['http_code'])
			);
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR);
			$this->setTestMessage($message);
			$report = NULL;
		}

		return $report;
	}

	/**
	 * Tests and evaluates the model
	 *
	 * @param Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService
	 * @return void
	 */
	public function test(Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService) {
		$testUrl = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($this->getTestUrl());
		$compareUrl = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($this->getCompareUrl());

		try {
			$compareUrlReport = $this->resolveUrl($compareUrl, $urlCheckerService);
			if ($compareUrlReport === NULL) {
				return;
			}

			$this->setCompareContent($compareUrlReport['content']);
			if ($testUrl === $compareUrl) {
				$testContent = $this->getTestContent();
				if ($testContent === '') {
					$this->setTestContent($compareUrlReport['content']);
					$testContent = $compareUrlReport['content'];
				}
			} else {
				$testUrlReport = $this->resolveUrl($testUrl, $urlCheckerService);
				if ($testUrlReport === NULL) {
					return;
				}
				$this->setTestContent($testUrlReport['content']);
				$testContent = $testUrlReport['content'];
			}

			$this->checkDifferences($compareUrlReport['content'], $testContent);

		} catch (Exception $exception) {
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);
			$this->setTestMessage($exception->getMessage());
			$this->setDifference('');
		}
	}

	/**
	 * Updates the test content of the test url
	 *
	 * @param Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService
	 * @return void
	 */
	public function updateTestContent(Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService) {
		$testUrl = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($this->getTestUrl());

		try {
			$report = $urlCheckerService->setUrl($testUrl)->resolveURL();
			$this->setTestContent($report['content']);

		} catch (Exception $exception) {
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);
			$this->setTestMessage($exception->getMessage());
			$this->setDifference('');
		}
	}
}

?>