<?php

namespace SGalinski\DfTools\Service\UrlChecker;

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
 * Concrete Url Checker Service
 *
 * This class expects a working curl module!
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class CurlService extends AbstractService {
	/**
	 * cUrl Handle
	 *
	 * @var resource
	 */
	protected $curlHandle = NULL;

	/**
	 * Proxy curl handle
	 *
	 * @var resource
	 */
	protected $proxyCurlHandle = NULL;

	/**
	 * Destructor
	 */
	public function __destruct() {
		if (is_resource($this->curlHandle)) {
			curl_close($this->curlHandle);
		}

		if (is_resource($this->proxyCurlHandle)) {
			curl_close($this->proxyCurlHandle);
		}

		parent::__destruct();
	}

	/**
	 * Initializes a curl handle and returns it
	 *
	 * @throws \RuntimeException
	 * @return resource
	 */
	protected function getCurlHandle() {
		$curlHandle = curl_init();
		if (!$curlHandle) {
			throw new \RuntimeException('cUrl could not be initialized!');
		}

		curl_setopt_array(
			$curlHandle, array(
				CURLOPT_ENCODING => '',
				CURLOPT_USERAGENT => $this->userAgent,
				CURLOPT_HTTPHEADER => array('Expect:'),
				CURLOPT_COOKIEJAR => tempnam('/tmp', 'CURLCOOKIE'),
				CURLOPT_FORBID_REUSE => TRUE,

				CURLOPT_CONNECTTIMEOUT => $this->timeout,
				CURLOPT_TIMEOUT => $this->timeout,

				CURLOPT_FOLLOWLOCATION => TRUE,
				CURLOPT_AUTOREFERER => TRUE,
				CURLOPT_MAXREDIRS => 10,

				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_VERBOSE => TRUE,
			)
		);

		return $curlHandle;
	}

	/**
	 * Initializes the cUrl instance
	 *
	 * @throws \RuntimeException
	 * @return void
	 */
	public function init() {
		if ($this->curlHandle !== NULL) {
			return;
		}

		$this->curlHandle = $this->getCurlHandle();
	}

	/**
	 * Initializes the proxy cUrl instance
	 *
	 * @return void
	 */
	public function initProxyCurlInstance() {
		$proxyServer = $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer'];
		if ($this->proxyCurlHandle !== NULL && $proxyServer !== '') {
			return;
		}

		$this->proxyCurlHandle = $this->getCurlHandle();
		curl_setopt($this->proxyCurlHandle, CURLOPT_PROXY, $proxyServer);

		$proxyTunnel = $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel'];
		if ($proxyTunnel !== '') {
			curl_setopt($this->proxyCurlHandle, CURLOPT_HTTPPROXYTUNNEL, $proxyTunnel);
		}

		$proxyUserPass = $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass'];
		if ($proxyUserPass !== '') {
			curl_setopt($this->proxyCurlHandle, CURLOPT_PROXYUSERPWD, $proxyUserPass);
		}
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
	 * @throws \RuntimeException contains any internal cUrl errors
	 * @param boolean $useProxyInstance
	 * @return array
	 */
	public function resolveURL($useProxyInstance = FALSE) {
		$curlHandle = $this->curlHandle;
		if ($useProxyInstance) {
			$curlHandle = $this->proxyCurlHandle;
		}

		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		$content = curl_exec($curlHandle);
		$curlInfo = curl_getinfo($curlHandle);

		$curlInfo['error'] = intval(curl_errno($curlHandle));
		$curlInfo['errorMessage'] = curl_error($curlHandle);
		if ($curlInfo['error'] === 56) {
			$this->initProxyCurlInstance();
			return $this->resolveURL(TRUE);

		} elseif ($curlInfo['error'] !== 0 || $curlInfo['errorMessage'] !== '') {
			throw new \RuntimeException($curlInfo['errorMessage'] . ' [' . $curlInfo['error'] . ']');
		}

		return array(
			'http_code' => $curlInfo['http_code'],
			'url' => $curlInfo['url'],
			'content' => $content,
		);
	}
}

?>