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
 * Back Link Test
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_BackLinkTest extends Tx_Extbase_DomainObject_AbstractEntity implements Tx_DfTools_Domain_Model_TestableInterface {
	/**
	 * Test URL
	 *
	 * @validate NotEmpty
	 * @var string
	 */
	protected $testUrl = '';

	/**
	 * Expected URL
	 *
	 * @validate NotEmpty
	 * @var string
	 */
	protected $expectedUrl = '';

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
	 * Setter for expectedUrl
	 *
	 * @param string $expectedUrl
	 * @return void
	 */
	public function setExpectedUrl($expectedUrl) {
		$this->expectedUrl = $expectedUrl;
	}

	/**
	 * Getter for expectedUrl
	 *
	 * @return string
	 */
	public function getExpectedUrl() {
		return $this->expectedUrl;
	}

	/**
	 * Setter for testResult
	 *
	 * @validate $testResult NumberRange(startRange=0,endRange=1000)
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
	 * Returns this object as a simple array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'__identity' => $this->getUid(),
			'testUrl' => $this->getTestUrl(),
			'expectedUrl' => $this->getExpectedUrl(),
			'testResult' => $this->getTestResult(),
			'testMessage' => $this->getTestMessage(),
		);
	}

	/**
	 * Tests and evaluates the model
	 *
	 * @param Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService
	 * @return void
	 */
	public function test(Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService) {
		$testUrl = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($this->getTestUrl());
		$expectedUrl = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($this->getExpectedUrl());

		try {
			$report = $urlCheckerService->setUrl($testUrl)->resolveURL();

			$message = '';
			$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK;
			$regularExpression = '/href="' . str_replace(array('\/', '/'), array('/', '\/'), $expectedUrl) . '"/is';
			if (!preg_match($regularExpression, $report['content'])) {
				$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR;
				$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
					'tx_dftools_domain_model_backlinktest.test.missingExpectedUrl',
					array($testUrl, $expectedUrl)
				);
			} else {
				$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK);
			}

			$this->setTestResult($result);
			$this->setTestMessage($message);

		} catch (Exception $exception) {
			$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);
			$this->setTestMessage($exception->getMessage());
		}
	}
}

?>