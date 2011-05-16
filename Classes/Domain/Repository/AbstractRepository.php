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
 * Abstract Repository
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Domain_Repository_AbstractRepository extends Tx_Extbase_Persistence_Repository {
	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect = NULL;

	/**
	 * Initializes the repository default settings
	 *
	 * @return void
	 */
	public function initializeObject() {
		/** @var $querySettings Tx_Extbase_Persistence_Typo3QuerySettings */
		$querySettings = $this->objectManager->create('Tx_Extbase_Persistence_Typo3QuerySettings');
		$querySettings->setRespectStoragePage(FALSE);
		$this->setDefaultQuerySettings($querySettings);
	}

	/**
	 * Returns an instance of t3lib_pageSelect to call the enableFields method
	 * for self-made queries.
	 *
	 * @return t3lib_pageSelect
	 */
	public function getPageSelectInstance() {
		if ($this->pageSelect === NULL) {
			$this->pageSelect = t3lib_div::makeInstance('t3lib_pageSelect');
		}

		return $this->pageSelect;
	}
}

?>