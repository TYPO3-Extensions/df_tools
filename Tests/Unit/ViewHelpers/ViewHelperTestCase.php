<?php

namespace SGalinski\DfTools\Tests\Unit\ViewHelpers;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Fluid\Tests\Unit\ViewHelpers\ViewHelperBaseTestcase;

require_once(ExtensionManagementUtility::extPath('fluid') . 'Tests/Unit/ViewHelpers/ViewHelperBaseTestcase.php');

/**
 * Class ViewHelperTestCase
 */
abstract class ViewHelperTestCase extends ViewHelperBaseTestcase {
	/**
	 * @var \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper|object
	 */
	protected $fixture;

	/**
	 * Page Renderer
	 *
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		/** @noinspection PhpUndefinedMethodInspection */
		$this->pageRenderer = $this->getMockBuilder('TYPO3\CMS\Core\Page\PageRenderer')->disableOriginalConstructor(
		)->getMock();
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