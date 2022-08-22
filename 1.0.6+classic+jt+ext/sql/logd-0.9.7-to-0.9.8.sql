# PRIVATE_CODE
DROP TABLE IF EXISTS translations;
CREATE TABLE translations (
	tid int(11) not null auto_increment,
	language varchar(10) not null,
	uri varchar(255) not null,
	intext text not null,
	outtext text not null,
	global tinyint(4) not null default 0,
	author varchar(50),
	version varchar(10),
	
	INDEX(language,uri(25),global),
	PRIMARY KEY(tid)
);

ALTER TABLE referers ADD INDEX(site);
ALTER TABLE accounts DROP charisma;
ALTER TABLE accounts DROP hauntpoints;
ALTER TABLE accounts DROP history;

ALTER TABLE bans ADD banner varchar(50) not null default '';
ALTER TABLE bans ADD lasthit datetime not null default '0000-00-00 00:00:00';

CREATE TABLE paylog (
	payid int(11) not null auto_increment,
	info text not null,
	PRIMARY KEY (payid)
);
ALTER TABLE paylog ADD response text not null;
ALTER TABLE paylog ADD txnid varchar(32) not null;
ALTER TABLE paylog ADD INDEX(txnid);
ALTER TABLE paylog ADD amount float(9,2) not null default 0.00;
ALTER TABLE paylog ADD name varchar(50) not null;
ALTER TABLE paylog ADD acctid int(11) unsigned not null;
ALTER TABLE paylog ADD processed tinyint(4) unsigned not null default 0;
ALTER TABLE paylog ADD filed tinyint(4) unsigned not null default 0;
ALTER TABLE paylog ADD txfee float(9,2) not null default 0.00;

ALTER TABLE accounts ADD moderator tinyint(4) unsigned not null default 0;

DROP TABLE IF EXISTS moderatedcomments;
CREATE TABLE moderatedcomments (
	modid int(11) unsigned not null auto_increment,
	comment text,
	moderator int(11) unsigned not null,
	moddate datetime not null,
	PRIMARY KEY(modid)
);

CREATE TABLE antidos (
	lineid int(11) unsigned not null auto_increment,
	ip varchar(40) not null,
	lastrequest datetime not null,
	count int(11) unsigned not null,
	avgdiff float(2,10) unsigned not null,
	PRIMARY KEY (lineid)
);

ALTER TABLE antidos ADD INDEX(ip);
ALTER TABLE antidos ADD INDEX(lastrequest);

ALTER TABLE motd ADD motdauthor int(11) unsigned NOT NULL DEFAULT 0;

ALTER TABLE accounts ADD slaydragon tinyint(4) unsigned NOT NULL DEFAULT 0;
ALTER TABLE accounts ADD fedmount tinyint(4) unsigned NOT NULL DEFAULT 0;
ALTER TABLE accounts ADD index(locked,loggedin,laston);

CREATE TABLE clans (
	clanid int(11) unsigned NOT NULL auto_increment,
	clanname varchar(255) NOT NULL,
	clanshort varchar(5) NOT NULL,
	PRIMARY KEY (clanid),
	INDEX(clanname),
	INDEX(clanshort)
) Type=INNODB;

ALTER TABLE accounts ADD clanid int(11) unsigned not null default 0;
ALTER TABLE accounts ADD clanrank tinyint(4) unsigned not null default 0;
ALTER TABLE accounts ADD clanjoindate datetime not null default "0000-00-00 00:00:00";
ALTER TABLE accounts ADD INDEX(clanid);

ALTER TABLE clans ADD clanmotd varchar(255);
ALTER TABLE clans ADD clandesc text;
ALTER TABLE clans ADD motdauthor int(11) unsigned not null default 0;
ALTER TABLE clans ADD descauthor int(11) unsigned not null default 0;
ALTER TABLE clans ADD customsay varchar(15) not null default '';

ALTER TABLE mounts ADD mountfeedcost int(11) unsigned not null default 20;

UPDATE accounts SET superuser=0x7ffbffff WHERE superuser=3;
UPDATE accounts SET superuser=540 WHERE superuser=2;
UPDATE accounts SET superuser=256 WHERE superuser=1;
UPDATE accounts SET superuser=superuser|16 WHERE moderator=1;
ALTER TABLE accounts DROP moderator;

ALTER TABLE petitions ADD closedate datetime not null default '0000-00-00 00:00:00';
UPDATE petitions SET closedate = date WHERE status=2;
ALTER TABLE petitions ADD closeuserid int(11) unsigned not null default 0;

ALTER TABLE news ADD INDEX(newsdate);

ALTER TABLE riddles ADD author varchar(255) not null default "System";

ALTER TABLE referers ADD INDEX(uri(100));

ALTER TABLE commentary ADD INDEX(author);
ALTER TABLE petitions ADD INDEX(status);
ALTER TABLE petitions ADD INDEX(author);
ALTER TABLE petitions ADD INDEX(closeuserid);

update masters set creaturewin='Learn to adapt your style, and you shall prevail.' where creaturename='Sensei Noetha';

ALTER TABLE referers ADD dest varchar(255);
ALTER TABLE referers ADD ip varchar(40); #ip6 compatible

