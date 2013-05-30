<?php

########################################################################
# Extension Manager/Repository config file for ext "df_tools".
#
# Auto generated 30-05-2012 19:41
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
	'version' => '1.5.3',
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
	'_md5_values_when_last_written' => '',
);

?>