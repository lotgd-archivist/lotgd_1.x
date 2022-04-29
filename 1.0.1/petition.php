<?php
// addnews ready
// translator ready
// mail ready
define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);
require_once("common.php");
require_once("lib/systemmail.php");
require_once("lib/output_array.php");
require_once("lib/http.php");

$op = httpget('op');

if ($op=="primer"){
	tlschema("faq");
	popup_header("New Player Primer");
	$c = translate_inline("Return to Contents");
	rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
	output("`n`n`^Welcome to the Legend of the Green Dragon New Player Primer`n`n");
	output("`^`bThe village square`b`n");
	output("`@Legend of the Green Dragon (LotGD) is turning out to be a fairly expansive game, with a lot of areas to explore.");
	output("It's easy to get lost with all that there is to do out there, so keep in mind that the village square is pretty much the center of the village you start in.");
	output("This area will give you access to most other areas that you can get to, with a few exceptions (we'll talk about those in a little while).");
	output("If you ever get lost, or are not sure what's going on, head to the village square and regain your bearings.`n`n");
	output("`^`bYour first day`b`n");
	output("`@Your first day in the world can be very confusing!");
	output("You're presented with a lot of information, and you don't need almost any of it!  It's true!");
	output("One thing you should probably keep an eye on though, are your hit points.  This is found under \"Vital Info.\"");
	output("No matter what profession you choose, in the end, you are some kind of warrior or fighter, and so you need to learn how to do battle.");
	output("The best way to do this is to look for creatures to kill in the forest.");
	output("When you find one, check it out, and make sure that it's not a higher level than you, because if it is, you might not live through the fight.");
	output("Keep in mind that you can always try to run away from something that you encountered, but sometimes it might take several tries before you get away.");
	output("You might want to buy armor and weapons in the village square in order to give yourself a better chance against these creatures out in the forest.`n`n");
	output("Once you have defeated a creature, you'll notice that you're probably a little hurt.");
	output("Head on over to the Healer's Hut, and you can get patched up in short order.");
	output("While you're level 1, healing is free, but as you advance, it becomes more and more expensive.");
	output("Also keep in mind that it's more expensive to heal 1 point, then later heal 1 point again than it is to heal 2 in one shot.");
	output("So if you're trying to save up some money, and you're barely hurt, you might risk a fight or two while you're a little hurt, and heal the damage from several fights in one shot.`n`n");
	output("After you've killed a few creatures, you should head back to the village, into Bluspring's Warrior Training, and talk to your master.");
	output("Your master will tell you when you are ready to challenge him, and when you are ready, you should give him a shot (make sure you're healed up first though!).");
	output("Your master won't kill you if you lose, instead he'll give you a complimentary healing potion and send you on your way.");
	if (getsetting("multimaster",1) == 0) {
		output(" You can only challenge your master once a day.");
	}
	output("`n`n`^`bDeath`b`n");
	output("`@Death is a natural part of any game that contains some kind of combat.");
	output("In Legend of the Green Dragon, being dead is only a temporary condition.");
	output("When you die, you'll lose any money that you had on hand (money in the bank is safe!), and some of the experience you've accumulated.");
	output("While you're dead, you can explore the land of the shades and the graveyard.");
	output("In the graveyard, you'll find Ramius the Overlord of Death.");
	output("He has certain things that he would like you to do for him, and in return, he may grant you special powers or favors.");
	output("The graveyard is one of those areas that you can't get to from the main Square.");
	output("In fact, while you're dead, you can't go to the village square at all!`n`n");
	output("Unless you can convince Ramius to resurrect you, you'll remain dead until the next game day.");
	output("There are %s game days each real day.", getsetting("daysperday", 2));
	output("These occur when the clock in the village square reaches midnight.`n`n");
	output("`^`bNew Days`b`n");
	output("`@As stated just above, there are %s game days each real day.", getsetting("daysperday", 2));
	output("These occur when the clock in the village square reaches midnight.");
	output("When you get a new day, you'll be granted new forest fights, interest on gold you have in the bank (if the bankers are pleased with your performance!), and a lot of your other statistics will be refreshed.");
	output("You'll also be resurrected if you were dead, and get another chance to take on the world.");
	output("If you don't log on over the course of an entire game day, you'll miss your opportunity to partake in that game day (this means that new game days are only assigned when you actually log on, being away from the game for a few days won't grant you a whole bunch of new days).");
	output("Forest fights, PvP battles, special power usages and other things that get refreshed on a daily basis do NOT get carried over from one day to the next (you can't build up a whole bunch of them).`n`n");

	if (getsetting("pvp",1)){
		output("`^`bPvP (Player versus Player)`b`n");
		output("`@Legend of the Green Dragon contains a PvP element, where players can attack each other.");
		output("As a new player, you are protected from PvP for your first %s game days or until you accumulate %s experience, unless you choose to attack another player.", getsetting("pvpimmunity", 5), getsetting("pvpminexp", 1500));
		output("Some servers might have the PvP aspect turned off, in which case there is no chance that you'll be attacked by any other players.");
		output("You can tell if the server you play on has PvP turned off by looking in the village square for \"Slay Other Players.\"");
		output("If it's not there, you can't engage (or be engaged) in PvP.`n`n");
		output("When you are attacked and die in PvP, you only lose gold you had on hand, and %s%% of your experience.", getsetting("pvpdeflose", 5));
		output("You won't lose any turns in the forest, or any other stats.");
		output("If you attack someone else in PvP, you'll get %s%% of the experience they had, and any gold they had on hand.", getsetting("pvpattgain", 10));
		output("If you attack someone else and lose, however, you'll lose %s%% of your experience, and you'll lose any gold that you had on hand.", getsetting("pvpattlose", 15));
		output("If someone else attacks you and they lose, you'll gain the gold they had on hand, and %s%% of their experience.", getsetting("pvpdefgain", 10));
		output("You can only attack someone who is close to your level, so don't worry that as a level 1, some big level 15 player is going to come along and beat on you.`n`n");
		output("If you buy a room in the inn when you decide to quit the game, you'll protect yourself somewhat from casual attacking.");
		output("The only way for someone to attack you when you're in the inn is for them to bribe the bartender, which can be a costly procedure.");
		output("Quitting to the fields means that someone can attack you without having to pay money or gems to the bartender.");
		output("You cannot be attacked while you are online, only while you are offline, so the more  you play, the more protected you are ;-).");
		output("Also, if you are attacked and die, no one else can attack you again until you log on again, so don't worry that you'll be attacked 30 or 40 times in one night.");
		output("Logging back into the game will make you a viable PvP target again if you've already been killed today.`n`n");
	}
	output("`^`bReady to take on the world!`b`n");
	output("`@You should now have a pretty good idea of how the basics of the game work, how to advance, and how to protect yourself.");
	output("There's a whole lot more to the world, so explore it!");
	output("Don't be afraid of dying, particularly when you're young, as even when you're dead, there's yet more stuff to do!");
	rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
}elseif($op=="faq3"){
	tlschema("faq");
	popup_header("Specific and Technical Questions");
	$c = translate_inline("Return to Contents");
	rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
	output("`n`n`c`bSpecific and technical questions`b`c`n");
	output("`^1.a. How can I have been killed by another player while I was currently playing?`n");
	output("`@The biggest cause of this is someone who began attacking you while you were offline, and completed the fight while you were online.");
	output("This can even happen if you have been playing nonstop for the last hour.");
	output("When someone starts a fight, they are forced by the game to finish it at some point.");
	output("If they start a fight with you, and close their browser, the next time they log on, they will have to finish the fight.");
	output("You will lose the lesser of the gold you had on hand when they attacked you, or the gold on hand when they finished the fight.");
	output("So if you logged out with 1 gold on hand, they attack you, you log on, accumulate 2000 gold on hand, and they complete the fight, they will only come away from it with 1 gold.");
	output("The same is true if you logged out with 2000 gold, and when they completed killing you, you only had 1 gold.`n`n");
	output("`^1.b. Why did it say I was killed in the fields when I slept in the inn?`n");
	output("`@The same thing can happen where someone started attacking you when you were in the fields, and finished after you had retired to the inn for the day.");
	output("Keep in mind that if you are idle on the game for too long, you become a valid target for others to attack you in the fields.");
	output("If you're going to go away from your computer for a few minutes, it's a good idea to head to the inn for your room first so that you don't risk someone attacking you while you're idle.`n`n");
	output("`^2. The game tells me that I'm not accepting cookies, what are they and what do I do?`n");
	output("`@Cookies are little bits of data that websites store on your computer so they can distinguish you from other players.");
	output("Sometimes if you have a firewall it will block cookies, and some web browsers will let you block cookies.");
	output("Check the documentation for your browser or firewall, or look around in its preferences for settings to modify whether or not you accept cookies.");
	output("You need to at least accept session cookies to play the game, though all cookies are better.`n`n");
	output("`^3. What do`n&nbsp;&nbsp;`iWarning: mysql_pconnect(): Lost connection to MySQL server during query in /home/lotgd/public_html/dbwrapper.php on line 82`i`nand`n&nbsp;&nbsp;`iWarning: mysql_error(): supplied argument is not a valid MySQL-Link resource in /home/lotgd/public_html/dbwrapper.php on line 54`i`nmean?`n", true);
	output("`@It's a secret message from your computer telling you to stop staring at a screen and to go play outside.`n");
	output("Actually, it's a common temporary error, usually having to do with server load.");
	output("Don't worry about it, just reload the page (it may take a few tries).`n`n");
	output("`^4. Nothing is responding for hours now - what should I do ?`n");
	output("`@Go outside play a bit in Real Life (tm). When you get back it will work again - if not it's a serious problem.");
	output("Any server problems are caught less then 5 minutes after occurring, so if there is a problem, it's known - and we are working on it.");
	output("Every mail and ye olde mail reporting the same problem is just making it harder for us to work.`n`n");
	output("`^5. Why is the site giving me so many popups?`n");
	output("`@Please turn off your popup blocker. These aren't ads.`n");
	output("We use popup windows in the game for the following purposes:`n");
	output("a) To file a petition.`n");
	output("b) To write and receive Ye Olde Mail.`n");
	output("c) To make sure you see our newest Message of the Day (MoTD).`n");
	output("That last one is very important, since until you've viewed it the window will continue to try to open on every page load. These messages are for server announcements such as outages, current known bugs (which you really don't have to petition about, since we already know of them), and other things that the staff think you need to know about right away.`n`n");
	rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
}elseif ($op=="faq"){
	tlschema("faq");
	popup_header("Frequently Asked Questions (FAQ)");
	output("`^Welcome to Legend of the Green Dragon.`n`n");
	output("`@You wake up one day, and you're in a village for some reason.");
	output("You wander around, bemused, until you stumble upon the main village square.");
	output("Once there you start asking lots of stupid questions.");
	output("People (who are mostly naked for some reason) throw things at you.");
	output("You escape by ducking into a nearby building and find a rack of pamphlets by the door.");
	output("The title of the pamphlet reads: `&\"Everything You Wanted to Know About the LotGD, but Were Afraid to Ask.\"");
	output("`@Looking furtively around to make sure nobody's watching, you open one and read:`n`n");
	output("\"`#So, you're a Newbie.  Welcome to the club.");
	output("Here you will find answers to the questions that plague you.");
	output("Well, actually you will find answers to the questions that plagued US.");
	output("So, here, read and learn, and leave us alone!`@\"`n`n");
	output("`^`bContents:`b`0`n");

	modulehook("faq-pretoc");
	output("`^`bNew Player & FAQ`b`0`n");
	$t = translate_inline("`@New Player Primer`0");
	output_notl("&#149;<a href='petition.php?op=primer'>%s</a><br/>", $t, true);
	$t = translate_inline("`@Frequently Asked Questions on Game Play (General)`0");
	output_notl("&#149;<a href='petition.php?op=faq1'>%s</a><br/>", $t, true);
	$t = translate_inline("`@Frequently Asked Questions on Game Play (with spoilers)`0");
	output_notl("&#149;<a href='petition.php?op=faq2'>%s</a><br/>", $t, true);
	$t = translate_inline("`@Frequently Asked Questions on Technical Issues`0");
	output_notl("&#149;<a href='petition.php?op=faq3'>%s</a><br/>", $t, true);
	modulehook("faq-toc");
	modulehook("faq-posttoc");
	output("`nThank you,`nthe Management.`n");
}elseif($op=="faq1"){
	tlschema("faq");
	popup_header("General Questions");
	$c = translate_inline("Return to Contents");
	rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
	output("`n`n`c`bGeneral questions`b`c`n");
	output("`^1.  What is the purpose of this game?`n");
	output("`@To get chicks.`n");
	output("Seriously, though.  The purpose is to slay the green dragon.`n`n");
	output("`^2.  How do I find the green dragon?`n");
	output("`@You can't.`n");
	output("Well, sort of.");
	output("You can't find her until you've reached a certain level.");
	output("When you're at that level, it will be immediately obvious.`n`n");
	output("`^3.  How do I increase my level?`n");
	output("`@Send us money.`n");
	output("No, don't send money - you increase your experience by fighting creatures in the forest.");
	output("Once you've gotten enough experience, you can challenge your master in the village.`n`n");
	output("Well, you can send us money if you want (see PayPal link).`n`n");
	output("`^4.  Why can't I beat my master?`n");
	output("`@He's far too wiley for the likes of you.`n");
	output("Did you ask him if you have enough experience?`n");
	output("Have you tried purchasing some armor or weapons in the village?`n`n");
	output("`^5.  I used up all my turns.   How do I get more?`n");
	output("`@Send money.`n");
	output("No, put your wallet away.");
	output("There *are* a few ways to get an extra turn or two, but by and large you just have to wait for tomorrow.");
	output("When a new day comes you'll have more energy.`n");
	output("Don't bother asking us what those few ways are - some things are fun to find on your own.`n`n");
	output("`^6.  When does a new day start?`n");
	output("`@Right after the old one ends.`n`n");
	output("`^7.  Arghhh, you guys are killing me with your smart answers - can't you just give me a straight answer?`n");
	output("`@Nope.`n");
	output("Well, okay, new days correspond with the clock in the village (can also be viewed from other places).");
	output("When the clock strikes midnight, expect a new day to begin.");
	output("The number of times a clock in LotGD strikes midnight per calendar day may vary by server.");
	output("Beta server has 4 play days per calendar day, main server at LotGD.net has 2.");
	output("Other servers depend on the admin.`n");
	output("This server has %s days per calendar day.`n`n", getsetting("daysperday", 2));
	output("`^8.  Something's gone wrong!!!  How do I let you know?`n");
	output("`@Send money.");
	output("Better yet, send a petition.");
	output("A petition should not say 'this doesn't work' or 'I'm broken' or 'I can't log in' or 'yo.  Sup?'");
	output("A petition *should* be very complete in describing *what* doesn't work.");
	output("Please tell us what happened, what the error message is (copy and paste is your friend), when it occurred, and anything else that may be helpful.");
	output("\"`#I'm broken`@\" is not helpful.");
	output("\"`#There are salmon flying out of my monitor when I log in`@\" is much more descriptive as well as humorous, although there's not much we can do about it.");
	output("In general, please be patient with these requests - many people play the game, and as long as the admin is swamped with 'yo - Sup?' petitions, it will take some time to sift through them.`n`n");
	output("`^9.  What if all I have to say is 'yo - sup?'?`n");
	output("`@If you don't have something nice (or useful, or interesting, or creative that adds to the general revelry of the game) to say, don't say anything.`n");
	output("But if you do want to converse with someone, send them an email through Ye Olde Post Office.`n`n");
	output("`^10.  How do I use emotes?`n");
	output("`@Type :, ::, or /me before your text.`n`n");
	output("`^11.  What's an emote?`n");
	output("`&AnObviousAnswer punches you in the gut.`n");
	output("`@That's an emote.");
	output("You can emote in the village if you want to do an action rather than simply speaking.`n`n");
	output("`^12.  How do you get colors in your name?`n");
	output("`@Eat funny mushrooms.`n");
	output("No, put that mushroom away, colors are given out by a site's admin for a variety of reasons -- for example it might signify that the character was integral to the beta-testing process - finding a bug, helping to create creatures, etc, or being married to the admin (*cough*Appleshiner*cough*).");
	output("Check with your admins to find out how they grant colors.`n`n");
	output("`^13.  Sup dOOd, iz  it cool 2 uz  common IM wurds in the village?  Cuz u no, it's faster.  R u down wit that?`n");
	output("`@NO, for the love of Pete, use full words and good grammar, PLEASE!");
	output("These are not words: U, R, Ur, Cya, K, Kay, d00d, L8tr, sup, na and anything else like that!`n`n");
	output("`^14. Does that Curious Looking Rock have a purpose?`n");
	output("`@Of course it does! It confuses newbies!`n");
	output("Seriously, a wise man once said, \"`#Good things come to those who wait.`@\"");
	output("This should also be applied to the Rock.`n`n");
	output("`^15. Wow, there are mounts AND familiars in the stables! Can I have one of each?`n");
	output("`@(Oh for the love of... [we get this question a lot])`n");
	output("No! Not! Nix! Nada! You can only have one creature at a time.");
	output("Not two. Certainly not three. Four is right out. Five? You must be joking!");
	output("Now all together now, HOW many companion creatures can you have at a time?`n`n");
	output("`^16. Why not?`n");
	output("`@Because we're big meanies. Actually, the game's code just doesn't allow for that right now.");
	output("It might in the next version, then again it might not. Please stop asking!`n`n");
	output("`^17. What's with the <CLAN> thingies before peoples' names?`n");
	output("`#<`2CLAN`#> `&Clan Member ClanMember strikes you with the flat side of his weapon.`n");
	output("`3\"`@You dare claim ignorance of my clan's famous deeds? I am `\$ClanMember`@, a member of the mighty clan CLAN, which is short for Completely Ludicrous And Nonsensical! We have performed many a deed after having planned it in the privacy of our `%Clan Hall`@.");
	output("Our leaders and officers are among the mightiest in the land. Bolstered by the support of our friends and clanmates, we prevail!`3\"`n`n");
	output("`^18. I am so confused! What is going on in the village square/Garden/Inn/etc.?`n");
	output("`@A hubbub.`n");
	output("The fact is that because there's so many people, there can be several conversations happening at once in any given area.");
	output("Also, the thing to understand is that not all the comments are posted immediately, and that sometimes a person won't press the refresh or add button for several minutes, during which time more comments could have been said that the person missed while they were typing their own.");
	output("Not to worry, though. Don't be shy, join in!`n`n");
	rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
}elseif($op=="faq2"){
	tlschema("faq");
	popup_header("General Questions with Spoilers");
	$c = translate_inline("Return to Contents");
	rawoutput("<a href='petition.php?op=faq'>$c</a><hr>");
	output("`n`n`&(Warning, the FAQs below might contain some spoilers, so if you really want to discover things on your own, you'd be better off not reading too far.");
	output("This is not a manual.  It's a self-help pamphlet.)`n`n`n`n`n`n`n`n`n`n`n`n`n");
	output("`^1.  What are gems for?`n");
	output("`@Gems are magical portals into the future. Gaze into the depths of a gem and you can see that which is to come.`n");
	output("Gullible? We sure hope not!`n");
	output("Lots of people love those little gemstones and are willing to trade things for them.");
	output("Your local bartender is something of a gemologist.`n`n");
	output("`^2.  How do you get gems?`n");
	output("`@To the mines with you!!`n");
	output("Actually, you can't mine them.  (Well, you can, but only if you get lucky and find the mine.  Warning though, mines can be dangerous.)");
	output("Gems can be found in the forest during 'special events' that happen randomly - if you play often enough, you're bound to stumble across one at some point.");
	output("Gems can also be gained very occasionally from a forest fight.`n`n");
	output("`^3.  Why do some people seem to have so many hitpoints at a low level?`n");
	output("`@Cause they're bigger than you.`n");
	output("No, really, they *are* bigger than you. You'll be big too someday.`n`n");
	output("`^4.  Does that have something to do with the titles that people have?`n");
	output("`@But of course!`n");
	output("Indeed, every time you kill the dragon, you get a new title and return to level one.");
	output("So low level players with titles have had opportunities to embiggen themselves.  (see Hall of Fame)`n`n");
	output("`^5.  What's a buff?`n");
	output("`@It's what we use to shine our shoes.`n");
	output("Did you think it was some sort of temporary or permanent effect which affects your stats in a positive or negative manner? Hah!`n`n");
	output("`^6.  Why does that old man keep hitting me with an ugly/pretty stick in the forest?`n");
	output("`@You look like a pi&ntilde;ata!`n",true);
	output("It's a special event that can add or remove charm.`n`n");
	output("`^7.  Well, what's the point of charm?`n");
	output("`@To get chicks (or guys).`n");
	output("Well, actually, that *is* the point.");
	output("Visit some folks at the Inn, and you ought to be able to figure this one out.");
	output("The more charm you have, the more successful you'll be at wooing said folks.`n`n");
	output("`^8.  Okay, I saw the man in the forest and he hit me with his ugly stick, but it says I'm uglier than the stick, and I made it lose a charm point. What's going on?`n");
	output("`@You're clearly the least charming person on the planet.");
	output("And if you're the person who actually *asked* this question, you're also the dumbest.");
	output("Use a little power of inference, wouldja?");
	output("No.  Really.`n");
	output("Okay, we did say you were the dumbest, so: it means you currently have zero charm points.`n`n");
	output("`^9.  How do I check my charm?`n");
	output("`@Take a peek in the mirror once in a while.`n");
	output("We jest - there's no mirror.");
	output("You'll have to ask a friend how you look today - the responses may be vague, but they'll give you a clue how you're doing.`n`n");
	output("`^10.  Who is the Management?`n");
	output("`@Appleshiner, Foilwench and Catscradler are in charge of this FAQ, but if something goes wrong, blame MightyE or Kendaer.");
	output("They're in charge of everything else.");
	output("To get ahold of them, or one of their trusty helpers, use the Petition for Help link.`n`n");
	output("`^11.  How did they get to be so darn attractive, anyway?`n");
	output("`@Lots of at-home facials, my dear!!");
	output("MightyE especially enjoys the Grapefruit Essence Facial Masque.`n");
	rawoutput("<hr><a href='petition.php?op=faq'>$c</a>");
}else{
	tlschema("petition");
	popup_header("Petition for Help");
	$post = httpallpost();
	if (count($post)>0){
		$ip = explode(".",$_SERVER['REMOTE_ADDR']);
		array_pop($ip);
		$ip = join($ip,".").".";
		$sql = "SELECT count(*) AS c FROM ".db_prefix("petitions")." WHERE (ip LIKE '$ip%' OR id = '".addslashes($_COOKIE['lgi'])."') AND date > '".date("Y-m-d H:i:s",strtotime("-1 day"))."'";
		$result = db_query($sql);
		$row = db_fetch_assoc($result);
		if ($row['c'] < 5 || (isset($session['user']['superuser']) && $session['user']['superuser']&~SU_DOESNT_GIVE_GROTTO)){
			if (!isset($session['user']['acctid']))
				$session['user']['acctid']=0;
			if (!isset($session['user']['password']))
				$session['user']['password']="";
			$p = $session['user']['password'];
			unset($session['user']['password']);
			$date = date("Y-m-d H:i:s");
			$sql = "INSERT INTO " . db_prefix("petitions") . " (author,date,body,pageinfo,ip,id) VALUES (".(int)$session['user']['acctid'].",'$date',\"".addslashes(output_array($post))."\",\"".addslashes(output_array($session,"Session:"))."\",'{$_SERVER['REMOTE_ADDR']}','".addslashes($_COOKIE['lgi'])."')";
			db_query($sql);

			// If the admin wants it, email the petitions to them.
			if (getsetting("emailpetitions", 0)) {
				// Yeah, the format of this is ugly.
				require_once("lib/sanitize.php");
				$name = color_sanitize($session['user']['name']);
				$url = getsetting("serverurl",
						"http://".$_SERVER['SERVER_NAME'] .
						($_SERVER['SERVER_PORT']==80?"":":".$_SERVER['SERVER_PORT']) .
						dirname($_SERVER['REQUEST_URI']));
				if (!preg_match("/\/$/", $url)) {
					$url = $url . "/";
					savesetting("serverurl", $url);
				}

				$msg  = "Server: $url\n";
				$msg .= "Author: $name\n";
				$msg .= "Date  : $date\n";
				$msg .= "Body  :\n".output_array($post)."\n";
				mail(getsetting("gameadminemail","postmaster@localhost.com"),"New LoGD Petition at " . $url, $msg);
			}

			$session['user']['password']=$p;

			output("Your petition has been sent to the server admin.");
			output("Please be patient, most server admins have jobs and obligations beyond their game, so sometimes responses will take a while to be received.");
		}else{
			output("`\$`bError:`b There have already been %s petitions filed from your network in the last day; to prevent abuse of the petition system, you must wait until there have been 5 or fewer within the last 24 hours.",$row['c']);
			output("If you have multiple issues to bring up with the staff of this server, you might think about consolidating those issues to reduce the overall number of petitions you file.");
		}
	}else{
		output("`c`b`\$Before sending a petition, please make sure you have read the motd.`n");
		output("Petitions about problems we already know about just take up time we could be using to fix those problems.`b`c`n");
		rawoutput("<form action='petition.php?op=submit' method='POST'>");
		if ($session['user']['loggedin']) {
			output("Your Character's Name: ");
			output_notl("%s", $session['user']['name']);
			rawoutput("<input type='hidden' name='charname' value=\"".htmlentities($session['user']['name'])."\">");
			output("`nYour email address: ");
			output_notl("%s", htmlentities($session['user']['emailaddress']));
			rawoutput("<input type='hidden' name='email' value=\"".htmlentities($session['user']['emailaddress'])."\">");
		} else {
			output("Your Character's Name: ");
			rawoutput("<input name='charname' value=\"".htmlentities($session['user']['name'])."\" size='46'>");
			output("`nYour email address: ");
			rawoutput("<input name='email' value=\"".htmlentities($session['user']['emailaddress'])."\" size='50'>");
			$nolog = translate_inline("Character is not logged in!!");
			rawoutput("<input name='unverified' type='hidden' value='$nolog'>");
		}
		output("`nDescription of the problem:`n");
		$abuse = httpget("abuse");
		if ($abuse == "yes") {
			rawoutput("<textarea name='description' cols='55' rows='7' class='input'></textarea>");
			rawoutput("<input type='hidden' name='abuse' value=\"".htmlentities(httpget("problem"))."\"><br><hr><pre>".htmlentities(httpget("problem"))."</pre><hr><br>");
		} else {
			rawoutput("<textarea name='description' cols='55' rows='7' class='input'>".htmlentities(httpget("problem"))."</textarea>");
		}
		$submit = translate_inline("Submit");
		rawoutput("<br/><input type='submit' class='button' value='Submit'><br/>");
		output("Please be as descriptive as possible in your petition.");
		output("If you have questions about how the game works, please check out the <a href='petition.php?op=faq'>FAQ</a>.", true);
		output("Petitions about game mechanics will more than likely not be answered unless they have something to do with a bug.");
		output("Remember, if you are not signed in, and do not provide an email address, we have no way to contact you.");
		rawoutput("</form>");
	}
}
popup_footer();
?>
