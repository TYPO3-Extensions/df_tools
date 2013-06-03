<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	/** @var $_EXTKEY string */
	$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants(
		file_get_contents($extPath . 'Configuration/TypoScript/Backend/constants.txt')
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
		file_get_contents($extPath . 'Configuration/TypoScript/Backend/setup.txt')
	);

	// set global storage pid
	$dfToolsExtConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['df_tools']);
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['storagePid'] = intval($dfToolsExtConf['storagePid']);

	// hook registration
	if (!$dfToolsExtConf['disableAutoLinkSynchronizationFeature']) {
		$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
			'EXT:df_tools/Classes/Hooks/ProcessDatamap.php:SGalinski\DfTools\Hooks\ProcessDatamap';
		$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
			'EXT:df_tools/Classes/Hooks/ProcessDatamap.php:SGalinski\DfTools\Hooks\ProcessDatamap';
	}

	// Ext.Direct component registration
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.RedirectTest.DataProvider',
		$extPath . 'Classes/ExtDirect/RedirectTestDataProvider.php:SGalinski\DfTools\ExtDirect\RedirectTestDataProvider',
		'tools_DfToolsTools'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.RedirectTestCategory.DataProvider',
		$extPath . 'Classes/ExtDirect/RedirectTestCategoryDataProvider.php:SGalinski\DfTools\ExtDirect\RedirectTestCategoryDataProvider',
		'tools_DfToolsTools'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.LinkCheck.DataProvider',
		$extPath . 'Classes/ExtDirect/LinkCheckDataProvider.php:SGalinski\DfTools\ExtDirect\LinkCheckDataProvider',
		'tools_DfToolsTools'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.RecordSet.DataProvider',
		$extPath . 'Classes/ExtDirect/RecordSetDataProvider.php:SGalinski\DfTools\ExtDirect\RecordSetDataProvider',
		'tools_DfToolsTools'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.BackLinkTest.DataProvider',
		$extPath . 'Classes/ExtDirect/BackLinkTestDataProvider.php:SGalinski\DfTools\ExtDirect\BackLinkTestDataProvider',
		'tools_DfToolsTools'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::registerExtDirectComponent(
		'TYPO3.DfTools.ContentComparisonTest.DataProvider',
		$extPath . 'Classes/ExtDirect/ContentComparisonTestDataProvider.php:SGalinski\DfTools\ExtDirect\ContentComparisonTestDataProvider',
		'tools_DfToolsTools'
	);

	// fix for loading of EXT:tinymce_rte
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('RTE.default.useFEediting = 1');

	// register commandlets
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
		'SGalinski\DfTools\Command\RedirectTestCommandController';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
		'SGalinski\DfTools\Command\ContentComparisonTestCommandController';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
		'SGalinski\DfTools\Command\BacklinkTestCommandController';

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
		'SGalinski\DfTools\Command\LinkCheckCommandController';
}

?>