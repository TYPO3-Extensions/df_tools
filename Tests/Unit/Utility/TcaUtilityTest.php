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
 * Test case for class Tx_DfTools_Utility_TcaUtility.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Utility_TcaUtilityTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @return array
	 */
	public function filterCallbackFiltersCorrectlyDataProvider() {
		return array(
			'undefined max value' => array(
				FALSE,
				array('config' => array('title' => '')),
			),
			'defined max value of 100' => array(
				FALSE,
				array('config' => array('title' => '', 'max' => 100)),
			),
			'defined max value of 10' => array(
				TRUE,
				array('config' => array('title' => '', 'max' => 10)),
			),
			'defined max value of 50' => array(
				TRUE,
				array('config' => array('title' => '', 'max' => 50)),
			),
			'defined max value of string 10' => array(
				TRUE,
				array('config' => array('title' => '', 'max' => '10')),
			),
			'defined max value of string 100' => array(
				FALSE,
				array('config' => array('title' => '', 'max' => '100')),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider filterCallbackFiltersCorrectlyDataProvider
	 *
	 * @param boolean $expected
	 * @param array $input
	 * @return void
	 */
	public function filterCallbackFiltersCorrectly($expected, array $input) {
		$this->assertSame($expected, Tx_DfTools_Utility_TcaUtility::filterCallback($input));
	}
}

?>