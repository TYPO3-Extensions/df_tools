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
 * Concrete Url Checker Service
 *
 * This class utilizes the PHP stream wrapping functionality.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Service_UrlChecker_StreamService extends Tx_DfTools_Service_UrlChecker_AbstractService {
	/**
	 * Stream Context
	 *
	 * @var resource
	 */
	protected $context = NULL;

	/**
	 * Destructor
	 */
	public function __destruct() {
		unset($this->context);
		parent::__destruct();
	}

	/**
	 * Initializes the stream context
	 *
	 * @return void
	 */
	public function init() {
		$this->context = stream_context_create(array(
			'http' => array(
				'method' => 'GET',
				'user_agent' => $this->userAgent,
				'timeout' => $this->getTimeout()
			)
	   ));
	}

	/**
	 * Returns the last http code in an array of http headers
	 *
	 * @param array $headers
	 * @return int
	 */
	protected function getHttpCode(array $headers) {
		$httpCode = 0;
		foreach ($headers as $header) {
			$match = array();
			if (!preg_match('/HTTP\/1\.[0|1] ([0-9]{3}) .+/is', $header, $match)) {
				continue;
			}

			$httpCode = $match[1];
		}

		return intval($httpCode);
	}

	/**
	 * Returns the last used url in an array of http headers
	 *
	 * @param array $headers
	 * @return string
	 */
	protected function getLastUrl(array $headers) {
		$url = '';
		foreach ($headers as $header) {
			if (substr($header, 0, 9) !== 'Location:') {
				continue;
			}

			$parts = explode(' ', $header, 2);
			$url = $parts[1];
			if ($url{0} === '/') {
				$url = $this->protocol . '://' . $this->authority . $parts[1];
			}
		}

		return trim($url);
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
	 * @throws RuntimeException contains any internal errors
	 * @return array
	 */
	public function resolveURL() {
		$http_response_header = array();
		$stream = fopen($this->url, 'r', NULL, $this->context);
		try {
			$content = '';
			$metaData = array('timed_out' => FALSE, 'uri' => $this->url);
			if ($stream) {
				$metaData = stream_get_meta_data($stream);
				$content = stream_get_contents($stream);
			}

			$headers = $http_response_header;
			if (!count($headers)) {
				throw new RuntimeException('Could not connect to host \'' . $this->host . '\'', 10001);
			} elseif ($metaData['timed_out']) {
				throw new RuntimeException('Connection timed out (Limit: ' . $this->timeout . ')!', 10002);
			}

			$url = $this->getLastUrl($headers);
			$url = ($url === '' ? $metaData['uri'] : $url);

			$informations = array(
				'http_code' => $this->getHttpCode($headers),
				'url' => $url,
				'content' => $content,
			);
			fclose($stream);

		} catch (Exception $exception) {
			fclose($stream);
			throw $exception;
		}

		return $informations;
	}
}

?>