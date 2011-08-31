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

require_once(t3lib_extMgm::extPath('fluid') . 'Tests/Unit/ViewHelpers/ViewHelperBaseTestcase.php');

/**
 * Special Test Case For View Helpers
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_ViewHelpers_ViewHelperTestCase extends Tx_Fluid_ViewHelpers_ViewHelperBaseTestcase {
	/**
	 * @var object
	 */
	protected $fixture;

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
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->pageRenderer = $this->getMockBuilder('t3lib_PageRenderer')->disableOriginalConstructor()->getMock();
		$this->fixture->expects($this->once())->method('getPageRenderer')
			->will($this->returnValue($this->pageRenderer));
		$this->injectDependenciesIntoViewHelper($this->fixture);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture, $this->pageRenderer);
	}
}

?>