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
 * Link Check
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_LinkCheck extends Tx_Extbase_DomainObject_AbstractEntity implements Tx_DfTools_Domain_Model_TestableInterface {
	/**
	 * Test URL
	 *
	 * @validate NotEmpty
	 * @var string
	 */
	protected $testUrl = '';

	/**
	 * Result URL
	 *
	 * @var string
	 */
	protected $resultUrl = '';

	/**
	 * HTTP Status Code
	 *
	 * @validate NumberRange(startRange = 0, endRange = 1000)
	 * @var integer
	 */
	protected $httpStatusCode = 0;

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
	 * Record Sets
	 *
	 * @lazy
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_DfTools_Domain_Model_RecordSet>
	 */
	protected $recordSets = NULL;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->recordSets = new Tx_Extbase_Persistence_ObjectStorage();
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
	 * Setter for resultUrl
	 *
	 * @param string $resultUrl
	 * @return void
	 */
	public function setResultUrl($resultUrl) {
		$this->resultUrl = $resultUrl;
	}

	/**
	 * Getter for resultUrl
	 *
	 * @return string
	 */
	public function getResultUrl() {
		return $this->resultUrl;
	}

	/**
	 * Setter for httpStatusCode
	 *
	 * @param integer $httpStatusCode
	 * @return void
	 */
	public function setHttpStatusCode($httpStatusCode) {
		$this->httpStatusCode = intval($httpStatusCode);
	}

	/**
	 * Getter for httpStatusCode
	 *
	 * @return integer
	 */
	public function getHttpStatusCode() {
		return $this->httpStatusCode;
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
	 * Setter for recordSets
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage $recordSets
	 * @return void
	 */
	public function setRecordSets(Tx_Extbase_Persistence_ObjectStorage $recordSets) {
		$this->recordSets = $recordSets;
	}

	/**
	 * Getter for recordSets
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_DfTools_Domain_Model_RecordSet>
	 */
	public function getRecordSets() {
		return $this->recordSets;
	}

	/**
	 * Adds a RecordSet
	 *
	 * @param Tx_DfTools_Domain_Model_RecordSet $recordSet
	 * @return void
	 */
	public function addRecordSet(Tx_DfTools_Domain_Model_RecordSet $recordSet) {
		$this->recordSets->attach($recordSet);
	}

	/**
	 * Removes a RecordSet
	 *
	 * @param Tx_DfTools_Domain_Model_RecordSet $recordSetToRemove
	 * @return void
	 */
	public function removeRecordSet(Tx_DfTools_Domain_Model_RecordSet $recordSetToRemove) {
		$this->recordSets->detach($recordSetToRemove);
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
			'resultUrl' => $this->getResultUrl(),
			'httpStatusCode' => $this->getHttpStatusCode(),
			'testResult' => $this->getTestResult(),
			'testMessage' => $this->getTestMessage()
		);
	}

	/**
	 * Tests and evaluates the model
	 *
	 * @param Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService
	 * @return void
	 */
	public function test(Tx_DfTools_Service_UrlChecker_AbstractService $urlCheckerService) {
		$testResult = $this->getTestResult();
		if ($testResult === Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_IGNORE) {
			return;
		}

		try {
			$testUrl = $this->getTestUrl();
			$report = $urlCheckerService->setUrl($testUrl)->resolveURL();

			$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_OK;
			$testUrl = $this->getTestUrl();
			$message = '';

			if (!in_array($report['http_code'], array(200, 301, 302))) {
				$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_ERROR;
				$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
					'tx_dftools_domain_model_linkcheck.test.httpCodeMismatch',
					array('[200, 301, 302]', $report['http_code'])
				);

			} elseif ($report['url'] !== $testUrl) {
				$result = Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_WARNING;
				$message = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
					'tx_dftools_domain_model_linkcheck.test.urlMismatch',
					array($testUrl, $report['url'])
				);
			}

			if ($testResult !== Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO) {
				$this->setTestResult($result);
			}

			$this->setTestMessage($message);
			$this->setResultUrl($report['url']);
			$this->setHttpStatusCode($report['http_code']);

		} catch (Exception $exception) {
			if ($testResult !== Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_INFO) {
				$this->setTestResult(Tx_DfTools_Service_UrlChecker_AbstractService::SEVERITY_EXCEPTION);
			}

			$this->setTestMessage($exception->getMessage());
		}
	}
}

?>