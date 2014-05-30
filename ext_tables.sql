CREATE TABLE tx_dftools_domain_model_redirecttest (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	test_url varchar(1024) DEFAULT '' NOT NULL,
	expected_url varchar(1024) DEFAULT '' NOT NULL,
	http_status_code int(11) DEFAULT '0' NOT NULL,
	test_result tinyint(1) unsigned DEFAULT '9' NOT NULL,
	test_message text NOT NULL,
	category int(11) unsigned DEFAULT '0',

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_domain_model_redirecttestcategory (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	category varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_domain_model_linkcheck (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	test_url varchar(1024) DEFAULT '' NOT NULL,
	result_url varchar(1024) DEFAULT '' NOT NULL,
	http_status_code int(11) DEFAULT '0' NOT NULL,
	test_result tinyint(1) unsigned DEFAULT '9' NOT NULL,
	test_message text NOT NULL,
	record_sets int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_domain_model_recordset (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	table_name varchar(512) DEFAULT '' NOT NULL,
	field varchar(512) DEFAULT '' NOT NULL,
	identifier int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_domain_model_backlinktest (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	test_url varchar(1024) DEFAULT '' NOT NULL,
	expected_url varchar(1024) DEFAULT '' NOT NULL,
	test_result tinyint(1) unsigned DEFAULT '9' NOT NULL,
	test_message text NOT NULL,
	comment text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_domain_model_contentcomparisontest (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	test_url varchar(1024) DEFAULT '' NOT NULL,
	compare_url varchar(1024) DEFAULT '' NOT NULL,
	test_content longblob NOT NULL,
	difference longblob NOT NULL,
	compare_content longblob NOT NULL,
	test_result tinyint(1) unsigned DEFAULT '9' NOT NULL,
	test_message text NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(10) unsigned DEFAULT '0' NOT NULL,
	endtime int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_dftools_linkcheck_recordset_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);