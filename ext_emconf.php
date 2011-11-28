<?php

########################################################################
# Extension Manager/Repository config file for ext "df_tools".
#
# Auto generated 24-11-2011 20:32
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'dF Tools',
	'description' => 'Contains some useful tools like a testing tool for redirects, a link checker, a back link checker and a content comparison tool between the same or different urls. Furthermore there is full scheduler support for all tests and synchronization tasks.',
	'category' => 'be',
	'author' => 'Stefan Galinski',
	'author_email' => 'sgalinski@df.eu',
	'author_company' => 'domainfactory GmbH',
	'shy' => '',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'doNotLoadInFE' => 1,
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '1.4.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.5-4.6.99',
			'extbase' => '1.3.0-1.4.99',
			'fluid' => '1.3.0-1.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:200:{s:16:"ext_autoload.php";s:4:"eb8a";s:21:"ext_conf_template.txt";s:4:"04e4";s:12:"ext_icon.gif";s:4:"c092";s:17:"ext_localconf.php";s:4:"90bb";s:14:"ext_tables.php";s:4:"88f3";s:14:"ext_tables.sql";s:4:"c0c9";s:41:"Classes/Controller/AbstractController.php";s:4:"133c";s:45:"Classes/Controller/BackLinkTestController.php";s:4:"0997";s:54:"Classes/Controller/ContentComparisonTestController.php";s:4:"1c68";s:42:"Classes/Controller/LinkCheckController.php";s:4:"95cc";s:41:"Classes/Controller/OverviewController.php";s:4:"ccd9";s:53:"Classes/Controller/RedirectTestCategoryController.php";s:4:"6d2e";s:45:"Classes/Controller/RedirectTestController.php";s:4:"8994";s:37:"Classes/Domain/Model/BackLinkTest.php";s:4:"a537";s:46:"Classes/Domain/Model/ContentComparisonTest.php";s:4:"988a";s:34:"Classes/Domain/Model/LinkCheck.php";s:4:"7bf8";s:34:"Classes/Domain/Model/RecordSet.php";s:4:"8fe7";s:37:"Classes/Domain/Model/RedirectTest.php";s:4:"107e";s:45:"Classes/Domain/Model/RedirectTestCategory.php";s:4:"05f0";s:42:"Classes/Domain/Model/TestableInterface.php";s:4:"4064";s:48:"Classes/Domain/Repository/AbstractRepository.php";s:4:"b5a8";s:52:"Classes/Domain/Repository/BackLinkTestRepository.php";s:4:"a635";s:61:"Classes/Domain/Repository/ContentComparisonTestRepository.php";s:4:"a677";s:49:"Classes/Domain/Repository/LinkCheckRepository.php";s:4:"3d04";s:49:"Classes/Domain/Repository/RecordSetRepository.php";s:4:"b078";s:60:"Classes/Domain/Repository/RedirectTestCategoryRepository.php";s:4:"b3a7";s:52:"Classes/Domain/Repository/RedirectTestRepository.php";s:4:"53a7";s:42:"Classes/ExtDirect/AbstractDataProvider.php";s:4:"d005";s:46:"Classes/ExtDirect/BackLinkTestDataProvider.php";s:4:"4b0a";s:55:"Classes/ExtDirect/ContentComparisonTestDataProvider.php";s:4:"8cee";s:43:"Classes/ExtDirect/LinkCheckDataProvider.php";s:4:"7205";s:43:"Classes/ExtDirect/RecordSetDataProvider.php";s:4:"c6b3";s:54:"Classes/ExtDirect/RedirectTestCategoryDataProvider.php";s:4:"49f3";s:46:"Classes/ExtDirect/RedirectTestDataProvider.php";s:4:"0a92";s:32:"Classes/Hooks/ProcessDatamap.php";s:4:"4422";s:33:"Classes/Response/AjaxResponse.php";s:4:"b76c";s:43:"Classes/Service/ExtBaseConnectorService.php";s:4:"c9b3";s:36:"Classes/Service/LinkCheckService.php";s:4:"5a1c";s:40:"Classes/Service/RealUrlImportService.php";s:4:"f193";s:36:"Classes/Service/TcaParserService.php";s:4:"487d";s:36:"Classes/Service/UrlParserService.php";s:4:"82c5";s:41:"Classes/Service/UrlSynchronizeService.php";s:4:"84a5";s:46:"Classes/Service/UrlChecker/AbstractService.php";s:4:"d0fd";s:42:"Classes/Service/UrlChecker/CurlService.php";s:4:"ae72";s:38:"Classes/Service/UrlChecker/Factory.php";s:4:"9432";s:44:"Classes/Service/UrlChecker/StreamService.php";s:4:"072e";s:31:"Classes/Task/AbstractFields.php";s:4:"c0bc";s:29:"Classes/Task/AbstractTask.php";s:4:"a336";s:35:"Classes/Task/BackLinkTestFields.php";s:4:"f28e";s:33:"Classes/Task/BackLinkTestTask.php";s:4:"c994";s:44:"Classes/Task/ContentComparisonTestFields.php";s:4:"b8ad";s:53:"Classes/Task/ContentComparisonTestSynchronizeTask.php";s:4:"e4bc";s:42:"Classes/Task/ContentComparisonTestTask.php";s:4:"4d12";s:32:"Classes/Task/LinkCheckFields.php";s:4:"e39d";s:41:"Classes/Task/LinkCheckSynchronizeTask.php";s:4:"6468";s:30:"Classes/Task/LinkCheckTask.php";s:4:"caeb";s:35:"Classes/Task/RedirectTestFields.php";s:4:"e2c8";s:46:"Classes/Task/RedirectTestRealUrlImportTask.php";s:4:"0576";s:33:"Classes/Task/RedirectTestTask.php";s:4:"eb11";s:37:"Classes/Utility/CompressorUtility.php";s:4:"6aa9";s:31:"Classes/Utility/HtmlUtility.php";s:4:"c67b";s:31:"Classes/Utility/HttpUtility.php";s:4:"343a";s:39:"Classes/Utility/LocalizationUtility.php";s:4:"daa9";s:31:"Classes/Utility/PageUtility.php";s:4:"dc37";s:30:"Classes/Utility/TcaUtility.php";s:4:"a764";s:34:"Classes/View/AbstractArrayView.php";s:4:"8bd8";s:39:"Classes/View/BackLinkTest/ArrayView.php";s:4:"0516";s:48:"Classes/View/ContentComparisonTest/ArrayView.php";s:4:"4e8e";s:36:"Classes/View/LinkCheck/ArrayView.php";s:4:"5d59";s:36:"Classes/View/RecordSet/ArrayView.php";s:4:"7fea";s:39:"Classes/View/RedirectTest/ArrayView.php";s:4:"9143";s:47:"Classes/View/RedirectTestCategory/ArrayView.php";s:4:"625a";s:42:"Classes/ViewHelpers/AbstractViewHelper.php";s:4:"ee60";s:44:"Classes/ViewHelpers/AddCssFileViewHelper.php";s:4:"eab3";s:50:"Classes/ViewHelpers/AddExtDirectCodeViewHelper.php";s:4:"22b8";s:60:"Classes/ViewHelpers/AddInlineLanguageLabelFileViewHelper.php";s:4:"34ad";s:51:"Classes/ViewHelpers/AddJavaScriptFileViewHelper.php";s:4:"352a";s:55:"Classes/ViewHelpers/AddJavaScriptSettingsViewHelper.php";s:4:"7b8d";s:43:"Configuration/ExtensionManager/FlexForm.php";s:4:"544f";s:41:"Configuration/ExtensionManager/Helper.php";s:4:"af66";s:60:"Configuration/ExtensionManager/VirtualConfigurationTable.php";s:4:"3fc5";s:34:"Configuration/TCA/BackLinkTest.php";s:4:"94cb";s:43:"Configuration/TCA/ContentComparisonTest.php";s:4:"8ce0";s:31:"Configuration/TCA/LinkCheck.php";s:4:"f243";s:31:"Configuration/TCA/RecordSet.php";s:4:"5e22";s:34:"Configuration/TCA/RedirectTest.php";s:4:"33ca";s:42:"Configuration/TCA/RedirectTestCategory.php";s:4:"4400";s:46:"Configuration/TypoScript/Backend/constants.txt";s:4:"f223";s:42:"Configuration/TypoScript/Backend/setup.txt";s:4:"8fad";s:46:"Resources/Private/Backend/Layouts/Default.html";s:4:"e131";s:59:"Resources/Private/Backend/Templates/BackLinkTest/Index.html";s:4:"777d";s:68:"Resources/Private/Backend/Templates/ContentComparisonTest/Index.html";s:4:"d38f";s:56:"Resources/Private/Backend/Templates/LinkCheck/Index.html";s:4:"6e5c";s:55:"Resources/Private/Backend/Templates/Overview/Index.html";s:4:"2272";s:59:"Resources/Private/Backend/Templates/RedirectTest/Index.html";s:4:"780e";s:43:"Resources/Private/Language/de.locallang.xml";s:4:"c585";s:46:"Resources/Private/Language/de.locallang_db.xml";s:4:"0a13";s:47:"Resources/Private/Language/de.locallang_tca.xml";s:4:"7246";s:49:"Resources/Private/Language/de.locallang_tools.xml";s:4:"7e81";s:40:"Resources/Private/Language/locallang.xml";s:4:"86a4";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"36fc";s:44:"Resources/Private/Language/locallang_tca.xml";s:4:"e99d";s:46:"Resources/Private/Language/locallang_tools.xml";s:4:"547b";s:35:"Resources/Public/Icons/ext_icon.gif";s:4:"c092";s:40:"Resources/Public/Icons/row-editor-bg.gif";s:4:"109b";s:42:"Resources/Public/Icons/row-editor-btns.gif";s:4:"f43e";s:30:"Resources/Public/Icons/run.png";s:4:"f783";s:63:"Resources/Public/Icons/tx_dftools_domain_model_backlinktest.gif";s:4:"905a";s:72:"Resources/Public/Icons/tx_dftools_domain_model_contentcomparisontest.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_linkcheck.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_recordset.gif";s:4:"4e5b";s:63:"Resources/Public/Icons/tx_dftools_domain_model_redirecttest.gif";s:4:"905a";s:71:"Resources/Public/Icons/tx_dftools_domain_model_redirecttestcategory.gif";s:4:"4e5b";s:39:"Resources/Public/Scripts/AbstractApp.js";s:4:"e940";s:52:"Resources/Public/Scripts/Ext.ux.grid.GroupActions.js";s:4:"1d11";s:49:"Resources/Public/Scripts/Ext.ux.grid.RowEditor.js";s:4:"bfca";s:51:"Resources/Public/Scripts/Ext.ux.grid.RowExpander.js";s:4:"faa1";s:42:"Resources/Public/Scripts/ExtendedStores.js";s:4:"d683";s:32:"Resources/Public/Scripts/Grid.js";s:4:"acd3";s:37:"Resources/Public/Scripts/HelpPanel.js";s:4:"2982";s:44:"Resources/Public/Scripts/BackLinkTest/App.js";s:4:"48ac";s:50:"Resources/Public/Scripts/BackLinkTest/PopUpForm.js";s:4:"2a47";s:46:"Resources/Public/Scripts/BackLinkTest/Store.js";s:4:"ffd8";s:53:"Resources/Public/Scripts/ContentComparisonTest/App.js";s:4:"8769";s:55:"Resources/Public/Scripts/ContentComparisonTest/Store.js";s:4:"a7b6";s:41:"Resources/Public/Scripts/LinkCheck/App.js";s:4:"684b";s:43:"Resources/Public/Scripts/LinkCheck/Store.js";s:4:"0335";s:43:"Resources/Public/Scripts/RecordSet/Store.js";s:4:"0350";s:44:"Resources/Public/Scripts/RedirectTest/App.js";s:4:"0a20";s:46:"Resources/Public/Scripts/RedirectTest/Store.js";s:4:"3193";s:58:"Resources/Public/Scripts/RedirectTestCategory/PopUpForm.js";s:4:"2191";s:54:"Resources/Public/Scripts/RedirectTestCategory/Store.js";s:4:"68dd";s:39:"Resources/Public/StyleSheets/Common.css";s:4:"6011";s:57:"Resources/Public/StyleSheets/Ext.ux.grid.GroupActions.css";s:4:"edd4";s:54:"Resources/Public/StyleSheets/Ext.ux.grid.RowEditor.css";s:4:"820d";s:45:"Resources/Public/Templates/destroyWindow.html";s:4:"b87f";s:44:"Tests/Fixture/serializedTcaConfiguration.txt";s:4:"ca48";s:39:"Tests/Unit/ExtBaseConnectorTestCase.php";s:4:"b282";s:48:"Tests/Unit/Controller/AbstractControllerTest.php";s:4:"9592";s:52:"Tests/Unit/Controller/BackLinkTestControllerTest.php";s:4:"a09c";s:61:"Tests/Unit/Controller/ContentComparisonTestControllerTest.php";s:4:"e4b8";s:44:"Tests/Unit/Controller/ControllerTestCase.php";s:4:"12d5";s:49:"Tests/Unit/Controller/LinkCheckControllerTest.php";s:4:"4fba";s:48:"Tests/Unit/Controller/OverviewControllerTest.php";s:4:"cb57";s:60:"Tests/Unit/Controller/RedirectTestCategoryControllerTest.php";s:4:"0af0";s:52:"Tests/Unit/Controller/RedirectTestControllerTest.php";s:4:"3615";s:44:"Tests/Unit/Domain/Model/BackLinkTestTest.php";s:4:"4f40";s:53:"Tests/Unit/Domain/Model/ContentComparisonTestTest.php";s:4:"1a3f";s:41:"Tests/Unit/Domain/Model/LinkCheckTest.php";s:4:"cefc";s:41:"Tests/Unit/Domain/Model/RecordSetTest.php";s:4:"d169";s:52:"Tests/Unit/Domain/Model/RedirectTestCategoryTest.php";s:4:"b86e";s:44:"Tests/Unit/Domain/Model/RedirectTestTest.php";s:4:"8cf6";s:55:"Tests/Unit/Domain/Repository/AbstractRepositoryTest.php";s:4:"f202";s:67:"Tests/Unit/Domain/Repository/RedirectTestCategoryRepositoryTest.php";s:4:"bcf9";s:59:"Tests/Unit/Domain/Repository/RedirectTestRepositoryTest.php";s:4:"964b";s:49:"Tests/Unit/ExtDirect/AbstractDataProviderTest.php";s:4:"f096";s:53:"Tests/Unit/ExtDirect/BackLinkTestDataProviderTest.php";s:4:"ab70";s:62:"Tests/Unit/ExtDirect/ContentComparisonTestDataProviderTest.php";s:4:"2f6e";s:50:"Tests/Unit/ExtDirect/LinkCheckDataProviderTest.php";s:4:"f8e4";s:50:"Tests/Unit/ExtDirect/RecordSetDataProviderTest.php";s:4:"20e8";s:61:"Tests/Unit/ExtDirect/RedirectTestCategoryDataProviderTest.php";s:4:"1187";s:53:"Tests/Unit/ExtDirect/RedirectTestDataProviderTest.php";s:4:"f555";s:39:"Tests/Unit/Hooks/ProcessDatamapTest.php";s:4:"2c76";s:50:"Tests/Unit/Service/ExtBaseConnectorServiceTest.php";s:4:"3f96";s:43:"Tests/Unit/Service/LinkCheckServiceTest.php";s:4:"aca9";s:47:"Tests/Unit/Service/RealUrlImportServiceTest.php";s:4:"c958";s:43:"Tests/Unit/Service/TcaParserServiceTest.php";s:4:"63f9";s:43:"Tests/Unit/Service/UrlParserServiceTest.php";s:4:"c526";s:48:"Tests/Unit/Service/UrlSynchronizeServiceTest.php";s:4:"b315";s:53:"Tests/Unit/Service/UrlChecker/AbstractServiceTest.php";s:4:"001a";s:49:"Tests/Unit/Service/UrlChecker/CurlServiceTest.php";s:4:"6cec";s:45:"Tests/Unit/Service/UrlChecker/FactoryTest.php";s:4:"5197";s:51:"Tests/Unit/Service/UrlChecker/StreamServiceTest.php";s:4:"72b8";s:38:"Tests/Unit/Task/AbstractFieldsTest.php";s:4:"7b30";s:36:"Tests/Unit/Task/AbstractTaskTest.php";s:4:"5589";s:40:"Tests/Unit/Task/BackLinkTestTaskTest.php";s:4:"1eca";s:60:"Tests/Unit/Task/ContentComparisonTestSynchronizeTaskTest.php";s:4:"05f1";s:49:"Tests/Unit/Task/ContentComparisonTestTaskTest.php";s:4:"c7e4";s:48:"Tests/Unit/Task/LinkCheckSynchronizeTaskTest.php";s:4:"4e07";s:37:"Tests/Unit/Task/LinkCheckTaskTest.php";s:4:"36e3";s:53:"Tests/Unit/Task/RedirectTestRealUrlImportTaskTest.php";s:4:"80cd";s:40:"Tests/Unit/Task/RedirectTestTaskTest.php";s:4:"0426";s:38:"Tests/Unit/Utility/HtmlUtilityTest.php";s:4:"b60f";s:38:"Tests/Unit/Utility/HttpUtilityTest.php";s:4:"82f2";s:46:"Tests/Unit/Utility/LocalizationUtilityTest.php";s:4:"85a3";s:37:"Tests/Unit/Utility/TcaUtilityTest.php";s:4:"8c7b";s:41:"Tests/Unit/View/AbstractArrayViewTest.php";s:4:"9cc5";s:46:"Tests/Unit/View/BackLinkTest/ArrayViewTest.php";s:4:"19f5";s:55:"Tests/Unit/View/ContentComparisonTest/ArrayViewTest.php";s:4:"5a8e";s:43:"Tests/Unit/View/LinkCheck/ArrayViewTest.php";s:4:"abdb";s:43:"Tests/Unit/View/RecordSet/ArrayViewTest.php";s:4:"e53f";s:46:"Tests/Unit/View/RedirectTest/ArrayViewTest.php";s:4:"28bf";s:54:"Tests/Unit/View/RedirectTestCategory/ArrayViewTest.php";s:4:"ff4d";s:49:"Tests/Unit/ViewHelpers/AbstractViewHelperTest.php";s:4:"7bea";s:51:"Tests/Unit/ViewHelpers/AddCssFileViewHelperTest.php";s:4:"b26a";s:57:"Tests/Unit/ViewHelpers/AddExtDirectCodeViewHelperTest.php";s:4:"a940";s:67:"Tests/Unit/ViewHelpers/AddInlineLanguageLabelFileViewHelperTest.php";s:4:"798c";s:58:"Tests/Unit/ViewHelpers/AddJavaScriptFileViewHelperTest.php";s:4:"2981";s:45:"Tests/Unit/ViewHelpers/ViewHelperTestCase.php";s:4:"481e";s:14:"doc/manual.sxw";s:4:"6f48";}',
);

?>