<?php
// addnews ready
// mail ready
// translator ready
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

$sql = array(
	"CREATE TABLE ".db_prefix("riddles")." (id int(11) NOT NULL auto_increment, riddle mediumtext NOT NULL, answer tinytext NOT NULL, author varchar(255) NOT NULL DEFAULT 'System', PRIMARY KEY  (id)) TYPE=InnoDB;",
#--
#-- Dumping data for table '".db_prefix("riddles")."'
#--
	"INSERT INTO ".db_prefix("riddles")." VALUES (1,'You eat something you neither plant nor plow.`nIt is the son of water, but if water touches it, it dies.','Salt','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (2,'Teacher, open thy book.','A butterfly','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (3,'My tines are long.`nMy tines are short.`nMy tines end ere`nMy first report.','Lightning; lightning bolt','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (4,'Turn us on our backs`nAnd open up our stomachs`nYou will be the wisest of men`nThough at start a lummox.','A book','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (5,'Bury deep,`nPile on stones,`nYet I will`nDig up the bones.','Memories','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (6,'It occurs once in every minute`nTwice in every moment`nAnd yet never in one hundred thousand years.','The letter \'M\'; M','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (7,'Never ahead, ever behind,`nYet flying swiftly past;`nFor a child I last forever,`nFor adults I\'m gone too fast.','Childhood;youth','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (8,'Two horses, swiftest travelling,`nHarnessed in a pair, and`nGrazing ever in places`nDistant from them.','Your eyes; the sun and the moon','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (9,'It can be said:`nTo be gold is to be good;`nTo be stone is to be nothing;`nTo be glass is to be fragile;`nTo be cold is to be cruel.`nUnmetaphored, what am I?','A heart','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (10,'Round she is, yet flat as a board`nAltar of the Lupine Lords.`nJewel on black velvet, pearl in the sea`nUnchanged but e\'erchanging, eternally.','The moon','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (11,'It has a golden head`nIt has a golden tail`nbut it hasn\'t got a body.','A golden coin; coin','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (12,'Speak, friend, and enter!','Friend','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (13,'A leathery snake,`nWith a stinging bite,`nI\'ll stay coiled up,`nUnless I must fight.','A whip','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (14,'What has roots as nobody sees,`nIs taller than trees,`nUp, up it goes,`nAnd yet never grows?','A mountain','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (15,'Thirty white horses on a red hill,`nFirst they champ,`nThen they stamp,`nThen they stand still.','Your teeth','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (16,'Voiceless it cries,`nWingless it flutters,`nToothless bites,`nMouthless mutters.','The wind','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (17,'It cannot be seen, cannot be felt,`nCannot be heard, cannot be smelt.`nIt lies behind stars and under hills,`nAnd empty holes it fills.`nIt comes first and follows after,`nEnds life, kills laughter.','The darkness','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (18,'A box without hinges, key, or lid,`nYet golden treasure inside is hid.','Eggs; oranges','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (19,'Alive without breath,`nAs cold as death;`nNever thirsty, ever drinking,`nAll in mail never clinking.','A fish','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (20,'This thing all things devours:`nBirds, beast, trees, flowers;`nGnaws iron, bites steel;`nGrinds hard stones to meal;`nSlays king, ruins town,`nAnd beats high mountain down.','Time','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (21,'You feel it, but never see it and never will.','Your heart','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (22,'You must keep it after giving it.','Your word;promise','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (23,'As light as a feather, but you can\'t hold it for ten minutes.','Your breath','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (24,'Has a mouth but does not speak, has a bed but never sleeps.','A river','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (25,'Runs smoother than any rhyme, loves to fall but cannot climb!','Water','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (26,'You break it even if you name it!','Silence','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (27,'It passes before the sun and makes no shadow.','The air','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (28,'You feed it, it lives, you give it something to drink, it dies.','A fire','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (29,'A red drum which sounds`nWithout being touched,`nAnd grows silent,`nWhen it is touched.','Your heart','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (30,'A harvest sown and reaped on the same day`nIn an unplowed field,`nWhich increases without growing,`nRemains whole though it is eaten`nWithin and without,`nIs useless and yet`nThe staple of nations.','A war','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (31,'If you break me`nI do not stop working,`nIf you touch me`nI may be snared,`nIf you lose me`nNothing will matter.','Hope','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (32,'All about, but cannot be seen,`nCan be captured, cannot be held`nNo throat, but can be heard.','Sounds; noises; wind','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (33,'I go around in circles,`nBut always straight ahead`nNever complain,`nNo matter where I am led.','A wheel','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (34,'Lighter than what`nI am made of,`nMore of me is hidden`nThan is seen.','An iceberg','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (35,'If a man carried my burden,`nHe would break his back.`nI am not rich,`nBut leave silver in my track.','A snail','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (36,'My life can be measured in hours,`nI serve by being devoured.`nThin, I am quick`nFat, I am slow`nWind is my foe.','A candle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (37,'Weight in my belly,`nTrees on my back,`nNails in my ribs,`nFeet I do lack.','A boat; a cave; a ship','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (38,'You can see nothing else`nWhen you look in my face`nI will look you in the eye`nAnd I will never lie.','A mirror','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (39,'I am always hungry,`nI must always be fed,`nThe finger I lick`nWill soon turn red.','A fire','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (40,'Three lives have I.`nGentle enough to soothe the skin,`nLight enough to caress the sky`nHard enough to crack rocks.','Water','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (41,'Glittering points`nThat downward thrust,`nSparkling spears`nThat never rust.','Icicles','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (42,'Each morning I appear`nTo lie at your feet,`nAll day I follow`nNo matter how fast you run,`nYet I nearly perish`nIn the midday sun.','A shadow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (43,'Keys without locks`nYet I unlock the soul.','A piano; a harpsichord; music; song; notes','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (44,'I am so simple,`nThat I can only point`nYet I guide men`nAll over the world.','A compass','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (45,'For our ambrosia we were blessed,`nby Jupiter, with a sting of death.`nThough our might, to some is jest,`nwe have quelled the dragon\'s breath.`nWho are we?','Bees','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (46,'Colored as a maiden tweaked,`ntime was naught when I began;`nthrough the garden I was sneaked,`nI alone am the fall of man.`nWhat am I?','An apple','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (47,'One where none should be,`nor maybe where two should be,`nseeking out purity,`nin the kings trees.`nWhat am I?','A unicorn','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (48,'One tooth to bite,`nhe\'s the forests foe.`nOne tooth to fight,`nas all Norse know.`nWhat is it?','An axe','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (49,'The part of the bird`nthat is not in the sky,`nwhich can swim in the ocean`nand always stay dry.`nWhat is it?','The bird\'s shadow; shadow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (50,'The root tops the trunk`non this backward thing,`nthat grows in the winter`nand dies in the spring.`nWhat is it?','An icicle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (51,'Touching one, yet holding two,`nit is a one link chain`nbinding those who keep words true,`n\'til death rent it in twain.`nWhat is it?','A wedding ring; ring','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (52,'The wise and knowledgeable man is sure of it.`nEven the fool knows it.`nThe rich man wants it.`nThe greatest of heroes fears it.`nYet the lowliest of cowards would die for it.`nWhat is this upon which I ponder?','Nothing; something','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (53,'What is greater than God,`nWorse than the Devil,`nDead man eat it,`nIf you eat it you\'ll die.','Nothing','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (54,'I am a wonderful help to women,`nThe hope of something to come. I harm`nNo citizen except my slayer.`nRooted I stand on a high bed.`nI am shaggy below. Sometimes the beautiful`nPeasant\'s daughter, an eager-armed,`nProud woman grabs my body,`nRushes my red skin, holds me hard,`nClaims my head. The curly-haired`nWoman who catches me fast will feel`nOur meeting. Her eye will be wet.','An onion','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (55,'Power and treasure for a prince to hold,`nHard and steep-cheeked, wrapped in red`nGold and garnet, ripped from a plain`nOf bright flowers, wrought - a remnant`nOf fire and file, bound in stark beauty`nWith delicate wire, my grip makes`nWarriors weep, my sting threatens`nThe hand that grasps gold. Studded`nWith a ring, I ravage heir and heirloom.`nTo my lord and foes always lovely`nAnd deadly, altering face and form.','A sword','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (56,'As I was going to St. Ives,`nI met a man with seven wives;`nEvery wife had seven sacks,`nEvery sack had seven cats,`nEvery cat had seven kits:`nKits, cats, sacks, and wives,`nHow many were there going to St. Ives?','One;1','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (57,'Dawns away,`nThe day\'s turned grey,`nAnd I must travel far away.`nBut I\'ll be back,`nAnd then we\'ll track,`nThe light of yet another day.','The sun; a shadow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (58,'Deep, dark, underground,`nThat is the place where I\'ll be found.`nYet brought into the light of day,`nI sprinkle sunlight every-which-a-way.`nThough dulled with oil I will be found,`nI am remarkably well and throughly sound.`nCut me quick and it will be seen,`nThat I instantly have a marvelous sheen.','A diamond; a gem','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (59,'What must be in the oven yet can not be baked?`nGrows in the heat yet shuns the light of day?`nWhat sinks in water but rises with air?`nLooks like skin, but is fine as hair?','Yeast','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (60,'Little Johnny Walker,`nMy, but he was a talker!`nYet nary a word did he say!`nWhen I took him out,`nThen they would all point and shout!`nAnd ask that I put him away.','Your opinions','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (61,'They are many and one,`nThey wave and they drum,`nUsed to cover a stare,`nThey go with you everywhere.','Your hands','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (62,'Stomp, stomp,`nChomp, chomp,`nRomp, romp.`nStanding still,`nall in gear.','Horses','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (63,'Sweet tooth,`nAh shoot,`nAll gone,`nWe all long,`nFor another piece of it.','Candy','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (64,'It comes in on little cat\'s feet,`nIs neither sour, nor sweet.`nHovers in the air,`nAnd then is not there.','Fog; mist','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (65,'A laugh,`nA cry,`nA moan,`nA sigh.','Emotions','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (66,'What is it you have to answer?`nBut to answer you have to ask?`nAnd to ask you have to speak?`nAnd to speak you have to know,`nThe answer.','A riddle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (67,'I can hit you in the eye,`nYet twinkle in the sky,`nExpanding when I die,`nWhat do you think am I?','A star','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (68,'Squishes,`nSquashes,`nWishes I washes,`nCan get it in my hair,`nMakes me not look too fair.','Mud','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (69,'Up a hill,`nDown a hill,`nOver them I may roam,`nBut after all my walking,`nThere\'s no place like my own.','Your home','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (70,'This thing is a most amazing thing.`nFor it can be both as sharp as a knife,`nOr as flat as a floor.`nAnd yet, for all that it can be,`nIt is as natural as a bee.','Music; Notes','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (71,'Deep, deep, do they go.`nSpreading out as they go.`nNever needing any air.`nThey are sometimes as fine as hair.','Roots','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (72,'Oh Lord!  I am not worthy!`nI bend my limbs to the ground.`nI cry, yet without a sound.`nLet me drink of waters deep.`nAnd in silence I will weep.','A willow; weeping willow; willow tree; weeping willow tree','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (73,'Shifting, Shifting, Drifting deep.`nBelow me great and mighty cities sleep.`nSwirling, Scurlling, All around.`nI\'m only where no water will be found.','The desert','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (74,'I bubble and laugh`nAnd spit water in your face.`nI am no lady,`nAnd I don\'t wear lace.','A fountain','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (75,'What has wings,`nBut can not fly.`nIs enclosed,`nBut can outside also lie.`nCan open itself up,`nOr close itself away.`nIs the place of kings and queens,`nAnd doggerel of every means.`nWhat is it upon which I stand?`nWhich can lead us to different lands.','A stage','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (76,'Do not begrudge this,`nFor it is the fate of every man.`nYet it is feared,`nAnd shunned in many lands.`nCauses problems, and sometimes gaps,`nCan hobble the strongest, and make memory lapse.`nWhat is this danger we all face?`nFor being a part - of the human race.','Growing old; age; old age','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (77,'Of these things - I have two.`nOne for me - and one for you.`nAnd when you ask about the price,`nI simply smile and nod twice.','Sharing','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (78,'I am a strange creature,`nHovering in the air,`nMoving from here to there,`nWith a brilliant flare.`nSome say I sing,`nBut others say I have no voice.`nSo I just hum - as a matter of choice.`nWhat am I?','A hummingbird','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (79,'Sleeping during the day,`nI hide away.`nWatchful through the night,`nI open at dawn\'s light.`nBut only for the briefest time,`nDo I shine.`nAnd then I hide away,`nAnd sleep through the day.','A morning glory','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (80,'Looks like water,`nBut its heat.`nSits on sand,`nLays on concrete.`nPeople have been known,`nTo follow it everywhere.`nBut it gets them no place,`nAnd all they can do is stare.','A mirage','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (81,'A part of heaven,`nThough it touches the earth.`nSome say it\'s valuable,`nOthers - no worth.','A rainbow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (82,'I stand,`nAnd look across the sea,`nWith its waves, crests, troughs, and valleys.`nI stride,`nAcross this water, my horse following after,`nAnd while it laps against his withers,`nAnd brushes against my thighs,`nI fill the emptiness with laughter.`nAnd he - with his sighs.`nWhether do we go?`nOr do we go at all?`nOr are we simply out here wading,`nTo the next port of call.`nWhere the sea ends,`nWhere the loam lays firm beneath my feet,`nAnd I can mount my steed again,`nAnd continue til next we meet.`nWhat is really being talked about?','The open plains; plain; prairie','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (83,'I was born blind,`nAnd could not see,`nUntil it was a quarter of three.`nI could not smile,`nTil half past six,`nAnd all of my arms and legs`nWere made of sticks.','A doll','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (84,'Ah!  My breath doth shake,`nMy limbs are thin,`nMy belly aches.`nWhiteness doth crown my head,`nAnd the tracks I leave,`nAre unsteady where I\'ve led.`nI look out through rheumy eyes,`nAnd seem to say my last goodbyes.`nThe darkness doth draw me near,`nI lean towards it - the better to hear.','Old age; age','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (85,'Hick-a-more, Hack-a-more,`nOn the King\'s kitchen door.`nAll the King\'s horses,`nAnd all the King\'s men,`nCouldn\'t get Hick-a-more, Hack-a-more,`nOff the King\'s kitchen door.','Sunlight','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (86,'It was asked of me what I could be made,`nAnd so people were fed from me.`nIt was asked of me what I could be made,`nAnd so houses were built.`nIt was asked of me what I could be made,`nAnd so things were written.`nIt was asked of me what I could be made,`nAnd so I fertilized the ground.`nBut when asked more of what I could be made,`nThere was nothing to be found.','A tree','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (87,'With this you can do wonderous things.`nLook at things close, or far away,`nYou can see things big,`nOr you can see things small.`nOr maybe you don\'t see things at all.`nI come in many colors and hues,`nSometimes green and sometimes blue.`nAnd when I\'m red - it\'s not from shame,`nBut from something with a different name.','Your eyes','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (88,'Oh how I love my dancing feet!`nThey stay together - oh so neat.`nAnd when I want to walk a line,`nThey all stay together and do double time.`nI count them up, ten times or more,`nAnd race on-off, across the floor.','A centipede; millipede','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (89,'A muttered rumble was heard from the pen,`nAnd I, in my walking, stopped to look in.`nWhat was this I saw?`nA massive beast, hooved, and jawed.`nWith spikes upon its mighty brow,`nI watched as he struck the turf and prowled.`nAnd yet for all of his magnificience,`nHe couldn\'t get out of that wooden fence.','A bull','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (90,'Twas the night of the day`nin which I must relay`nthat in which I took part in.`nFor the sun was out`nand without so much as a shout`nhe quietly went in.`nTwas ever so queer`nI thought he would leer`nbut never a word did I get in.`nFor without another word`n(at least that\'s what I heard)`nHe was back to the place he\'d been in.','An eclipse','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (91,'From sun up to sun down I stare out across the sea.`nFrom sun down to sun up I stare out across the sea.`nBut while with sun up I can only blink in the brightness.`nWith the sun down I can blink out the brightness.','A lighthouse','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (92,'A lot of bark,`nBut no one notices.`nA lot to bite,`nAnd everyone cares.`nI\'m not a dog,`nIf anyone notices.`nAnd there\'s a lot to me,`nBut I don\'t have hair.`nI stand up straight,`nIf you\'ve noticed me.`nI\'ve got lots of limbs,`nIf anyone cares.`nI can give you shade,`nIf you\'ve noticed it.`nAnd I do even more,`nI give you air.','A tree','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (93,'Twas in December or June,`nWhen my lady did swoon.`nWhen her hair did fall off,`nAnd her glasses were lost.`nWhen she did scream,`nIn a manner most obscene.`nWhile pointing at me,`nAnd saying �Eeeeee!  Eeeeee!�`nI must say it was all a bit much,`nSince no one did I touch.`nBut it was quite apparent,`nThat something was errant.`nSo I decided to come back another day,`nWhen, mayhap, she was away.','A mouse','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (94,'I drift,`nAs slowly as a lazy river.`nI dance,`nUpon as little as a puff of air.`nI tumble,`nBetter than the greatest acrobat.`nSwirling,`nTwirling,`nDown to the ground.`nWhere I lie,`nTil I get my second wind.`nSo I can begin again.','A leaf','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (95,'A riddle, easily solved.`nRed breasted.`nOnly one in a field of many.`nBorn in an egg.`nInspired to sing.`nNow gather the letters and tell me what I mean.','A robin','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (96,'I have four of these,`nWith matching extremities.`nThey can do many things,`nAnd hardly ever bring me pain.`nUnless I stick them with a pin,`nOr burn them sometimes when...`nWhat is it that I can wiggle at will?`nAnd use in other means still?','Fingers','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (97,'When I looked upon the flames of his passion,`nAnd the coolness of her touch,`nI knew tragedy could only come from their union.`nAnd indeed, when they came together,`nDarkness reigned upon the land.`nAnd although they were soon separated,`nLearning as they did that they were not for each other,`nStill, their passing regards for each other,`nLeft it\'s impression upon all who had witnessed it.`nAnd would be talked about for ages still to come.','A solar eclipse; eclipse','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (98,'What has a coat?`nHugs you not in sympathy?`nWhose smile you\'d rather not see?`nWhose stance is a terrible thing to see?`nWho is it that brave men run away from?`nWhose fingers are clawed?`nWhose sleep lasts for months?`nAnd whose company we shun?','A bear','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (99,'You can tumble in it,`nRoll in it,`nBurn it,`nAnimals eat it,`nUsed to cover floors,`nStill used beyond stall doors.`nFreshens whatever it is placed on,`nAbsorbs whatever is poured into it.`nWhat is it?','Hay','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (100,'Within passion\'s fruit they will be found,`nAnd more of them in the pomegranate\'s crown.`nRowed they are within an apple\'s core,`nYet other fruits have them more.`nAnd though the nectarine has but one,`nStill, this is all just in fun.`nPlaying hide and seek - a children\'s game.`nFinding out each player is just the same.','Seeds','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (101,'\'Twas whispered in Heaven, \'twas muttered in hell,`nAnd echo caught faintly the sound as it fell;`nOn the confines of earth \'twas permitted to rest,`nAnd in the depths of the ocean its presence confes\'d;`n\'Twill be found in the sphere when \'tis riven asunder,`nBe seen in the lightning and heard in the thunder;`n\'Twas allotted to man with his earliest breath,`nAttends him at birth and awaits him at death,`nPresides o\'er his happiness, honor and health,`nIs the prop of his house, and the end of his wealth.`nIn the heaps of the miser \'tis hoarded with care,`nBut is sure to be lost on his prodigal heir;`nIt begins every hope, every wish it must bound,`nWith the husbandman toils, and with monarchs is crowned;`nWithout it the soldier and seaman may roam,`nBut woe to the wretch who expels it from home!`nIn the whispers of conscience its voice will be found,`nNor e\'er in the whirlwind of passion be drowned;`n\'Twill soften the heart; but though deaf be the ear,`nIt will make him acutely and instantly hear.`nSet in shade, let it rest like a delicate flower;`nAh!  Breathe on it softly, it dies in an hour','The letter H; H','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (102,'We are little airy creatures,`nAll of different voice and features;`nOne of us in glass is set,`nOne of us you\'ll find in jet,`nT\'other you may see in tin,`nAnd the fourth a box within;`nIf the fifth you should pursue,`nIt can never fly from you.','The vowels','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (103,'I\'m a strange contradiction; I\'m new, and I\'m old,`nI\'m often in tatters, and oft decked with gold.`nThough I never could read, yet lettered I\'m found;`nThough blind, I enlighten; though loose, I am bound,`nI\'m always in black, and I\'m always in white;`nI\'m grave and I\'m gay, I am heavey and light-`nIn form too I differ - I\'m thick and I\'m thin,`nI\'ve no flesh and bones, yet I\'m covered with skin;`nI\'ve more points than the compass, more stops than the flute;`nI sing without voice, without speaking confute.`nI\'m English, I\'m German, I\'m French, and I\'m Dutch;`nSome love me too fondly, some slight me too much;`nI often die soon, though I sometimes live ages,`nAnd no monarch alive has so many pages.','A book','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (104,'As I went through the garden gap,`nWho should I meet but Dick Red-cap!`nA stick in his hand, a stone in his throat,`nIf you\'ll tell me this riddle, I\'ll give you a groat.','A cherry','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (105,'Little Nancy Etticote,`nIn a white petticoat,`nWith a red nose;`nThe longer she stands`nThe shorter she grows.','A candle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (106,'I have a little sister, they call her Peep, Peep;`nShe wades the waters deep, deep, deep;`nShe climbs the mountains high, high, high;`nPoor little creature she has but one eye.','A star','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (107,'I saw a company a marching,`nA marching across the sea.`nAnd looking upon them,`nI asked myself �What can they be?�`nFor there was a horse,`nAnd there was a cow,`nAnd there were men marching,`nWith houses and trees.  But how?`nI saw a company marching,`nA marching across the sea.`nAnd wondered in my rest,`nHow lazy I must be.','Clouds in the sky; clouds','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (108,'I\'m up.`nI\'m down.`nI\'m all around.`nYet never can I be found.`nWho am I?','The wind','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (109,'I can be moved.`nI can be rolled.`nBut nothing will I hold.`nI\'m red and I\'m blue.`nAnd I can be other colors too.`nHaving no head, though similar in shape,`nI have no eyes - yet move all over the place.`nWhat am I?','A ball','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (110,'Upon me you can tread,`nThough softly under cover.`nAnd I will take you places,`nThat you have yet to discover.`nI\'m high, and I\'m low,`nThough flat in the middle.`nAnd though a joy to the children,`nAdults think of me little.`nWhat am I?','Stairs','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (111,'What is it which builds things up?`nLays mountains low?`nDries up lakes,`nAnd makes things grow?`nCares not a whim about your passing?`nAnd is like few other things,`nBecause it is everlasting?','Time','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (112,'It sat upon a willow tree,`nAnd sang softly unto me.`nEasing my pain and sorrow with its song,`nI wished to fly, but tarried long.`nAnd in my suffering,`nThe willow was like a cool clear spring.`nWhat was it that helped me so?`nTo spend my time in my woe.','A bird','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (113,'They can be harbored, but few hold water,`nYou can nurse them, but only by holding them against someone else,`nYou can carry them, but not with your arms,`nYou can bury them, but not in the earth.','A grudge','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (114,'Deep as a bowl, round as a cup,`nYet all the world\'s oceans can\'t fill it up.','A sieve; collander','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (115,'Though desert men once called me God,`nTo-day men call me mad,`nFor I wag my tail when I am angry,`nAnd growl when I am glad.','A cat','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (116,'I heard of an invading, vanquishing army`nsweeping across the land, liquid-quick;`nconquering everything, quelling resistance.`nWith it came darkness, dimming the light.`nHumans hid in their houses, while outside`nspears pierced, shattering stone walls.`nUncountable soldiers smashed into the ground,`nbut each elicited life as he died;`nwhen the army had vanished, advancing northward,`nthe land was green and growing, refreshed.','A rainstorm; rain','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (117,'Tall she is, and round as a cup,`nYet all the king\'s horses`nCan\'t draw her up.','A well','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (118,'The more of it there is,`nThe less you see.','Darkness; fog','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (119,'What is not enough for one,`nJust right for two,`nToo much for three?','A secret','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (120,'What gets wetter the more it dries?','A towel','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (121,'A long snake`nWith a stinging bite,`nI stay coiled up`nUnless I must fight.','Whip','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (122,'A warrior amongst the flowers,`nHe bears a thrusting sword.`nAble and ready to use,`nTo guard his golden hoard.','Bees','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (123,'The Load-bearer, the Warrior,`nThe Frightened One, the Brave,`nThe Fleet-of-foot, the Ironshod`nThe Faithful One, the Slave','A horse','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (124,'Walks in the wind`nRuns in the rain`nMakes dry oceans in the sun`nCounts time, stops clocks`nSwallows kingdoms, gnaws rocks.','Sand','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (125,'The rolling hills, the heart that beats forever,`nThe land that never changes, never stills`nPloughed by travellers far from home, not planted,`nWhite in anger, green in peace, and always blue.','The sea; ocean','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (126,'Listen closely, I\'m hard to understand`nI am as elusive as is a handful of sand.`nEven if you perceive me, you know me not`nbefore you can tell me, what I have forgot.','A riddle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (127,'What goes through the door without pinching itself?`nWhat sits on the stove without burning itself?`nWhat sits on the table and is not ashamed?','The sun','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (128,'Whilst I was engaged in sitting`nI spied the dead carrying the living`nWhat did I see?','A ship','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (129,'I know a word of letters three,`nAdd two and fewer there will be.','Few','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (130,'Who makes it, has no need of it.`nWho buys it, has no use for it.`nWho uses it, can neither see nor feel it.','A coffin','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (131,'The man who made it didn\'t need it.`nThe man who bought it didn\'t use it.`nThe man who used it didn\'t want it.','A coffin; a casket','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (132,'You seized me, and yet I fled`nYou see me flee and cannot hold me tight`nYou press me in your hand, then your fist is empty.`nWhat am I?','Snow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (133,'What has four legs in the morning,`nTwo legs in the afternoon,`nAnd three legs in the evening?','Man','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (134,'What is deaf, dumb and blind`nand always tells the truth ?','A mirror','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (135,'What is always in front of you`nbut cannot be seen?','The future','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (136,'What does man love more than life,`nhate more than death or mortal strife;`nThat which contented men desire,`nthe poor have, the rich require;`nThe miser spends, the spendthrift saves,`nand all men carry to their graves?','Nothing','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (137,'A life longer than any man,`nit dies each year to be reborn.','A tree','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (138,'In the eyes it causes blindness,`nin the nose just a sneeze;`nYet some suck this down,`nand act as if pleased.','Smoke','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (139,'It stands alone, with no bone or solid form.`nAdamant, it prospers never wrong,`nthough hurt it may.`nTwistable, malleable, might it be,`nbut always straight as an arrow.','The truth','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (140,'What sphinxes employ,`nthe players enjoy.','A riddle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (141,'There\'s someone that I\'m always near,`nYet in the dark I disappear.`nTo this one only I am loyal,`nThough in his wake I\'m doomed to toil.`nHe feels me not (we always touch);`nIf I were lost, he\'d not lose much.`nAnd now I come to my surprise,`nFor you are he - but who am I ?','Your shadow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (142,'I\'m often held, yet rarely touched;`nI\'m always wet, yet never rust;`nI\'m sometimes wagged and sometimes bit;`nTo use me well, you must have wit.','Tongue','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (143,'In the window she sat weeping.`nAnd with each tear her life went seeping.','A candle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (144,'I\'m not really more than holes tied to more`nholes;`nI\'m strong as good steel, though not as stiff`nas a pole.','A chain','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (145,'I\'ve little strength, but mighty powers;`nI guard small hovels and great towers.`nBut if perchance my master leaves,`nHe must ensure he safeguards me.','A key','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (146,'Delivered by breath,`nscares heroes to death.','The Riddle','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (147,'In daytime I lie pooled about,`nAt night I cloak like a mist.`nI creep inside shut boxes and`nInside your tightened fist.`nYou see me best when you can\'t see,`nFor I do not exist.','Darkness','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (148,'Devils and rogues know nothing else,`nsave starlight.','Darkness','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (149,'Both king and horse have this, of course,`nBut you\'ll want neither of them, perforce.','Reign','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (150,'My spring up on the cliff.','A coconut','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (151,'Three walls and you reach water.','A coconut','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (152,'My kapa (a type of cloth) log that`nis always sounding without rest.','The sea','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (153,'In the morning four legs,`nat noon two legs,`nat evening three legs.','Man','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (154,'My man that cannot be cut.','A shadow','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (155,'My canoes, going day and night,`nten bowspirits, two sterns.','Someone\'s feet','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (156,'My red cave, white soldiers standing in line.','Someone\'s mouth','System');",
	"INSERT INTO ".db_prefix("riddles")." VALUES (157,'My man crying day and night,`nall through the year.','The sea','System');",
);

foreach ($sql as $statement) {
	db_query($statement);
}
