# PRIVATE_CODE
CREATE TABLE mail (
messageid int(11) unsigned not null auto_increment,
msgfrom int(11) unsigned not null,
msgto int(11) unsigned not null,
subject varchar(50) not null,
body text not null,
sent datetime not null,
seen bit not null default 0,
PRIMARY KEY (messageid)
);

ALTER TABLE accounts ADD prefs text not null;
