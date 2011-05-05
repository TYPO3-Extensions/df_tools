<?php

########################################################################
# Extension Manager/Repository config file for ext "df_tools".
#
# Auto generated 05-05-2011 00:15
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'dF Tools',
	'description' => 'Contains some useful tools like a testing tool for redirects, a link checker and a content comparison tool between the same or different urls. Furthermore there is full scheduler support for all tests and synchronization tasks.',
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
	'version' => '1.0.6',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-4.5.99',
			'extbase' => '1.3.0-1.3.99',
			'fluid' => '1.3.0-1.3.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:162:{s:16:"ext_autoload.php";s:4:"ed36";s:21:"ext_conf_template.txt";s:4:"6cf0";s:12:"ext_icon.gif";s:4:"c092";s:12:"ext_icon.png";s:4:"44c9";s:17:"ext_localconf.php";s:4:"68bd";s:14:"ext_tables.php";s:4:"1f7e";s:14:"ext_tables.sql";s:4:"c95b";s:28:"ext_typoscript_constants.txt";s:4:"deb8";s:24:"ext_typoscript_setup.txt";s:4:"a0ab";s:41:"Classes/Controller/AbstractController.php";s:4:"95af";s:54:"Classes/Controller/ContentComparisonTestController.php";s:4:"a22e";s:42:"Classes/Controller/LinkCheckController.php";s:4:"0806";s:41:"Classes/Controller/OverviewController.php";s:4:"2fb1";s:53:"Classes/Controller/RedirectTestCategoryController.php";s:4:"9dc9";s:45:"Classes/Controller/RedirectTestController.php";s:4:"4cbd";s:46:"Classes/Domain/Model/ContentComparisonTest.php";s:4:"0a60";s:34:"Classes/Domain/Model/LinkCheck.php";s:4:"117e";s:34:"Classes/Domain/Model/RecordSet.php";s:4:"e6b7";s:37:"Classes/Domain/Model/RedirectTest.php";s:4:"e4bd";s:45:"Classes/Domain/Model/RedirectTestCategory.php";s:4:"591f";s:42:"Classes/Domain/Model/TestableInterface.php";s:4:"1707";s:48:"Classes/Domain/Repository/AbstractRepository.php";s:4:"5e38";s:61:"Classes/Domain/Repository/ContentComparisonTestRepository.php";s:4:"d49d";s:49:"Classes/Domain/Repository/LinkCheckRepository.php";s:4:"974f";s:49:"Classes/Domain/Repository/RecordSetRepository.php";s:4:"5386";s:60:"Classes/Domain/Repository/RedirectTestCategoryRepository.php";s:4:"449b";s:52:"Classes/Domain/Repository/RedirectTestRepository.php";s:4:"0b95";s:42:"Classes/ExtDirect/AbstractDataProvider.php";s:4:"3362";s:55:"Classes/ExtDirect/ContentComparisonTestDataProvider.php";s:4:"e682";s:43:"Classes/ExtDirect/LinkCheckDataProvider.php";s:4:"f1c1";s:43:"Classes/ExtDirect/RecordSetDataProvider.php";s:4:"aad0";s:54:"Classes/ExtDirect/RedirectTestCategoryDataProvider.php";s:4:"a3c7";s:46:"Classes/ExtDirect/RedirectTestDataProvider.php";s:4:"eaa2";s:33:"Classes/Response/AjaxResponse.php";s:4:"9cb5";s:43:"Classes/Service/ExtBaseConnectorService.php";s:4:"96b0";s:36:"Classes/Service/TcaParserService.php";s:4:"4a5d";s:36:"Classes/Service/UrlParserService.php";s:4:"8b5a";s:41:"Classes/Service/UrlSynchronizeService.php";s:4:"76ea";s:46:"Classes/Service/UrlChecker/AbstractService.php";s:4:"771e";s:42:"Classes/Service/UrlChecker/CurlService.php";s:4:"58be";s:38:"Classes/Service/UrlChecker/Factory.php";s:4:"15ce";s:44:"Classes/Service/UrlChecker/StreamService.php";s:4:"9af3";s:31:"Classes/Task/AbstractFields.php";s:4:"b016";s:29:"Classes/Task/AbstractTask.php";s:4:"0b34";s:44:"Classes/Task/ContentComparisonTestFields.php";s:4:"5cda";s:42:"Classes/Task/ContentComparisonTestTask.php";s:4:"801d";s:32:"Classes/Task/LinkCheckFields.php";s:4:"6aa1";s:41:"Classes/Task/LinkCheckSynchronizeTask.php";s:4:"5bf0";s:30:"Classes/Task/LinkCheckTask.php";s:4:"743f";s:35:"Classes/Task/RedirectTestFields.php";s:4:"d3ad";s:33:"Classes/Task/RedirectTestTask.php";s:4:"8343";s:31:"Classes/Utility/HtmlUtility.php";s:4:"6c49";s:31:"Classes/Utility/HttpUtility.php";s:4:"ee04";s:39:"Classes/Utility/LocalizationUtility.php";s:4:"e758";s:34:"Classes/View/AbstractArrayView.php";s:4:"7308";s:48:"Classes/View/ContentComparisonTest/ArrayView.php";s:4:"cb83";s:36:"Classes/View/LinkCheck/ArrayView.php";s:4:"ca68";s:36:"Classes/View/RecordSet/ArrayView.php";s:4:"be25";s:39:"Classes/View/RedirectTest/ArrayView.php";s:4:"67b8";s:47:"Classes/View/RedirectTestCategory/ArrayView.php";s:4:"c8da";s:42:"Classes/ViewHelpers/AbstractViewHelper.php";s:4:"41be";s:44:"Classes/ViewHelpers/AddCssFileViewHelper.php";s:4:"8cae";s:50:"Classes/ViewHelpers/AddExtDirectCodeViewHelper.php";s:4:"9fa2";s:60:"Classes/ViewHelpers/AddInlineLanguageLabelFileViewHelper.php";s:4:"1c53";s:51:"Classes/ViewHelpers/AddJavaScriptFileViewHelper.php";s:4:"e554";s:55:"Classes/ViewHelpers/AddJavaScriptSettingsViewHelper.php";s:4:"4bc5";s:43:"Configuration/TCA/ContentComparisonTest.php";s:4:"2025";s:31:"Configuration/TCA/LinkCheck.php";s:4:"c0e4";s:31:"Configuration/TCA/RecordSet.php";s:4:"3c00";s:34:"Configuration/TCA/RedirectTest.php";s:4:"ce56";s:42:"Configuration/TCA/RedirectTestCategory.php";s:4:"094d";s:46:"Resources/Private/Backend/Layouts/Default.html";s:4:"0933";s:68:"Resources/Private/Backend/Templates/ContentComparisonTest/Index.html";s:4:"a721";s:56:"Resources/Private/Backend/Templates/LinkCheck/Index.html";s:4:"76c8";s:55:"Resources/Private/Backend/Templates/Overview/Index.html";s:4:"1db7";s:59:"Resources/Private/Backend/Templates/RedirectTest/Index.html";s:4:"fe34";s:43:"Resources/Private/Language/de.locallang.xml";s:4:"c4d4";s:46:"Resources/Private/Language/de.locallang_db.xml";s:4:"2724";s:49:"Resources/Private/Language/de.locallang_tools.xml";s:4:"7e81";s:40:"Resources/Private/Language/locallang.xml";s:4:"59e4";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"fb80";s:46:"Resources/Private/Language/locallang_tools.xml";s:4:"dc72";s:35:"Resources/Public/Icons/ext_icon.gif";s:4:"c092";s:40:"Resources/Public/Icons/row-editor-bg.gif";s:4:"109b";s:42:"Resources/Public/Icons/row-editor-btns.gif";s:4:"f43e";s:30:"Resources/Public/Icons/run.png";s:4:"f783";s:72:"Resources/Public/Icons/tx_dftools_domain_model_contentcomparisontest.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_linkcheck.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_recordset.gif";s:4:"4e5b";s:63:"Resources/Public/Icons/tx_dftools_domain_model_redirecttest.gif";s:4:"905a";s:71:"Resources/Public/Icons/tx_dftools_domain_model_redirecttestcategory.gif";s:4:"4e5b";s:39:"Resources/Public/Scripts/AbstractApp.js";s:4:"0a6a";s:52:"Resources/Public/Scripts/Ext.ux.grid.GroupActions.js";s:4:"1d11";s:49:"Resources/Public/Scripts/Ext.ux.grid.RowEditor.js";s:4:"bfca";s:51:"Resources/Public/Scripts/Ext.ux.grid.RowExpander.js";s:4:"faa1";s:42:"Resources/Public/Scripts/ExtendedStores.js";s:4:"aa3a";s:32:"Resources/Public/Scripts/Grid.js";s:4:"6a3e";s:53:"Resources/Public/Scripts/ContentComparisonTest/App.js";s:4:"9eaa";s:55:"Resources/Public/Scripts/ContentComparisonTest/Store.js";s:4:"b815";s:41:"Resources/Public/Scripts/LinkCheck/App.js";s:4:"06ff";s:43:"Resources/Public/Scripts/LinkCheck/Store.js";s:4:"d1c8";s:43:"Resources/Public/Scripts/RecordSet/Store.js";s:4:"1d2c";s:44:"Resources/Public/Scripts/RedirectTest/App.js";s:4:"6a96";s:46:"Resources/Public/Scripts/RedirectTest/Store.js";s:4:"8050";s:58:"Resources/Public/Scripts/RedirectTestCategory/PopUpForm.js";s:4:"ccb2";s:54:"Resources/Public/Scripts/RedirectTestCategory/Store.js";s:4:"2035";s:39:"Resources/Public/StyleSheets/Common.css";s:4:"6326";s:57:"Resources/Public/StyleSheets/Ext.ux.grid.GroupActions.css";s:4:"edd4";s:54:"Resources/Public/StyleSheets/Ext.ux.grid.RowEditor.css";s:4:"820d";s:45:"Resources/Public/Templates/destroyWindow.html";s:4:"b87f";s:28:"Tests/ControllerTestCase.php";s:4:"eebe";s:34:"Tests/ExtBaseConnectorTestCase.php";s:4:"4f2f";s:43:"Tests/Controller/AbstractControllerTest.php";s:4:"277b";s:56:"Tests/Controller/ContentComparisonTestControllerTest.php";s:4:"dfb0";s:44:"Tests/Controller/LinkCheckControllerTest.php";s:4:"1072";s:43:"Tests/Controller/OverviewControllerTest.php";s:4:"3ec8";s:55:"Tests/Controller/RedirectTestCategoryControllerTest.php";s:4:"2102";s:47:"Tests/Controller/RedirectTestControllerTest.php";s:4:"93ac";s:48:"Tests/Domain/Model/ContentComparisonTestTest.php";s:4:"11b2";s:36:"Tests/Domain/Model/LinkCheckTest.php";s:4:"e8d5";s:36:"Tests/Domain/Model/RecordSetTest.php";s:4:"006e";s:47:"Tests/Domain/Model/RedirectTestCategoryTest.php";s:4:"cff0";s:39:"Tests/Domain/Model/RedirectTestTest.php";s:4:"6ecc";s:50:"Tests/Domain/Repository/AbstractRepositoryTest.php";s:4:"5984";s:51:"Tests/Domain/Repository/LinkCheckRepositoryTest.php";s:4:"726b";s:62:"Tests/Domain/Repository/RedirectTestCategoryRepositoryTest.php";s:4:"3644";s:44:"Tests/ExtDirect/AbstractDataProviderTest.php";s:4:"c53d";s:57:"Tests/ExtDirect/ContentComparisonTestDataProviderTest.php";s:4:"5b04";s:45:"Tests/ExtDirect/LinkCheckDataProviderTest.php";s:4:"6423";s:45:"Tests/ExtDirect/RecordSetDataProviderTest.php";s:4:"ce3b";s:56:"Tests/ExtDirect/RedirectTestCategoryDataProviderTest.php";s:4:"efd2";s:48:"Tests/ExtDirect/RedirectTestDataProviderTest.php";s:4:"17c4";s:44:"Tests/Fixture/serializedTcaConfiguration.txt";s:4:"ca48";s:45:"Tests/Service/ExtBaseConnectorServiceTest.php";s:4:"1787";s:38:"Tests/Service/TcaParserServiceTest.php";s:4:"38a9";s:38:"Tests/Service/UrlParserServiceTest.php";s:4:"3f4c";s:43:"Tests/Service/UrlSynchronizeServiceTest.php";s:4:"0d53";s:48:"Tests/Service/UrlChecker/AbstractServiceTest.php";s:4:"4fd1";s:44:"Tests/Service/UrlChecker/CurlServiceTest.php";s:4:"e914";s:40:"Tests/Service/UrlChecker/FactoryTest.php";s:4:"4912";s:46:"Tests/Service/UrlChecker/StreamServiceTest.php";s:4:"4fa2";s:33:"Tests/Task/AbstractFieldsTest.php";s:4:"5625";s:31:"Tests/Task/AbstractTaskTest.php";s:4:"f78a";s:44:"Tests/Task/ContentComparisonTestTaskTest.php";s:4:"a422";s:43:"Tests/Task/LinkCheckSynchronizeTaskTest.php";s:4:"c736";s:32:"Tests/Task/LinkCheckTaskTest.php";s:4:"8dea";s:35:"Tests/Task/RedirectTestTaskTest.php";s:4:"e434";s:33:"Tests/Utility/HtmlUtilityTest.php";s:4:"1602";s:33:"Tests/Utility/HttpUtilityTest.php";s:4:"3ce0";s:41:"Tests/Utility/LocalizationUtilityTest.php";s:4:"13dd";s:36:"Tests/View/AbstractArrayViewTest.php";s:4:"a58f";s:50:"Tests/View/ContentComparisonTest/ArrayViewTest.php";s:4:"a5ed";s:38:"Tests/View/LinkCheck/ArrayViewTest.php";s:4:"c8b6";s:38:"Tests/View/RecordSet/ArrayViewTest.php";s:4:"54b2";s:41:"Tests/View/RedirectTest/ArrayViewTest.php";s:4:"7327";s:49:"Tests/View/RedirectTestCategory/ArrayViewTest.php";s:4:"c2b2";s:44:"Tests/ViewHelpers/AbstractViewHelperTest.php";s:4:"457f";s:46:"Tests/ViewHelpers/AddCssFileViewHelperTest.php";s:4:"ba39";s:52:"Tests/ViewHelpers/AddExtDirectCodeViewHelperTest.php";s:4:"88c3";s:62:"Tests/ViewHelpers/AddInlineLanguageLabelFileViewHelperTest.php";s:4:"a6f5";s:53:"Tests/ViewHelpers/AddJavaScriptFileViewHelperTest.php";s:4:"51bc";s:14:"doc/manual.sxw";s:4:"4a78";}',
);

?>