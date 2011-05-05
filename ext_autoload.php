<?php

$extensionPath = t3lib_extMgm::extPath('df_tools');

return array(
	'tx_dftools_service_extbaseconnectorservice' => $extensionPath . 'Classes/Service/ExtBaseConnectorService.php',
	'tx_dftools_extdirect_abstractdataprovider' => $extensionPath . 'Classes/ExtDirect/AbstractDataProvider.php',

	'tx_dftools_task_abstracttask' => $extensionPath . 'Classes/Task/AbstractTask.php',
	'tx_dftools_task_abstractfields' => $extensionPath . 'Classes/Task/AbstractFields.php',
	'tx_dftools_task_redirecttesttask' => $extensionPath . 'Classes/Task/RedirectTestTask.php',
	'tx_dftools_task_redirecttestfields' => $extensionPath . 'Classes/Task/RedirectTestFields.php',
	'tx_dftools_task_contentcomparisontesttask' => $extensionPath . 'Classes/Task/ContentComparisonTestTask.php',
	'tx_dftools_task_contentcomparisontestfields' => $extensionPath . 'Classes/Task/ContentComparisonTestFields.php',
	'tx_dftools_task_linkchecktask' => $extensionPath . 'Classes/Task/LinkCheckTask.php',
	'tx_dftools_task_linkcheckfields' => $extensionPath . 'Classes/Task/LinkCheckFields.php',
	'tx_dftools_task_linkchecksynchronizetask' => $extensionPath . 'Classes/Task/LinkCheckSynchronizeTask.php',

	'tx_dftools_tests_extbaseconnectortestcase' => $extensionPath . 'Tests/ExtBaseConnectorTestCase.php',
	'tx_dftools_tests_controllertestcase' => $extensionPath . 'Tests/ControllerTestCase.php',
);

?>