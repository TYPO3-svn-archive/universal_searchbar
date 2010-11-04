#
# Table structure for table 'tx_searchbar_items'
#
CREATE TABLE tx_searchbar_items (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	title tinytext,
	hotkey tinytext,
	glue tinytext,
	searchurl tinytext,
	typoscript text,
	itemtype int(11) DEFAULT '0' NOT NULL,
	additionalfunctions tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);