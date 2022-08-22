# PRIVATE_CODE
alter table accounts change goldinbank goldinbank int(11) not null default 0;
alter table accounts add emailaddress varchar(128) not null default "";
alter table accounts add emailvalidation varchar(32) not null default "";
alter table accounts add killedin varchar(20) not null default "";
alter table accounts add sentnotice int(11) not null default 0;

CREATE TABLE motd (
	motditem int(11) unsigned not null auto_increment,
	motdtitle varchar(200),
	motdbody text,
	motddate datetime,
	PRIMARY KEY (motditem)
) Type=MyISAM;

update accounts set title="Farmboy" where sex=0 and dragonkills=0;
update accounts set title="Farmgirl" where sex=1 and dragonkills=0;
update accounts set name=concat("Farmboy ",name) where sex=0 and dragonkills=0;
update accounts set name=concat("Farmgirl ",name) where sex=1 and dragonkills=0;
