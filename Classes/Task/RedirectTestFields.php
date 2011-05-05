<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Stefan Galinski <sgalinski@df.eu>, domainfactory GmbH
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
 * Additional fields for the scheduler redirect test task
 *
 * Note: The class must begin with a lower cased "tx_". Otherwise an exception
 * is thrown by TYPO3.
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class tx_DfTools_Task_RedirectTestFields extends Tx_DfTools_Task_AbstractFields {
	/**
	 * @var	string
	 */
	protected $fieldPrefix = 'Tx_DfTools_Task_RedirectTestTask_';
}

?>