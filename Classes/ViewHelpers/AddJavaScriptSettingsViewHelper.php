<?php

namespace SGalinski\DfTools\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) domainfactory GmbH (Stefan Galinski <stefan.galinski@gmail.com>)
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

use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * View helper to add the df_tools inline settings for the access via javascript
 *
 * Example:
 * {namespace df=Tx_DfTools_ViewHelpers}
 * <df:addJavaScriptSettings />
 */
class AddJavaScriptSettingsViewHelper extends AbstractViewHelper {
	/**
	 * Adds javascript inline settings
	 *
	 * @return void
	 */
	public function render() {
		$settings = array(
			'Sprites' => array(
				'create' => IconUtility::getSpriteIconClasses('actions-edit-add'),
				'destroy' => IconUtility::getSpriteIconClasses('actions-edit-delete'),
				'edit' => IconUtility::getSpriteIconClasses('actions-document-open'),
				'run' => IconUtility::getSpriteIconClasses('extensions-df_tools-run'),
				'refresh' => IconUtility::getSpriteIconClasses('actions-system-refresh'),
				'error' => IconUtility::getSpriteIconClasses('status-dialog-error'),
				'warning' => IconUtility::getSpriteIconClasses('status-dialog-warning'),
				'information' => IconUtility::getSpriteIconClasses('status-dialog-information'),
				'unknown' => IconUtility::getSpriteIconClasses('actions-system-help-open'),
				'ok' => IconUtility::getSpriteIconClasses('status-dialog-ok'),
				'hide' => IconUtility::getSpriteIconClasses('actions-edit-hide'),
				'unhide' => IconUtility::getSpriteIconClasses('actions-edit-unhide'),
				'showPage' => IconUtility::getSpriteIconClasses('actions-document-view'),
				'comment' => IconUtility::getSpriteIconClasses('actions-edit-localize-status-low'),
			),
			'Settings' => array(
				'destroyWindowFile' => '../' . ExtensionManagementUtility::siteRelPath('df_tools') .
					'/Resources/Public/Templates/destroyWindow.html',
			),
		);

		$this->getPageRenderer()->addInlineSettingArray('DfTools', $settings);

	}
}

?>