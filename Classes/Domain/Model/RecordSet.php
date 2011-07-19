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
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Record Set Assignments For The Link Checks
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Domain_Model_RecordSet extends Tx_Extbase_DomainObject_AbstractValueObject {
	/**
	 * Database Table
	 *
	 * @validate NotEmpty
	 * @var string $tableName
	 */
	protected $tableName = '';

	/**
	 * Database Field
	 *
	 * @validate NotEmpty
	 * @var string $field
	 */
	protected $field = '';

	/**
	 * Identifier
	 *
	 * @validate NotEmpty
	 * @validate Integer
	 * @var integer $identifier
	 */
	protected $identifier = 0;

	/**
	 * Setter for tableName
	 *
	 * @param string $tableName
	 * @return void
	 */
	public function setTableName($tableName) {
		$this->tableName = $tableName;
	}

	/**
	 * Getter for tableName
	 *
	 * @return string
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * Setter for field
	 *
	 * @param string $field
	 * @return void
	 */
	public function setField($field) {
		$this->field = $field;
	}

	/**
	 * Getter for field
	 *
	 * @return string
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * Setter for identifier
	 *
	 * @param integer $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = intval($identifier);
	}

	/**
	 * Getter for identifier
	 *
	 * @return integer
	 */
	public function getIdentifier() {
		return $this->identifier;
	}
}

?>