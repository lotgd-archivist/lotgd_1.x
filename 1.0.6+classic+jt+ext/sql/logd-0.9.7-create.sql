# PRIVATE_CODE
#
# Table structure for table 'accounts'
#

CREATE TABLE accounts (
  acctid int(11) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  sex tinyint(4) unsigned NOT NULL default '0',
  specialty int(4) unsigned NOT NULL default '0',
  darkarts int(11) unsigned NOT NULL default '0',
  magic int(11) unsigned NOT NULL default '0',
  thievery int(11) unsigned NOT NULL default '0',
  experience int(11) unsigned NOT NULL default '0',
  charisma int(11) unsigned NOT NULL default '0',
  seenlover tinyint(4) unsigned NOT NULL default '0',
  gold int(11) unsigned NOT NULL default '0',
  weapon varchar(50) NOT NULL default 'Fists',
  armor varchar(50) NOT NULL default 'T-Shirt',
  seenmaster int(4) unsigned NOT NULL default '0',
  level int(11) unsigned NOT NULL default '1',
  defence int(11) unsigned NOT NULL default '1',
  attack int(11) unsigned NOT NULL default '1',
  alive int(11) unsigned NOT NULL default '1',
  goldinbank int(11) unsigned NOT NULL default '0',
  marriedto int(11) unsigned NOT NULL default '0',
  playerkills int(4) unsigned NOT NULL default '1',
  spirits int(4) NOT NULL default '0',
  laston datetime NOT NULL default '0000-00-00 00:00:00',
  hitpoints int(11) NOT NULL default '10',
  maxhitpoints int(11) unsigned NOT NULL default '10',
  gems int(11) unsigned NOT NULL default '0',
  weaponvalue int(11) unsigned NOT NULL default '0',
  armorvalue int(11) unsigned NOT NULL default '0',
  location int(4) unsigned NOT NULL default '0',
  turns int(11) unsigned NOT NULL default '10',
  title varchar(20) NOT NULL default '',
  password varchar(32) binary NOT NULL default '',
  darkartuses int(4) unsigned NOT NULL default '0',
  magicuses int(4) unsigned NOT NULL default '0',
  thieveryuses int(4) unsigned NOT NULL default '0',
  badguy text NOT NULL,
  allowednavs text NOT NULL,
  output text NOT NULL,
  loggedin tinyint(4) unsigned NOT NULL default '0',
  resurrections int(11) unsigned NOT NULL default '0',
  superuser int(11) unsigned NOT NULL default '0',
  weapondmg int(11) NOT NULL default '0',
  armordef int(11) NOT NULL default '0',
  age int(11) unsigned NOT NULL default '0',
  charm int(11) unsigned NOT NULL default '0',
  specialinc varchar(50) NOT NULL default '',
  specialmisc text NOT NULL,
  login varchar(50) NOT NULL default '',
  slainby varchar(50) NOT NULL default '',
  lastmotd datetime NOT NULL default '0000-00-00 00:00:00',
  playerfights int(11) unsigned NOT NULL default '3',
  lasthit datetime NOT NULL default '0000-00-00 00:00:00',
  seendragon tinyint(4) unsigned NOT NULL default '0',
  dragonkills int(11) unsigned NOT NULL default '0',
  drunkenness int(11) unsigned NOT NULL default '0',
  locked tinyint(4) unsigned NOT NULL default '0',
  restorepage varchar(128) default '',
  seenbard tinyint(4) unsigned NOT NULL default '0',
  hashorse tinyint(4) unsigned NOT NULL default '0',
  bufflist text NOT NULL,
  gentime double unsigned NOT NULL default '0',
  gentimecount int(11) unsigned NOT NULL default '0',
  lastip varchar(15) NOT NULL default '',
  uniqueid varchar(32) default NULL,
  PRIMARY KEY  (acctid)
) TYPE=MyISAM;

#
# Dumping data for table 'accounts'
#
INSERT INTO accounts (login,name,password,superuser,laston) VALUES ("ADMIN","ADMIN","CHANGEME",3,now());


#
# Table structure for table 'armor'
#

CREATE TABLE armor (
  armorid int(11) unsigned NOT NULL auto_increment,
  armorname varchar(128) default NULL,
  value int(11) NOT NULL default '0',
  defense int(11) NOT NULL default '1',
  PRIMARY KEY  (armorid)
) TYPE=MyISAM;

#
# Dumping data for table 'armor'
#

INSERT INTO armor VALUES (1,'Cloth Jerkin',45,1);
INSERT INTO armor VALUES (2,'Knitted Tunic',225,2);
INSERT INTO armor VALUES (3,'Leather Tunic',585,3);
INSERT INTO armor VALUES (4,'Studded Leather Tunic',990,4);
INSERT INTO armor VALUES (5,'Chainmail Hauberk',1575,5);
INSERT INTO armor VALUES (6,'Apprentice Robe Lev 1',2250,6);
INSERT INTO armor VALUES (7,'Field Plate',2790,7);
INSERT INTO armor VALUES (8,'Full Plate',3420,8);
INSERT INTO armor VALUES (9,'Medeusa Skin Tunic',4230,9);
INSERT INTO armor VALUES (10,'Apprentice Robe Lev 2',5040,10);
INSERT INTO armor VALUES (11,'Dragonscale Cape',5850,11);
INSERT INTO armor VALUES (12,'Greaves of the Stone Stance',6840,12);
INSERT INTO armor VALUES (13,'Master E\'s Robe',8010,13);
INSERT INTO armor VALUES (14,'Mantle of Sky',9000,14);
INSERT INTO armor VALUES (15,'Shroud of Magic',10350,15);

#
# Table structure for table 'bans'
#

CREATE TABLE bans (
  ipfilter varchar(15) NOT NULL default '',
  uniqueid varchar(32) NOT NULL default '',
  banexpire date default NULL,
  banreason text NOT NULL
) TYPE=MyISAM;

#
# Dumping data for table 'bans'
#


#
# Table structure for table 'commentary'
#

CREATE TABLE commentary (
  commentid int(11) unsigned NOT NULL auto_increment,
  section varchar(20) default NULL,
  author int(11) unsigned NOT NULL default '0',
  comment varchar(200) NOT NULL default '',
  postdate datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (commentid)
) TYPE=MyISAM;


#
# Table structure for table 'creatures'
#

CREATE TABLE creatures (
  creatureid int(11) NOT NULL auto_increment,
  creaturename varchar(50) default NULL,
  creaturelevel int(11) default NULL,
  creatureweapon varchar(50) default NULL,
  creaturelose varchar(120) default NULL,
  creaturewin varchar(120) default NULL,
  creaturegold int(11) default NULL,
  creatureexp int(11) default NULL,
  creaturehealth int(11) default NULL,
  creatureattack int(11) default NULL,
  creaturedefense int(11) default NULL,
  oldcreatureexp int(11) default NULL,
  createdby varchar(50) default NULL,
  PRIMARY KEY  (creatureid)
) TYPE=MyISAM;

#
# Dumping data for table 'creatures'
#

