<?php

namespace SGalinski\DfTools\View;

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

use SGalinski\DfTools\Domain\Model\ContentComparisonTest;
use SGalinski\DfTools\Utility\LocalizationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom View
 */
class ContentComparisonTestArrayView extends AbstractArrayView {
	/**
	 * Returns the hmac configuration
	 *
	 * @return array
	 */
	protected function getHmacFieldConfiguration() {
		$namespace = $this->getNamespace();
		$configuration = array(
			'update' => array(
				$namespace . '[contentComparisonTest][__identity]',
				$namespace . '[contentComparisonTest][testUrl]',
				$namespace . '[contentComparisonTest][compareUrl]',
				$namespace . '[contentComparisonTest][difference]',
			),
			'create' => array(
				$namespace . '[newContentComparisonTest][testUrl]',
				$namespace . '[newContentComparisonTest][compareUrl]',
				$namespace . '[newContentComparisonTest][difference]',
				$namespace . '[newContentComparisonTest][testResult]',
				$namespace . '[newContentComparisonTest][testMessage]',
			),
		);

		return $configuration;
	}

	/**
	 * Renders a redirect test into a plain array
	 *
	 * @param ContentComparisonTest $record
	 * @return array
	 */
	protected function getPlainRecord($record) {
		return array(
			'__identity' => intval($record->getUid()),
			'testUrl' => htmlspecialchars($record->getTestUrl()),
			'compareUrl' => htmlspecialchars($record->getCompareUrl()),
			'difference' => GeneralUtility::removeXSS($record->getDifference()),
			'testResult' => intval($record->getTestResult()),
			'testMessage' => htmlspecialchars(
				LocalizationUtility::localizeParameterDrivenString(
					$record->getTestMessage(), 'df_tools'
				)
			),
		);
	}
}

?>