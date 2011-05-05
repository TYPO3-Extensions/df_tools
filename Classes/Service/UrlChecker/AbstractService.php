<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <sgalinski@df.eu>, domainfactory GmbH
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
 * Abstract Url Checker Service
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Service_UrlChecker_AbstractService implements t3lib_Singleton {
	const SEVERITY_UNTESTED = 9;
	const SEVERITY_EXCEPTION = 6;
	const SEVERITY_ERROR = 5;
	const SEVERITY_WARNING = 4;
	const SEVERITY_INFO = 3;
	const SEVERITY_OK = 2;
	const SEVERITY_IGNORE = 1;

	/**
	 * Timeout in seconds
	 *
	 * @var int
	 */
	protected $timeout = 7;

	/**
	 * User Agent
	 *
	 * @var string
	 */
	protected $userAgent = '';

	/**
	 * @var string
	 */
	protected $url = '';

	/**
	 * @var string
	 */
	protected $protocol = '';

	/**
	 * @var string
	 */
	protected $authority = '';

	/**
	 * @var string
	 */
	protected $host = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; de-DE) ' .
			'AppleWebKit/534.17 (KHTML, like Gecko) Chrome/10.0.649.0 Safari/534.17';
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
	}

	/**
	 * Sets the timeout
	 *
	 * @param int $timeout
	 * @return void
	 */
	public function setTimeout($timeout) {
		$this->timeout = intval($timeout);
	}

	/**
	 * Returns the timeout
	 *
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * Sets the user agent
	 *
	 * @param string $userAgent
	 * @return void
	 */
	public function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}

	/**
	 * Returns the user agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}

	/**
	 * Sets and prepares the test url
	 *
	 * @param string $url
	 * @return Tx_DfTools_Service_UrlChecker_AbstractService
	 */
	public function setUrl($url) {
		$this->url = trim($url);
		$urlInfo = parse_url($this->url);

		$this->protocol = $urlInfo['scheme'];
		$this->host = $urlInfo['host'];

		if (isset($urlInfo['user'])) {
			$this->authority = $urlInfo['user'];
			if (isset($urlInfo['pass'])) {
				$this->authority .= ':' . $urlInfo['pass'];
			}
			$this->authority .= '@';
		}
		$this->authority .= $urlInfo['host'];
		if (isset($urlInfo['port'])) {
			$this->authority .= ':' . $urlInfo['port'];
		}

		return $this;
	}

	/**
	 * Returns the prepared url
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Returns the authority of the url
	 *
	 * @return string
	 */
	public function getAuthority() {
		return $this->authority;
	}

	/**
	 * Returns the protocol of the domain
	 *
	 * @return void
	 */
	public function getProtocol() {
		return $this->protocol;
	}

	/**
	 * Returns the host part of the url
	 *
	 * @return string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Initializes the service
	 *
	 * @return void
	 */
	abstract public function init();

	/**
	 * Resolves an url and returns an array of the following structure
	 *
	 * - content => Content
	 * - http_code => HTTP Code
	 * - url => Resolved URL
	 *
	 * Note: You must use setUrl to set the testing url!
	 *
	 * @return array
	 */
	abstract public function resolveURL();
}

?>