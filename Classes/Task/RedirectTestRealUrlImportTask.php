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
 * Scheduler task to create redirect tests of the entries in the realUrl redirect table
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_Task_RedirectTestRealUrlImportTask extends Tx_DfTools_Task_AbstractTask {
	/**
	 * Calls the ExtBase controller to execute the tests
	 *
	 * @return boolean
	 */
	public function execute() {
		$this->getExtBaseConnector()->runControllerAction('RedirectTest', 'importFromRealUrl');
		return TRUE;
	}

	/**
	 * Sends a notification email about the failed tests
	 *
	 * Note: Unused!
	 *
	 * @param array $failedRecords
	 * @return void
	 */
	protected function sendNotificationEmail(array $failedRecords) {
	}
}

?>