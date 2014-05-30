<?php

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck',
		'label' => 'test_url',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'searchFields' => 'test_url, result_url',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('df_tools') .
			'Resources/Public/Icons/tx_dftools_domain_model_linkcheck.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, test_url, result_url, http_status_code, test_result, test_message,
		record_sets, starttime, endtime',
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1, test_url,result_url, http_status_code, test_result, test_message, record_sets,
				--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
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
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.test_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required',
				'readOnly' => TRUE,
			),
		),
		'result_url' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.result_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
		'test_result' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.test_result',
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
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.test_message',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 2,
				'eval' => 'trim',
				'readOnly' => TRUE,
			),
		),
		'http_status_code' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.http_status_code',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int',
				'readOnly' => TRUE,
			),
		),
		'record_sets' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck.record_sets',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_dftools_domain_model_recordset',
				'MM' => 'tx_dftools_linkcheck_recordset_mm',
				'maxitems' => 99999,
				'appearance' => array(
					'collapse' => 0,
					'levelLinksPosition' => 'bottom',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1,
				),
				'readOnly' => TRUE,
			),
		),
	),
);
?>