<?php

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest',
		'label' => 'test_url',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'searchFields' => 'test_url, compare_url, test_result',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('df_tools') .
			'Resources/Public/Icons/tx_dftools_domain_model_contentcomparisontest.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, test_url, compare_url, test_result, test_message, test_content,
			compare_content, difference, starttime, endtime',
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1, test_url, compare_url, test_result, test_message, test_content,
				compare_content, difference, --div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
				starttime, endtime'
		),
	),
	'palettes' => array(
		'0' => array(
			'showitem' => ''
		),
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
			)
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 8,
				'max' => 20,
				'eval' => 'date',
				'default' => 0,
			)
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 8,
				'max' => 20,
				'eval' => 'date',
				'default' => 0,
			)
		),
		'test_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.test_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'compare_url' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.compare_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'test_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.test_content',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
		'compare_content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.compare_content',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
		'difference' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.difference',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
		'test_result' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.test_result',
			'config' => array(
				'type' => 'input',
				'size' => 1,
				'max' => 1,
				'eval' => 'int',
				'default' => 9,
				'range' => array(
					'upper' => 9,
					'lower' => 0,
				),
				'readOnly' => TRUE,
			),
		),
		'test_message' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest.test_message',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
	),
);
?>