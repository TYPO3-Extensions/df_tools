<?php

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_redirecttestcategory',
		'label' => 'category',
		'dividers2tabs' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'searchFields' => 'category',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('df_tools') .
			'Resources/Public/Icons/tx_dftools_domain_model_redirecttestcategory.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'hidden, category, starttime, endtime',
	),
	'types' => array(
		'0' => array(
			'showitem' => 'hidden;;1, category,
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
		'category' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_redirecttestcategory.category',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required,unique'
			),
		),
	),
);
?>