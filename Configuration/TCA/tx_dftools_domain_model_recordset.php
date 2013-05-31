<?php

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_recordset',
		'label' => 'table_name',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'searchFields' => 'table_name',
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('df_tools') .
		'Resources/Public/Icons/tx_dftools_domain_model_recordset.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden,
			table_name, field, identifier, starttime, endtime',
	),
	'types' => array(
		'0' => array(
			'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
				table_name, field, identifier,
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
				'foreign_table' => 'tx_dftools_domain_model_recordset',
				'foreign_table_where' => 'AND tx_dftools_domain_model_recordset.pid=###CURRENT_PID### ' .
				' AND tx_dftools_domain_model_recordset.sys_language_uid IN (-1,0)',
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
		'table_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_recordset.table_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'field' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_recordset.field',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'identifier' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_recordset.identifier',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int,required'
			),
		),
	),
);
?>