ALTER TABLE logdnet ADD version varchar(255) not null default 'Unknown';
ALTER TABLE logdnet ADD admin varchar(255) not null default 'unknown';
ALTER TABLE logdnet ADD lastping datetime not null default '0000-00-00 00:00:00';
UPDATE logdnet SET lastping=lastupdate;


ALTER TABLE accounts CHANGE race race varchar(25) NOT NULL default "Horrible Gelatinous Blob";
UPDATE accounts SET attack=attack-1,race="Troll" WHERE race='1';
UPDATE accounts SET defence=defence-1,race="Elf" WHERE race='2';
UPDATE accounts SET race="Human" WHERE race='3';
UPDATE accounts SET race="Dwarf" WHERE race='4';
UPDATE accounts SET race="Horrible Gelatinous Blob" WHERE race='0';

ALTER TABLE accounts CHANGE location location varchar(25) NOT NULL default "Degolburg";
UPDATE accounts SET location="The Boar's Head Inn" WHERE location='1';
UPDATE accounts SET location="Degolburg" WHERE location='0';

UPDATE accounts SET password=md5(password) WHERE length(password) < 32;
UPDATE accounts SET password=md5(password);

ALTER TABLE paylog ADD processdate datetime default '0000-00-00';

ALTER TABLE antidos TYPE=INNODB;
ALTER TABLE armor TYPE=INNODB;
ALTER TABLE bans TYPE=INNODB;
ALTER TABLE commentary TYPE=INNODB;
ALTER TABLE creatures TYPE=INNODB;
ALTER TABLE logdnet TYPE=INNODB;
ALTER TABLE mail TYPE=INNODB;
ALTER TABLE masters TYPE=INNODB;
ALTER TABLE moderatedcomments TYPE=INNODB;
ALTER TABLE motd TYPE=INNODB;
ALTER TABLE nastywords TYPE=INNODB;
ALTER TABLE news TYPE=INNODB;
ALTER TABLE paylog TYPE=INNODB;
ALTER TABLE petitions TYPE=INNODB;
ALTER TABLE pollresults TYPE=INNODB;
ALTER TABLE referers TYPE=INNODB;
ALTER TABLE riddles TYPE=INNODB;
ALTER TABLE settings TYPE=INNODB;
ALTER TABLE taunts TYPE=INNODB;
ALTER TABLE translations TYPE=INNODB;
ALTER TABLE weapons TYPE=INNODB;

ALTER TABLE mounts ADD mountlocation varchar(25) NOT NULL DEFAULT 'all';
ALTER TABLE mounts ADD mountdkcost int(11) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE mounts CHANGE mountactive mountactive int(11) unsigned not null default 0;

ALTER TABLE accounts CHANGE name name varchar(60) not null default '';

alter table nastywords add type varchar(10);
update nastywords set type="nasty";

# Updates from prerelease.1 to prerelease.2
alter table translations change version version varchar(50);

# Updates from prelease.2 to prerelease.3
ALTER TABLE news ADD arguments text not null default '';

# Updates from prerelease.4 to prerelease.5
# YOU MUST TURN ON THE SERVER SUSPEND MODULE, MAKE THIS SQL UPGRADE, UPGRADE
# THE FILES AND THEN TuRN OFF THE SERVER SUSPEND MODULE.  DOING OTHERWISE
# WILL GIVE YOUR USERS ERRORS UNTIL BOTH DATABASE AND CODE ARE IN SYNC
ALTER TABLE accounts change defence defense int(11) unsigned NOT NULL default '1';

# Updates from prerelease.5 to prerelease.6
# Some folks have bogus replacements in their master data in their database,
# so, let's fix it.
REPLACE INTO masters VALUES (1,'Mireraband',1,'Small Dagger','Well done %w`&, I should have guessed you\'d grown some.','As I thought, %w`^, your skills are no match for my own!',NULL,NULL,12,2,2);
REPLACE INTO masters VALUES (2,'Fie',2,'Short Sword','Well done %w`&, you really know how to use your %x.','You should have known you were no match for my %X',NULL,NULL,22,4,4);
REPLACE INTO masters VALUES (4,'Guth',4,'Spiked Club','Ha!  Hahaha, excellent fight %w`&!  Haven\'t had a battle like that since I was in the RAF!','Back in the RAF, we\'d have eaten the likes of you alive!  Go work on your skills some old boy!',NULL,NULL,44,8,8);
REPLACE INTO masters VALUES (6,'Adwares',6,'Dwarven Battle Axe','Ach!  Y\' do hold yer %x with skeel!','Har!  Y\' do be needin moore praktise y\' wee cub!',NULL,NULL,66,12,12);
REPLACE INTO masters VALUES (8,'Ceiloth',8,'Orkos Broadsword','Well done %w`&, I can see that great things lie in the future for you!','You are becoming powerful, but not yet that powerful.',NULL,NULL,88,16,16);
REPLACE INTO masters VALUES (9,'Dwiredan',9,'Twin Swords','Perhaps I should have considered your %x...','Perhaps you\'ll reconsider my twin swords before you try that again?',NULL,NULL,99,18,18);
ALTER TABLE translations ADD INDEX (uri);

# Update from prerelease.6 to prerelease.7
# Nothing has used these fields for a while
ALTER TABLE accounts DROP slainby;
ALTER TABLE accounts DROP killedin;

# Update from prerelease.8 to prerelease.9
ALTER TABLE mail change subject subject varchar(100) not null;
