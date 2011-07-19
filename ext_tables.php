<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
		// CSH for the virtual configuration table
	t3lib_extMgm::addLLrefForTCAdescr(
		'tx_dftools_configuration',
		'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_tx_dftools_configuration.xml'
	);

	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools',
		'tools',
		'',
		array(
			'Overview' => 'index',
			'RedirectTest' => 'index',
			'RedirectTestCategory' => 'read',
			'LinkCheck' => 'index',
			'BackLinkTest' => 'index',
			'ContentComparisonTest' => 'index',
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:df_tools/ext_icon.gif',
			'labels' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tools.xml',
		)
	);

	$icons = array(
		'run' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/run.png',
	);
	t3lib_SpriteManager::addSingleIcons($icons, $_EXTKEY);
}

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_redirecttest');
$TCA['tx_dftools_domain_model_redirecttest'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_redirecttest',
		'label' => 'test_url',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/RedirectTest.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_redirecttest.gif'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_redirecttestcategory');
$TCA['tx_dftools_domain_model_redirecttestcategory'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_redirecttestcategory',
		'label' => 'category',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/RedirectTestCategory.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_redirecttestcategory.gif'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_linkcheck');
$TCA['tx_dftools_domain_model_linkcheck'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_linkcheck',
		'label' => 'test_url',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/LinkCheck.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_linkcheck.gif'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_recordset');
$TCA['tx_dftools_domain_model_recordset'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_recordset',
		'label' => 'table_name',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/RecordSet.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_recordset.gif'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_backlinktest');
$TCA['tx_dftools_domain_model_backlinktest'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_backlinktest',
		'label' => 'test_url',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/BackLinkTest.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_backlinktest.gif'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_dftools_domain_model_contentcomparisontest');
$TCA['tx_dftools_domain_model_contentcomparisontest'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_db.xml:tx_dftools_domain_model_contentcomparisontest',
		'label' => 'test_url',
		'dividers2tabs' => true,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
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
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/ContentComparisonTest.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_dftools_domain_model_contentcomparisontest.gif'
	)
);

?>
