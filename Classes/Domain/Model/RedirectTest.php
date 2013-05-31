<?php

namespace SGalinski\DfTools\Domain\Model;

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

use SGalinski\DfTools\UrlChecker\AbstractService;
use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Redirect Test
 */
class RedirectTest extends AbstractEntity implements TestableInterface {
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
	 * HTTP Status Code
	 *
	 * @validate NumberRange(startRange = 0, endRange = 1000)
	 * @var int
	 */
	protected $httpStatusCode = 0;

	/**
	 * Test Result
	 *
	 * @validate NumberRange(startRange = 0, endRange = 9)
	 * @var int
	 */
	protected $testResult = AbstractService::SEVERITY_UNTESTED;

	/**
	 * Test Message
	 *
	 * @validate StringLength(maximum = 65536)
	 * @var string
	 */
	protected $testMessage = '';

	/**
	 * Category
	 *
	 * @var \SGalinski\DfTools\Domain\Model\RedirectTestCategory
	 */
	protected $category = NULL;

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
	 * Setter for httpStatusCode
	 *
	 * @param int $httpStatusCode
	 * @return void
	 */
	public function setHttpStatusCode($httpStatusCode) {
		$this->httpStatusCode = intval($httpStatusCode);
	}

	/**
	 * Getter for httpStatusCode
	 *
	 * @return int
	 */
	public function getHttpStatusCode() {
		return $this->httpStatusCode;
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
	 * Setter for category
	 *
	 * @param RedirectTestCategory $category
	 * @return void
	 */
	public function setCategory(RedirectTestCategory $category) {
		$this->category = $category;
	}

	/**
	 * Getter for category
	 *
	 * @return RedirectTestCategory
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Returns this object as a simple array
	 *
	 * @return array
	 */
	public function toArray() {
		$category = $this->getCategory();
		return array(
			'__identity' => $this->getUid(),
			'testUrl' => $this->getTestUrl(),
			'expectedUrl' => $this->getExpectedUrl(),
			'httpStatusCode' => $this->getHttpStatusCode(),
			'testResult' => $this->getTestResult(),
			'testMessage' => $this->getTestMessage(),
			'category' => ($category !== NULL ? $category->getUid() : NULL),
		);
	}

	/**
	 * Tests and evaluates the model
	 *
	 * @param AbstractService $urlCheckerService
	 * @return void
	 */
	public function test(AbstractService $urlCheckerService) {
		$testUrl = HttpUtility::prefixStringWithCurrentHost($this->getTestUrl());
		$expectedUrl = HttpUtility::prefixStringWithCurrentHost($this->getExpectedUrl());

		try {
			$report = $urlCheckerService->setUrl($testUrl)->resolveURL();

			$message = '';
			$result = AbstractService::SEVERITY_OK;
			if ($report['url'] !== $expectedUrl) {
				$result = AbstractService::SEVERITY_ERROR;
				$message = LocalizationUtility::createLocalizableParameterDrivenString(
					'tx_dftools_domain_model_redirecttest.test.urlMismatch',
					array($testUrl, $expectedUrl, $report['url'])
				);

			} elseif ($report['http_code'] !== $this->getHttpStatusCode()) {
				$result = AbstractService::SEVERITY_WARNING;
				$message = LocalizationUtility::createLocalizableParameterDrivenString(
					'tx_dftools_domain_model_redirecttest.test.httpCodeMismatch',
					array($this->getHttpStatusCode(), $report['http_code'])
				);

			} else {
				$this->setTestResult(AbstractService::SEVERITY_OK);
			}

			$this->setTestResult($result);
			$this->setTestMessage($message);

		} catch (\Exception $exception) {
			$this->setTestResult(AbstractService::SEVERITY_EXCEPTION);
			$this->setTestMessage($exception->getMessage());
		}
	}
}

?>