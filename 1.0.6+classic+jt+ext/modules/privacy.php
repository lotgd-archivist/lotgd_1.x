<?php
// PRIVATE_CODE
// addnews ready
// mail ready

function privacy_getmoduleinfo(){
	$info = array(
		"name"=>"Privacy Policy",
		"author"=>"Eric Stevens",
		"category"=>"General",
		// "download"=>"core_module",
		"version"=>"1.0",
		"allowanonymous"=>true,
		"override_forced_nav"=>true,
	);
	return $info;
}

function privacy_install(){
	module_addhook("about");
	module_addhook("index");
	module_addhook("create-form");
	return true;
}
function privacy_uninstall(){
	return true;
}
function privacy_dohook($hookname,$args){
	if ($hookname=="about" || $hookname=="index"){
		addnav("Privacy Policy","runmodule.php?module=privacy&op=policy",false,true);
	}elseif($hookname=="create-form"){
		addnav("Privacy Policy","runmodule.php?module=privacy&op=policy",false,true);
		output("Your privacy is important to us, please read %sour privacy policy%s`n","<a href='runmodule.php?module=privacy&op=policy' target='_blank' onClick=\"".popup("runmodule.php?module=privacy&op=policy")."; return false;\">","</a>",true);
	}
	return $args;
}
function privacy_run(){
	popup_header("Privacy Policy");
	rawoutput("<br /><b>");
	output("`&Privacy Notice Table Of Contents:`0");
	rawoutput("</b><br />");
	$commit = translate_inline("Our Commitment To Privacy");
	$info = translate_inline("The Information We Collect");
	$use = translate_inline("How We Use Information");
	$secure = translate_inline("Our Commitment To Data Security");
	$child = translate_inline("Our Commitment To Children's Privacy");
	$access = translate_inline("How To Access Or Correct Your Information");
	$contact = translate_inline("How To Contact Us");
	rawoutput("<ul><li><a href='#1'>$commit</a></li>");
	rawoutput("<li><a href='#2'>$info</a></li>");
	rawoutput("<li><a href='#3'>$use</a></li>");
	rawoutput("<li><a href='#4'>$secure</a></li>");
	rawoutput("<li><a href='#5'>$child</a></li>");
	rawoutput("<li><a href='#6'>$access</a></li>");
	rawoutput("<li><a href='#7'>$contact</a></li>");

	rawoutput("</ul><br /><br /><a name='1'></a><b>");
	output_notl("`^%s:`0", $commit);
	rawoutput("</b><br />");
	output("Your privacy is important to us.");
	output("To better protect your privacy we provide this notice explaining our online information practices and the choices you can make about the way your information is collected and used.");
	output("This privacy policy applies to the extent that the U.S. PATRIOT act permits it to.");
	output("Where this act provides for disclosure of information to U.S. government officials, this policy does not apply.");
	output("Although we disagree with many of the provisions set forth in the the PATRIOT act on a fundamental level, we are also respectable law-abiding citizens, and so will comply with this act to the extent that the act compels us.");

	rawoutput("<br /><br /><a name='2'></a><b>");
	output_notl("`^%s:`0", $info);
	rawoutput("</b><br />");
	output("This notice applies to all information collected or submitted on the LotGD.net, logd.dragoncat.net, or directly affiliated websites.");
	output("We only request a username, password, and your email address.");
	output("On LotGD.net and LoGD.DragonCat.net, your email address is required.");
	output("Other directly affiliated sites might or might not require your email address.");
	output("Although a gender is required for your character(s), no expectation is made that this gender matches your true gender, and this information is not seen as being valid demographic information.");
	output("This site will store a cookie on your computer to allow tracking of state during a session.");
	output("This site also keeps logs of IP addresses for statistical purposes and those IP addresses may be able to be associated with a character and thus an email address.");
	output("Lastly, conversation in Ye Olde Mail and the various comment areas are kept and stored within the game database.");
	output("Conversations taking place within Ye Olde Mail are considered to be private between the sender and the recipient, no disclosure will be made by the site staff to any third party except as required by the U.S. PATRIOT act.");
	output("This data can exist for an indefinite amount of time due to backups made of the database in order to recover in the face of an emergency.");
	rawoutput("<br />");
	output("Directly affiliated websites include the following:");
	rawoutput("<ul><li><a href='http://lotgd.net'>LotGD.net</a>");
	output("and sub domains.");
	rawoutput("</li><li><a href='http://logd.mightye.org'>MightyE.org</a>");
	output("and sub domains.");
	rawoutput("</li><li><a href='http://logd.dragoncat.net'>LoGD.DragonCat.net</a>");
	output("and sub domains.");
	rawoutput("</li></ul><br/>");
	output("Other websites (even those running Legend of the Green Dragon code, or containing information about Legend of the Green Dragon) are not covered by this privacy policy.");
	output("You will have to check with these sites to see if they have a privacy policy.");
	output("Keep in mind that Legend of the Green Dragon is an open source software product.");
	output("Many distinct users run the same software, and these sites can look very similar to those sites which are covered by this privacy policy, but are not under our control.");

	rawoutput("<br/ ><br /><a name='3'></a><b>");
	output_notl("`^%s:`0", $use);
	rawoutput("</b><br />");
	output("We use email addresses to answer the email we receive, or to facilitate forgotten password requests.");
	output("Such addresses are not used for any other purpose and are not shared with outside parties except as required by the U.S. PATRIOT act.");
	rawoutput("<br />");
	output("We use non-identifying and aggregate information to better design our website and to share with advertisers.");
	output("For example, we may tell an advertiser that X number of individuals visited a certain area on our website, or that Y number of male and Z number of female characters were created on our registration form, but we would not disclose anything that could be used to identify those individuals.");
    rawoutput("<br />");
	output("We use the IP information to help prevent against malicious attacks and do not release it to outside parties.");
    rawoutput("<br />");
	output("Cookie information as well as content of Ye Olde Mail and commentary areas are used to provide the interactive game experience.");
	output("Commentary information is viewable by anyone who enters the comment area and the game administrators.");
	output("Ye Olde Mail is viewable by the intended recipient, and under some rare instances by game administrators with specific privileges.");
	output("Cookie information is stored for a limited duration and is associated with accounts for administrative purposes.");
	rawoutput("<br />");
	output("Finally, except as required by the U.S. PATRIOT act, we do not currently use and will not use or share the personally identifiable information provided to us online in ways unrelated to the ones described above without also providing you an opportunity to opt-out or otherwise prohibit such unrelated uses.");
	rawoutput("<br /><br /><a name='4'></a><b>");
	output_notl("`^%s:`0", $secure);
	rawoutput("</b><br />");
	output("To prevent unauthorized access, maintain data accuracy, and ensure the correct use of information, we have put in place appropriate physical, electronic, and managerial procedures to safeguard and secure the information we collect online.");
	rawoutput("<br /><br /><a name='5'></a><b>");
	output_notl("`^%s:`0", $child);
	rawoutput("</b><br />");
	output("Protecting the privacy of the very young is especially important.");
	output("For that reason, none of the required information that we collect for our accounts would be harmful to children under the age of 13 if inadvertently disclosed, regardless of whether the user is actually over 13.");
	rawoutput("<br />");
	output("Comments entered in the various chat areas may be harmful to a child if the child discloses such identifying information as a real name or address, but we will never request identifying information from anyone.");
	rawoutput("<br /><br /><a name='6'></a><b>");
	output_notl("`^%s:`0", $access);
	rawoutput("</b><br />");
	output("You can view all your personally identifiable information that we collect online and maintain by visiting your account preferences page (found in the village square).");
	output("We use this procedure to better safeguard your information.");
	rawoutput("<br />");
	output("You can correct factual errors in your personally identifiable information by sending us a request that credibly shows error.");
	rawoutput("<br />");
	output("To protect your privacy and security, we will also take reasonable steps to verify your identity before granting access or making corrections.");
	rawoutput("<br /><br /><a name='7'></a><b>");
	output_notl("`^%s:`0", $contact);
	rawoutput("</b><br />");
	output("Should you have other questions or concerns about these privacy policies, please use the 'Petition for Help' link found on every main page throughout the site.");
	popup_footer();
}
?>
