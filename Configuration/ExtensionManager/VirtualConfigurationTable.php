<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$TCA['tx_dftools_configuration'] = array(
	'ctrl' => array(
		'label' => 'title',
		'dividers2tabs' => TRUE,
	),
	'types' => array(
		0 => array(
			'showitem' => 'storagePid,excludedTables,excludedTableFields,disableAutoLinkSynchronizationFeature',
		),
	),
	'palettes' => array(
		0 => array(
			'showitem' => ''
		),
	),
	'columns' => array(
		'storagePid' => array(
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tca.xml:tx_dftools.storagePid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'selectedListStyle' => 'width: 250px; height: 34px;',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			),
		),
		'excludedTables' => array(
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tca.xml:tx_dftools.excludedTables',
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => 'EXT:df_tools/Configuration/ExtensionManager/Helper.php:tx_DfTools_ExtensionManager_Helper->getAllTables',
				'minitems' => 0,
				'maxitems' => 9999,
				'size' => 6,
			),
		),
		'excludedTableFields' => array(
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tca.xml:tx_dftools.excludedTableFields',
			'config' => array(
				'type' => 'select',
				'itemsProcFunc' => 'EXT:df_tools/Configuration/ExtensionManager/Helper.php:tx_DfTools_ExtensionManager_Helper->getAllTableFields',
				'minitems' => 0,
				'maxitems' => 9999,
				'size' => 6,
			),
		),
		'disableAutoLinkSynchronizationFeature' => array(
			'label' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tca.xml:tx_dftools.disableAutoLinkSynchronizationFeature',
			'config' => array(
				'type' => 'check',
			)
		),
	),
);

?>