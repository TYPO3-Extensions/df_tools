<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "df_tools".
 *
 * Auto generated 03-06-2013 22:20
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'dF Tools',
	'description' => 'Contains some useful tools like a testing tool for redirects, a link checker, a back link checker and a content comparison tool between the same or different urls. Furthermore there is full scheduler support for all tests and synchronization tasks.',
	'category' => 'be',
	'author' => 'Stefan Galinski',
	'author_email' => 'stefan.galinski@gmail.com',
	'author_company' => '',
	'shy' => '',
	'dependencies' => '',
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
	'version' => '1.6.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.1.0-6.1.99',
			'php' => '5.3.0-5.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:185:{s:9:"config.rb";s:4:"0865";s:16:"ext_autoload.php";s:4:"e5c9";s:21:"ext_conf_template.txt";s:4:"9b13";s:12:"ext_icon.png";s:4:"9011";s:17:"ext_localconf.php";s:4:"7573";s:14:"ext_tables.php";s:4:"ebfc";s:14:"ext_tables.sql";s:4:"c0c9";s:45:"Classes/Command/AbstractCommandController.php";s:4:"aa36";s:49:"Classes/Command/BacklinkTestCommandController.php";s:4:"6de3";s:58:"Classes/Command/ContentComparisonTestCommandController.php";s:4:"9801";s:46:"Classes/Command/LinkCheckCommandController.php";s:4:"8803";s:49:"Classes/Command/RedirectTestCommandController.php";s:4:"40b6";s:38:"Classes/Connector/ExtBaseConnector.php";s:4:"770f";s:41:"Classes/Controller/AbstractController.php";s:4:"ceee";s:45:"Classes/Controller/BackLinkTestController.php";s:4:"e7ad";s:54:"Classes/Controller/ContentComparisonTestController.php";s:4:"11f2";s:42:"Classes/Controller/LinkCheckController.php";s:4:"2dd7";s:41:"Classes/Controller/OverviewController.php";s:4:"3a4f";s:53:"Classes/Controller/RedirectTestCategoryController.php";s:4:"0f9d";s:45:"Classes/Controller/RedirectTestController.php";s:4:"c3bc";s:37:"Classes/Domain/Model/BackLinkTest.php";s:4:"30b6";s:46:"Classes/Domain/Model/ContentComparisonTest.php";s:4:"f423";s:34:"Classes/Domain/Model/LinkCheck.php";s:4:"0df8";s:34:"Classes/Domain/Model/RecordSet.php";s:4:"17bf";s:37:"Classes/Domain/Model/RedirectTest.php";s:4:"fa90";s:45:"Classes/Domain/Model/RedirectTestCategory.php";s:4:"0bfe";s:42:"Classes/Domain/Model/TestableInterface.php";s:4:"3ee9";s:48:"Classes/Domain/Repository/AbstractRepository.php";s:4:"326b";s:52:"Classes/Domain/Repository/BackLinkTestRepository.php";s:4:"23c1";s:61:"Classes/Domain/Repository/ContentComparisonTestRepository.php";s:4:"5df8";s:49:"Classes/Domain/Repository/LinkCheckRepository.php";s:4:"3ade";s:49:"Classes/Domain/Repository/RecordSetRepository.php";s:4:"5111";s:60:"Classes/Domain/Repository/RedirectTestCategoryRepository.php";s:4:"2aed";s:52:"Classes/Domain/Repository/RedirectTestRepository.php";s:4:"32c8";s:43:"Classes/Domain/Service/LinkCheckService.php";s:4:"85d0";s:47:"Classes/Domain/Service/RealUrlImportService.php";s:4:"a7f5";s:48:"Classes/Domain/Service/UrlSynchronizeService.php";s:4:"fa83";s:38:"Classes/Exception/GenericException.php";s:4:"8a57";s:42:"Classes/ExtDirect/AbstractDataProvider.php";s:4:"15b2";s:46:"Classes/ExtDirect/BackLinkTestDataProvider.php";s:4:"57a9";s:55:"Classes/ExtDirect/ContentComparisonTestDataProvider.php";s:4:"8a82";s:43:"Classes/ExtDirect/LinkCheckDataProvider.php";s:4:"a44d";s:43:"Classes/ExtDirect/RecordSetDataProvider.php";s:4:"36d4";s:54:"Classes/ExtDirect/RedirectTestCategoryDataProvider.php";s:4:"d351";s:46:"Classes/ExtDirect/RedirectTestDataProvider.php";s:4:"d300";s:32:"Classes/Hooks/ProcessDatamap.php";s:4:"f378";s:40:"Classes/MVC/Web/CustomRequestHandler.php";s:4:"2566";s:28:"Classes/Parser/TcaParser.php";s:4:"3652";s:28:"Classes/Parser/UrlParser.php";s:4:"aa4e";s:35:"Classes/Response/CustomResponse.php";s:4:"66b4";s:38:"Classes/UrlChecker/AbstractService.php";s:4:"d85a";s:34:"Classes/UrlChecker/CurlService.php";s:4:"9c3c";s:30:"Classes/UrlChecker/Factory.php";s:4:"7822";s:36:"Classes/UrlChecker/StreamService.php";s:4:"105d";s:37:"Classes/Utility/CompressorUtility.php";s:4:"a9ce";s:31:"Classes/Utility/HtmlUtility.php";s:4:"b558";s:31:"Classes/Utility/HttpUtility.php";s:4:"4bb3";s:39:"Classes/Utility/LocalizationUtility.php";s:4:"4dad";s:31:"Classes/Utility/PageUtility.php";s:4:"635f";s:30:"Classes/Utility/TcaUtility.php";s:4:"ef8b";s:34:"Classes/View/AbstractArrayView.php";s:4:"97f1";s:38:"Classes/View/BackLinkTestArrayView.php";s:4:"dae5";s:47:"Classes/View/ContentComparisonTestArrayView.php";s:4:"552b";s:35:"Classes/View/LinkCheckArrayView.php";s:4:"e68d";s:35:"Classes/View/RecordSetArrayView.php";s:4:"f953";s:38:"Classes/View/RedirectTestArrayView.php";s:4:"4804";s:46:"Classes/View/RedirectTestCategoryArrayView.php";s:4:"9081";s:42:"Classes/ViewHelpers/AbstractViewHelper.php";s:4:"0fd8";s:44:"Classes/ViewHelpers/AddCssFileViewHelper.php";s:4:"ac33";s:50:"Classes/ViewHelpers/AddExtDirectCodeViewHelper.php";s:4:"67fa";s:60:"Classes/ViewHelpers/AddInlineLanguageLabelFileViewHelper.php";s:4:"faf2";s:51:"Classes/ViewHelpers/AddJavaScriptFileViewHelper.php";s:4:"335c";s:55:"Classes/ViewHelpers/AddJavaScriptSettingsViewHelper.php";s:4:"7683";s:58:"Configuration/TCA/tx_dftools_domain_model_backlinktest.php";s:4:"c99f";s:67:"Configuration/TCA/tx_dftools_domain_model_contentcomparisontest.php";s:4:"91bb";s:55:"Configuration/TCA/tx_dftools_domain_model_linkcheck.php";s:4:"58f2";s:55:"Configuration/TCA/tx_dftools_domain_model_recordset.php";s:4:"eadf";s:58:"Configuration/TCA/tx_dftools_domain_model_redirecttest.php";s:4:"e4a3";s:66:"Configuration/TCA/tx_dftools_domain_model_redirecttestcategory.php";s:4:"280c";s:46:"Configuration/TypoScript/Backend/constants.txt";s:4:"f223";s:42:"Configuration/TypoScript/Backend/setup.txt";s:4:"7139";s:46:"Resources/Private/Backend/Layouts/Default.html";s:4:"0d1b";s:59:"Resources/Private/Backend/Templates/BackLinkTest/Index.html";s:4:"f6be";s:68:"Resources/Private/Backend/Templates/ContentComparisonTest/Index.html";s:4:"b995";s:56:"Resources/Private/Backend/Templates/LinkCheck/Index.html";s:4:"3812";s:55:"Resources/Private/Backend/Templates/Overview/Index.html";s:4:"2272";s:59:"Resources/Private/Backend/Templates/RedirectTest/Index.html";s:4:"3d5a";s:43:"Resources/Private/Language/de.locallang.xml";s:4:"ea21";s:46:"Resources/Private/Language/de.locallang_db.xml";s:4:"0a13";s:47:"Resources/Private/Language/de.locallang_tca.xml";s:4:"46de";s:49:"Resources/Private/Language/de.locallang_tools.xml";s:4:"7e81";s:40:"Resources/Private/Language/locallang.xml";s:4:"c17e";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"36fc";s:44:"Resources/Private/Language/locallang_tca.xml";s:4:"ae02";s:46:"Resources/Private/Language/locallang_tools.xml";s:4:"547b";s:40:"Resources/Public/Icons/row-editor-bg.gif";s:4:"109b";s:42:"Resources/Public/Icons/row-editor-btns.gif";s:4:"f43e";s:30:"Resources/Public/Icons/run.png";s:4:"f783";s:63:"Resources/Public/Icons/tx_dftools_domain_model_backlinktest.gif";s:4:"905a";s:72:"Resources/Public/Icons/tx_dftools_domain_model_contentcomparisontest.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_linkcheck.gif";s:4:"905a";s:60:"Resources/Public/Icons/tx_dftools_domain_model_recordset.gif";s:4:"4e5b";s:63:"Resources/Public/Icons/tx_dftools_domain_model_redirecttest.gif";s:4:"905a";s:71:"Resources/Public/Icons/tx_dftools_domain_model_redirecttestcategory.gif";s:4:"4e5b";s:33:"Resources/Public/Sass/Common.scss";s:4:"04d5";s:51:"Resources/Public/Sass/Ext.ux.grid.GroupActions.scss";s:4:"5791";s:48:"Resources/Public/Sass/Ext.ux.grid.RowEditor.scss";s:4:"f928";s:39:"Resources/Public/Scripts/AbstractApp.js";s:4:"e940";s:37:"Resources/Public/Scripts/HelpPanel.js";s:4:"2982";s:44:"Resources/Public/Scripts/BackLinkTest/App.js";s:4:"48ac";s:50:"Resources/Public/Scripts/BackLinkTest/PopUpForm.js";s:4:"2a47";s:46:"Resources/Public/Scripts/BackLinkTest/Store.js";s:4:"f82f";s:53:"Resources/Public/Scripts/ContentComparisonTest/App.js";s:4:"8769";s:55:"Resources/Public/Scripts/ContentComparisonTest/Store.js";s:4:"a0b2";s:57:"Resources/Public/Scripts/Grid/Ext.ux.grid.GroupActions.js";s:4:"ddf5";s:54:"Resources/Public/Scripts/Grid/Ext.ux.grid.RowEditor.js";s:4:"cf80";s:56:"Resources/Public/Scripts/Grid/Ext.ux.grid.RowExpander.js";s:4:"b7fd";s:37:"Resources/Public/Scripts/Grid/Grid.js";s:4:"e6de";s:41:"Resources/Public/Scripts/LinkCheck/App.js";s:4:"684b";s:43:"Resources/Public/Scripts/LinkCheck/Store.js";s:4:"8f48";s:43:"Resources/Public/Scripts/RecordSet/Store.js";s:4:"9662";s:44:"Resources/Public/Scripts/RedirectTest/App.js";s:4:"0a20";s:46:"Resources/Public/Scripts/RedirectTest/Store.js";s:4:"6e17";s:58:"Resources/Public/Scripts/RedirectTestCategory/PopUpForm.js";s:4:"2191";s:54:"Resources/Public/Scripts/RedirectTestCategory/Store.js";s:4:"6364";s:48:"Resources/Public/Scripts/Store/ExtendedStores.js";s:4:"14b2";s:39:"Resources/Public/StyleSheets/Common.css";s:4:"95e2";s:57:"Resources/Public/StyleSheets/Ext.ux.grid.GroupActions.css";s:4:"18fb";s:54:"Resources/Public/StyleSheets/Ext.ux.grid.RowEditor.css";s:4:"f067";s:45:"Resources/Public/Templates/destroyWindow.html";s:4:"fccc";s:44:"Tests/Fixture/serializedTcaConfiguration.txt";s:4:"ca48";s:39:"Tests/Unit/ExtBaseConnectorTestCase.php";s:4:"0c3c";s:52:"Tests/Unit/Command/AbstractCommandControllerTest.php";s:4:"b214";s:45:"Tests/Unit/Connector/ExtBaseConnectorTest.php";s:4:"4136";s:48:"Tests/Unit/Controller/AbstractControllerTest.php";s:4:"ed9c";s:52:"Tests/Unit/Controller/BackLinkTestControllerTest.php";s:4:"7be9";s:61:"Tests/Unit/Controller/ContentComparisonTestControllerTest.php";s:4:"cf37";s:44:"Tests/Unit/Controller/ControllerTestCase.php";s:4:"97ee";s:49:"Tests/Unit/Controller/LinkCheckControllerTest.php";s:4:"b151";s:48:"Tests/Unit/Controller/OverviewControllerTest.php";s:4:"2118";s:60:"Tests/Unit/Controller/RedirectTestCategoryControllerTest.php";s:4:"008c";s:52:"Tests/Unit/Controller/RedirectTestControllerTest.php";s:4:"919a";s:44:"Tests/Unit/Domain/Model/BackLinkTestTest.php";s:4:"f606";s:53:"Tests/Unit/Domain/Model/ContentComparisonTestTest.php";s:4:"c4ea";s:41:"Tests/Unit/Domain/Model/LinkCheckTest.php";s:4:"7a80";s:41:"Tests/Unit/Domain/Model/RecordSetTest.php";s:4:"1fe6";s:52:"Tests/Unit/Domain/Model/RedirectTestCategoryTest.php";s:4:"72ab";s:44:"Tests/Unit/Domain/Model/RedirectTestTest.php";s:4:"8963";s:55:"Tests/Unit/Domain/Repository/AbstractRepositoryTest.php";s:4:"0f13";s:67:"Tests/Unit/Domain/Repository/RedirectTestCategoryRepositoryTest.php";s:4:"7e41";s:59:"Tests/Unit/Domain/Repository/RedirectTestRepositoryTest.php";s:4:"9190";s:50:"Tests/Unit/Domain/Service/LinkCheckServiceTest.php";s:4:"8787";s:54:"Tests/Unit/Domain/Service/RealUrlImportServiceTest.php";s:4:"5f9a";s:55:"Tests/Unit/Domain/Service/UrlSynchronizeServiceTest.php";s:4:"0efd";s:49:"Tests/Unit/ExtDirect/AbstractDataProviderTest.php";s:4:"14bf";s:53:"Tests/Unit/ExtDirect/BackLinkTestDataProviderTest.php";s:4:"86e0";s:62:"Tests/Unit/ExtDirect/ContentComparisonTestDataProviderTest.php";s:4:"577a";s:50:"Tests/Unit/ExtDirect/LinkCheckDataProviderTest.php";s:4:"c9d5";s:50:"Tests/Unit/ExtDirect/RecordSetDataProviderTest.php";s:4:"e5d1";s:61:"Tests/Unit/ExtDirect/RedirectTestCategoryDataProviderTest.php";s:4:"c756";s:53:"Tests/Unit/ExtDirect/RedirectTestDataProviderTest.php";s:4:"d50d";s:39:"Tests/Unit/Hooks/ProcessDatamapTest.php";s:4:"2f5c";s:35:"Tests/Unit/Parser/TcaParserTest.php";s:4:"8070";s:35:"Tests/Unit/Parser/UrlParserTest.php";s:4:"2aa4";s:45:"Tests/Unit/UrlChecker/AbstractServiceTest.php";s:4:"3517";s:41:"Tests/Unit/UrlChecker/CurlServiceTest.php";s:4:"c929";s:37:"Tests/Unit/UrlChecker/FactoryTest.php";s:4:"49f2";s:43:"Tests/Unit/UrlChecker/StreamServiceTest.php";s:4:"1b82";s:38:"Tests/Unit/Utility/HtmlUtilityTest.php";s:4:"3911";s:38:"Tests/Unit/Utility/HttpUtilityTest.php";s:4:"f02d";s:46:"Tests/Unit/Utility/LocalizationUtilityTest.php";s:4:"8845";s:41:"Tests/Unit/View/AbstractArrayViewTest.php";s:4:"d593";s:45:"Tests/Unit/View/BackLinkTestArrayViewTest.php";s:4:"6bf9";s:54:"Tests/Unit/View/ContentComparisonTestArrayViewTest.php";s:4:"857c";s:42:"Tests/Unit/View/LinkCheckArrayViewTest.php";s:4:"bc60";s:42:"Tests/Unit/View/RecordSetArrayViewTest.php";s:4:"672a";s:45:"Tests/Unit/View/RedirectTestArrayViewTest.php";s:4:"7591";s:53:"Tests/Unit/View/RedirectTestCategoryArrayViewTest.php";s:4:"4cc0";s:49:"Tests/Unit/ViewHelpers/AbstractViewHelperTest.php";s:4:"6ee7";s:51:"Tests/Unit/ViewHelpers/AddCssFileViewHelperTest.php";s:4:"57cb";s:57:"Tests/Unit/ViewHelpers/AddExtDirectCodeViewHelperTest.php";s:4:"1023";s:67:"Tests/Unit/ViewHelpers/AddInlineLanguageLabelFileViewHelperTest.php";s:4:"d2c5";s:58:"Tests/Unit/ViewHelpers/AddJavaScriptFileViewHelperTest.php";s:4:"e11d";s:45:"Tests/Unit/ViewHelpers/ViewHelperTestCase.php";s:4:"6563";s:14:"doc/manual.sxw";s:4:"3e02";}',
);

?>