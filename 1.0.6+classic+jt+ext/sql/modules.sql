# PRIVATE_CODE
CREATE TABLE modules (
	modulename varchar(20) NOT NULL,
	formalname varchar(50) NOT NULL,
	moduleauthor varchar(255) NOT NULL,
	active tinyint(4) NOT NULL default 0,
	filename varchar(20) NOT NULL,
	installdate datetime NOT NULL,
	installedby varchar(50) NOT NULL,
	filemoddate datetime NOT NULL default '0000-00-00 00:00:00',
	
	PRIMARY KEY(modulename)
) TYPE=INNODB;
ALTER TABLE modules ADD category varchar(50) not null default '';
ALTER TABLE modules ADD infokeys text not null default '';

CREATE TABLE module_hooks (
	modulename varchar(20) NOT NULL,
	location varchar(20) NOT NULL,
	function varchar(50),
	
	PRIMARY KEY(modulename,location)
) TYPE=INNODB;

CREATE TABLE module_event_hooks (
	event_type varchar(20) NOT NULL,
	modulename varchar(20) NOT NULL,
	event_chance varchar(255) NOT NULL,

	INDEX (event_type),
	INDEX (modulename)
) TYPE = InnoDB;

CREATE TABLE module_settings (
	modulename varchar(20) NOT NULL,
	setting varchar(20) NOT NULL,
	value text,
	
	PRIMARY KEY(modulename,setting)
) TYPE=INNODB;

CREATE TABLE module_userprefs (
	modulename varchar(20) NOT NULL,
	setting varchar(20) NOT NULL,
	userid int(11) unsigned not null,
	value text,
	
	PRIMARY KEY(modulename,setting,userid)
) TYPE=INNODB;

CREATE TABLE module_objprefs(
	modulename varchar(20) NOT NULL,
	objtype varchar(20) NOT NULL,
	setting varchar(20) NOT NULL,
	objid int(11) unsigned NOT NULL,
	value text,

	PRIMARY KEY(modulename,objtype,setting,objid)
) TYPE=INNODB;

CREATE TABLE module_settingdesc (
	modulename varchar(20) NOT NULL,
	setting varchar(20) NOT NULL,
	description varchar(255),
	type tinyint(4),
	def varchar(255) NOT NULL,
	
	PRIMARY KEY(modulename,setting)
) TYPE=InnoDB;

CREATE TABLE module_prefdesc (
	modulename varchar(20) NOT NULL,
	setting varchar(20) NOT NULL,
	description varchar(255),
	type tinyint(4) NOT NULL default 0,
	def varchar(255) NOT NULL,
	
	PRIMARY KEY(modulename,setting)
) TYPE=InnoDB;

alter table module_hooks change function function varchar(50) not null default '';
alter table module_hooks drop primary key;
alter table module_hooks add primary key(modulename,location,function);

ALTER TABLE modules ADD version varchar(10);
ALTER TABLE modules ADD download varchar(50);

#--- Everything after here is post-prerelease.1
ALTER TABLE modules change download download varchar(200);

#---Updates from prerelease.4 to prerelease.5
ALTER TABLE module_userprefs add key(modulename,userid);
