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
class Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * Fixture
	 *
	 * @var Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelper
	 */
	protected $fixture = NULL;

	/**
	 * Page Renderer
	 *
	 * @var t3lib_PageRenderer
	 */
	protected $pageRenderer = NULL;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new Tx_DfTools_ViewHelpers_AddInlineLanguageLabelFileViewHelper();

		/** @var $pageRenderer t3lib_PageRenderer */
		$this->pageRenderer = $this->getMock(
			't3lib_PageRenderer',
			array('addInlineLanguageLabelFile'), array(), '', FALSE, FALSE, FALSE
		);

		/** @noinspection PhpUndefinedMethodInspection */
		$this->pageRenderer->expects($this->once())
			->method('addInlineLanguageLabelFile')
			->with('EXT:df_tools/Resources/Foo/Bar.xml');
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture, $this->pageRenderer);
	}

	/**
	 * @return void
	 */
	public function setFakeControllerContext() {
		/** @var $controller Tx_Extbase_MVC_Controller_ControllerContext */
		$controller = $this->getMock(
			'Tx_Extbase_MVC_Controller_ControllerContext',
			array('getRequest')
		);

		$request = $this->getMock('Tx_Extbase_MVC_Request', array('getControllerExtensionName'));

		/** @noinspection PhpUndefinedMethodInspection */
		$controller->expects($this->once())->method('getRequest')
			->will($this->returnValue($request));

		$request->expects($this->once())->method('getControllerExtensionName')
			->will($this->returnValue('DfTools'));

		$this->fixture->setControllerContext($controller);
	}

	/**
	 * @test
	 * @return void
	 */
	public function languageFileCanBeAddedWithExtensionKey() {
		$this->fixture->injectPageRenderer($this->pageRenderer);
		$this->fixture->render('Foo/Bar.xml', 'DfTools');
	}

	/**
	 * @test
	 * @return void
	 */
	public function languageFileCanBeAddedWithoutExtensionKey() {
		$this->setFakeControllerContext();
		$this->fixture->injectPageRenderer($this->pageRenderer);
		$this->fixture->render('Foo/Bar.xml');
	}
}

?>