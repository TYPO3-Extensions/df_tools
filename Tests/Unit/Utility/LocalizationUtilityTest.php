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
 * Test case for class Tx_DfTools_Utility_LocalizationUtility.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Utility_LocalizationUtilityTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @return array
	 */
	public function localizableStringCanBeCreatedDataProvider() {
		return array(
			'with two parameters' => array(
				'FooBar1|!|#1|!|#2',
				'FooBar1',
				array('#1', '#2'),
			),
			'with one parameter' => array(
				'FooBar1|!|#1',
				'FooBar1',
				array('#1'),
			),
			'with zero parameters' => array(
				'FooBar',
				'FooBar',
				array()
			),
			'parameter this special character string' => array(
				'FooBar1|!|# 1',
				'FooBar1',
				array('#|!|1'),
			),
		);
	}

	/**
	 * @test
	 * @dataProvider localizableStringCanBeCreatedDataProvider
	 *
	 * @param string $expected
	 * @param string $label
	 * @param array $parameters
	 * @return void
	 */
	public function localizableStringCanBeCreated($expected, $label, array $parameters) {
		$result = Tx_DfTools_Utility_LocalizationUtility::createLocalizableParameterDrivenString(
			$label,
			$parameters
		);
		$this->assertSame($expected, $result);
	}

	/**
	 * Note: Unfortunately I cannot test any cases with valid parameters, because
	 * the localization utility is a static method. Maybe someday this will be changed
	 * into an injectable service object.
	 *
	 * @test
	 * @return void
	 */
	public function localizableStringWithNonExistingLabel() {
		$result = Tx_DfTools_Utility_LocalizationUtility::localizeParameterDrivenString('FooBar', 'df_tools');
		$this->assertSame('FooBar', $result);
	}
}

?>