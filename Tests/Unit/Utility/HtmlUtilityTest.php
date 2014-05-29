<?php

namespace SGalinski\DfTools\Tests\Unit\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinsk@gmail.com>)
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

use SGalinski\DfTools\Tests\Unit\Controller\ControllerTestCase;
use SGalinski\DfTools\Utility\HtmlUtility;

/**
 * Class HtmlUtilityTest
 */
class HtmlUtilityTest extends ControllerTestCase {
	/**
	 * @return array
	 */
	public function typo3SearchBlocksAreParsedDataProvider() {
		return array(
			'multiple search blocks' => array(
				array(
					'Foo; Bar; Narf',
					'Foo; Bar'
				), '
					<!--TYPO3SEARCH_begin-->
					Foo; Bar; Narf
					<!--TYPO3SEARCH_end-->
					Some Foo Content
					<!--TYPO3SEARCH_begin-->
					Foo; Bar
					<!--TYPO3SEARCH_end-->
				',
			),
			'single search blocks' => array(
				array(
					'Foo; Bar; Narf
					Some Foo Content
					Foo; Bar',
				), '
					<!--TYPO3SEARCH_begin-->
					Foo; Bar; Narf
					Some Foo Content
					Foo; Bar
					<!--TYPO3SEARCH_end-->
				',
			),
			'no search blocks' => array(
				array(
					'Foo; Bar; Narf
					Some Foo Content
					Foo; Bar',
				), '
					Foo; Bar; Narf
					Some Foo Content
					Foo; Bar
				',
			),
		);
	}

	/**
	 * @test
	 * @dataProvider typo3SearchBlocksAreParsedDataProvider
	 *
	 * @param array $expectedParts
	 * @param string $content
	 * @return void
	 */
	public function typo3SearchBlocksAreParsed($expectedParts, $content) {
		$parts = HtmlUtility::getTypo3SearchBlocksFromContent($content);
		$this->assertSame($expectedParts, $parts);
	}
}

?>