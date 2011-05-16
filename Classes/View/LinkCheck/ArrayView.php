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
 * Custom View
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class Tx_DfTools_View_LinkCheck_ArrayView extends Tx_DfTools_View_AbstractArrayView {
	/**
	 * Returns the hmac configuration
	 *
	 * @return array
	 */
	protected function getHmacFieldConfiguration() {
		$namespace = $this->getNamespace();
		$configuration = array(
			'update' => array(
				$namespace . '[linkCheck][__identity]',
				$namespace . '[linkCheck][testUrl]',
				$namespace . '[linkCheck][resultUrl]',
				$namespace . '[linkCheck][httpStatusCode]'
			),
			'create' => array(
				$namespace . '[newLinkCheck][testUrl]',
				$namespace . '[newLinkCheck][resultUrl]',
				$namespace . '[newLinkCheck][httpStatusCode]',
			),
		);

		return $configuration;
	}

	/**
	 * Renders an link check test into a plain array
	 *
	 * @param Tx_DfTools_Domain_Model_LinkCheck $record
	 * @return array
	 */
	protected function getPlainRecord($record) {
		return array(
			'__identity' => intval($record->getUid()),
			'testUrl' => htmlspecialchars($record->getTestUrl()),
			'resultUrl' => htmlspecialchars($record->getResultUrl()),
			'httpStatusCode' => intval($record->getHttpStatusCode()),
			'testResult' => intval($record->getTestResult()),
			'testMessage' => htmlspecialchars(
				Tx_DfTools_Utility_LocalizationUtility::localizeParameterDrivenString(
					$record->getTestMessage(), 'df_tools'
				)
			),
		);
	}
}

?>