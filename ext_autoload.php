<?php

$extensionPath = t3lib_extMgm::extPath('df_tools');

return array(
	'tx_dftools_service_tcaparserservice' => $extensionPath . 'Classes/Service/TcaParserService.php',
	'tx_dftools_service_extbaseconnectorservice' => $extensionPath . 'Classes/Service/ExtBaseConnectorService.php',
	'tx_dftools_utility_tcautility' => $extensionPath . 'Classes/Utility/TcaUtility.php',
	'tx_dftools_extdirect_abstractdataprovider' => $extensionPath . 'Classes/ExtDirect/AbstractDataProvider.php',

	'tx_dftools_task_abstracttask' => $extensionPath . 'Classes/Task/AbstractTask.php',
	'tx_dftools_task_abstractfields' => $extensionPath . 'Classes/Task/AbstractFields.php',
	'tx_dftools_task_redirecttesttask' => $extensionPath . 'Classes/Task/RedirectTestTask.php',
	'tx_dftools_task_redirecttestfields' => $extensionPath . 'Classes/Task/RedirectTestFields.php',
	'tx_dftools_task_backlinktesttask' => $extensionPath . 'Classes/Task/BackLinkTestTask.php',
	'tx_dftools_task_backlinktestfields' => $extensionPath . 'Classes/Task/BackLinkTestFields.php',
	'tx_dftools_task_contentcomparisontesttask' => $extensionPath . 'Classes/Task/ContentComparisonTestTask.php',
	'tx_dftools_task_contentcomparisontestfields' => $extensionPath . 'Classes/Task/ContentComparisonTestFields.php',
	'tx_dftools_task_contentcomparisontestsynchronizetask' => $extensionPath . 'Classes/Task/ContentComparisonTestSynchronizeTask.php',
	'tx_dftools_task_linkchecktask' => $extensionPath . 'Classes/Task/LinkCheckTask.php',
	'tx_dftools_task_linkcheckfields' => $extensionPath . 'Classes/Task/LinkCheckFields.php',
	'tx_dftools_task_linkchecksynchronizetask' => $extensionPath . 'Classes/Task/LinkCheckSynchronizeTask.php',
	'tx_dftools_task_redirecttestrealurlimporttask' => $extensionPath . 'Classes/Task/RedirectTestRealUrlImportTask.php',

	'tx_dftools_extbaseconnectortestcase' => $extensionPath . 'Tests/Unit/ExtBaseConnectorTestCase.php',
	'tx_dftools_controller_controllertestcase' => $extensionPath . 'Tests/Unit/Controller/ControllerTestCase.php',
	'tx_dftools_viewhelpers_viewhelpertestcase' => $extensionPath . 'Tests/Unit/ViewHelpers/ViewHelperTestCase.php',
);

?>