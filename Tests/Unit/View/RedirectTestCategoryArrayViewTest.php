<?php

namespace SGalinski\DfTools\Tests\Unit\View;

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

use SGalinski\DfTools\Domain\Model\RedirectTestCategory;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Class RedirectTestCategoryArrayViewTest
 */
class RedirectTestCategoryArrayViewTest extends UnitTestCase {
	/**
	 * @var \SGalinski\DfTools\View\RedirectTestCategoryArrayView
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('SGalinski\DfTools\View\RedirectTestCategoryArrayView');
		$this->fixture = $this->getMockBuilder($class)
			->setMethods(array('dummy'))
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @return array
	 */
	public function recordsCanBeRenderedDataProvider() {
		$category = new RedirectTestCategory();
		$category->setCategory('FooBar');

		$categoryWithXSS = new RedirectTestCategory();
		$categoryWithXSS->setCategory('<img src="" onerror="alert(\'Ooops!!!\');"/>');

		return array(
			'normal category' => array(
				$category,
				array(
					'__identity' => 0,
					'category' => 'FooBar',
				),
			),
			'XSS attack' => array(
				$categoryWithXSS,
				array(
					'__identity' => 0,
					'category' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
				),
			),
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 * @param RedirectTestCategory $category
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($category, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $category));
	}
}

?>