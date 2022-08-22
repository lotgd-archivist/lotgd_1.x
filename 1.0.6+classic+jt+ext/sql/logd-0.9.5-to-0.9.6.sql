# PRIVATE_CODE
ALTER TABLE motd ADD motdtype tinyint(4) unsigned not null default 0;

CREATE TABLE pollresults (
	resultid int(11) unsigned not null auto_increment,
	choice int(11) unsigned not null,
	account int(11) unsigned not null,
	motditem int(11) unsigned not null,
	PRIMARY KEY (resultid)
) Type=INNODB;

CREATE TABLE referers (
	refererid  int(11) unsigned not null auto_increment,
	uri text,
	count int(11),
	last datetime,
	PRIMARY KEY (refererid)
) Type=INNODB;

ALTER TABLE referers ADD site varchar(50) not null default "";

-- Graveyard Changes
ALTER TABLE creatures ADD location tinyint(4) not null default 0;
ALTER TABLE accounts ADD hauntpoints int(11) unsigned not null default 0;
ALTER TABLE accounts ADD soulpoints int(11) unsigned not null default 0;
ALTER TABLE accounts ADD gravefights int(11) unsigned not null default 0;
ALTER TABLE accounts ADD hauntedby varchar(50) not null default "";
ALTER TABLE accounts ADD deathpower int(11) unsigned not null default 0;
ALTER TABLE accounts ADD gensize int(11) unsigned not null default 0;
ALTER TABLE accounts ADD recentcomments datetime not null default "0000-00-00 00:00:00";
UPDATE accounts SET recentcomments = lasthit;
UPDATE accounts SET gravefights=10;
UPDATE accounts SET soulpoints=100;

UPDATE creatures SET location=1 WHERE creaturename LIKE "% bat%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% skeleton%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% banschee%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% banshee%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% demon%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% devil%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% spirit%";
UPDATE creatures SET location=1 WHERE creaturename LIKE "% necro%";

ALTER TABLE accounts ADD donation int(11) unsigned not null default 0;
ALTER TABLE accounts ADD donationspent int(11) unsigned not null default 0;
ALTER TABLE accounts ADD donationconfig text not null default "";

ALTER TABLE accounts ADD referer int(11) unsigned not null default 0;
ALTER TABLE accounts ADD refererawarded tinyint(4) unsigned not null default 0;
ALTER TABLE accounts CHANGE lastip lastip varchar(40) not null default "";
