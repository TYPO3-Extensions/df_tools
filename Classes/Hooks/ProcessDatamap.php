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
 * Hooks for synchronize urls directly on-the-fly while editing
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class tx_DfTools_Hooks_ProcessDatamap {
	/**
	 * BootStrap Instance
	 *
	 * @var Tx_DfTools_Service_ExtBaseConnectorService
	 */
	protected $extBaseConnector = NULL;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->extBaseConnector = t3lib_div::makeInstance('Tx_DfTools_Service_ExtBaseConnectorService');
		$this->extBaseConnector->setExtensionKey('DfTools');
		$this->extBaseConnector->setModuleOrPluginKey('tools_DfToolsTools');
	}

	/**
	 * Hook for synchronizing urls after any database operation
	 *
	 * @param t3lib_tcemain $tceMain
	 * @return void
	 */
	public function processDatamap_afterAllOperations($tceMain) {
		foreach ($tceMain->datamap as $table => $tableData) {
			foreach ($tableData as $identity => $_) {
				if (strpos($identity, 'NEW') !== FALSE) {
					$identity = $tceMain->substNEWwithIDs[$identity];
				}

				/** @noinspection PhpUndefinedFieldInspection */
				$this->extBaseConnector->setParameters(array('table' => $table, 'identity' => intval($identity)));
				$this->extBaseConnector->runControllerAction('LinkCheck', 'synchronizeUrlsFromASingleRecord');
			}
		}
	}

	/**
	 * Hook for synchronizing urls after any command map operation like delete and recover
	 *
	 * @param string $command
	 * @param string $table
	 * @param int $identity
	 * @return void
	 */
	public function processCmdmap_postProcess($command, $table, $identity) {
		if (!in_array($command, array('delete', 'undelete'))) {
			return;
		}

		/** @noinspection PhpUndefinedFieldInspection */
		$this->extBaseConnector->setParameters(array('table' => $table, 'identity' => intval($identity)));
		$this->extBaseConnector->runControllerAction('LinkCheck', 'synchronizeUrlsFromASingleRecord');
	}
}

?>