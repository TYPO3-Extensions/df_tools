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
 * Test case for class Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelper.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelperTest extends Tx_DfTools_ViewHelpers_ViewHelperTestCase {
	/**
	 * @var Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelper
	 */
	protected $fixture = NULL;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelper';
		$this->fixture = $this->getMock($class, array('getPageRenderer'));
		parent::setUp();
	}

	/**
	 * @return void
	 */
	protected function prepareTests() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->pageRenderer->expects($this->once())->method('addInlineLanguageLabelFile')
			->with('EXT:my_ext/Resources/Foo/Bar.xml');
	}

	/**
	 * @test
	 * @return void
	 */
	public function languageFileCanBeAddedWithExtensionKey() {
		$this->prepareTests();
		$this->fixture->render('Foo/Bar.xml', 'MyExt');
	}

	/**
	 * @test
	 * @return void
	 */
	public function languageFileCanBeAddedWithoutExtensionKey() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->prepareTests();
		$this->request->expects($this->once())->method('getControllerExtensionName')
			->will($this->returnValue('MyExt'));
		$this->fixture->render('Foo/Bar.xml');
	}
}

?>