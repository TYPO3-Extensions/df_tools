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

use SGalinski\DfTools\Domain\Model\RecordSet;
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;

/**
 * Test case for class Tx_DfTools_View_RecordSet_ArrayView
 *
 * @author Stefan Galinski <stefan@sgalinski.de>
 * @package df_tools
 */
class RecordSetArrayViewTest extends BaseTestCase {
	/**
	 * @var \SGalinski\DfTools\View\RecordSetArrayView|object
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$class = $this->buildAccessibleProxy('SGalinski\DfTools\View\RecordSetArrayView');
		$this->fixture = $this->getMockBuilder($class)
			->setMethods(array('getReadableTableAndFieldName'))
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
		/** @var $recordSetNormal RecordSet */
		$recordSetNormal = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RecordSet')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$recordSetNormal->setTableName('FooBar');
		$recordSetNormal->setField('FooBar');
		$recordSetNormal->setIdentifier('12');

		/** @var $recordSetWithXSS RecordSet */
		$recordSetWithXSS = $this->getMockBuilder('SGalinski\DfTools\Domain\Model\RecordSet')
			->setMethods(array('dummy'))->disableOriginalClone()->getMock();
		$recordSetWithXSS->setTableName('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$recordSetWithXSS->setField('<img src="" onerror="alert(\'Ooops!!!\');"/>');
		$recordSetWithXSS->setIdentifier(500);

		return array(
			'normal record set' => array(
				$recordSetNormal,
				array(
					'__identity' => 0,
					'tableName' => 'FooBar',
					'humanReadableTableName' => 'Readable',
					'field' => 'FooBar',
					'identifier' => 12
				),
			),
			'XSS attack' => array(
				$recordSetWithXSS,
				array(
					'__identity' => 0,
					'tableName' => htmlspecialchars('<img src="" onerror="alert(\'Ooops!!!\');"/>'),
					'humanReadableTableName' => 'Readable',
					'field' => 'FooBar',
					'identifier' => 500,
				),
			)
		);
	}

	/**
	 * @dataProvider recordsCanBeRenderedDataProvider
	 * @test
	 *
	 * @param RecordSet $recordSet
	 * @param array $expected
	 * @return void
	 */
	public function recordsCanBeRendered($recordSet, $expected) {
		/** @noinspection PhpUndefinedMethodInspection */
		$this->fixture->expects($this->once())->method('getReadableTableAndFieldName')
			->with($recordSet)->will($this->returnValue(array('Readable', 'FooBar')));

		$this->assertSame($expected, $this->fixture->_call('getPlainRecord', $recordSet));
	}
}

?>