# PRIVATE_CODE
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
INSERT INTO creatures VALUES (0, 'An Ogre', 5, 'Broken treebranch', 'Boy, thats a face not even a mother could love.', NULL, 198, 55, 53, 9, 7, NULL, 'Hank', 0);
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

INSERT INTO mounts VALUES (1, 'Pony', 'This docile beast is young yet.', 'Horses', 'a:5:{s:4:"name";s:13:"`&Pony Attack";s:8:"roundmsg";s:26:"Your pony fights with you!";s:6:"rounds";s:2:"20";s:6:"atkmod";s:3:"1.2";s:8:"activate";s:7:"offense";}', 6, 0, 1, 1, 0, 'You strap your {weapon} to your pony\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your pony, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your pony to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your pony whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your pony trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`0', 0, 0, 0, '', '', '');
INSERT INTO mounts VALUES (2, 'Gelding', 'This powerful beast is fiercely loyal.', 'Horses', 'a:5:{s:4:"name";s:16:"`&Gelding Attack";s:8:"roundmsg";s:29:"Your gelding fights with you!";s:6:"rounds";s:2:"40";s:6:"atkmod";s:3:"1.2";s:8:"activate";s:7:"offense";}', 10, 0, 1, 2, 1, 'You strap your {weapon} to your gelding\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your gelding, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your gelding to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your gelding whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your gelding trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`n`nAnd here you thought geldings weren\'t equipped that way any longer!`0', 0, 0, 0, '', '', '');
INSERT INTO mounts VALUES (3, 'Stallion', 'This noble beast is huge and powerful!', 'Horses', 'a:5:{s:4:"name";s:17:"`&Stallion Attack";s:8:"roundmsg";s:30:"Your stallion fights with you!";s:6:"rounds";s:2:"60";s:6:"atkmod";s:3:"1.2";s:8:"activate";s:7:"offense";}', 16, 0, 1, 3, 1, 'You strap your {weapon} to your stallion\'s saddle bags, and head out for some adventure!', '`&Remembering that is has been quite some time since you last fed your stallion, you decide this is a perfect time to relax and allow it to graze the field a bit. You doze off enjoying this peaceful serenity.`0', '`&You dismount in the field to allow your stallion to graze for a moment even though it has recently been fully fed.  As you lean back in the grass to watch the clouds, your stallion whickers softly and trots off into the underbrush.  You search for a while before returning to the fields hoping that it\'ll return.  A short time later, your stallion trots back into the clearing holding its head high, looking much more energized and with a very equine grin on its face.`0', 0, 0, 0, '', '', '');

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
