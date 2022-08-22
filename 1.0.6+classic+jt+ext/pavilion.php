<?php
// PRIVATE_CODE
// translator ready
// mail ready
// addnews ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/villagenav.php");
require_once("lib/http.php");

tlschema("pavilion");

if ($session['user']['beta']) addcommentary();
checkday();

$beta = httpget('beta');

if ($beta=='true'){
	$session['user']['beta']=1;
}elseif ($beta=='false'){
	$session['user']['beta']=0;
}

page_header("Eye-catching Pavilion");
if (!$session['user']['beta']) {
	addnav("B?Sign up for Beta!", "pavilion.php?beta=true");
} else {
	addnav("B?Leave Beta!", "pavilion.php?beta=false");
}
villagenav();

output("`7You enter the pavilion and are immediately surrounded by a throng of people talking about various oddities such as 'beta programs' and 'bugs' and 'balance'.");
output("Being a mighty warrior, and having a decent sense of balance (unless someone is tripping you), you laugh as you hear them talk.`n`n");

if (!$session['user']['beta']) {
	output("A young man dressed in the livery of `&Pontifex `5Moon`%childe `7comes up to you.");
	output("`3\"You look like a strong and adventurous sort!\"`7, he exclaims, `3\"How would you like to help the `&Pontifex`3 make this realm safer for its citizens?  All you need to do is sign up, and the `&Pontifex`3 will make sure that you are given special assignments which help him test out his new `\$code`3 spells.  It's a bit dangerous I admit, but it does seem to have some benefits.  I mean, where else can you get free food and drink whenever you want it?  And, you can always resign if it doesn't suit you!\"`0`n`n");
	output("`7(In plain English, 'Are you willing to beta test new features?')`n");
}else{
	output("Having signed up for the `&Pontifex's`7 'beta' program, you now understand, however, that what they are talking about can mean life or death for those living here.");
	output("After all, you're now one of them, sworn to look for the slightest discrepencies in reality that will tell the `&Pontifex `7that his `\$code`7 spells are failing in some fashion.`0`n`n");
	output("`7(In plain English, 'You are a willing beta tester.");
	output("Please resign now unless you are willing to be a guinea pig `&and`7 give constructive criticism of any problems that you see.");
	output("Also, if you find a bug, you are agreeing to report it rather than abuse it, etc, etc, etc.')`n`n");
	commentdisplay("", "beta", "Discuss here", 50);
}
page_footer();
?>
