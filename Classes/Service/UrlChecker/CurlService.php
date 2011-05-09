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
 * Concrete Url Checker Service
 *
 * This class expects a working curl module!
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_CurlService extends Tx_DfTools_Service_UrlChecker_AbstractService {
	/**
	 * cUrl Handle
	 *
	 * @var resource
	 */
	protected $cUrlHandle = NULL;

	/**
	 * Destructor
	 */
	public function __destruct() {
		if ($this->cUrlHandle) {
			curl_close($this->cUrlHandle);
		}
		parent::__destruct();
	}

	/**
	 * Initializes cUrl
	 *
	 * @throws RuntimeException
	 * @return void
	 */
	public function init() {
		if ($this->cUrlHandle) {
			return;
		}

		$this->cUrlHandle = curl_init();
		if (!$this->cUrlHandle) {
			$this->cUrlHandle = NULL;
			throw new RuntimeException('cUrl could not be initialized!');
		}

		curl_setopt_array($this->cUrlHandle, array(
			CURLOPT_ENCODING => '',
			CURLOPT_USERAGENT => $this->userAgent,
			CURLOPT_HTTPHEADER => array('Expect:'),
			CURLOPT_COOKIEJAR => tempnam('/tmp', 'CURLCOOKIE'),

			CURLOPT_CONNECTTIMEOUT => $this->timeout,
			CURLOPT_TIMEOUT => $this->timeout,

			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_AUTOREFERER => TRUE,
			CURLOPT_MAXREDIRS => 10,

			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_VERBOSE => TRUE,
		 ));
	}

	/**
	 * Resolves an url and returns an array of the following structure
	 *
	 * - content => Content
	 * - http_code => HTTP Code
	 * - url => Resolved URL
	 *
	 * Note: You must use setUrl to set the testing url!
	 *
	 * @throws RuntimeException contains any internal cUrl errors
	 * @return array
	 */
	public function resolveURL() {
		curl_setopt($this->cUrlHandle, CURLOPT_URL, $this->url);
		$content = curl_exec($this->cUrlHandle);
		$curlInfo = curl_getinfo($this->cUrlHandle);

		$curlInfo['error'] = intval(curl_errno($this->cUrlHandle));
		$curlInfo['errorMessage'] = curl_error($this->cUrlHandle);
		if ($curlInfo['error'] !== 0 || $curlInfo['errorMessage'] !== '') {
			throw new RuntimeException($curlInfo['errorMessage'] . ' [' . $curlInfo['error'] . ']');
		}

		return array(
			'http_code' => $curlInfo['http_code'],
			'url' => $curlInfo['url'],
			'content' => $content,
		);
	}
}

?>