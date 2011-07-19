<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 domainfactory GmbH (Stefan Galinski <sgalinski@df.eu>)
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
 * Test case for class Tx_DfTools_Domain_Model_RecordSet.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_RecordSetTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_DfTools_Domain_Model_RecordSet
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = new Tx_DfTools_Domain_Model_RecordSet();
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @return void
	 */
	public function objectCanBeInitialized() {
		$this->assertSame(0, $this->fixture->getIdentifier());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setTableNameWorks() {
		$this->fixture->setTableName('FooBar');
		$this->assertSame('FooBar', $this->fixture->getTableName());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setFieldWorks() {
		$this->fixture->setField('FooBar');
		$this->assertSame('FooBar', $this->fixture->getField());
	}

	/**
	 * @test
	 * @return void
	 */
	public function setIdentifierWorks() {
		$this->fixture->setIdentifier(12);
		$this->assertSame(12, $this->fixture->getIdentifier());

		$this->fixture->setIdentifier('12');
		$this->assertSame(12, $this->fixture->getIdentifier());
	}
}

?>