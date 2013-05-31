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
use SGalinski\DfTools\ViewHelpers\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class AbstractViewHelperTest
 */
class AbstractViewHelperTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\ViewHelpers\AbstractViewHelper
	 */
	protected $fixture;

	/**
	 * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected $backupTSFE;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->backupTSFE = $GLOBALS['TSFE'];
		$this->fixture = $this->getMock('SGalinski\DfTools\ViewHelpers\AbstractViewHelper', array('dummy'));
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