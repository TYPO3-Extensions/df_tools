<?php

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('df_tools');

return array(
	// Required, because of the usage in the scheduler and Ext.Direct before ExtBase is instantiated
	'sgalinski\dftools\connector\extbaseconnector' => $extensionPath . 'Classes/Connector/ExtBaseConnector.php',
	'sgalinski\dftools\extdirect\abstractdataprovider' => $extensionPath . 'Classes/ExtDirect/AbstractDataProvider.php',

	// Required, because of the usage in phpunit before ExtBase is instantiated
	// @TODO häääää?
	'sgalinski\dftools\extbaseconnectortestcase' => $extensionPath . 'Tests/Unit/ExtBaseConnectorTestCase.php',
	'sgalinski\dftools\controller\controllertestcase' => $extensionPath . 'Tests/Unit/Controller/ControllerTestCase.php',
	'sgalinski\dftools\viewhelpers\viewhelpertestcase' => $extensionPath . 'Tests/Unit/ViewHelpers/ViewHelperTestCase.php',

	// Required, because of the usage in the TCA
	'sgalinski\dftools\parser\tcaparser' => $extensionPath . 'Classes/Parser/TcaParser.php',
	'sgalinski\dftools\utility\tcautility' => $extensionPath . 'Classes/Utility/TcaUtility.php',

	// Required, because they are used within the scheduler
	'sgalinski\dftools\task\abstracttask' => $extensionPath . 'Classes/Task/AbstractTask.php',
	'sgalinski\dftools\task\abstractfields' => $extensionPath . 'Classes/Task/AbstractFields.php',
	'sgalinski\dftools\task\redirecttesttask' => $extensionPath . 'Classes/Task/RedirectTestTask.php',
	'sgalinski\dftools\task\redirecttestfields' => $extensionPath . 'Classes/Task/RedirectTestFields.php',
	'sgalinski\dftools\task\backlinktesttask' => $extensionPath . 'Classes/Task/BackLinkTestTask.php',
	'sgalinski\dftools\task\backlinktestfields' => $extensionPath . 'Classes/Task/BackLinkTestFields.php',
	'sgalinski\dftools\task\contentcomparisontesttask' => $extensionPath . 'Classes/Task/ContentComparisonTestTask.php',
	'sgalinski\dftools\task\contentcomparisontestfields' => $extensionPath . 'Classes/Task/ContentComparisonTestFields.php',
	'sgalinski\dftools\task\contentcomparisontestsynchronizetask' => $extensionPath . 'Classes/Task/ContentComparisonTestSynchronizeTask.php',
	'sgalinski\dftools\task\linkchecktask' => $extensionPath . 'Classes/Task/LinkCheckTask.php',
	'sgalinski\dftools\task\linkcheckfields' => $extensionPath . 'Classes/Task/LinkCheckFields.php',
	'sgalinski\dftools\task\linkchecksynchronizetask' => $extensionPath . 'Classes/Task/LinkCheckSynchronizeTask.php',
	'sgalinski\dftools\task\redirecttestrealurlimporttask' => $extensionPath . 'Classes/Task/RedirectTestRealUrlImportTask.php',
);

?>