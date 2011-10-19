<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/** @var $TCA array */
$TCA['tx_dftools_domain_model_linkcheck'] = array(
	'ctrl' => $TCA['tx_dftools_domain_model_linkcheck']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden,
			test_url, result_url, http_status_code, test_result, test_message, record_sets, starttime, endtime',
	),
	'types' => array(
		'0' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
				test_url,result_url, http_status_code, test_result, test_message, record_sets,
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
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
				),
			)
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_dftools_domain_model_linkcheck',
				'foreign_table_where' => 'AND tx_dftools_domain_model_linkcheck.pid=###CURRENT_PID### ' .
					' AND tx_dftools_domain_model_linkcheck.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
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