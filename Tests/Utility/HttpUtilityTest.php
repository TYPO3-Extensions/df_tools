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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_DfTools_Utility_HttpUtility.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Utility_HttpUtilityTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var bool
	 */
	protected $backupGlobals = TRUE;

	/**
	 * @return array
	 */
	public function stringIsPrefixedWithHostDataProvider() {
		return array(
			'starts with /' => array(
				t3lib_div::getIndpEnv('TYPO3_SITE_URL') . 'foo/bar',
				'/foo/bar',
			),
			'starts with http' => array(
				'http://www.example.org/foo/bar',
				'http://www.example.org/foo/bar',
			),
		);
	}

	/**
	 * @test
	 * @dataProvider stringIsPrefixedWithHostDataProvider
	 *
	 * @param string $expected
	 * @param string $input
	 * @return void
	 */
	public function stringIsPrefixedWithHost($expected, $input) {
		$result = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost($input);
		$this->assertSame($expected, $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function stringIsPrefixedWithHostButWithoutValidEnvironmentSiteUrlButWithConfiguredBaseUrl() {
		$_SERVER['HTTP_HOST'] = '';
		$GLOBALS['TSFE']->baseUrl = 'http://www.example.org/';
		$result = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost('/foo/bar/');
		$this->assertSame('http://www.example.org/foo/bar/', $result);
	}

	/**
	 * @test
	 * @return void
	 */
	public function stringIsPrefixedWithHostButWithoutValidEnvironmentSiteUrlButWithConfiguredAbsRefPrefix() {
		$_SERVER['HTTP_HOST'] = '';
		$GLOBALS['TSFE']->absRefPrefix = 'http://www.example.org/';
		$result = Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost('/foo/bar/');
		$this->assertSame('http://www.example.org/foo/bar/', $result);
	}

	/**
	 * @test
	 * @expectedException RuntimeException
	 * @return void
	 */
	public function stringIsPrefixedWithHostButWithoutSiteUrlInformations() {
		$_SERVER['HTTP_HOST'] = $GLOBALS['TSFE']->absRefPrefix = $GLOBALS['TSFE']->baseUrl = '';
		Tx_DfTools_Utility_HttpUtility::prefixStringWithCurrentHost('/foo/bar/');
	}
}

?>