<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	/** @var $_EXTKEY string */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'SGalinski.' . $_EXTKEY,
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
			'icon' => 'EXT:df_tools/ext_icon.png',
			'labels' => 'LLL:EXT:df_tools/Resources/Private/Language/locallang_tools.xlf',
		)
	);

	$icons = array(
		'run' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath(
			$_EXTKEY
		) . 'Resources/Public/Icons/run.png',
	);
	\TYPO3\CMS\Backend\Sprite\SpriteManager::addSingleIcons($icons, $_EXTKEY);
}

/** @var $_EXTKEY string */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dftools_domain_model_redirecttest');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
	'tx_dftools_domain_model_redirecttestcategory'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dftools_domain_model_linkcheck');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dftools_domain_model_recordset');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_dftools_domain_model_backlinktest');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
	'tx_dftools_domain_model_contentcomparisontest'
);
?>