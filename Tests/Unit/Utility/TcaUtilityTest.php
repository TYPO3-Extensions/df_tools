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

use SGalinski\DfTools\Domain\Model\BackLinkTest;
use SGalinski\DfTools\Domain\Repository\AbstractRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestCategoryRepository;
use SGalinski\DfTools\Domain\Repository\RedirectTestRepository;
use SGalinski\DfTools\Exception\GenericException;
use SGalinski\DfTools\Service\UrlChecker\AbstractService;
use SGalinski\DfTools\Utility\HtmlUtility;
use SGalinski\DfTools\Utility\HttpUtility;
use SGalinski\DfTools\Utility\LocalizationUtility;
use SGalinski\DfTools\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Class TcaUtilityTest
 */
class TcaUtilityTest extends BaseTestCase {
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
		$this->assertSame($expected, TcaUtility::filterCallback($input));
	}
}

?>