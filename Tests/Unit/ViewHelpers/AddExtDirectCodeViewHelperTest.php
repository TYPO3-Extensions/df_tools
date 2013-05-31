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

use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\ViewHelpers\AddExtDirectCodeViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class AddExtDirectCodeViewHelperTest
 */
class AddExtDirectCodeViewHelperTest extends ViewHelperTestCase {
	/**
	 * @var \SGalinski\DfTools\ViewHelpers\AddExtDirectCodeViewHelper
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = 'SGalinski\DfTools\ViewHelpers\AddExtDirectCodeViewHelper';
		$this->fixture = $this->getMock($class, array('getPageRenderer'));
		parent::setUp();
	}

	/**
	 * @test
	 * @return void
	 */
	public function extDirectCodeCanBeAdded() {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->pageRenderer->expects($this->once())->method('addExtDirectCode');
		$this->fixture->render();
	}
}

?>