INSERT INTO creatures VALUES (1,'Thieving Kender',1,'Whirling Hoopak','You only wish you could pickpocket your money back from the thief.',NULL,36,14,10,1,1,14,'Appleshiner');
INSERT INTO creatures VALUES (2,'Rude Student',1,'Worn Textbook','You have sent this student to permanent detention',NULL,36,14,10,1,1,14,'Appleshiner');
INSERT INTO creatures VALUES (3,'Baby Unicorn',1,'Blunt Horn','You feel like a real jerk killing something so pretty',NULL,36,14,10,1,1,14,'Appleshiner');
INSERT INTO creatures VALUES (4,'Small Purple Mouse',1,'Lashing pink tail','It is written in black and white: this strange technicolor mouse is no more',NULL,36,14,10,1,1,14,'Appleshiner');
INSERT INTO creatures VALUES (5,'Grunting Piggy',1,'Wiggly Snout','Mmm...Bacon',NULL,36,14,10,1,1,14,'Appleshiner');
INSERT INTO creatures VALUES (6,'Dumb Old Eyesore',2,'Nagging complaints','All she ever did was nag...nag...nag...',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (7,'Pygmy Marmoset',2,'Pieces of treebark','The strange little monkey falls from the trees and lies still.',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (8,'Burly Man',15,'Amazing Absorbency','Fantasize, fantasize about Burly ...Eeeh, Eeeh, Eeeh!',NULL,531,189,155,29,21,14,'Bluspring');
INSERT INTO creatures VALUES (9,'Screaming Banshee',2,'Hauntingly Beautiful Voice','You plunge your weapon into her breast, silencing her song',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (10,'Ursus Minor',2,'Twinkling Lights','After an astronomical battle Ursus Minor is the one seeing stars',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (11,'Stone Troll',2,'Gaping  Maw','That troll is stone cold ugly.',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (12,'Slithering asp',2,'Hypnotic eyes','You break the deadly gaze of the snake to save your own life',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (13,'Windan The Barbarian',2,'Feathered Spear','He really was just a feather-weight',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (14,'Studious young sorceror',2,'Newly memorized spells','Perhaps she should have studied harder.',NULL,97,24,21,3,3,14,'Appleshiner');
INSERT INTO creatures VALUES (15,'Four and Twenty Blackbirds',3,'Freshly Baked Pies','Now isn\'t this a dainty dish to set before a king?',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (16,'Amazon',3,'Bow and Arrow','The beautiful warrior lass has lost a battle for the first time.',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (17,'Overbearing mother',3,'9pm Curfew','With one fell swoop you save your social life!',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (18,'Huge Bald Programmer',3,'Shining Forehead','You feel as if you have seen this man before in the village',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (19,'Someone\'s Grandfather',3,'Stories that go nowhere','This reminds you of the time you needed a new heel for your shoe',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (20,'Juvenile Delinquent',3,'Poor Attitude','With a satisfying thump, you knock the smirk right off his face',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (21,'Beautiful Woodland Sprite',3,'Kindness and tranquility','You really cut that wood sprite down to size',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (22,'Society Belle',3,'Southern Charm','Now she will never be on time for the ball!',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (23,'Giant Goldfish Cracker',3,'Annoying Commercial Jingles','Now *there* is a fish that smiled till you bit his head off.',NULL,148,34,32,5,4,14,'Appleshiner');
INSERT INTO creatures VALUES (24,'An Angry Mob',4,'Torches','There is no justice for an angry mob',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (25,'Polar Bear',4,'Terrible claws','That bear really needed an attitude adjustment',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (26,'Decaying Skeleton',4,'Rusty Sword','His leg ought to make a good toy for your dog.',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (27,'Willie the Wonder Hampster',4,'Nuclear Sunflower Seeds','You smile a grim smile at Willie and quietly munch on some seeds',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (28,'Winged Demon Of Death',4,'Red Glare','You cut off the Demon\'s head, to be sure of its death.',NULL,162,45,43,7,6,15,'foilwench');
INSERT INTO creatures VALUES (29,'Small Dragon',4,'Hot air','For one moment you thought THIS was the Green Dragon, but alas not.',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (30,'Monstrous Dustbunny',4,'Sneezing fits','You sweep the poor bunny under the rug when no one is looking',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (31,'Small Rabbit',4,'Sharp Pointy Teeth','To think you almost ran away!',NULL,162,45,43,7,6,15,'Appleshiner');
INSERT INTO creatures VALUES (32,'Mature Unicorn',5,'Powerful Horn','A unicorn is a thing of beauty, even in death',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (33,'Gnome Necromancer',5,'Skeleton Familiar','Once you killed its familiar that Gnome was really weak.',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (34,'Purple Dinosaur',5,'Helpful Nature','There seems to be a person inside that strange dino.  You wonder why.',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (35,'Orc Emmisary',5,'Diplomacy','You never were the diplomatic type.',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (36,'A Dark Spiritling',5,'Dark Magic','The spiritling gasps a final breath as you end its pitiful life',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (37,'An Enchanted Toilet',5,'Ninja Flushing Action','That was just weird',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (38,'Giant Water Elemental',5,'Torrential Downpours','The giant creature is reduced a meager spring shower',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (39,'Violins',5,'Off-Key notes','Won\'t Someone PLEASE stop the Violins?',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (40,'Whirled Peas',5,'Green Slime','We should all pray for whirled peas.',NULL,198,55,53,9,7,15,'Appleshiner');
INSERT INTO creatures VALUES (41,'Magic Mushroom',6,'Swirling Colors','Guess you don\'t need to go ask Alice now',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (42,'Invisible Man',6,'Something Invisible and Sharp','You really aren\'t sure if it is dead, or just hiding...after all it is invisible',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (43,'Clown Of Death',6,'Balloon Animals from Beyond','You give the clown\'s nose a honk just for fun before you leave',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (44,'Heart of Stone',6,'Unromantic Thoughts','Aww, now look, you broke its heart!',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (45,'Troll Warrior',6,'Steel Broadsword','Wow, he smelled bad enough alive, can you imagine how he\'ll smell tomorrow?',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (46,'Bettie the Amazing Kung-Fu Butterfly',6,'Stupendous Kung-Fu Wings','You survived a battle to the death with a butterfly.  You must be so proud.',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (47,'Golden Goose',6,'14 Karat Gold Eggs','You have slain the goose but it is too heavy to take back to town.  All that wealth wasted.',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (48,'Black Tiger',6,'Claws','You ponder the tiger\'s body thinking what a good rug it might make',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (49,'Lambert the Sheepish Lion',6,'Timid Growling','You marched in like a lion and he died like a lamb',NULL,234,66,64,11,8,16,'Appleshiner');
INSERT INTO creatures VALUES (50,'Marsha of the Jungle',7,'Trained Monkeys','Poor Marsha, she thought those monkeys were better trained',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (51,'Moe',7,'Two Knives','Maybe those knives weren\'t so great',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (52,'Coffin',7,'Terrifying Premonitions','Best way to get rid of the coffin is Robitussin.',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (53,'Green Alligator',7,'Crushing Jaws','Crikey!  That thing could have killed you!',NULL,268,77,74,13,10,17,'foilwench');
INSERT INTO creatures VALUES (54,'Lindsey, Daughter of Erin the Ninja Squirrel',7,'A peanut-shooter','All these squirrels are nuts.',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (55,'Wizarding School Senior Student',7,'Memory Charm','Drat!  He took your memory before you killed him.  Who are you anyway?',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (56,'Enchanted Laundry Basket',7,'Stinky Socks of Terror','So that\'s what happens to lost socks!',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (57,'Carrot Top',7,'Phone Company Slogans','That has got to be the most satisfying kill you ever made.',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (58,'Macintosh Computer',7,'Amazingly Bright Colors','So THAT\'S what happens when you right-click a Mac.',NULL,268,77,74,13,10,17,'Appleshiner');
INSERT INTO creatures VALUES (59,'Interior Designer',8,'Color-coordinating Sword and Shield','You didn\'t like his style',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (60,'Daughter of the Devil',8,'Sinfully Good Looks','She is the girl your mom always warned you about.',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (61,'Wild Horses',8,'Dragging','I guess the saying is true, Wild Horse can\'t drag you away',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (62,'Steam Elemental',8,'Scalding jets of air','There was just enough steam left to make an espresso',NULL,302,89,84,15,11,19,'foilwench');
INSERT INTO creatures VALUES (63,'Gordon the Cheese Eater',8,'Odiferous Nature','You sure cut that cheese....eater',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (64,'Narcoleptic',8,'Drowsiness','This narcoleptic won\'t be waking up anytime soon',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (65,'Swarm of Bees',8,'Stinging','You feel like a pincushion, but you are alive',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (66,'Halfling Mage',8,'Painful Spells','That fight wasn\'t half as difficult as you thought it would be',NULL,302,89,84,15,11,19,'Appleshiner');
INSERT INTO creatures VALUES (67,'Citizen of Eythgim Village',9,'Slurs','Geeze, what did you ever do to him?',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (68,'Old Hag',9,'Red red rose','It is her turn to sleep an enchanted sleep',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (69,'Charging Buffalo',9,'Stampeding Hoofs','Do you know how to stop a charging buffalo?  Take away his credit card!',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (70,'Pawn',9,'Simple tactics','Well, that was a bad move',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (71,'Romulan',9,'Wrong-Genre Confusion','You think to yourself they look tougher on TV',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (72,'Large Griffon',9,'Beak and Claws','The mythical creature is no more',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (73,'Cliche Bond Movie Villian',9,'Cell-phone of Death','Well, really, who throws a cell phone?  I mean, that really, really hurt!',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (74,'Fuzzy Ferret',9,'Disarming Cuteness','Who knew ferrets were so violent!',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (75,'Starving Artist',9,'Pointy Paintbrushes','This guy really painted himself into a corner',NULL,336,101,94,17,13,21,'Appleshiner');
INSERT INTO creatures VALUES (76,'Guard from Eythgim Village',10,'Rapier','The people from that village seem angry!',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (77,'Garden Gnome',10,'Painful Tackiness','Stupid garden gnomes, what are they doing in the forest anyway',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (78,'Chicken',10,'Squawking','Good fortune smiles upon you- you will eat well tonight',NULL,369,114,105,19,14,24,'foilwench');
INSERT INTO creatures VALUES (79,'Marksman',10,'Deadly Aim','He took so long to aim, you just walked up and kicked him',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (80,'Enchanted Tissue Box',10,'Cold Germs','You pick up the box and put it in your pocket.  You never know when a tissue might come in handy',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (81,'Ghostly Woodsman',10,'Phantom Axe','You aren\'t sure how you did it, but you killed something that was already dead.  Good for you.',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (82,'Zombie',10,'Rotting Flesh','He really went to pieces, didn\'t he?',NULL,369,114,105,19,14,24,'foilwench');
INSERT INTO creatures VALUES (83,'Fortula the Plains Cat',10,'Claws and Teeth','That plains cat is now plain dead',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (84,'Erin the Ninja Squirrel',10,'Acorn tossing madness','That squirrel was a little crazy',NULL,369,114,105,19,14,24,'Appleshiner');
INSERT INTO creatures VALUES (85,'Bluebird of Happiness',11,'Uplifting Melody','Happiness is over-rated anyway',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (86,'Potato Newscaster',11,'Personal Opinions','It\'s ok, he was just a common \'tater',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (87,'Pygmy Dragon',11,'Fire Breath','You hope he wasn\'t a relative of the Green Dragon',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (88,'Zealous Photographer',11,'Flashing Lights','The things beautiful people have to put up with.',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (89,'Gary Larson',11,'Strange Cartoons','You still don\'t get it.',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (90,'Soldier from Eythgim Village',11,'Battle Cry','Eythgim seems to be waging a personal war with you',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (91,'80\'s Glam Rocker',11,'Blinding Makeup','You know your motto, Live and let die.',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (92,'Vigilante',11,'Sack of Doorknobs','This carnage disgusts you',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (93,'The Devil',11,'Eternal Damnation','For being the Devil, he really wasn\'t so hot.',NULL,402,127,115,21,15,27,'Appleshiner');
INSERT INTO creatures VALUES (94,'Mercenary',12,'Blood-chilling stare','You could use an ale',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (95,'Magic Mirror',12,'Flattering Remarks','Mirror, Mirror, on the Ground, it is your death that you have found.',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (96,'Broadway Extra',12,'Memorable Showtunes','This extra is really on her own now',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (97,'Butter the Sprite',12,'Magic Pixie Dust','Butter?  What kind of a name is Butter?',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (98,'Father Time',12,'Old Age','Your victory is not comforting, you know he will still catch up to you',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (99,'Eythgim Village Archers',12,'Flaming Arrows','The tree is  burning, you\'d better try to put it out.',NULL,435,141,125,23,17,31,'foilwench');
INSERT INTO creatures VALUES (100,'Animate Stormcloud',12,'Lightning','The storm is no more, you can see the light of day peeking through the last of the clouds',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (101,'Three Toed Sloth',12,'Slow responses','Your swift moves were too fast for this sloth',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (102,'Death',12,'Cold Grasp','There it is!  Death!!!!!',NULL,435,141,125,23,17,31,'Appleshiner');
INSERT INTO creatures VALUES (103,'Cerberus',13,'Three drooling maws','Each one\'s breath was worse than the last',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (104,'Man from the deep deep, dark dark, deep dark wood',13,'Snarling and biting','Now the creature from the deep, deep, dark, dark, deep, dark woods is finally dead',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (105,'Devil in a blue dress',13,'Alluring Promises','You really prefer black dresses anyway.',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (106,'A Giant',13,'Smashing Club','Geeze, he almost fell on you!',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (107,'Commander of the Eythgim Army',13,'Winning Tactics','You have bested their army by killing its master.',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (108,'Ice Dragon',13,'Frost Breath','You defeated him, but caught a cold.',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (109,'Pushmi Pullyew',13,'Conflictory Arguments','That strange creature couldn\'t tell if it was coming or going.',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (110,'Whale',13,'Tail','You know Violet isn\'t going to believe a Whale attacked you in the forest',NULL,467,156,135,25,18,36,'Appleshiner');
INSERT INTO creatures VALUES (111,'Gorma The Leper',13,'Contagious Disease','It looks like the leper\'s fighting strategy has fallen apart...',NULL,467,156,135,25,18,36,'foilwench');
INSERT INTO creatures VALUES (112,'Samurai Master',14,'Daisho','You bow to the fallen master before you walk away',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (113,'Bartender',14,'Duck','That\'s not a Duck!  It\'s a Chicken!',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (114,'Prince of Eythgim Village',14,'Bribes of gold','You are not easily bribed, and he was a mortal enemy',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (115,'Loch Ness Monster',14,'Shocking appearance','Why in the world would that monster be in this forest.  You must have been dreaming!',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (116,'Fuzzie Wuzzie',14,'Male Pattern Baldness','Fuzzie Wuzzie wasn\'t very fuzzie, was he?',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (117,'Princess of Eythgim Village',15,'Beguiling Words','She tried to tempt you, but you resisted.',NULL,531,189,155,29,21,42,'Appleshiner');
INSERT INTO creatures VALUES (118,'Ugly Harpy',14,'Poisonus Breath','Her breath was worse than her bite',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (119,'Field Mouse',14,'Chewing','That was some powerful mouse!',NULL,499,172,145,27,20,42,'Appleshiner');
INSERT INTO creatures VALUES (120,'Monarch of Eythgim Village',16,'Royal Commands','I guess this makes you king of their Village.',NULL,563,207,166,31,22,49,'Appleshiner');
INSERT INTO creatures VALUES (121,'Cyclops Warrior',15,'Fire Eye','The dead Cyclops\' one eye stares at you blankly.',NULL,531,189,155,29,21,49,'foilwench');
INSERT INTO creatures VALUES (122,'Cupid',15,'Pink Arrows of Love','You have slain Cupid, you shall never find true love',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (123,'Tabloid Writer',15,'Far-fetched stories','If you ask me, SHE was the six-armed alien who gave birth to the world\'s largest infant Elvis impersonator',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (124,'Evil Wizard',15,'Tormented Souls','You have set the tortured souls free',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (125,'Artemis, Goddess of the Hunt',15,'Winged Stags','The moon itself shall weep for the slain Artemis',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (126,'Centaur Archer',15,'Deadly marksmanship','The twisted man-beast lies still',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (127,'Mime',15,'Something imaginary','His death is a rather sad charade',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (128,'Chili Chef',15,'Insanity Peppers','Man, that\'s good chili',NULL,531,189,155,29,21,49,'Appleshiner');
INSERT INTO creatures VALUES (129,'Brownie',16,'Minuscule weapons','The tiny warrior dies with a faint squeal',NULL,563,207,166,31,22,57,'Appleshiner');
INSERT INTO creatures VALUES (130,'Darkness',16,'Self-induced terror','You are afraid of the dark no longer',NULL,563,207,166,31,22,57,'Appleshiner');
INSERT INTO creatures VALUES (131,'Mountain King',16,'God-like fury','The King has died, long live the King',NULL,563,207,166,31,22,57,'Appleshiner');
INSERT INTO creatures VALUES (134,'Loneliness',17,'Silence','What is the sound of one hand clapping?','What is the sound of one hand clapping?',36,0,1,0,0,0,NULL);
INSERT INTO creatures VALUES (135,'Loneliness',18,'Silence','What is the sound of one hand clapping?','What is the sound of one hand clapping?',0,0,1,0,25,0,NULL);
INSERT INTO creatures VALUES (136,'Le Chat dans le chapeau',15,'Rhyming Nonsense','In spanish El Gato en el sombrero','',531,189,155,29,21,0,'Appleshiner');
INSERT INTO creatures VALUES (138,'Whopper With Cheese',8,'Abundant Cholesterol','This is one whopper that won\'t show up on your hips tomorrow.',NULL,302,89,84,15,11,NULL,'foilwench');
INSERT INTO creatures VALUES (139,'Swarming Ladybugs',3,'Stinking Residue','Those Ladybugs weren\'t very ladylike.',NULL,148,34,32,5,4,NULL,'foilwench');
INSERT INTO creatures VALUES (140,'Football Fan',6,'Raucous Cheers','This fan doesn\'t have much to cheer about.',NULL,234,66,64,11,8,NULL,'foilwench');
INSERT INTO creatures VALUES (141,'King Agrippa',13,'Flaming Wingnuts','As you stare at his smoking corpse, you marvel at the ineffectiveness of flaming wingnuts.',NULL,467,156,135,25,18,NULL,'foilwench');
INSERT INTO creatures VALUES (142,'Cherubically Charming Child',1,'Curious Questions','You think it was the alliteration that did him in.',NULL,36,14,10,1,1,NULL,'foilwench');
INSERT INTO creatures VALUES (143,'Haberdasher',5,'Heavy Hat','Hats have gone out of style anyway.',NULL,198,55,53,9,7,NULL,'foilwench');
INSERT INTO creatures VALUES (144,'Rush Hour Driver',11,'Road Rage','It was a no-win situation, speeding on these forest paths.  The driver had it coming.',NULL,402,127,115,21,15,NULL,'foilwench');
INSERT INTO creatures VALUES (145,'Sorority Girl in Nightie',7,'Pillow Fighting Action','It\'s too bad she\'s dead . . . you liked her action.',NULL,268,77,74,13,10,NULL,'foilwench');
INSERT INTO creatures VALUES (146,'Ulysses WÃ¶lfgang',14,'Ridiculous Lies','Death to lies!  Death to Ulysses WÃ¶lfgang',NULL,499,172,145,27,20,NULL,'Joe');
INSERT INTO creatures VALUES (147,'Crazy Audrey',9,'Ravenous Kittens','With a well placed stomp, Audrey and the kittens are no more.',NULL,336,101,94,17,13,NULL,'Appleshiner');
INSERT INTO creatures VALUES (148,'Shining Happy People',5,'Hand Holding Terror','They just wanted to be your friend.  You meanie.',NULL,198,55,53,9,7,NULL,'foilwench');
INSERT INTO creatures VALUES (149,'Elsabutt',8,'Gravitational Pull','This is indeed a grave situation',NULL,302,89,84,15,11,NULL,'foilwench');
INSERT INTO creatures VALUES (150,'Mr. Bear',2,'Overwhelming Sleepiness','Mr. Bear was too tired to finish playing with you and went back to napping',NULL,97,24,21,3,3,NULL,'foilwench');
INSERT INTO creatures VALUES (151,'Everquest Junkie',12,'1337 Sp34k','D00d U pwned that guy!',NULL,435,141,125,23,17,NULL,'foilwench');
INSERT INTO creatures VALUES (152,'Little Cocobean',4,'Her Special Roast','She really made your heart race and pound, but now she has crumbled to the ground',NULL,162,45,43,7,6,NULL,'Appleshiner');
INSERT INTO creatures VALUES (153,'Humongous Hairball',5,'Incredible Drain-Clogging Power','The hairball and all its accompanying gook have been washed out to sea!',NULL,198,55,53,9,7,NULL,'foilwench');
INSERT INTO creatures VALUES (154,'Iron Chef',4,'Sizzling Eel Flambe!','You have rendered the Chef Ironless!  He\'s now simply a copper/tin alloy!',NULL,162,45,43,7,6,NULL,'foilwench');
INSERT INTO creatures VALUES (155,'The Government',7,'Taxes','That\'s a tax return you\'ll be taking to the bank!',NULL,268,77,74,13,10,NULL,'Bluspring');
INSERT INTO creatures VALUES (156,'Bar Fly',1,'Alcoholism','Good thing you got him before cirrhosis did!',NULL,36,14,10,1,1,NULL,'Bluspring');
INSERT INTO creatures VALUES (157,'Methane Man',7,'Unfriendly Gasses','Must have been that bean he ate!',NULL,268,77,74,13,10,NULL,'foilwench');
INSERT INTO creatures VALUES (158,'Wooly Mammoth',8,'Wildebeest Sidekick','You climb atop the Mammoth\'s remains and declare yourself king (or queen) for a day!',NULL,302,89,84,15,11,NULL,'foilwench');
INSERT INTO creatures VALUES (159,'Nasty Nevin',10,'Bossy Mom','Wrecked\'em?  Darn near killed \'em!',NULL,369,114,105,19,14,NULL,'Joe');
INSERT INTO creatures VALUES (160,'the Gar Man',3,'Jar of Cotton Balls','There is but one Gar Man!  And now he\'s just fluff!',NULL,148,34,32,5,4,NULL,'foilwench');
INSERT INTO creatures VALUES (161,'Hiker',1,'Walking Stick','His last words were \"...I must protect the dragon\"',NULL,36,14,10,1,1,NULL,'Appleshiner');
INSERT INTO creatures VALUES (162,'Camper',2,'Marshmallow-roasting Stick','With her dying breath she gasps \"...and I never saw the dragon.\"',NULL,97,24,21,3,3,NULL,'Appleshiner');
INSERT INTO creatures VALUES (163,'Forest Ranger',3,'Hunting Rifle','In his pack you find a copy of \"Dragon Seeking for Dummies\"',NULL,148,34,32,5,4,NULL,'Appleshiner');
INSERT INTO creatures VALUES (164,'Tree-Hugger',4,'Propaganda Leaflets','The leaflett reads \"Who Will Protect The Helpless Dragon?\"',NULL,162,45,43,7,6,NULL,'Appleshiner');
INSERT INTO creatures VALUES (165,'Forest Dwelling Hermit',5,'Hand-Made Sling','He speaks only these words as he dies: \"You must leave the Dragon\'s Forest.\"',NULL,198,55,53,9,7,NULL,'Appleshiner');
INSERT INTO creatures VALUES (166,'Member of Dragon Lovers Anonymous',6,'Stuffed Dragon Toy','When one wears a pin saying \"Dragon Lovers Anonymous\" they really are not as anonomous as they might think',NULL,234,66,64,11,8,NULL,'Appleshiner');
INSERT INTO creatures VALUES (167,'Initiate Dragon Watcher',7,'Binoculars','All he could say was \"Shhh, you are scaring the dragons!\"',NULL,268,77,74,13,10,NULL,'Appleshiner');
INSERT INTO creatures VALUES (168,'Dragon Watcher, Junior Class',8,'Annoyance','You find a dragon-watching textbook in his backpack.',NULL,302,89,84,15,11,NULL,'Appleshiner');
INSERT INTO creatures VALUES (169,'Dragon-watcher Senior Class',9,'Spelunking Equipment','You find a copy of \"Cave-Dwelling Dragons, and Where to Find Them.\" in his pack',NULL,336,101,94,17,13,NULL,'Appleshiner');
INSERT INTO creatures VALUES (170,'Master Dragon Watcher',10,'Stealth','You wonder who watches the watchers',NULL,369,114,105,19,14,NULL,'Appleshiner');
INSERT INTO creatures VALUES (171,'Initiate Dragon Tamer',11,'10-foot dragon pole','Good thing for you that pole only works on 10-foot dragons!',NULL,402,127,115,21,15,NULL,'Appleshiner');
INSERT INTO creatures VALUES (172,'Junior Dragon Tamer',12,'Large Whip','If he cannot tell the difference between you and a dragon, he has much work to do',NULL,435,141,125,23,17,NULL,'Appleshiner');
INSERT INTO creatures VALUES (173,'Senior Dragon Tamer',13,'Very Large Hoop','You regret killing him, you wanted to see him make a dragon jump through that hoop',NULL,467,156,135,25,18,NULL,'Appleshiner');
INSERT INTO creatures VALUES (174,'Master Dragon Tamer',14,'Tame Dragon','Once you killed the master, the dragon ran away',NULL,499,172,145,27,20,NULL,'Appleshiner');
INSERT INTO creatures VALUES (175,'Dragon Rider in Training',15,'Mechanical Bull','They learn to ride dragons using a mechanical bull?',NULL,531,189,155,29,21,NULL,'Appleshiner');
INSERT INTO creatures VALUES (176,'Dragon Rider',16,'Flying Dragon','A well-aimed strike grounds the flying dragon and his rider',NULL,563,207,166,31,22,NULL,'Appleshiner');
INSERT INTO creatures VALUES (177,'A batling',1,'Tiny flapping wings','Silly batling',NULL,36,14,10,1,1,NULL,'Appleshiner');
INSERT INTO creatures VALUES (178,'A Small Bat',2,'Slightly venomous fangs','The small mammal falls to the ground',NULL,97,24,21,3,3,NULL,'Appleshiner');
INSERT INTO creatures VALUES (179,'Medium Sized Bat',3,'Screeching Cries','You killed it just to shut it up.',NULL,148,34,32,5,4,NULL,'Appleshiner');
INSERT INTO creatures VALUES (180,'A Large Bat',4,'Guano','You feel the need to take a bath',NULL,162,45,43,7,6,NULL,'Appleshiner');
INSERT INTO creatures VALUES (181,'A Giant Bat',5,'Clinging and Biting','Why do movie bats always fly at people\'s hair?',NULL,198,55,53,9,7,NULL,'Appleshiner');
INSERT INTO creatures VALUES (182,'A Vampire Batling',6,'Pricking teeth','He resembled a mosquito more than a vampire bat',NULL,234,66,64,11,8,NULL,'Appleshiner');
INSERT INTO creatures VALUES (183,'A Small Vampire Bat',7,'Deadly Fangs','You kick at the annoying pest as it lies on the ground',NULL,268,77,74,13,10,NULL,'Appleshiner');
INSERT INTO creatures VALUES (184,'A Medium Sized Vampire Bat',8,'Echoing Sounds','Who decides that this bat is a \"Medium\"?  Maybe he is a giant bat who hasn\'t bloomed yet.',NULL,302,89,84,15,11,NULL,'Appleshiner');
INSERT INTO creatures VALUES (185,'A Huge Vampire Bat',9,'Blood-Sucking','You gave more blood to the Red Cross',NULL,336,101,94,17,13,NULL,'Appleshiner');
INSERT INTO creatures VALUES (186,'A Vampire in Bat Form',10,'Bad Romanian Accent','\"I vant to suk your blooood!\"',NULL,369,114,105,19,14,NULL,'Appleshiner');
INSERT INTO creatures VALUES (187,'A Vampire-Bat Prince',11,'Death Grip','You thought bats were republicans.',NULL,402,127,115,21,15,NULL,'Appleshiner');
INSERT INTO creatures VALUES (188,'Vampire Bat King',12,'Red Eyes','You decide to leave before the rest of the bats come to find their king',NULL,435,141,125,23,17,NULL,'Appleshiner');
INSERT INTO creatures VALUES (189,'A Sorcerous Vampire',13,'Petrifying Spells','A cold shiver goes down your spine.  You would rather be with Violet',NULL,467,156,135,25,18,NULL,'Appleshiner');
INSERT INTO creatures VALUES (221,'Vampirus,the Bat God',16,'Godly Power','You are even more god-like then he',NULL,563,207,166,31,22,NULL,'Appleshiner');
INSERT INTO creatures VALUES (220,'Frenzied vampire bats',15,'Numerous attacks','Why are there so many darn bats in this forest?',NULL,531,189,155,29,21,NULL,'Appleshiner');
INSERT INTO creatures VALUES (219,'A Seductive Vampiress',14,'Sultry Moves','It was a shame to kill her.',NULL,499,172,145,27,20,NULL,'Appleshiner');
INSERT INTO creatures VALUES (222,'Old Man Winter',9,'Bitter Cold','You shake your fist over his dead, pathetic corpse.',NULL,336,101,94,17,13,NULL,NULL);

#
# Table structure for table 'masters'
#

CREATE TABLE masters (
  creatureid int(11) unsigned NOT NULL auto_increment,
  creaturename varchar(50) default NULL,
  creaturelevel int(11) default NULL,
  creatureweapon varchar(50) default NULL,
  creaturelose varchar(120) default NULL,
  creaturewin varchar(120) default NULL,
  creaturegold int(11) default NULL,
  creatureexp int(11) default NULL,
  creaturehealth int(11) default NULL,
  creatureattack int(11) default NULL,
  creaturedefense int(11) default NULL,
  PRIMARY KEY  (creatureid)
) TYPE=MyISAM;

#
# Dumping data for table 'masters'
#

INSERT INTO masters VALUES (1,'Mireraband',1,'Small Dagger','Well done %w`&, I should have guessed you\'d grown some.','As I thought, %w`^, your skills are no match for my own!',NULL,NULL,12,2,2);
INSERT INTO masters VALUES (2,'Fie',2,'Short Sword','Well done %w`&, you really know how to use your %x.','You should have known you were no match for my %X',NULL,NULL,22,4,4);
INSERT INTO masters VALUES (3,'Glynyc',3,'Hugely Spiked Mace','Aah, defeated by the likes of you!  Next thing you know, Mireraband will be hunting me down!','Haha, maybe you should go back to Mireraband\'s class.',NULL,NULL,33,6,6);
INSERT INTO masters VALUES (4,'Guth',4,'Spiked Club','Ha!  Hahaha, excellent fight %w`&!  Haven\'t had a battle like that since I was in the RAF!','Back in the RAF, we\'d have eaten the likes of you alive!  Go work on your skills some old boy!',NULL,NULL,44,8,8);
INSERT INTO masters VALUES (5,'Unélith',5,'Thought Control','Your mind is greater than mine.  I concede defeat.','Your mental powers are lacking.  Meditate on this failure and perhaps some day you will defeat me.',NULL,NULL,55,10,10);
INSERT INTO masters VALUES (6,'Adwares',6,'Dwarven Battle Axe','Ach!  Y\' do hold yer %x with skeel!','Har!  Y\' do be needin moore praktise y\' wee cub!',NULL,NULL,66,12,12);
INSERT INTO masters VALUES (7,'Gerrard',7,'Battle Bow','Hmm, mayhaps I underestimated you.','As I thought.',NULL,NULL,77,14,14);
INSERT INTO masters VALUES (8,'Ceiloth',8,'Orkos Broadsword','Well done %w`&, I can see that great things lie in the future for you!','You are becoming powerful, but not yet that powerful.',NULL,NULL,88,16,16);
INSERT INTO masters VALUES (9,'Dwiredan',9,'Twin Swords','Perhaps I should have considered your %x...','Perhaps you\'ll reconsider my twin swords before you try that again?',NULL,NULL,99,18,18);
INSERT INTO masters VALUES (10,'Sensei Noetha',10,'Martial Arts Skills','Your style was superior, your form greater.  I bow to you.','Learn to adapt your style, and you shal prevail.',NULL,NULL,110,20,20);
INSERT INTO masters VALUES (11,'Celith',11,'Throwing Halos','Wow, how did you dodge all those halos?','Watch out for that last halo, it\'s coming back this way!',NULL,NULL,121,22,22);
INSERT INTO masters VALUES (12,'Gadriel the Elven Ranger',12,'Elven Long Bow','I can accept that you defeated me, because after all elves are immortal while you are not, so the victory will be mine.','Do not forget that elves are immortal.  Mortals will likely never defeat one of the fey.',NULL,NULL,132,24,24);
INSERT INTO masters VALUES (13,'Adoawyr',13,'Gargantuan Broad Sword','If I could have picked up this sword, I probably would have done better!','Haha, I couldn\'t even pick the sword UP and I still won!',NULL,NULL,143,26,26);
INSERT INTO masters VALUES (14,'Yoresh',14,'Death Touch','Well, you evaded my touch.  I salute you!','Watch out for my touch next time!',NULL,NULL,154,28,28);

#
# Table structure for table 'news'
#

CREATE TABLE news (
  newsid int(11) unsigned NOT NULL auto_increment,
  newstext text NOT NULL,
  newsdate date NOT NULL default '0000-00-00',
  PRIMARY KEY  (newsid,newsdate)
) TYPE=MyISAM;

#
# Dumping data for table 'news'
#

INSERT INTO news VALUES (1,'`^A strange awakening takes place in the world.`0',now());


#
# Table structure for table 'taunts'
#

CREATE TABLE taunts (
  tauntid int(11) unsigned NOT NULL auto_increment,
  taunt text,
  editor varchar(50) default NULL,
  PRIMARY KEY  (tauntid)
) TYPE=MyISAM;

#
# Dumping data for table 'taunts'
#

INSERT INTO taunts VALUES (1,'`5\"`6Ya know, `4%w`6 really had it coming to %s after all those things I said about %p mom,`5\" commented %W.','Joe');
INSERT INTO taunts VALUES (2,'`5\"`6I\'m really going to enjoy this new `4%x`6 that %w`6 had,`5\" exclaimed %W.','base');
INSERT INTO taunts VALUES (3,'`5\"`6Aah, so `bthat\'s`b what `4%X`6 is for!`5\" exclaimed %W','base');
INSERT INTO taunts VALUES (7,'`5\"`6Oh man!  I didn\'t think you had it in you, `4%W`6,`5\" %w exclaims.','base');
INSERT INTO taunts VALUES (5,'`5%W was heard to say, \"`6%p %x was no match for my `4%X`6!`5\"','Joe');
INSERT INTO taunts VALUES (6,'`5\"`6You know, you really shouldn\'t hold your `4%x`6 by the pointy end next time,`5\" suggested %W.','base');
INSERT INTO taunts VALUES (8,'`5\"`6Damn, Damn, Damn!`5\" %w roars in frustration.','base');
INSERT INTO taunts VALUES (9,'`5\"`6Geeze!  I don\'t believe this!  This is the last time I attack you, `4%W`6!`5\" %w laments.','Joe');
INSERT INTO taunts VALUES (10,'`5\"`6I must not be as studly as I thought...!`5\" %w concedes.','base');
INSERT INTO taunts VALUES (11,'`5\"`6You got lucky, `4%W`6.  You know you did!`5\" %w declares.','Joe');
INSERT INTO taunts VALUES (12,'`5\"`6I\'m having a very bad day....`5\" %w moans.','base');
INSERT INTO taunts VALUES (13,'`5\"`6Somebody tell me this is a dream....`5\" %w groans.','base');
INSERT INTO taunts VALUES (14,'`5\"`6LIFE IS `bNOT`b FAIR!`5\" yowls %w in agony.','base');
INSERT INTO taunts VALUES (15,'`5\"`6I\'ll tell everyone you wear a girdle, `4%w`6,`5\" reveals %W.','Joe');
INSERT INTO taunts VALUES (16,'`5\"`6YOU WILL PAY WITH YOUR FIRSTBORN, `4%W`6,`5\" screams %w.','Joe');
INSERT INTO taunts VALUES (17,'`5%W laughs.  \"`6I don\'t think you will be attacking me again, pal.`5\"','base');
INSERT INTO taunts VALUES (18,'`5%W smiles.  \"`6You are too slow.  You are too stupid.`5\"','base');
INSERT INTO taunts VALUES (19,'`5%w sobs uncontrollably.','base');
INSERT INTO taunts VALUES (20,'`5\"`6Ack!  I\'m humiliated in front of all!`5\" exclaims %w','base');
INSERT INTO taunts VALUES (21,'`5\"`6Why do I even try?  Why did I get up today?`5\" wonders %w aloud.','base');
INSERT INTO taunts VALUES (22,'`5\"`6Haw!  Haw!  I told you to run!`5\" laughs %W','base');

#
# Table structure for table 'weapons'
#

CREATE TABLE weapons (
  weaponid int(11) unsigned NOT NULL auto_increment,
  weaponname varchar(128) default NULL,
  value int(11) NOT NULL default '0',
  damage int(11) NOT NULL default '1',
  PRIMARY KEY  (weaponid)
) TYPE=MyISAM;

#
# Dumping data for table 'weapons'
#

INSERT INTO weapons VALUES (1,'Twig',48,1);
INSERT INTO weapons VALUES (2,'Pointy Twig',225,2);
INSERT INTO weapons VALUES (3,'Big Stick',585,3);
INSERT INTO weapons VALUES (4,'Board w/ nail',990,4);
INSERT INTO weapons VALUES (5,'Heavy Club',1575,5);
INSERT INTO weapons VALUES (6,'Heavy Spiked Club',2250,6);
INSERT INTO weapons VALUES (7,'Long Sword',2790,7);
INSERT INTO weapons VALUES (8,'Broadsword',3420,8);
INSERT INTO weapons VALUES (9,'Morning Star of Glory',4230,9);
INSERT INTO weapons VALUES (10,'Flaming Sword of Falme',5040,10);
INSERT INTO weapons VALUES (11,'Shining Claymore',5850,11);
INSERT INTO weapons VALUES (12,'Gauntlets of the Stone Fist',6840,12);
INSERT INTO weapons VALUES (13,'Master E\'s Halberd',8010,13);
INSERT INTO weapons VALUES (14,'Scythe of Thunder',9000,14);
INSERT INTO weapons VALUES (15,'Spell: Mind Melt',10350,15);

###
### settings TABLE
###

CREATE TABLE settings (
	setting VARCHAR(20) NOT NULL,
	value varchar(128) NOT NULL,
	PRIMARY KEY (setting)
	);

alter table weapons add level int(11) not null default 0;
alter table armor add level int(11) not null default 0;
alter table accounts add dragonpoints text not null default "";
alter table accounts add boughtroomtoday tinyint(4) not null default 0;

# This optional script deletes all existing taunts, replacing them with new ones (the original 
# taunts came from LoRD), and deletes all weapons and armor, replacing them with MANY MANY new 
# ones, a different set for each dragon kill up to 12 dragon kills.

DELETE FROM taunts;
DELETE FROM weapons;
DELETE FROM armor;

INSERT INTO taunts VALUES (0,'`5\"`6Just wait for my revenge, `4%W`6. It will be swift!`5\" %w declares.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6I\'m really going to enjoy this new `4%x`6 that %w`6 had,`5\" exclaimed %W.','joe');
INSERT INTO taunts VALUES (0,'`5\"`6Aah, so `bthat\'s`b what `4%X`6 is for!`5\" exclaimed %W','joe');
INSERT INTO taunts VALUES (0,'`5\"`6Oh man!  I didn\'t think you had it in you, `5%W`6,`5\" %w exclaims.','Bluspring');
INSERT INTO taunts VALUES (0,'`5%W was overheard saying, \"`6%p `4%x`6 was no match for my `4%X`6!`5\"','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6You know, you really shouldn\'t have a `4%x`6 unless you know how to use it,`5\" suggested %W.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6`bARRRGGGGGGG`b!!`5\" %w screams in frustration.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6How could I be so feeble?`5\" %w laments.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6I must not be as sturdy as I thought...!`5\" %w concedes.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Watch your back, `4%W`6 I am coming for you!`5\" %w warns.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6This both sucks and blows!`5\" wails %w.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6I see London, I see France, I see `4%w\'s`6, underpants!`5\" reveals %W.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6The Healer\'s Hut can\'t help you now, `4%w`6!,`5\" chides %W.','Bluspring');
INSERT INTO taunts VALUES (0,'`5%W smiles.  \"`6You are too slow.  You are too weak.`5\"','Bluspring');
INSERT INTO taunts VALUES (0,'`5%w bangs %p head against a stone...\"`6Stupid, stupid, stupid!`5\" %o was heard to say.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6My ego can\'t take much more of this bruising!`5\" exclaims %w.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Why didn\'t I become a successful doctor like my father suggested?`5\" wonders %w aloud.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Maybe `bnext`b time you won\'t be so cocky!`5\" laughs %W','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6A baby could wield a `4%x `6better than that!`5\" %W proclaims.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6You should have just stayed in bed.`5\" %W suggests.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Well isn\'t that a kick in the crotch?!`5\" %w observes.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Come back when you learn how to fight.`5\" %W scoffs.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6Next time, eat your Wheaties.`5\" %W suggests.','Bluspring');
INSERT INTO taunts VALUES (0,'`5 \"`6You are dishonorable, `4%W`6!`5\" %w cries.','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`4%w`6, your lack of posture is a disgrace.`5\" %W states. ','Bluspring');
INSERT INTO taunts VALUES (0,'`5\"`6You know, `4%w`6 really had it coming to %s after all those things `bI`b said about `b%p`b mom`5,\" commented %W.','Joe');

#
# Dumping data for table 'weapons'
# WHERE:  level=0
#

INSERT INTO weapons VALUES (0,'Rake',48,1,0);
INSERT INTO weapons VALUES (0,'Trowel',225,2,0);
INSERT INTO weapons VALUES (0,'Spade',585,3,0);
INSERT INTO weapons VALUES (0,'Adze',990,4,0);
INSERT INTO weapons VALUES (0,'Gardening Hoe',1575,5,0);
INSERT INTO weapons VALUES (0,'Torch',2250,6,0);
INSERT INTO weapons VALUES (0,'Pitchfork',2790,7,0);
INSERT INTO weapons VALUES (0,'Shovel',3420,8,0);
INSERT INTO weapons VALUES (0,'Hedge Trimmers',4230,9,0);
INSERT INTO weapons VALUES (0,'Hatchet',5040,10,0);
INSERT INTO weapons VALUES (0,'Carving Knife',5850,11,0);
INSERT INTO weapons VALUES (0,'Rusty Iron Wood-Chopping Axe',6840,12,0);
INSERT INTO weapons VALUES (0,'Dull Steel Wood-chopping Axe',8010,13,0);
INSERT INTO weapons VALUES (0,'Sharp Steel Wood-chopping Axe',9000,14,0);
INSERT INTO weapons VALUES (0,'Woodsman\'s Axe',10350,15,0);

#
# Dumping data for table 'armor'
# WHERE:  level=0
#

INSERT INTO armor VALUES (0,'Fuzzy Slippers',48,1,0);
INSERT INTO armor VALUES (0,'Flannel Pajamas',225,2,0);
INSERT INTO armor VALUES (0,'Homespun Longjohns',585,3,0);
INSERT INTO armor VALUES (0,'Homespun Undershirt',990,4,0);
INSERT INTO armor VALUES (0,'Knitted Socks',1575,5,0);
INSERT INTO armor VALUES (0,'Knitted Gloves',2250,6,0);
INSERT INTO armor VALUES (0,'Old Leather Boots',2790,7,0);
INSERT INTO armor VALUES (0,'Homespun Pants',3420,8,0);
INSERT INTO armor VALUES (0,'Homespun Tunic',4230,9,0);
INSERT INTO armor VALUES (0,'Gypsy Cape',5040,10,0);
INSERT INTO armor VALUES (0,'Old Leather Cap',5850,11,0);
INSERT INTO armor VALUES (0,'Old Leather Bracers',6840,12,0);
INSERT INTO armor VALUES (0,'Traveler\'s Shield',8010,13,0);
INSERT INTO armor VALUES (0,'Old Leather Pants',9000,14,0);
INSERT INTO armor VALUES (0,'Old Leather Tunic',10350,15,0);

#
# Dumping data for table 'weapons'
# WHERE:  level=1
#

INSERT INTO weapons VALUES (0,'Pebbles',48,1,1);
INSERT INTO weapons VALUES (0,'Stones',225,2,1);
INSERT INTO weapons VALUES (0,'Rocks',585,3,1);
INSERT INTO weapons VALUES (0,'Small Treebranch',990,4,1);
INSERT INTO weapons VALUES (0,'Large Treebranch',1575,5,1);
INSERT INTO weapons VALUES (0,'Thickly Padded Sparring Pole',2250,6,1);
INSERT INTO weapons VALUES (0,'Thinly Padded Sparring Pole',2790,7,1);
INSERT INTO weapons VALUES (0,'Wooden Stave',3420,8,1);
INSERT INTO weapons VALUES (0,'Wooden Practice Sword',4230,9,1);
INSERT INTO weapons VALUES (0,'Blunt Bronze Short Sword',5040,10,1);
INSERT INTO weapons VALUES (0,'Well Crafted Bronze Short Sword',5850,11,1);
INSERT INTO weapons VALUES (0,'Rusty Steel Short Sword',6840,12,1);
INSERT INTO weapons VALUES (0,'Dull Steel Short Sword',8010,13,1);
INSERT INTO weapons VALUES (0,'Sharp Steel Short Sword',9000,14,1);
INSERT INTO weapons VALUES (0,'Pages\'s Short Sword',10350,15,1);

#
# Dumping data for table 'armor'
# WHERE:  level=1
#

INSERT INTO armor VALUES (0,'Flip-Flops',48,1,1);
INSERT INTO armor VALUES (0,'Swimsuit and Towel',225,2,1);
INSERT INTO armor VALUES (0,'Cotton Undershirt',585,3,1);
INSERT INTO armor VALUES (0,'Wool Socks',990,4,1);
INSERT INTO armor VALUES (0,'Wool Gloves',1575,5,1);
INSERT INTO armor VALUES (0,'Leather Boots',2250,6,1);
INSERT INTO armor VALUES (0,'Leather Cap',2790,7,1);
INSERT INTO armor VALUES (0,'Leather Bracers',3420,8,1);
INSERT INTO armor VALUES (0,'Leather Leggings',4230,9,1);
INSERT INTO armor VALUES (0,'Leather Tunic',5040,10,1);
INSERT INTO armor VALUES (0,'Hooded Leather Cape',5850,11,1);
INSERT INTO armor VALUES (0,'Deerskin Leggings',6840,12,1);
INSERT INTO armor VALUES (0,'Deerskin Belt',8010,13,1);
INSERT INTO armor VALUES (0,'Deerskin Tunic',9000,14,1);
INSERT INTO armor VALUES (0,'Small Rawhide Shield',10350,15,1);

#
# Dumping data for table 'weapons'
# WHERE:  level=2
#

INSERT INTO weapons VALUES (0,'Dull Bronze Sword',48,1,2);
INSERT INTO weapons VALUES (0,'Bronze Sword',225,2,2);
INSERT INTO weapons VALUES (0,'Well Crafted Bronze Sword',585,3,2);
INSERT INTO weapons VALUES (0,'Dull Iron Sword',990,4,2);
INSERT INTO weapons VALUES (0,'Iron Sword',1575,5,2);
INSERT INTO weapons VALUES (0,'Enchanted Sword',9000,14,2);
INSERT INTO weapons VALUES (0,'Well Crafted Iron Sword',2250,6,2);
INSERT INTO weapons VALUES (0,'Rusty Steel Sword',2790,7,2);
INSERT INTO weapons VALUES (0,'Dull Steel Sword',3420,8,2);
INSERT INTO weapons VALUES (0,'Well Crafted Steel Sword',4230,9,2);
INSERT INTO weapons VALUES (0,'Engraved Steel Sword',5040,10,2);
INSERT INTO weapons VALUES (0,'Steel Sword with Jeweled Hilt',5850,11,2);
INSERT INTO weapons VALUES (0,'Golden Hilted Sword',6840,12,2);
INSERT INTO weapons VALUES (0,'Platinum Hilted Sword',8010,13,2);
INSERT INTO weapons VALUES (0,'Adept\'s Sword',10350,15,2);

#
# Dumping data for table 'armor'
# WHERE:  level=2
#

INSERT INTO armor VALUES (0,'Workboots',48,1,2);
INSERT INTO armor VALUES (0,'Overalls',225,2,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Gloves',585,3,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Bracers',990,4,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Boots',1575,5,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Helm',2250,6,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Pants',2790,7,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Tunic',3420,8,2);
INSERT INTO armor VALUES (0,'Sturdy Leather Cloak',4230,9,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Helm',5040,10,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Gauntlets',5850,11,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Bracers',6840,12,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Greaves',8010,13,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Tunic',9000,14,2);
INSERT INTO armor VALUES (0,'Woodsman\'s Kite Shield',10350,15,2);

#
# Dumping data for table 'weapons'
# WHERE:  level=3
#

INSERT INTO weapons VALUES (0,'Steel Longsword',48,1,3);
INSERT INTO weapons VALUES (0,'Etched Steel Longsword',585,3,3);
INSERT INTO weapons VALUES (0,'Polished Steel Longsword',225,2,3);
INSERT INTO weapons VALUES (0,'Well Balanced Steel Longsword',990,4,3);
INSERT INTO weapons VALUES (0,'Perfectly Balanced Steel Longsword',1575,5,3);
INSERT INTO weapons VALUES (0,'Engraved Steel Longsword',2250,6,3);
INSERT INTO weapons VALUES (0,'Longsword with Silver-plated Hilt',2790,7,3);
INSERT INTO weapons VALUES (0,'Longsword with Gold-plated Hilt',3420,8,3);
INSERT INTO weapons VALUES (0,'Longsword with Solid Gold Hilt',4230,9,3);
INSERT INTO weapons VALUES (0,'Longsword with Solid Platinum Hilt',5040,10,3);
INSERT INTO weapons VALUES (0,'Moonsilver Longsword',5850,11,3);
INSERT INTO weapons VALUES (0,'Autumngold Longsword',6840,12,3);
INSERT INTO weapons VALUES (0,'Elfsilver Longsword',8010,13,3);
INSERT INTO weapons VALUES (0,'Enchanted Longsword',9000,14,3);
INSERT INTO weapons VALUES (0,'Wolfmaster\'s Longsword',10350,15,3);

#
# Dumping data for table 'armor'
# WHERE:  level=3
#

INSERT INTO armor VALUES (0,'Showercap and Towel',48,1,3);
INSERT INTO armor VALUES (0,'Bathrobe',225,2,3);
INSERT INTO armor VALUES (0,'Wolfskin Gloves',585,3,3);
INSERT INTO armor VALUES (0,'Wolfskin-lined Boots',990,4,3);
INSERT INTO armor VALUES (0,'Wolfskin Bracers',1575,5,3);
INSERT INTO armor VALUES (0,'Wolfskin Pants',2250,6,3);
INSERT INTO armor VALUES (0,'Wolfskin Tunic',2790,7,3);
INSERT INTO armor VALUES (0,'Hooded Wolfskin Cape',3420,8,3);
INSERT INTO armor VALUES (0,'Wolfmaster\'s Bracers',4230,9,3);
INSERT INTO armor VALUES (0,'Wolfmaster\'s Gauntlets',5040,10,3);
INSERT INTO armor VALUES (0,'Wolfmasters Helm',5850,11,3);
INSERT INTO armor VALUES (0,'Wolfmaster\'s Leggings',6840,12,3);
INSERT INTO armor VALUES (0,'Wolfmaster\'s Belted Jerkin',8010,13,3);
INSERT INTO armor VALUES (0,'Wolfhide Cape',9000,14,3);
INSERT INTO armor VALUES (0,'Shield of the Wolf Master',10350,15,3);

#
# Dumping data for table 'weapons'
# WHERE:  level=4
#

INSERT INTO weapons VALUES (0,'Poorly Balanced Bastard Sword',48,1,4);
INSERT INTO weapons VALUES (0,'Tarnished Bastard Sword',225,2,4);
INSERT INTO weapons VALUES (0,'Iron Bastard Sword',585,3,4);
INSERT INTO weapons VALUES (0,'Steel Bastard Sword',990,4,4);
INSERT INTO weapons VALUES (0,'Well Balanced Steel Bastard Sword',1575,5,4);
INSERT INTO weapons VALUES (0,'Perfectly Balanced Bastard Sword',2250,6,4);
INSERT INTO weapons VALUES (0,'Rune-etched Bastard Sword',2790,7,4);
INSERT INTO weapons VALUES (0,'Bronze-inlay Bastard Sword',3420,8,4);
INSERT INTO weapons VALUES (0,'Silver-inlay Bastard Sword',4230,9,4);
INSERT INTO weapons VALUES (0,'Gold-inlay Bastard Sword',5040,10,4);
INSERT INTO weapons VALUES (0,'Nightsilver Bastard Sword',5850,11,4);
INSERT INTO weapons VALUES (0,'Morning-gold Bastard Sword',6840,12,4);
INSERT INTO weapons VALUES (0,'Truesplendor Bastard Sword',8010,13,4);
INSERT INTO weapons VALUES (0,'Enchanted Elfgold Bastard Sword',9000,14,4);
INSERT INTO weapons VALUES (0,'Noble\'s Bastard Sword',10350,15,4);

#
# Dumping data for table 'armor'
# WHERE:  level=4
#

INSERT INTO armor VALUES (0,'Sweat Pants',48,1,4);
INSERT INTO armor VALUES (0,'Sweat Shirt',225,2,4);
INSERT INTO armor VALUES (0,'Studded Leather Helm',585,3,4);
INSERT INTO armor VALUES (0,'Studded Leather Gauntlets',990,4,4);
INSERT INTO armor VALUES (0,'Hardened Leather Boots',1575,5,4);
INSERT INTO armor VALUES (0,'Studded Leather Leggings',2250,6,4);
INSERT INTO armor VALUES (0,'Studded Leather Tunic',2790,7,4);
INSERT INTO armor VALUES (0,'Tanner\'s Cape',3420,8,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Helm',4230,9,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Gauntlets',5040,10,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Bracers',5850,11,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Boots',6840,12,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Greaves',8010,13,4);
INSERT INTO armor VALUES (0,'Rusty Chainmail Tunic',9000,14,4);
INSERT INTO armor VALUES (0,'Large Iron Buckler',10350,15,4);

#
# Dumping data for table 'weapons'
# WHERE:  level=5
#

INSERT INTO weapons VALUES (0,'Tarnished Iron Claymore',48,1,5);
INSERT INTO weapons VALUES (0,'Polished Iron Claymore',225,2,5);
INSERT INTO weapons VALUES (0,'Rusty Steel Claymore',585,3,5);
INSERT INTO weapons VALUES (0,'Steel Claymore',990,4,5);
INSERT INTO weapons VALUES (0,'Finely Crafted Steel Claymore',1575,5,5);
INSERT INTO weapons VALUES (0,'Scottish Broadsword',2250,6,5);
INSERT INTO weapons VALUES (0,'Viking War Sword',2790,7,5);
INSERT INTO weapons VALUES (0,'Barbarian\'s Sword',3420,8,5);
INSERT INTO weapons VALUES (0,'Scottish Basket-Hilt Claymore',4230,9,5);
INSERT INTO weapons VALUES (0,'Agincourt Steel Sword',5040,10,5);
INSERT INTO weapons VALUES (0,'Celtic Combat Sword',5850,11,5);
INSERT INTO weapons VALUES (0,'Norseman\'s Sword',6840,12,5);
INSERT INTO weapons VALUES (0,'Knight\'s Sword',8010,13,5);
INSERT INTO weapons VALUES (0,'Heraldic Lion Claymore',9000,14,5);
INSERT INTO weapons VALUES (0,'Dragon Soldier\'s Claymore',10350,15,5);

#
# Dumping data for table 'armor'
# WHERE:  level=5
#

INSERT INTO armor VALUES (0,'Bunny Slippers',48,1,5);
INSERT INTO armor VALUES (0,'Feety Pajamas',225,2,5);
INSERT INTO armor VALUES (0,'Comfortable Leather Undergarments',585,3,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Helm',990,4,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Gauntlets',1575,5,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Bracers',2250,6,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Boots',2790,7,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Greaves',3420,8,5);
INSERT INTO armor VALUES (0,'Heavy Chainmail Tunic',4230,9,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Bracers',5040,10,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Gauntlets',5850,11,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Boots',6840,12,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Greaves',8010,13,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Chestplate',9000,14,5);
INSERT INTO armor VALUES (0,'Dragon Soldier\'s Shield',10350,15,5);

#
# Dumping data for table 'weapons'
# WHERE:  level=6
#

INSERT INTO weapons VALUES (0,'Two Broken Short Swords',48,1,6);
INSERT INTO weapons VALUES (0,'Two Short Swords',225,2,6);
INSERT INTO weapons VALUES (0,'Iron Scimitars',585,3,6);
INSERT INTO weapons VALUES (0,'Balanced Scimitars',990,4,6);
INSERT INTO weapons VALUES (0,'Tarnished Steel Scimitars',1575,5,6);
INSERT INTO weapons VALUES (0,'Rusty Steel Scimitars',2250,6,6);
INSERT INTO weapons VALUES (0,'Steel Scimitars',2790,7,6);
INSERT INTO weapons VALUES (0,'Bronze Hilted Steel Scimitars',3420,8,6);
INSERT INTO weapons VALUES (0,'Gold Hilted Steel Scimitars',4230,9,6);
INSERT INTO weapons VALUES (0,'Platinum Hilted Steel Scimitars',5040,10,6);
INSERT INTO weapons VALUES (0,'Well Crafted Adamantite Scimitars',5850,11,6);
INSERT INTO weapons VALUES (0,'Perfectly Crafted Adamantite Scimitars',6840,12,6);
INSERT INTO weapons VALUES (0,'Enchanted Scimitars',8010,13,6);
INSERT INTO weapons VALUES (0,'Drow Crafted Scimitars',9000,14,6);
INSERT INTO weapons VALUES (0,'Unicorn Blood-Forged Scimitars',10350,15,6);

#
# Dumping data for table 'armor'
# WHERE:  level=6
#

INSERT INTO armor VALUES (0,'Bluejeans',48,1,6);
INSERT INTO armor VALUES (0,'Flannel Shirt',225,2,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Helm',585,3,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Gauntlets',990,4,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Bracers',1575,5,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Boots',2250,6,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Greaves',2790,7,6);
INSERT INTO armor VALUES (0,'Well Crafted Bronze Chestplate',3420,8,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Helm',4230,9,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Gauntlets',5040,10,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Bracers',5850,11,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Boots',6840,12,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Greaves',8010,13,6);
INSERT INTO armor VALUES (0,'Enchanted Bronze Chestplate',9000,14,6);
INSERT INTO armor VALUES (0,'Hooded Unicorn Skin Cloak',10350,15,6);

#
# Dumping data for table 'weapons'
# WHERE:  level=7
#

INSERT INTO weapons VALUES (0,'Chipped Iron Axe',48,1,7);
INSERT INTO weapons VALUES (0,'Iron Axe',225,2,7);
INSERT INTO weapons VALUES (0,'Rusty Steel Axe',585,3,7);
INSERT INTO weapons VALUES (0,'Fine Steel Axe',990,4,7);
INSERT INTO weapons VALUES (0,'Lumberjack\'s Axe',1575,5,7);
INSERT INTO weapons VALUES (0,'Low Quality Battle Axe',2250,6,7);
INSERT INTO weapons VALUES (0,'Medium Quality Battle Axe',2790,7,7);
INSERT INTO weapons VALUES (0,'High Quality Battle Axe',3420,8,7);
INSERT INTO weapons VALUES (0,'Double Bladed Axe',4230,9,7);
INSERT INTO weapons VALUES (0,'Double Bladed Battle Axe',5040,10,7);
INSERT INTO weapons VALUES (0,'Gold Plated Battle Axe',5850,11,7);
INSERT INTO weapons VALUES (0,'Platinum Hilted Battle Axe',6840,12,7);
INSERT INTO weapons VALUES (0,'Enchanted Battle Axe',8010,13,7);
INSERT INTO weapons VALUES (0,'Dwarf Smith\'s Battle Axe',9000,14,7);
INSERT INTO weapons VALUES (0,'Dwarf Warrior\'s Battle Axe',10350,15,7);

#
# Dumping data for table 'armor'
# WHERE:  level=7
#

INSERT INTO armor VALUES (0,'Barrel',48,1,7);
INSERT INTO armor VALUES (0,'Lampshade',225,2,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Helm',585,3,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Gauntlets',990,4,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Boots',1575,5,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Bracers',2250,6,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Greaves',2790,7,7);
INSERT INTO armor VALUES (0,'Perfectly Crafted Steel Chestplate',3420,8,7);
INSERT INTO armor VALUES (0,'Griffon-Feather Cloak',4230,9,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Helm',5040,10,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Gauntlets',5850,11,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Boots',6840,12,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Bracers',8010,13,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Greaves',9000,14,7);
INSERT INTO armor VALUES (0,'Dwarven Chainmail Chestplate',10350,15,7);

#
# Dumping data for table 'weapons'
# WHERE:  level=8
#

INSERT INTO weapons VALUES (0,'Broken Iron Mace',48,1,8);
INSERT INTO weapons VALUES (0,'Tarnished Iron Mace',225,2,8);
INSERT INTO weapons VALUES (0,'Polished Iron Mace',585,3,8);
INSERT INTO weapons VALUES (0,'Well Crafted Iron Mace',990,4,8);
INSERT INTO weapons VALUES (0,'Polished Steel Mace',1575,5,8);
INSERT INTO weapons VALUES (0,'Well Crafted Steel Mace',2250,6,8);
INSERT INTO weapons VALUES (0,'Poorly Balanced Double Mace',2790,7,8);
INSERT INTO weapons VALUES (0,'Well Balanced Double Mace',3420,8,8);
INSERT INTO weapons VALUES (0,'Battle Mace',4230,9,8);
INSERT INTO weapons VALUES (0,'War Chieftan\'s Battle Mace',5040,10,8);
INSERT INTO weapons VALUES (0,'War Chieftan\'s Morning Star',5850,11,8);
INSERT INTO weapons VALUES (0,'Adamantite Morning Star',6840,12,8);
INSERT INTO weapons VALUES (0,'Dwarf Crafted Morning Star',8010,13,8);
INSERT INTO weapons VALUES (0,'Dwarf Warlord\'s Morning Star',9000,14,8);
INSERT INTO weapons VALUES (0,'Enchanted Morning Star',10350,15,8);

#
# Dumping data for table 'armor'
# WHERE:  level=8
#

INSERT INTO armor VALUES (0,'Fig Leaf',48,1,8);
INSERT INTO armor VALUES (0,'Kilt',225,2,8);
INSERT INTO armor VALUES (0,'Majestic Gold Helm',585,3,8);
INSERT INTO armor VALUES (0,'Majestic Gold Gauntlets',990,4,8);
INSERT INTO armor VALUES (0,'Majestic Gold Boots',1575,5,8);
INSERT INTO armor VALUES (0,'Bracers',2250,6,8);
INSERT INTO armor VALUES (0,'Majestic Gold Greaves',2790,7,8);
INSERT INTO armor VALUES (0,'Majestic Gold Chestplate',3420,8,8);
INSERT INTO armor VALUES (0,'Majestic Gold Shield',4230,9,8);
INSERT INTO armor VALUES (0,'Gold-Threaded Cloak',5040,10,8);
INSERT INTO armor VALUES (0,'Enchanted Ruby Ring',5850,11,8);
INSERT INTO armor VALUES (0,'Enchanted Sapphire Ring',6840,12,8);
INSERT INTO armor VALUES (0,'Enchanted Jade Ring',8010,13,8);
INSERT INTO armor VALUES (0,'Enchanted Amethyst Ring',9000,14,8);
INSERT INTO armor VALUES (0,'Enchanted Diamond Ring',10350,15,8);

#
# Dumping data for table 'weapons'
# WHERE:  level=9
#

INSERT INTO weapons VALUES (0,'Boot Knife',48,1,9);
INSERT INTO weapons VALUES (0,'Target Knife',225,2,9);
INSERT INTO weapons VALUES (0,'Blackjack',585,3,9);
INSERT INTO weapons VALUES (0,'Throwing Star',990,4,9);
INSERT INTO weapons VALUES (0,'Hira-Shuriken',1575,5,9);
INSERT INTO weapons VALUES (0,'Throwing Spike',2250,6,9);
INSERT INTO weapons VALUES (0,'Atlatl',2790,7,9);
INSERT INTO weapons VALUES (0,'Qilamitautit Bolo',3420,8,9);
INSERT INTO weapons VALUES (0,'War Quoait',4230,9,9);
INSERT INTO weapons VALUES (0,'Cha Kran',5040,10,9);
INSERT INTO weapons VALUES (0,'Fei Piau',5850,11,9);
INSERT INTO weapons VALUES (0,'Jen Piau',6840,12,9);
INSERT INTO weapons VALUES (0,'Gau dim Piau',8010,13,9);
INSERT INTO weapons VALUES (0,'Enchanted Throwing Axe',9000,14,9);
INSERT INTO weapons VALUES (0,'Teksolo\'s Ninja Stars',10350,15,9);

#
# Dumping data for table 'armor'
# WHERE:  level=9
#

INSERT INTO armor VALUES (0,'Button',48,1,9);
INSERT INTO armor VALUES (0,'Elven Silk Nightclothes',225,2,9);
INSERT INTO armor VALUES (0,'Elven Silk Gloves',585,3,9);
INSERT INTO armor VALUES (0,'Elven Silk Slippers',990,4,9);
INSERT INTO armor VALUES (0,'Elven Silk Wristband',1575,5,9);
INSERT INTO armor VALUES (0,'Leggings',2250,6,9);
INSERT INTO armor VALUES (0,'Elven Silk Tunic',2790,7,9);
INSERT INTO armor VALUES (0,'Elven Silk Cloak',3420,8,9);
INSERT INTO armor VALUES (0,'Ring of Night',4230,9,9);
INSERT INTO armor VALUES (0,'Ring of Day',5040,10,9);
INSERT INTO armor VALUES (0,'Ring of Solitude',5850,11,9);
INSERT INTO armor VALUES (0,'Ring of Peace',6840,12,9);
INSERT INTO armor VALUES (0,'Ring of Courage',8010,13,9);
INSERT INTO armor VALUES (0,'Ring of Virtue',9000,14,9);
INSERT INTO armor VALUES (0,'Ring of Life',10350,15,9);

#
# Dumping data for table 'weapons'
# WHERE:  level=10
#

INSERT INTO weapons VALUES (0,'Farmer\'s Bow & Wooden Arrows',48,1,10);
INSERT INTO weapons VALUES (0,'Farmer\'s Bow & Stone Tipped Arrows',225,2,10);
INSERT INTO weapons VALUES (0,'Farmer\'s Bow & Steel Tipped Arrows',585,3,10);
INSERT INTO weapons VALUES (0,'Hunter\'s Bow & Wooden Arrows',990,4,10);
INSERT INTO weapons VALUES (0,'Hunter\'s Bow & Stone Tipped Arrows',1575,5,10);
INSERT INTO weapons VALUES (0,'Hunter\'s Bow & Steel Tipped Arrows',2250,6,10);
INSERT INTO weapons VALUES (0,'Ranger\'s Bow & Wooden Arrows',2790,7,10);
INSERT INTO weapons VALUES (0,'Ranger\'s Bow & Stone Tipped Arrows',3420,8,10);
INSERT INTO weapons VALUES (0,'Ranger\'s Bow & Steel Tipped Arrows',4230,9,10);
INSERT INTO weapons VALUES (0,'Longbow',5040,10,10);
INSERT INTO weapons VALUES (0,'Crossbow',5850,11,10);
INSERT INTO weapons VALUES (0,'Elvish Longbow',6840,12,10);
INSERT INTO weapons VALUES (0,'Elvish Longbow & Flame Tipped Arrows',8010,13,10);
INSERT INTO weapons VALUES (0,'Elvish Longbow & Enchanted Arrows',9000,14,10);
INSERT INTO weapons VALUES (0,'Longbow of the Elf King',10350,15,10);

#
# Dumping data for table 'armor'
# WHERE:  level=10
#

INSERT INTO armor VALUES (0,'Pegasus\' Hooded Cloak',5040,10,10);
INSERT INTO armor VALUES (0,'Pegasus\' Chestplate',4230,9,10);
INSERT INTO armor VALUES (0,'Pegasus\' Greaves',3420,8,10);
INSERT INTO armor VALUES (0,'Pegasus\' Boots',2790,7,10);
INSERT INTO armor VALUES (0,'Pegasus\' Gorget',2250,6,10);
INSERT INTO armor VALUES (0,'Pegasus\' Bracers',1575,5,10);
INSERT INTO armor VALUES (0,'Pegasus\' Gauntlets',990,4,10);
INSERT INTO armor VALUES (0,'Pegasus\' Helm',585,3,10);
INSERT INTO armor VALUES (0,'Platform Shoes',225,2,10);
INSERT INTO armor VALUES (0,'Leisure Suit',48,1,10);
INSERT INTO armor VALUES (0,'Pegasus Feather Pendant',5850,11,10);
INSERT INTO armor VALUES (0,'Pegasus Feather Belt',6840,12,10);
INSERT INTO armor VALUES (0,'Pegasus\' Emblazoned Shield',8010,13,10);
INSERT INTO armor VALUES (0,'Pegasus\' Emblazoned Ring',9000,14,10);
INSERT INTO armor VALUES (0,'Pegasus\' Emblazoned Crown',10350,15,10);

#
# Dumping data for table 'weapons'
# WHERE:  level=11
#

INSERT INTO weapons VALUES (0,'MightyE\'s Long Sword',225,2,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Short Sword',48,1,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Bastard Sword',585,3,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Scimitars',990,4,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Battle Axe',1575,5,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Throwing Hammer',2250,6,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Morning Star',2790,7,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Compound Bow',3420,8,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Rapier',4230,9,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Sabre',5040,10,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Light Sabre',5850,11,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Wakizashi',6840,12,11);
INSERT INTO weapons VALUES (0,'MightyE\'s 2-Handed War Sword',8010,13,11);
INSERT INTO weapons VALUES (0,'MightyE\'s 2-handed War Axe',9000,14,11);
INSERT INTO weapons VALUES (0,'MightyE\'s Claymore',10350,15,11);

#
# Dumping data for table 'armor'
# WHERE:  level=11
#

INSERT INTO armor VALUES (0,'New Clothes',48,1,11);
INSERT INTO armor VALUES (0,'Chicken Suit',225,2,11);
INSERT INTO armor VALUES (0,'Gauntlets of Grace',585,3,11);
INSERT INTO armor VALUES (0,'Bracer of Beauty',990,4,11);
INSERT INTO armor VALUES (0,'Helm of Health',1575,5,11);
INSERT INTO armor VALUES (0,'Greaves of Good Fortune',2250,6,11);
INSERT INTO armor VALUES (0,'Boots of Bravery',2790,7,11);
INSERT INTO armor VALUES (0,'Tunic of Tolerance',3420,8,11);
INSERT INTO armor VALUES (0,'Cloak of Confidence',4230,9,11);
INSERT INTO armor VALUES (0,'Ring of Righteousness',5040,10,11);
INSERT INTO armor VALUES (0,'Necklace of Narcissism',5850,11,11);
INSERT INTO armor VALUES (0,'Pendant of Power',6840,12,11);
INSERT INTO armor VALUES (0,'Breastplate of Benevolence',8010,13,11);
INSERT INTO armor VALUES (0,'Shield of Superiority',9000,14,11);
INSERT INTO armor VALUES (0,'Scepter of Strength',10350,15,11);

#
# Dumping data for table 'weapons'
# WHERE:  level=12
#

INSERT INTO weapons VALUES (0,'Spell of Fire',48,1,12);
INSERT INTO weapons VALUES (0,'Spell of Earthquake',225,2,12);
INSERT INTO weapons VALUES (0,'Spell of Flood',585,3,12);
INSERT INTO weapons VALUES (0,'Spell of Hurricane',990,4,12);
INSERT INTO weapons VALUES (0,'Spell of Mind Control',1575,5,12);
INSERT INTO weapons VALUES (0,'Spell of Lightning',2250,6,12);
INSERT INTO weapons VALUES (0,'Spell of Weakness',2790,7,12);
INSERT INTO weapons VALUES (0,'Spell of Fear',3420,8,12);
INSERT INTO weapons VALUES (0,'Spell of Poison',4230,9,12);
INSERT INTO weapons VALUES (0,'Spell of Spirit Possession',5040,10,12);
INSERT INTO weapons VALUES (0,'Spell of Despair',5850,11,12);
INSERT INTO weapons VALUES (0,'Spell of Bat Summoning',6840,12,12);
INSERT INTO weapons VALUES (0,'Spell of Wolf Summoning',8010,13,12);
INSERT INTO weapons VALUES (0,'Spell of Unicorn Summoning',9000,14,12);
INSERT INTO weapons VALUES (0,'Spell of Dragon Summoning',10350,15,12);

#
# Dumping data for table 'armor'
# WHERE:  level=12
#

INSERT INTO armor VALUES (0,'Dragon Skin Leather Helm',48,1,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Gauntlets',225,2,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Boots',585,3,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Bracers',990,4,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Leggings',1575,5,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Tunic',2250,6,12);
INSERT INTO armor VALUES (0,'Dragon Skin Leather Cloak',2790,7,12);
INSERT INTO armor VALUES (0,'Dragon Scale Helm',3420,8,12);
INSERT INTO armor VALUES (0,'Dragon Scale Gauntlets',4230,9,12);
INSERT INTO armor VALUES (0,'Dragon Scale Boots',5040,10,12);
INSERT INTO armor VALUES (0,'Dragon Scale Bracers',5850,11,12);
INSERT INTO armor VALUES (0,'Dragon Scale Greaves',6840,12,12);
INSERT INTO armor VALUES (0,'Dragon Scale Chestplate',8010,13,12);
INSERT INTO armor VALUES (0,'Dragon Scale Cloak',9000,14,12);
INSERT INTO armor VALUES (0,'Dragon Talon Shield',10350,15,12);

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

ALTER TABLE accounts ADD pvpflag datetime not null;
ALTER TABLE accounts ADD transferredtoday int(11) unsigned not null default 0;
ALTER TABLE news ADD accountid int(11) unsigned not null default 0;
ALTER TABLE settings CHANGE value value varchar(255) not null;

CREATE TABLE logdnet (
	serverid int(11) unsigned not null auto_increment,
	address varchar(255) not null,
	description varchar(255) not null,
	priority double not null default 100,
	lastupdate datetime not null default '0000-00-00 00:00:00',
	PRIMARY KEY (serverid)
);

CREATE TABLE petitions (
	petitionid int(11) unsigned not null auto_increment,
	author int(11) unsigned not null default 0,
	date datetime not null,
	status int(11) unsigned not null default 0,
	body text,
	pageinfo text,
	PRIMARY KEY (petitionid)
) Type=INNODB;

UPDATE accounts SET hashorse=2 WHERE hashorse=1;

#-- These riddles were taken from the NetBook of Riddles, compiled by
#-- Mark Manning.  He deserves all credit.
#-- The NetBook used was dated 10/27/1999, and had no license or
#-- copyright information.
#-- I've just imported the ones with answers into the database, and
#-- modified a few of the answers slightly to work better in this format.
#-- (Joe Naylor)

#--
#-- Table structure for table 'riddles'
#--

CREATE TABLE riddles (
  id int(11) NOT NULL auto_increment,
  riddle mediumtext NOT NULL,
  answer tinytext NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#--
#-- Dumping data for table 'riddles'
#--

INSERT INTO riddles VALUES (1,'You eat something you neither plant nor plow.`nIt is the son of water, but if water touches it, it dies.','Salt');
INSERT INTO riddles VALUES (2,'Teacher, open thy book.','A butterfly');
INSERT INTO riddles VALUES (3,'My tines are long.`nMy tines are short.`nMy tines end ere`nMy first report.','Lightning; lightning bolt');
INSERT INTO riddles VALUES (4,'Turn us on our backs`nAnd open up our stomachs`nYou will be the wisest of men`nThough at start a lummox.','A book');
INSERT INTO riddles VALUES (5,'Bury deep,`nPile on stones,`nYet I will`nDig up the bones.','Memories');
INSERT INTO riddles VALUES (6,'It occurs once in every minute`nTwice in every moment`nAnd yet never in one hundred thousand years.','The letter \'M\'; M');
INSERT INTO riddles VALUES (7,'Never ahead, ever behind,`nYet flying swiftly past;`nFor a child I last forever,`nFor adults I\'m gone too fast.','Childhood;Youth');
INSERT INTO riddles VALUES (8,'Two horses, swiftest travelling,`nHarnessed in a pair, and`nGrazing ever in places`nDistant from them.','Your eyes; the sun and the moon');
INSERT INTO riddles VALUES (9,'It can be said:`nTo be gold is to be good;`nTo be stone is to be nothing;`nTo be glass is to be fragile;`nTo be cold is to be cruel.`nUnmetaphored, what am I?','A heart');
INSERT INTO riddles VALUES (10,'Round she is, yet flat as a board`nAltar of the Lupine Lords.`nJewel on black velvet, pearl in the sea`nUnchanged but e\'erchanging, eternally.','The moon');
INSERT INTO riddles VALUES (11,'It has a golden head`nIt has a golden tail`nbut it hasn\'t got a body.','A golden coin; coin');
INSERT INTO riddles VALUES (12,'Speak, friend, and enter!','Friend');
INSERT INTO riddles VALUES (13,'A leathery snake,`nWith a stinging bite,`nI\'ll stay coiled up,`nUnless I must fight.','A whip');
INSERT INTO riddles VALUES (14,'What has roots as nobody sees,`nIs taller than trees,`nUp, up it goes,`nAnd yet never grows?','A mountain');
INSERT INTO riddles VALUES (15,'Thirty white horses on a red hill,`nFirst they champ,`nThen they stamp,`nThen they stand still.','Your teeth');
INSERT INTO riddles VALUES (16,'Voiceless it cries,`nWingless it flutters,`nToothless bites,`nMouthless mutters.','The wind');
INSERT INTO riddles VALUES (17,'It cannot be seen, cannot be felt,`nCannot be heard, cannot be smelt.`nIt lies behind stars and under hills,`nAnd empty holes it fills.`nIt comes first and follows after,`nEnds life, kills laughter.','The darkness');
INSERT INTO riddles VALUES (18,'A box without hinges, key, or lid,`nYet golden treasure inside is hid.','Eggs; oranges');
INSERT INTO riddles VALUES (19,'Alive without breath,`nAs cold as death;`nNever thirsty, ever drinking,`nAll in mail never clinking.','A fish');
INSERT INTO riddles VALUES (20,'This thing all things devours:`nBirds, beast, trees, flowers;`nGnaws iron, bites steel;`nGrinds hard stones to meal;`nSlays king, ruins town,`nAnd beats high mountain down.','Time');
INSERT INTO riddles VALUES (21,'You feel it, but never see it and never will.','Your heart');
INSERT INTO riddles VALUES (22,'You must keep it after giving it.','Your word;Promise');
INSERT INTO riddles VALUES (23,'As light as a feather, but you can\'t hold it for ten minutes.','Your breath');
INSERT INTO riddles VALUES (24,'Has a mouth but does not speak, has a bed but never sleeps.','A river');
INSERT INTO riddles VALUES (25,'Runs smoother than any rhyme, loves to fall but cannot climb!','Water');
INSERT INTO riddles VALUES (26,'You break it even if you name it!','Silence');
INSERT INTO riddles VALUES (27,'It passes before the sun and makes no shadow.','The air');
INSERT INTO riddles VALUES (28,'You feed it, it lives, you give it something to drink, it dies.','A fire');
INSERT INTO riddles VALUES (29,'A red drum which sounds`nWithout being touched,`nAnd grows silent,`nWhen it is touched.','Your heart');
INSERT INTO riddles VALUES (30,'A harvest sown and reaped on the same day`nIn an unplowed field,`nWhich increases without growing,`nRemains whole though it is eaten`nWithin and without,`nIs useless and yet`nThe staple of nations.','A war');
INSERT INTO riddles VALUES (31,'If you break me`nI do not stop working,`nIf you touch me`nI may be snared,`nIf you lose me`nNothing will matter.','Hope');
INSERT INTO riddles VALUES (32,'All about, but cannot be seen,`nCan be captured, cannot be held`nNo throat, but can be heard.','Sounds; noises');
INSERT INTO riddles VALUES (33,'I go around in circles,`nBut always straight ahead`nNever complain,`nNo matter where I am led.','A wheel');
INSERT INTO riddles VALUES (34,'Lighter than what`nI am made of,`nMore of me is hidden`nThan is seen.','An iceberg');
INSERT INTO riddles VALUES (35,'If a man carried my burden,`nHe would break his back.`nI am not rich,`nBut leave silver in my track.','A snail');
INSERT INTO riddles VALUES (36,'My life can be measured in hours,`nI serve by being devoured.`nThin, I am quick`nFat, I am slow`nWind is my foe.','A candle');
INSERT INTO riddles VALUES (37,'Weight in my belly,`nTrees on my back,`nNails in my ribs,`nFeet I do lack.','A boat; a cave');
INSERT INTO riddles VALUES (38,'You can see nothing else`nWhen you look in my face`nI will look you in the eye`nAnd I will never lie.','A mirror');
INSERT INTO riddles VALUES (39,'I am always hungry,`nI must always be fed,`nThe finger I lick`nWill soon turn red.','A fire');
INSERT INTO riddles VALUES (40,'Three lives have I.`nGentle enough to soothe the skin,`nLight enough to caress the sky`nHard enough to crack rocks.','Water');
INSERT INTO riddles VALUES (41,'Glittering points`nThat downward thrust,`nSparkling spears`nThat never rust.','Icicles');
INSERT INTO riddles VALUES (42,'Each morning I appear`nTo lie at your feet,`nAll day I follow`nNo matter how fast you run,`nYet I nearly perish`nIn the midday sun.','A shadow');
INSERT INTO riddles VALUES (43,'Keys without locks`nYet I unlock the soul.','A piano; a harpsichord; music; song; notes');
INSERT INTO riddles VALUES (44,'I am so simple,`nThat I can only point`nYet I guide men`nAll over the world.','A compass');
INSERT INTO riddles VALUES (45,'For our ambrosia we were blessed,`nby Jupiter, with a sting of death.`nThough our might, to some is jest,`nwe have quelled the dragon\'s breath.`nWho are we?','Bees');
INSERT INTO riddles VALUES (46,'Colored as a maiden tweaked,`ntime was naught when I began;`nthrough the garden I was sneaked,`nI alone am the fall of man.`nWhat am I?','An apple');
INSERT INTO riddles VALUES (47,'One where none should be,`nor maybe where two should be,`nseeking out purity,`nin the kings trees.`nWhat am I?','A unicorn');
INSERT INTO riddles VALUES (48,'One tooth to bite,`nhe\'s the forests foe.`nOne tooth to fight,`nas all Norse know.`nWhat is it?','An axe');
INSERT INTO riddles VALUES (49,'The part of the bird`nthat is not in the sky,`nwhich can swim in the ocean`nand always stay dry.`nWhat is it?','The bird\'s shadow; shadow');
INSERT INTO riddles VALUES (50,'The root tops the trunk`non this backward thing,`nthat grows in the winter`nand dies in the spring.`nWhat is it?','An icicle');
INSERT INTO riddles VALUES (51,'Touching one, yet holding two,`nit is a one link chain`nbinding those who keep words true,`n\'til death rent it in twain.`nWhat is it?','A wedding ring; ring');
INSERT INTO riddles VALUES (52,'The wise and knowledgeable man is sure of it.`nEven the fool knows it.`nThe rich man wants it.`nThe greatest of heroes fears it.`nYet the lowliest of cowards would die for it.`nWhat is this upon which I ponder?','Nothing; something');
INSERT INTO riddles VALUES (53,'What is greater than God,`nWorse than the Devil,`nDead man eat it,`nIf you eat it you\'ll die.','Nothing');
INSERT INTO riddles VALUES (54,'I am a wonderful help to women,`nThe hope of something to come. I harm`nNo citizen except my slayer.`nRooted I stand on a high bed.`nI am shaggy below. Sometimes the beautiful`nPeasant\'s daughter, an eager-armed,`nProud woman grabs my body,`nRushes my red skin, holds me hard,`nClaims my head. The curly-haired`nWoman who catches me fast will feel`nOur meeting. Her eye will be wet.','An onion');
INSERT INTO riddles VALUES (55,'Power and treasure for a prince to hold,`nHard and steep-cheeked, wrapped in red`nGold and garnet, ripped from a plain`nOf bright flowers, wrought - a remnant`nOf fire and file, bound in stark beauty`nWith delicate wire, my grip makes`nWarriors weep, my sting threatens`nThe hand that grasps gold. Studded`nWith a ring, I ravage heir and heirloom.`nTo my lord and foes always lovely`nAnd deadly, altering face and form.','A sword');
INSERT INTO riddles VALUES (56,'As I was going to St. Ives,`nI met a man with seven wives;`nEvery wife had seven sacks,`nEvery sack had seven cats,`nEvery cat had seven kits:`nKits, cats, sacks, and wives,`nHow many were there going to St. Ives?','One;1');
INSERT INTO riddles VALUES (57,'Dawns away,`nThe day\'s turned grey,`nAnd I must travel far away.`nBut I\'ll be back,`nAnd then we\'ll track,`nThe light of yet another day.','The sun; a shadow');
INSERT INTO riddles VALUES (58,'Deep, dark, underground,`nThat is the place where I\'ll be found.`nYet brought into the light of day,`nI sprinkle sunlight every-which-a-way.`nThough dulled with oil I will be found,`nI am remarkably well and throughly sound.`nCut me quick and it will be seen,`nThat I instantly have a marvelous sheen.','A diamond; a gem');
INSERT INTO riddles VALUES (59,'What must be in the oven yet can not be baked?`nGrows in the heat yet shuns the light of day?`nWhat sinks in water but rises with air?`nLooks like skin, but is fine as hair?','Yeast');
INSERT INTO riddles VALUES (60,'Little Johnny Walker,`nMy, but he was a talker!`nYet nary a word did he say!`nWhen I took him out,`nThen they would all point and shout!`nAnd ask that I put him away.','Your opinions');
INSERT INTO riddles VALUES (61,'They are many and one,`nThey wave and they drum,`nUsed to cover a stare,`nThey go with you everywhere.','Your hands');
INSERT INTO riddles VALUES (62,'Stomp, stomp,`nChomp, chomp,`nRomp, romp.`nStanding still,`nall in gear.','Horses');
INSERT INTO riddles VALUES (63,'Sweet tooth,`nAh shoot,`nAll gone,`nWe all long,`nFor another piece of it.','Candy');
INSERT INTO riddles VALUES (64,'It comes in on little cat\'s feet,`nIs neither sour, nor sweet.`nHovers in the air,`nAnd then is not there.','Fog; mist');
INSERT INTO riddles VALUES (65,'A laugh,`nA cry,`nA moan,`nA sigh.','Emotions');
INSERT INTO riddles VALUES (66,'What is it you have to answer?`nBut to answer you have to ask?`nAnd to ask you have to speak?`nAnd to speak you have to know,`nThe answer.','A riddle');
INSERT INTO riddles VALUES (67,'I can hit you in the eye,`nYet twinkle in the sky,`nExpanding when I die,`nWhat do you think am I?','A star');
INSERT INTO riddles VALUES (68,'Squishes,`nSquashes,`nWishes I washes,`nCan get it in my hair,`nMakes me not look too fair.','Mud');
INSERT INTO riddles VALUES (69,'Up a hill,`nDown a hill,`nOver them I may roam,`nBut after all my walking,`nThere\'s no place like my own.','Your home');
INSERT INTO riddles VALUES (70,'This thing is a most amazing thing.`nFor it can be both as sharp as a knife,`nOr as flat as a floor.`nAnd yet, for all that it can be,`nIt is as natural as a bee.','Music; Notes');
INSERT INTO riddles VALUES (71,'Deep, deep, do they go.`nSpreading out as they go.`nNever needing any air.`nThey are sometimes as fine as hair.','Roots');
INSERT INTO riddles VALUES (72,'Oh Lord!  I am not worthy!`nI bend my limbs to the ground.`nI cry, yet without a sound.`nLet me drink of waters deep.`nAnd in silence I will weep.','A willow; weeping willow; willow tree; weeping willow tree');
INSERT INTO riddles VALUES (73,'Shifting, Shifting, Drifting deep.`nBelow me great and mighty cities sleep.`nSwirling, Scurlling, All around.`nI\'m only where no water will be found.','The desert');
INSERT INTO riddles VALUES (74,'I bubble and laugh`nAnd spit water in your face.`nI am no lady,`nAnd I don\'t wear lace.','A fountain');
INSERT INTO riddles VALUES (75,'What has wings,`nBut can not fly.`nIs enclosed,`nBut can outside also lie.`nCan open itself up,`nOr close itself away.`nIs the place of kings and queens,`nAnd doggerel of every means.`nWhat is it upon which I stand?`nWhich can lead us to different lands.','A stage');
INSERT INTO riddles VALUES (76,'Do not begrudge this,`nFor it is the fate of every man.`nYet it is feared,`nAnd shunned in many lands.`nCauses problems, and sometimes gaps,`nCan hobble the strongest, and make memory lapse.`nWhat is this danger we all face?`nFor being a part - of the human race.','Growing old; age; old age');
INSERT INTO riddles VALUES (77,'Of these things - I have two.`nOne for me - and one for you.`nAnd when you ask about the price,`nI simply smile and nod twice.','Sharing');
INSERT INTO riddles VALUES (78,'I am a strange creature,`nHovering in the air,`nMoving from here to there,`nWith a brilliant flare.`nSome say I sing,`nBut others say I have no voice.`nSo I just hum - as a matter of choice.`nWhat am I?','A hummingbird');
INSERT INTO riddles VALUES (79,'Sleeping during the day,`nI hide away.`nWatchful through the night,`nI open at dawn\'s light.`nBut only for the briefest time,`nDo I shine.`nAnd then I hide away,`nAnd sleep through the day.','A morning glory');
INSERT INTO riddles VALUES (80,'Looks like water,`nBut it\'s heat.`nSits on sand,`nLays on concrete.`nPeople have been known,`nTo follow it everywhere.`nBut it gets them no place,`nAnd all they can do is stare.','A mirage');
INSERT INTO riddles VALUES (81,'A part of heaven,`nThough it touches the earth.`nSome say it\'s valuable,`nOthers - no worth.','A rainbow');
INSERT INTO riddles VALUES (82,'I stand,`nAnd look across the sea,`nWith its waves, crests, troughs, and valleys.`nI stride,`nAcross this water, my horse following after,`nAnd while it laps against his withers,`nAnd brushes against my thighs,`nI fill the emptiness with laughter.`nAnd he - with his sighs.`nWhether do we go?`nOr do we go at all?`nOr are we simply out here wading,`nTo the next port of call.`nWhere the sea ends,`nWhere the loam lays firm beneath my feet,`nAnd I can mount my steed again,`nAnd continue til next we meet.`nWhat is really being talked about?','The open plains; plain');
INSERT INTO riddles VALUES (83,'I was born blind,`nAnd could not see,`nUntil it was a quarter of three.`nI could not smile,`nTil half past six,`nAnd all of my arms and legs`nWere made of sticks.','A doll');
INSERT INTO riddles VALUES (84,'Ah!  My breath doth shake,`nMy limbs are thin,`nMy belly aches.`nWhiteness doth crown my head,`nAnd the tracks I leave,`nAre unsteady where I\'ve led.`nI look out through rheumy eyes,`nAnd seem to say my last goodbyes.`nThe darkness doth draw me near,`nI lean towards it - the better to hear.','Old age; age');
INSERT INTO riddles VALUES (85,'Hick-a-more, Hack-a-more,`nOn the King\'s kitchen door.`nAll the King\'s horses,`nAnd all the King\'s men,`nCouldn\'t get Hick-a-more, Hack-a-more,`nOff the King\'s kitchen door.','Sunlight');
INSERT INTO riddles VALUES (86,'It was asked of me what I could be made,`nAnd so people were fed from me.`nIt was asked of me what I could be made,`nAnd so houses were built.`nIt was asked of me what I could be made,`nAnd so things were written.`nIt was asked of me what I could be made,`nAnd so I fertilized the ground.`nBut when asked more of what I could be made,`nThere was nothing to be found.','A tree');
INSERT INTO riddles VALUES (87,'With this you can do wonderous things.`nLook at things close, or far away,`nYou can see things big,`nOr you can see things small.`nOr maybe you don\'t see things at all.`nI come in many colors and hues,`nSometimes green and sometimes blue.`nAnd when I\'m red - it\'s not from shame,`nBut from something with a different name.','Your eyes');
INSERT INTO riddles VALUES (88,'Oh how I love my dancing feet!`nThey stay together - oh so neat.`nAnd when I want to walk a line,`nThey all stay together and do double time.`nI count them up, ten times or more,`nAnd race on-off, across the floor.','A centipede; millipede');
INSERT INTO riddles VALUES (89,'A muttered rumble was heard from the pen,`nAnd I, in my walking, stopped to look in.`nWhat was this I saw?`nA massive beast, hooved, and jawed.`nWith spikes upon its mighty brow,`nI watched as he struck the turf and prowled.`nAnd yet for all of his magnificience,`nHe couldn\'t get out of that wooden fence.','A bull');
INSERT INTO riddles VALUES (90,'Twas the night of the day`nin which I must relay`nthat in which I took part in.`nFor the sun was out`nand without so much as a shout`nhe quietly went in.`nTwas ever so queer`nI thought he would leer`nbut never a word did I get in.`nFor without another word`n(at least that\'s what I heard)`nHe was back to the place he\'d been in.','An eclipse');
INSERT INTO riddles VALUES (91,'From sun up to sun down I stare out across the sea.`nFrom sun down to sun up I stare out across the sea.`nBut while with sun up I can only blink in the brightness.`nWith the sun down I can blink out the brightness.','A lighthouse');
INSERT INTO riddles VALUES (92,'A lot of bark,`nBut no one notices.`nA lot to bite,`nAnd everyone cares.`nI\'m not a dog,`nIf anyone notices.`nAnd there\'s a lot to me,`nBut I don\'t have hair.`nI stand up straight,`nIf you\'ve noticed me.`nI\'ve got lots of limbs,`nIf anyone cares.`nI can give you shade,`nIf you\'ve noticed it.`nAnd I do even more,`nI give you air.','A tree');
INSERT INTO riddles VALUES (93,'Twas in December or June,`nWhen my lady did swoon.`nWhen her hair did fall off,`nAnd her glasses were lost.`nWhen she did scream,`nIn a manner most obscene.`nWhile pointing at me,`nAnd saying ”Eeeeee!  Eeeeee!”`nI must say it was all a bit much,`nSince no one did I touch.`nBut it was quite apparent,`nThat something was errant.`nSo I decided to come back another day,`nWhen, mayhap, she was away.','A mouse');
INSERT INTO riddles VALUES (94,'I drift,`nAs slowly as a lazy river.`nI dance,`nUpon as little as a puff of air.`nI tumble,`nBetter than the greatest acrobat.`nSwirling,`nTwirling,`nDown to the ground.`nWhere I lie,`nTil I get my second wind.`nSo I can begin again.','A leaf');
INSERT INTO riddles VALUES (95,'A riddle, easily solved.`nRed breasted.`nOnly one in a field of many.`nBorn in an egg.`nInspired to sing.`nNow gather the letters and tell me what I mean.','A robin');
INSERT INTO riddles VALUES (96,'I have four of these,`nWith matching extremities.`nThey can do many things,`nAnd hardly ever bring me pain.`nUnless I stick them with a pin,`nOr burn them sometimes when...`nWhat is it that I can wiggle at will?`nAnd use in other means still?','Fingers');
INSERT INTO riddles VALUES (97,'When I looked upon the flames of his passion,`nAnd the coolness of her touch,`nI knew tragedy could only come from their union.`nAnd indeed, when they came together,`nDarkness reigned upon the land.`nAnd although they were soon separated,`nLearning as they did that they were not for each other,`nStill, their passing regards for each other,`nLeft it\'s impression upon all who had witnessed it.`nAnd would be talked about for ages still to come.','A solar eclipse; eclipse');
INSERT INTO riddles VALUES (98,'What has a coat?`nHugs you not in sympathy?`nWhose smile you\'d rather not see?`nWhose stance is a terrible thing to see?`nWho is it that brave men run away from?`nWhose fingers are clawed?`nWhose sleep lasts for months?`nAnd whose company we shun?','A bear');
INSERT INTO riddles VALUES (99,'You can tumble in it,`nRoll in it,`nBurn it,`nAnimals eat it,`nUsed to cover floors,`nStill used beyond stall doors.`nFreshens whatever it is placed on,`nAbsorbs whatever is poured into it.`nWhat is it?','Hay');
INSERT INTO riddles VALUES (100,'Within passion\'s fruit they will be found,`nAnd more of them in the pomegranate\'s crown.`nRowed they are within an apple\'s core,`nYet other fruits have them more.`nAnd though the nectarine has but one,`nStill, this is all just in fun.`nPlaying hide and seek - a children\'s game.`nFinding out each player is just the same.','Seeds');
INSERT INTO riddles VALUES (101,'\'Twas whispered in Heaven, \'twas muttered in hell,`nAnd echo caught faintly the sound as it fell;`nOn the confines of earth \'twas permitted to rest,`nAnd in the depths of the ocean its presence confes\'d;`n\'Twill be found in the sphere when \'tis riven asunder,`nBe seen in the lightning and heard in the thunder;`n\'Twas allotted to man with his earliest breath,`nAttends him at birth and awaits him at death,`nPresides o\'er his happiness, honor and health,`nIs the prop of his house, and the end of his wealth.`nIn the heaps of the miser \'tis hoarded with care,`nBut is sure to be lost on his prodigal heir;`nIt begins every hope, every wish it must bound,`nWith the husbandman toils, and with monarchs is crowned;`nWithout it the soldier and seaman may roam,`nBut woe to the wretch who expels it from home!`nIn the whispers of conscience its voice will be found,`nNor e\'er in the whirlwind of passion be drowned;`n\'Twill soften the heart; but though deaf be the ear,`nIt will make him acutely and instantly hear.`nSet in shade, let it rest like a delicate flower;`nAh!  Breathe on it softly, it dies in an hour','The letter H; H');
INSERT INTO riddles VALUES (102,'We are little airy creatures,`nAll of different voice and features;`nOne of us in glass is set,`nOne of us you\'ll find in jet,`nT\'other you may see in tin,`nAnd the fourth a box within;`nIf the fifth you should pursue,`nIt can never fly from you.','The vowels');
INSERT INTO riddles VALUES (103,'I\'m a strange contradiction; I\'m new, and I\'m old,`nI\'m often in tatters, and oft decked with gold.`nThough I never could read, yet lettered I\'m found;`nThough blind, I enlighten; though loose, I am bound,`nI\'m always in black, and I\'m always in white;`nI\'m grave and I\'m gay, I am heavey and light-`nIn form too I differ - I\'m thick and I\'m thin,`nI\'ve no flesh and bones, yet I\'m covered with skin;`nI\'ve more points than the compass, more stops than the flute;`nI sing without voice, without speaking confute.`nI\'m English, I\'m German, I\'m French, and I\'m Dutch;`nSome love me too fondly, some slight me too much;`nI often die soon, though I sometimes live ages,`nAnd no monarch alive has so many pages.','A book');
INSERT INTO riddles VALUES (104,'As I went through the garden gap,`nWho should I meet but Dick Red-cap!`nA stick in his hand, a stone in his throat,`nIf you\'ll tell me this riddle, I\'ll give you a groat.','A cherry');
INSERT INTO riddles VALUES (105,'Little Nancy Etticote,`nIn a white petticoat,`nWith a red nose;`nThe longer she stands`nThe shorter she grows.','A candle');
INSERT INTO riddles VALUES (106,'I have a little sister, they call her Peep, Peep;`nShe wades the waters deep, deep, deep;`nShe climbs the mountains high, high, high;`nPoor little creature she has but one eye.','A star');
INSERT INTO riddles VALUES (107,'I saw a company a marching,`nA marching across the sea.`nAnd looking upon them,`nI asked myself ”What can they be?”`nFor there was a horse,`nAnd there was a cow,`nAnd there were men marching,`nWith houses and trees.  But how?`nI saw a company marching,`nA marching across the sea.`nAnd wondered in my rest,`nHow lazy I must be.','Clouds in the sky; clouds');
INSERT INTO riddles VALUES (108,'I\'m up.`nI\'m down.`nI\'m all around.`nYet never can I be found.`nWho am I?','The wind');
INSERT INTO riddles VALUES (109,'I can be moved.`nI can be rolled.`nBut nothing will I hold.`nI\'m red and I\'m blue.`nAnd I can be other colors too.`nHaving no head, though similar in shape,`nI have no eyes - yet move all over the place.`nWhat am I?','A ball');
INSERT INTO riddles VALUES (110,'Upon me you can tread,`nThough softly under cover.`nAnd I will take you places,`nThat you have yet to discover.`nI\'m high, and I\'m low,`nThough flat in the middle.`nAnd though a joy to the children,`nAdults think of me little.`nWhat am I?','Stairs');
INSERT INTO riddles VALUES (111,'What is it which builds things up?`nLays mountains low?`nDries up lakes,`nAnd makes things grow?`nCares not a whim about your passing?`nAnd is like few other things,`nBecause it is everlasting?','Time');
INSERT INTO riddles VALUES (112,'It sat upon a willow tree,`nAnd sang softly unto me.`nEasing my pain and sorrow with its song,`nI wished to fly, but tarried long.`nAnd in my suffering,`nThe willow was like a cool clear spring.`nWhat was it that helped me so?`nTo spend my time in my woe.','A bird');
INSERT INTO riddles VALUES (113,'They can be harbored, but few hold water,`nYou can nurse them, but only by holding them against someone else,`nYou can carry them, but not with your arms,`nYou can bury them, but not in the earth.','A grudge');
INSERT INTO riddles VALUES (114,'Deep as a bowl, round as a cup,`nYet all the world\'s oceans can\'t fill it up.','A sieve; collander');
INSERT INTO riddles VALUES (115,'Though desert men once called me God,`nTo-day men call me mad,`nFor I wag my tail when I am angry,`nAnd growl when I am glad.','A cat');
INSERT INTO riddles VALUES (116,'I heard of an invading, vanquishing army`nsweeping across the land, liquid-quick;`nconquering everything, quelling resistance.`nWith it came darkness, dimming the light.`nHumans hid in their houses, while outside`nspears pierced, shattering stone walls.`nUncountable soldiers smashed into the ground,`nbut each elicited life as he died;`nwhen the army had vanished, advancing northward,`nthe land was green and growing, refreshed.','A rainstorm; rain');
INSERT INTO riddles VALUES (117,'Tall she is, and round as a cup,`nYet all the king\'s horses`nCan\'t draw her up.','A well');
INSERT INTO riddles VALUES (118,'The more of it there is,`nThe less you see.','Darkness');
INSERT INTO riddles VALUES (119,'What is not enough for one,`nJust right for two,`nToo much for three?','A secret');
INSERT INTO riddles VALUES (120,'What gets wetter the more it dries?','A towel');
INSERT INTO riddles VALUES (121,'A long snake`nWith a stinging bite,`nI stay coiled up`nUnless I must fight.','Whip');
INSERT INTO riddles VALUES (122,'A warrior amongst the flowers,`nHe bears a thrusting sword.`nAble and ready to use,`nTo guard his golden hoard.','Bees');
INSERT INTO riddles VALUES (123,'The Load-bearer, the Warrior,`nThe Frightened One, the Brave,`nThe Fleet-of-foot, the Ironshod`nThe Faithful One, the Slave','A horse');
INSERT INTO riddles VALUES (124,'Walks in the wind`nRuns in the rain`nMakes dry oceans in the sun`nCounts time, stops clocks`nSwallows kingdoms, gnaws rocks.','Sand');
INSERT INTO riddles VALUES (125,'The rolling hills, the heart that beats forever,`nThe land that never changes, never stills`nPloughed by travellers far from home, not planted,`nWhite in anger, green in peace, and always blue.','The sea; ocean');
INSERT INTO riddles VALUES (126,'Listen closely, I\'m hard to understand`nI am as elusive as is a handful of sand.`nEven if you perceive me, you know me not`nbefore you can tell me, what I have forgot.','A riddle');
INSERT INTO riddles VALUES (127,'What goes through the door without pinching itself?`nWhat sits on the stove without burning itself?`nWhat sits on the table and is not ashamed?','The sun');
INSERT INTO riddles VALUES (128,'Whilst I was engaged in sitting`nI spied the dead carrying the living`nWhat did I see?','A ship');
INSERT INTO riddles VALUES (129,'I know a word of letters three,`nAdd two and fewer there will be.','Few');
INSERT INTO riddles VALUES (130,'Who makes it, has no need of it.`nWho buys it, has no use for it.`nWho uses it, can neither see nor feel it.','A coffin');
INSERT INTO riddles VALUES (131,'The man who made it didn\'t need it.`nThe man who bought it didn\'t use it.`nThe man who used it didn\'t want it.','A coffin');
INSERT INTO riddles VALUES (132,'You seized me, and yet I fled`nYou see me flee and cannot hold me tight`nYou press me in your hand, then your fist is empty.`nWhat am I?','Snow');
INSERT INTO riddles VALUES (133,'What has four legs in the morning,`nTwo legs in the afternoon,`nAnd three legs in the evening?','Man');
INSERT INTO riddles VALUES (134,'What is deaf, dumb and blind`nand always tells the truth ?','A mirror');
INSERT INTO riddles VALUES (135,'What is always in front of you`nbut cannot be seen?','The future');
INSERT INTO riddles VALUES (136,'What does man love more than life,`nhate more than death or mortal strife;`nThat which contented men desire,`nthe poor have, the rich require;`nThe miser spends, the spendthrift saves,`nand all men carry to their graves?','Nothing');
INSERT INTO riddles VALUES (137,'A life longer than any man,`nit dies each year to be reborn.','A tree');
INSERT INTO riddles VALUES (138,'In the eyes it causes blindness,`nin the nose just a sneeze;`nYet some suck this down,`nand act as if pleased.','Smoke');
INSERT INTO riddles VALUES (139,'It stands alone, with no bone or solid form.`nAdamant, it prospers never wrong,`nthough hurt it may.`nTwistable, malleable, might it be,`nbut always straight as an arrow.','The truth');
INSERT INTO riddles VALUES (140,'What sphinxes employ,`nthe players enjoy.','A riddle');
INSERT INTO riddles VALUES (141,'There\'s someone that I\'m always near,`nYet in the dark I disappear.`nTo this one only I am loyal,`nThough in his wake I\'m doomed to toil.`nHe feels me not (we always touch);`nIf I were lost, he\'d not lose much.`nAnd now I come to my surprise,`nFor you are he - but who am I ?','Your shadow');
INSERT INTO riddles VALUES (142,'I\'m often held, yet rarely touched;`nI\'m always wet, yet never rust;`nI\'m sometimes wagged and sometimes bit;`nTo use me well, you must have wit.','Tongue');
INSERT INTO riddles VALUES (143,'In the window she sat weeping.`nAnd with each tear her life went seeping.','A candle');
INSERT INTO riddles VALUES (144,'I\'m not really more than holes tied to more`nholes;`nI\'m strong as good steel, though not as stiff`nas a pole.','A chain');
INSERT INTO riddles VALUES (145,'I\'ve little strength, but mighty powers;`nI guard small hovels and great towers.`nBut if perchance my master leaves,`nHe must ensure he safeguards me.','A key');
INSERT INTO riddles VALUES (146,'Delivered by breath,`nscares heroes to death.','The Riddle');
INSERT INTO riddles VALUES (147,'In daytime I lie pooled about,`nAt night I cloak like a mist.`nI creep inside shut boxes and`nInside your tightened fist.`nYou see me best when you can\'t see,`nFor I do not exist.','Darkness');
INSERT INTO riddles VALUES (148,'Devils and rogues know nothing else,`nsave starlight.','Darkness');
INSERT INTO riddles VALUES (149,'Both king and horse have this, of course,`nBut you\'ll want neither of them, perforce.','Reign');
INSERT INTO riddles VALUES (150,'My spring up on the cliff.','A coconut');
INSERT INTO riddles VALUES (151,'Three walls and you reach water.','A coconut');
INSERT INTO riddles VALUES (152,'My kapa (a type of cloth) log that`nis always sounding without rest.','The sea');
INSERT INTO riddles VALUES (153,'In the morning four legs,`nat noon two legs,`nat evening three legs.','Man');
INSERT INTO riddles VALUES (154,'My man that cannot be cut.','A shadow');
INSERT INTO riddles VALUES (155,'My canoes, going day and night,`nten bowspirits, two sterns.','Someone\'s feet');
INSERT INTO riddles VALUES (156,'My red cave, white soldiers standing in line.','Someone\'s mouth');
INSERT INTO riddles VALUES (157,'My man crying day and night,`nall through the year.','The sea');


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

#-- Graveyard Changes
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
ALTER TABLE accounts ADD bio varchar(255) not null default '';
ALTER TABLE accounts ADD race tinyint(4) not null default 0;
ALTER TABLE accounts ADD INDEX (name);
ALTER TABLE accounts ADD INDEX (level);
ALTER TABLE accounts ADD INDEX (login);
ALTER TABLE accounts ADD INDEX (alive);
ALTER TABLE accounts ADD INDEX (laston);
ALTER TABLE accounts ADD INDEX (lasthit);
ALTER TABLE accounts ADD INDEX (emailaddress);
ALTER TABLE creatures ADD INDEX (creaturelevel);
ALTER TABLE commentary ADD INDEX (section);
ALTER TABLE commentary ADD INDEX (postdate);
ALTER TABLE mail ADD INDEX (msgto);
ALTER TABLE mail ADD INDEX (seen);
ALTER TABLE news ADD INDEX (accountid);

ALTER TABLE accounts ADD superuser2 int(11) unsigned not null default 0;
ALTER TABLE accounts ADD banoverride tinyint(4) default 0;
ALTER TABLE accounts ADD buffbackup text NOT NULL;
ALTER TABLE accounts ADD biotime datetime not null default '0000-00-00 00:00:00';
UPDATE accounts SET biotime=now() where bio>'';
ALTER TABLE accounts ADD history text not null;

INSERT INTO creatures VALUES (0, 'Old Man Winter', 9, 'Bitter Cold', 'You shake your fist over his dead, pathetic corpse.', NULL, 336, 101, 94, 17, 13, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Deranged Gnome', 1, 'A Cloth Draped Mirror', 'What happens when I do THIS?', NULL, 36, 14, 10, 1, 1, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Water Sprite', 1, 'Burst of Water', 'That was refreshing.', NULL, 36, 14, 10, 1, 1, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Water Nymph', 3, 'A torrent of water', 'I didn\'t need a bath, thank you.', NULL, 148, 34, 32, 5, 4, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Lady of the Lake', 10, 'Excalibur', 'Take that, you watery tart!', NULL, 369, 114, 105, 19, 14, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Cunning Sapling', 1, 'Arrrr!', 'Ah Ha! A toothpick!', NULL, 36, 14, 10, 1, 1, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Pirate Tree', 5, 'A loud ARRRRR!', 'Arrrrrr! indeed.', NULL, 198, 55, 53, 9, 7, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Undead Centurion', 5, 'Bronze Spear', 'Hmm Not too good at combat the second time around, either.', NULL, 198, 55, 53, 9, 7, NULL, NULL, 1);
INSERT INTO creatures VALUES (0, 'Insane Bard', 2, 'Iambic Pentameter', 'Slings and Arrows of outrageous fortune? Nope.  Just the can o\' Whoopass.', NULL, 97, 24, 21, 3, 3, NULL, NULL, 0);
INSERT INTO creatures VALUES (0, 'Twig Man', 1, 'Brambles and Briars', 'I snapped him like a.. well, twig.', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Rabid Mouse', 1, 'Little Bitty Teeth', 'Those little teeth HURT!', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Stray Cat', 1, 'A Midnight Serenade', 'Finally! Peace in the night!', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Big Hairy Spider', 1, 'Sticky stringy webs.', 'I *HATE* spiders!', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Goblin Lackey', 1, 'Dull Knife', 'Who knew goblins had lackeys?', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Crazy Chihuahua', 1, 'Fast food cravings', 'Here taco taco taco..', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Looney Rabbit', 1, 'Wisecracks', 'Th- Th- That\'s all folks!', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Undead Oarsman', 1, 'Rotten Oar', 'Funny.. I didn\'t see any water around here..', NULL, 36, 14, 10, 1, 1, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Lost Ghostling', 2, 'Sad Weeping', 'Well, im sure that helped him on his way...', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Angry Titmouse', 2, 'Incessant chattering', 'You swat the little bird out of the air with little effort.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Fabianist Youth Member', 2, 'Marxist Doctrine', 'Socialize THAT.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Little Bogeyman', 2, 'Noises in the Dark', 'Hmm not so scary when you have grown up...', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Big Bogeyman', 3, 'Fingers poking you as you sleep..', 'Ok.. that was weird.  But not scary.. Nope not at all.. I think ill leave the light on at the inn tonight though.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Coblynau', 2, 'Ill tempered tricks', 'Maybe I better leave him part of my lunch next time..', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Jack O\'Lantern', 2, 'Eternal Wandering', 'The light has gone out of his life.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Fresh Ghoul', 2, 'Long Pointed Nails', 'You leave him to the mercies of the other ghouls..', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Dry Skeleton', 2, 'a Rusty Dagger', 'The skeleton crumbles into a pile of dust.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Newly Dead Zombie', 2, 'Fists', 'Maybe now it can rest in peace.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Faerie hound', 2, 'Glowing Eyes', 'The embers in the eye sockets dim and sputter out.', NULL, 97, 24, 21, 3, 3, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Apprentice Necromancer', 3, 'Life Draining Spells', 'He gasps a final breath and melts into a tarry liquid.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Doppleganger', 3, 'Shape changing', 'You kill the doppleganger and he returns to his true form.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Forest Thief', 3, 'Poisoned Short Sword', 'His pockets were almost empty, I guess he wasn\'t much of a thief.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Mouldy Skeleton', 3, 'Rotten Fists', 'Heeeeey, didn\'t you see him in town last week?', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Angry Fairy', 3, 'Furious Fairy Dust', 'He disappears in a cloud of fairy dust.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Goblin Hunter', 3, 'a Shortbow', 'Only one? Don\'t they usually travel in packs?', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Bobcat Cub', 3, 'Sharp Claws', 'Even the cute fuzzy kittens are evil in this place.', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Animate Junkheap', 3, 'Stench of 1000 rotten potatos.', 'Just call you the De-Animator!', NULL, 148, 34, 32, 5, 4, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Necromancer', 4, 'a Strength Draining Touch', 'In a cloud of black smoke, his body wafts away on the breeze.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Reed Golem', 4, 'Fists of Thorns', 'The only thing left was a tiny bale of hay.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Goblin Shaman', 4, 'Primitive Magic', '"Oooga Booga," indeed.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Steam Spirit', 4, 'Scalding Steam', 'Not so hot now, eh?', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Restless Zombie', 4, 'Broken Longsword', 'Finally he is at peace.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Zombie Soldier', 4, 'Rusted Spear', 'His final orders were to march to hell..', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Dark Imp', 4, 'Nagging Doubts', 'The imp implodes in a ball of flame.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Giant Spider', 4, 'Fangs that glisten with poison', 'I really really hate spiders.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Undead Kitten', 4, 'Mews of Doom', 'Another evil cat sent to hell C.O.D.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Flying Dagger', 4, 'Sharpened Steel Point', 'The dagger shatters when it hits the ground.', NULL, 162, 45, 43, 7, 6, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Goblin Warrior', 5, 'Bent Short Sword', 'Gone.. and forgotten.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Experienced Necromancer', 5, 'a small group of Zombies', 'The necromancer turns into a pile of black sand and blows away.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Flame Sprite', 5, 'Searing Flame', 'You cooled him off in a hurry...', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Mud Man', 5, 'Enchanted Mud', 'Defeated, the Mud Man dries up in the warm sun.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Lupe the Hypnotic Chihuahua', 5, 'The stare of doom', 'You manage to break free of those deep hypnotic eyes...', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Ghoul', 5, 'Hunger for Flesh', 'The ghoul falls to the ground, a bit of flesh between his teeth.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Seedy Cutthroat', 5, 'Sharpened Daggers', 'One more bandit removed from this forest.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'An Ogre', 5, 'Broken treebranch', 'Boy, that\'s a face not even a mother could love.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Crazy Woodsman', 5, 'Dull axe', 'Chopped him down to size...', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Flying Drakeling', 5, 'a Lashing Tail', 'You swat the small, majestic beast out of the air.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Forest Cat', 6, 'Menacing Claws', 'Here kitty kitty kitty', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Journeyman Necromancer', 6, 'An undead Giant', 'The necromancer drifts away on a cloud of black smoke.', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Frigid Skeleton', 6, 'The Cold of the Grave', 'I\'ll bet one of these guys could cool ale at the inn', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Frost Spirit', 6, 'Bone Chilling Cold', 'Puttin down another cold one', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Dragon Slayer\'s ghost', 6, 'Warnings of Doom', 'Everybody wondered what happened to him.', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Ettin Mage', 6, 'Double threat spells', 'Two heads are not better than one in this case.  Just more to lop off', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Insane Brownie', 6, 'Faerie spells', 'This one\'s nuts!', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Ghostly Wolfpack', 6, 'Ghostly Howls', 'Quiet settles in around you.  Finally.', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Wood Golem', 6, 'Oaken Fists', 'The truth gave you life and the truth set you free', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Diseased Ent', 6, 'Twisted Branches', 'There must be fifty ways to cut your lumber (Groan)', NULL, 234, 66, 64, 11, 8, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Dark Druid', 7, 'Corrupted Nature', 'He lost me at the corrupt it to save it part.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Lizard Man Scout', 7, 'Marginal Archery', 'I know where I can get him made into a nice pair of boots', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Diabolic Footman', 7, 'A Demon Forged Mace', 'His armor sizzles and he melts as he crumples to the ground.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Gargantuan Tree Spider', 7, 'Projectile Webbing', 'After you lop him in half, you realize he\'s not so big.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Flaming Zombie', 7, 'Fists covered in Flame', 'You extinguish the flame of life and the zombie falls to the ground, smoking.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Eldritch Wizard', 7, 'Spells of Darkness', 'You slice him in two only to watch his body disappear, leaving an empty robe behind.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Master Necromancer', 7, 'An Army of the Dead', 'You kill the necromancer and his minions disintegrate before your eyes.', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Animate Darkness', 7, 'The Darkness of a Thousand Nights', 'Light Slowly returns to this part of the forest', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Temple Guardian Statue', 7, 'Marble Fists', 'So, where\'s the temple?', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Owlbear', 7, 'Claws and a bearhug', 'I don\'t even want to know how they breed these', NULL, 268, 77, 74, 13, 10, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'The Man In Black', 8, 'Necromantic Spells', 'Finally, I got my chance to kill him', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Gnome Illusionist', 8, 'Fearsome Illusions', 'It was all in his head', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Demon Skeleton', 8, 'Diabolic Runes', 'The spirits of its victims drag the skeleton back to hell', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Insane Master Druid', 8, 'The land Itself', 'His evil laughter dies with him.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Water Elementalist', 8, 'Water Based Spells', 'He\'s all wet.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Earth Elementalist', 8, 'Earth Based Spells', 'He Fought kind of Dirty.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Ettin Warrior', 8, 'Two Clubs of Iron', 'They did all right until they hit each other.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Forest Giant', 8, 'An Uprooted Tree', 'Ho, Ho, Ho that, green man.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Demoniac Knight', 8, 'A Diabolic Lance', 'Good Night Sir Knight.', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Giant Zombie', 8, 'Gigantic Fists', 'They DO smell worse when they are bigger!', NULL, 302, 89, 84, 15, 11, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Frost Spectre', 9, 'The Chill Touch of the Grave', 'As you recover from the fight, the Spectre melts away into the shadows.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Small Forest Dragon', 9, 'Snapping Jaws', 'Dragon.. Check. Green... Check.  Huge.. Nope.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'A Pack of Carnivorous Lemurs', 9, 'Hoots and Howls', 'What the heck is a Lemur??', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Albino Crypt Bear', 9, 'Foul Stench and Claws', 'You dispatch the bear and wonder if there are more lurking about.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Black Knight of The Vale', 9, 'Ebony Sword', 'As he dies, he curses you and tells you that you will never defeat the dragon.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Elmearian Spy', 9, 'Small Dagger', 'He eats his information before he dies, so you learn nothing from him.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Stalking Shadow', 9, 'Fingers of Darkness', 'You send the ceature back to where it came from.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Necromantic Archmagus', 9, 'Screams of a Thousand Souls', 'With his dying breath he explodes into a pillar of black flame!', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'Iron Golem', 9, 'Rusted Fists', 'The golem siezes up and you push him over.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 0);
INSERT INTO creatures VALUES (0, 'A Hell Hound', 9, 'Fiery Breath', 'He bursts into flames and is sucked into the earth.', NULL, 336, 101, 94, 17, 13, NULL, 'Hank', 1);
INSERT INTO creatures VALUES (0, 'Corporate Lackey', 7, 'Buzzwords', 'Well, you sure reprioritized his downsizing.', NULL, 268, 77, 74, 13, 10, NULL, 'Moonchilde', 0);
INSERT INTO creatures VALUES (0, 'SCO Lawyer', 8, 'Court Papers', 'There\'s one lawyer who\'ll never trouble you again!', NULL, 302, 89, 84, 15, 11, NULL, 'Moonchilde', 1);

CREATE TABLE nastywords (
words longtext
) Type=INNODB;

INSERT INTO nastywords VALUES (
"*damn* *dyke *fuck* *nigger* *phuck* *shit* amcik andskota arschloch arse* ass asshole atouche ayir bastard bitch* boiolas bollock* buceta butt* butt-pirate cabron cawk cazzo chink chraa chuj cipa clit cock* cum cunt* dago daygo dego dick* dike dildo dirsa dupa dziwka ejaculate ejackulate ekrem* ekto enculer faen fag* fanculo fanny fatass fcuk feces feg felcher ficken fitta fitte flikker foreskin phuck fuk* fut futkretzn fuxor gay gook guiena hell helvete hoer* honkey honky hor hore huevon hui injun jism jizz kanker* kawk kike klootzak knulle kraut kuk kuksuger kurac kurwa kusi* kyrpä* leitch lesbian lesbo mamhoon masturbat* merd merde mibun monkleigh mouliewop muie mulkku muschi nazis nepesaurio nigga* nigger nutsack orospu paska* pendejo penis perse phuck picka pierdol* pillu* pimmel pimpis piss* pizda poontsee poop porn preteen preud prick pron pula pule pusse pussy puta puto qahbeh queef* queer* qweef rautenberg schaffer scheiss* scheisse schlampe schmuck screw scrotum sharmuta sharmute shemale shipal shiz skribz skurwysyn slut smut sphencter shpincter spic spierdalaj splooge suka teets teez testicle tits titties titty twat twaty vittu votze wank* wetback* whoar whore wichser woose wop yed zabourah"
);

CREATE TABLE mounts (
	mountid int(11) unsigned not null auto_increment,
	mountname varchar(50) not null,
	mountdesc tinytext,
	mountcategory varchar(50) not null,
	mountbuff text,
	mountcostgems int(11) unsigned not null,
	mountcostgold int(11) unsigned not null,
	mountactive int(11) unsigned not null default 1,
	PRIMARY KEY (mountid)
) Type=INNODB;
ALTER TABLE mounts ADD INDEX (mountid);
ALTER TABLE mounts ADD mountforestfights int(11) not null default 0;
ALTER TABLE mounts ADD tavern tinyint(4) unsigned not null default 0;
ALTER TABLE mounts ADD newday tinytext not null default '';
ALTER TABLE mounts ADD recharge text not null default '';
ALTER TABLE mounts ADD partrecharge text not null default '';
ALTER TABLE mounts ADD mine_canenter int(10) unsigned not null default 0;
ALTER TABLE mounts ADD mine_candie int(10) unsigned not null default 0;
ALTER TABLE mounts ADD mine_cansave int(10) unsigned not null default 0;
ALTER TABLE mounts ADD mine_tethermsg text not null default '';
ALTER TABLE mounts ADD mine_deathmsg text not null default '';
ALTER TABLE mounts ADD mine_savemsg text not null default '';

ALTER TABLE accounts ADD bounty int (11) unsigned NOT NULL default 0;
ALTER TABLE accounts ADD amountouttoday int (11) unsigned NOT NULL default 0;
ALTER TABLE accounts ADD pk tinyint(3) unsigned NOT NULL default '0';
ALTER TABLE accounts ADD dragonage int(11) unsigned NOT NULL default '0';
ALTER TABLE accounts ADD bestdragonage int(11) unsigned NOT NULL default '0';
ALTER TABLE accounts ADD lastwebvote date NOT NULL default '0000-00-00';
ALTER TABLE accounts ADD bounties int(11) unsigned NOT NULL default '0';
ALTER TABLE accounts ADD ctitle varchar(25) NOT NULL default '';
ALTER TABLE accounts ADD usedouthouse tinyint(1) unsigned not null default 0;

INSERT INTO mounts VALUES (1, 'Pony', 'This docile beast is young yet.', 'Horses', 'a:4:{s:4:"name";s:13:"`&Pony Attack";s:8:"roundmsg";s:26:"Your pony fights with you!";s:6:"rounds";s:2:"20";s:6:"atkmod";s:3:"1.2";}', 6, 0, 1, 1, 0, 'You strap your {weapon} to your pony\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your pony, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your pony to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your pony whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your pony trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`0', 0, 0, 0, '', '', '');
INSERT INTO mounts VALUES (2, 'Gelding', 'This powerful beast is fiercely loyal.', 'Horses', 'a:4:{s:4:"name";s:16:"`&Gelding Attack";s:8:"roundmsg";s:29:"Your gelding fights with you!";s:6:"rounds";s:2:"40";s:6:"atkmod";s:3:"1.2";}', 10, 0, 1, 2, 1, 'You strap your {weapon} to your gelding\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your gelding, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your gelding to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your gelding whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your gelding trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`n`nAnd here you thought geldings weren\'t equipped that way any longer!`0', 0, 0, 0, '', '', '');
INSERT INTO mounts VALUES (3, 'Stallion', 'This noble beast is huge and powerful!', 'Horses', 'a:4:{s:4:"name";s:17:"`&Stallion Attack";s:8:"roundmsg";s:30:"Your stallion fights with you!";s:6:"rounds";s:2:"60";s:6:"atkmod";s:3:"1.2";}', 16, 0, 1, 3, 1, 'You strap your {weapon} to your stallion\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your stallion, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your stallion to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your stallion whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your stallion trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`0', 0, 0, 0, '', '', '');

ALTER TABLE accounts ADD beta tinyint(3) unsigned NOT NULL default '0';

CREATE TABLE faillog (
	eventid int(11) unsigned not null auto_increment,
	date datetime not null default '0000-00-00 00:00:00',
	post tinytext not null,
	ip varchar(40) not null,
	acctid int(11) unsigned,
	id varchar(32) not null default '',
	PRIMARY KEY (eventid)
) Type=INNODB;
ALTER TABLE faillog ADD INDEX (date);
ALTER TABLE faillog ADD INDEX (acctid);
ALTER TABLE faillog ADD INDEX (ip);

CREATE TABLE debuglog (
	id int(11) unsigned not null auto_increment,
	date datetime not null default '0000-00-00 00:00:00',
	actor int(11) unsigned,
	target int(11) unsigned,
	message text not null,
	PRIMARY KEY (id)
) Type=INNODB;
ALTER TABLE debuglog ADD INDEX (date);
ALTER TABLE debuglog ADD INDEX (actor);
ALTER TABLE debuglog ADD INDEX (target);
