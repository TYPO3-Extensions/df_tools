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
 * @author Stefan Galinski <sgalinski@df.eu>
 * @package df_tools
 */
abstract class Tx_DfTools_Task_AbstractFields implements tx_scheduler_AdditionalFieldProvider {
	/**
	 * @var	string
	 */
	protected $fieldPrefix = '';

	/**
	 * Sets the field prefix
	 *
	 * @param string $fieldPrefix
	 * @return void
	 */
	public function setFieldPrefix($fieldPrefix) {
		$this->fieldPrefix = $fieldPrefix;
	}

	/**
	 * Returns the field prefix
	 *
	 * @return string
	 */
	public function getFieldPrefix() {
		return $this->fieldPrefix;
	}

	/**
	 * Constructs the full field name which can be used in HTML markup.
	 *
	 * @param string $fieldName
	 * @return string
	 */
	protected function getFullFieldName($fieldName) {
		return $this->fieldPrefix . ucfirst($fieldName);
	}

	/**
	 * Returns additional fields for rendering in the add/edit tasks form
	 *
	 * Structure:
	 *
	 * array(
	 *   'Identifier' => array(
	 *	   'fieldId' => array(
	 *	     'code' => '',
	 *	     'label' => '',
	 *	     'cshKey' => '',
	 *	     'cshLabel' => ''
	 *	   )
	 *   )
	 * )
	 *
	 * @param array	$taskInfo Values of the fields from the add/edit task form
	 * @param Tx_DfTools_Task_AbstractTask $task The task object being edited. Null when adding a task!
	 * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
	 * @return array
	 */
	public function getAdditionalFields(array &$taskInfo, $task, tx_scheduler_Module $schedulerModule) {
		$fieldName = $this->getFullFieldName('notificationEmailAddress');
		if ($schedulerModule->CMD === 'edit') {
			$taskInfo[$fieldName] = $task->getNotificationEmailAddress();
		}

		$fieldId = 'task_' . $fieldName;
		$fieldHtml = '<input type="text" name="tx_scheduler[' . $fieldName . ']" ' .
			'id="' . $fieldId . '" value="' . htmlspecialchars($taskInfo[$fieldName]) . '" />';

		$additionalFields[$fieldId] = array(
			'code' => $fieldHtml,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:' .
				'tx_dftools_common.scheduler.notificationEmailAddress',
			'cshKey' => '',
			'cshLabel' => $fieldId
		);

		return $additionalFields;
	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
	 * @return boolean True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, tx_scheduler_Module $schedulerModule) {
		$validInput = TRUE;
		$fieldName = $this->getFullFieldName('notificationEmailAddress');

		$sanitizedAddresses = array();
		$addresses = explode(',', $submittedData[$fieldName]);
		foreach ($addresses as $address) {
			$address = filter_var(trim($address), FILTER_VALIDATE_EMAIL);
			if ($address === FALSE) {
				$validInput = FALSE;
				$label = $GLOBALS['LANG']->sL('LLL:EXT:df_tools/Resources/Private/Language/locallang.xml:' .
											  'tx_dftools_common.scheduler.emailNotValid');
				$schedulerModule->addMessage($label, t3lib_FlashMessage::ERROR);
				break;
			}
			$sanitizedAddresses[] = $address;
		}
		$submittedData[$fieldName] = implode(',', $sanitizedAddresses);

		return $validInput;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param array	$submittedData An array containing the data submitted by the add/edit task form
	 * @param Tx_DfTools_Task_AbstractTask	$task Reference to the scheduler backend module
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, tx_scheduler_Task $task) {
		$address = $submittedData[$this->getFullFieldName('notificationEmailAddress')];
		$task->setNotificationEmailAddress($address);
	}
}

?>