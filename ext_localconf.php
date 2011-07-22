<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	$extPath = t3lib_extMgm::extPath($_EXTKEY);
	t3lib_extMgm::addTypoScriptConstants(
		file_get_contents($extPath . 'Configuration/TypoScript/Backend/ext_typoscript_constants.txt')
	);
	
	t3lib_extMgm::addTypoScriptSetup(
		file_get_contents($extPath . 'Configuration/TypoScript/Backend/ext_typoscript_setup.txt')
	);

		// set global storage pid
	$dfToolsExtConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['df_tools']);
	$storagePids = Tx_DfTools_Utility_TcaUtility::stripTablePrefixFromGroupDBValues($dfToolsExtConf['storagePid']);
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['storagePid'] = $storagePids[0];

		// hook registration
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
		'EXT:df_tools/Classes/Hooks/ProcessDatamap.php:tx_DfTools_Hooks_ProcessDatamap';

		// Scheduler registration
	$prefix = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xml:';
	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_RedirectTestTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_redirecttest.scheduler.name',
		'description' => $prefix . 'tx_dftools_domain_model_redirecttest.scheduler.description',
		'additionalFields' => 'tx_DfTools_Task_RedirectTestFields'
	);

	$prefix = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xml:';
	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_RedirectTestRealUrlImportTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_redirecttest.schedulerImport.name',
		'description' => $prefix . 'tx_dftools_domain_model_redirecttest.schedulerImport.description'
	);

	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_ContentComparisonTestTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_contentcomparisontest.scheduler.name',
		'description' => $prefix . 'tx_dftools_domain_model_contentcomparisontest.scheduler.description',
		'additionalFields' => 'tx_DfTools_Task_ContentComparisonTestFields'
	);

	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_BackLinkTestTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_backlinktest.scheduler.name',
		'description' => $prefix . 'tx_dftools_domain_model_backlinktest.scheduler.description',
		'additionalFields' => 'tx_DfTools_Task_BackLinkTestFields'
	);

	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_LinkCheckTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_linkcheck.scheduler.name',
		'description' => $prefix . 'tx_dftools_domain_model_linkcheck.scheduler.description',
		'additionalFields' => 'tx_DfTools_Task_LinkCheckFields'
	);

	$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_DfTools_Task_LinkCheckSynchronizeTask'] = array(
		'extension' => $_EXTKEY,
		'title' => $prefix . 'tx_dftools_domain_model_linkcheck.schedulerSync.name',
		'description' => $prefix . 'tx_dftools_domain_model_linkcheck.schedulerSync.description'
	);

		// Ext.Direct registration
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.RedirectTest.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/RedirectTestDataProvider.php:Tx_DfTools_ExtDirect_RedirectTestDataProvider';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.RedirectTestCategory.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/RedirectTestCategoryDataProvider.php:Tx_DfTools_ExtDirect_RedirectTestCategoryDataProvider';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.LinkCheck.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/LinkCheckDataProvider.php:Tx_DfTools_ExtDirect_LinkCheckDataProvider';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.RecordSet.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/RecordSetDataProvider.php:Tx_DfTools_ExtDirect_RecordSetDataProvider';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.BackLinkTest.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/BackLinkTestDataProvider.php:Tx_DfTools_ExtDirect_BackLinkTestDataProvider';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ExtDirect']['TYPO3.DfTools.ContentComparisonTest.DataProvider'] =
		t3lib_extMgm::extPath($_EXTKEY) . 'Classes/ExtDirect/ContentComparisonTestDataProvider.php:Tx_DfTools_ExtDirect_ContentComparisonTestDataProvider';
}

?>