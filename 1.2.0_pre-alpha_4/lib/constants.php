<?php
// addnews ready
// translator ready
// mail ready

//Superuser constants
define('SU_MEGAUSER',1);
define('SU_EDIT_MOUNTS',2);
define('SU_EDIT_CREATURES',4);
define('SU_EDIT_PETITIONS',8);
define('SU_EDIT_COMMENTS',16);
define('SU_EDIT_DONATIONS',32);
define('SU_EDIT_USERS',64);
define('SU_EDIT_CONFIG',128);
define('SU_INFINITE_DAYS',256);
define('SU_EDIT_EQUIPMENT',512);
define('SU_EDIT_PAYLOG',1024);
define('SU_DEVELOPER',2048);
define('SU_POST_MOTD',4096);
define('SU_DEBUG_OUTPUT',8192);
define('SU_MODERATE_CLANS',16384);
define('SU_EDIT_RIDDLES',32768);
define('SU_MANAGE_MODULES',65536);
define('SU_AUDIT_MODERATION',131072);
define('SU_IS_TRANSLATOR',262144);
define('SU_RAW_SQL', 524288);
define('SU_VIEW_SOURCE', 1048576);
define('SU_NEVER_EXPIRE', 2097152);
define('SU_EDIT_ITEMS', 4194304);
define('SU_GIVE_GROTTO', 8388608);
define('SU_OVERRIDE_YOM_WARNING', 16777216);
define('SU_SHOW_PHPNOTICE', 33554432);
define('SU_IS_GAMEMASTER', 67108864);
define('SU_EDIT_ADVANCE_CONFIG', 134217728);

define('SU_ANYONE_CAN_SET',SU_DEBUG_OUTPUT | SU_INFINITE_DAYS | SU_OVERRIDE_YOM_WARNING | SU_SHOW_PHPNOTICE);
define('SU_DOESNT_GIVE_GROTTO',SU_DEBUG_OUTPUT | SU_INFINITE_DAYS | SU_VIEW_SOURCE|SU_NEVER_EXPIRE | SU_IS_GAMEMASTER);
define('SU_HIDE_FROM_LEADERBOARD',SU_MEGAUSER | SU_EDIT_DONATIONS | SU_EDIT_USERS | SU_EDIT_CONFIG | SU_EDIT_ADVANCE_CONFIG | SU_INFINITE_DAYS | SU_DEVELOPER | SU_RAW_SQL);
define('NO_ACCOUNT_EXPIRATION', SU_HIDE_FROM_LEADERBOARD|SU_NEVER_EXPIRE);
//likely privs which indicate a visible admin.
define('SU_GIVES_YOM_WARNING', SU_EDIT_COMMENTS | SU_EDIT_USERS | SU_EDIT_CONFIG | SU_POST_MOTD);

//Clan constants
//Changed for v1.1.0 Dragonprime Edition to extend clan possibilities
define('CLAN_APPLICANT',0);
define('CLAN_MEMBER',10);
define('CLAN_OFFICER',20);
define('CLAN_LEADER',30);
define('CLAN_FOUNDER',31);

//Location Constants
define('LOCATION_FIELDS','Degolburg');
define('LOCATION_INN','The Boar\'s Head Inn');

//Gender Constants
define('SEX_MALE',0);
define('SEX_FEMALE',1);

//Miscellaneous
define('INT_MAX',4294967295);

define('RACE_UNKNOWN','Horrible Gelatinous Blob');

//Character Deletion Types
define('CHAR_DELETE_AUTO',1);
define('CHAR_DELETE_MANUAL',2);
define('CHAR_DELETE_PERMADEATH',3); //reserved for the future -- I don't have any plans this way currently, but it seemed appropriate to have it here.
define('CHAR_DELETE_SUICIDE',4);

// Constants used in lib/modules - for providing more information about the
// status of the module
define('MODULE_NO_INFO',0);
define('MODULE_INSTALLED',1);
define('MODULE_VERSION_OK',2);
define('MODULE_NOT_INSTALLED',4);
define('MODULE_FILE_NOT_PRESENT',8);
define('MODULE_VERSION_TOO_LOW',16);
define('MODULE_ACTIVE',32);
define('MODULE_INJECTED',64);

function myDefine($name,$value) {
	define($name, $value);
}
