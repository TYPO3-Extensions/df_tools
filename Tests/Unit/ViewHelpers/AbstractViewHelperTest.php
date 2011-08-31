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
 * Test case for class Tx_DfTools_ViewHelpers_AbstractViewHelper.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ViewHelpers_AbstractViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_ViewHelpers_AbstractViewHelper
	 */
	protected $fixture = NULL;

	/**
	 * @var tslib_fe
	 */
	protected $backupTSFE = NULL;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->backupTSFE = $GLOBALS['TSFE'];
		$this->fixture = $this->getMock('Tx_DfTools_ViewHelpers_AbstractViewHelper', array('dummy'));
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		$GLOBALS['TSFE'] = $this->backupTSFE;
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function getBaseUrlWorksWithAbsRefPrefix() {
		$GLOBALS['TSFE']->baseUrl = '';
		$GLOBALS['TSFE']->absRefPrefix = 'AbsRefPrefix';
		$this->assertSame('AbsRefPrefix', $this->fixture->getBaseUrl());
	}

	/**
	 * @test
	 * @return void
	 */
	public function getBaseUrlWorksWithBaseUrl() {
		$GLOBALS['TSFE']->absRefPrefix = '';
		$GLOBALS['TSFE']->baseUrl = 'BaseUrl';
		$this->assertSame('BaseUrl', $this->fixture->getBaseUrl());
	}
}

?>