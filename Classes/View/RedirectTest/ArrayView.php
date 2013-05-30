<?php

namespace SGalinski\DfTools\View\RedirectTest;

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
use SGalinski\DfTools\Domain\Model\RedirectTest;
use SGalinski\DfTools\Utility\LocalizationUtility;
use SGalinski\DfTools\View\AbstractArrayView;

/**
 * Custom View
 *
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
class ArrayView extends AbstractArrayView {
	/**
	 * Returns the hmac configuration
	 *
	 * @return array
	 */
	protected function getHmacFieldConfiguration() {
		$namespace = $this->getNamespace();
		$configuration = array(
			'update' => array(
				$namespace . '[redirectTest][__identity]',
				$namespace . '[redirectTest][testUrl]',
				$namespace . '[redirectTest][expectedUrl]',
				$namespace . '[redirectTest][httpStatusCode]',
				$namespace . '[redirectTest][category][__identity]',
				$namespace . '[newCategory][category]',
			),
			'create' => array(
				$namespace . '[newRedirectTest][testUrl]',
				$namespace . '[newRedirectTest][expectedUrl]',
				$namespace . '[newRedirectTest][httpStatusCode]',
			),
		);

		return $configuration;
	}

	/**
	 * Renders a redirect test into a plain array
	 *
	 * @param RedirectTest $record
	 * @return array
	 */
	protected function getPlainRecord($record) {
		$category = $record->getCategory();

		return array(
			'__identity' => intval($record->getUid()),
			'testUrl' => htmlspecialchars($record->getTestUrl()),
			'expectedUrl' => htmlspecialchars($record->getExpectedUrl()),
			'httpStatusCode' => intval($record->getHttpStatusCode()),
			'testResult' => intval($record->getTestResult()),
			'testMessage' => htmlspecialchars(
				LocalizationUtility::localizeParameterDrivenString(
					$record->getTestMessage(), 'df_tools'
				)
			),
			'categoryId' => ($category === NULL ? '' : $category->getUid()),
		);
	}
}

?>