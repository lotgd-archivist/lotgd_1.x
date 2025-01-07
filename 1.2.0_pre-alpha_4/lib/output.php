<?php
/**
 * Library Functions for page output.
 *		translator ready
 *		addnews ready
 *		mail ready
 *
 * @author core_module
 */

$nestedtags=array();
$output="";

$block_new_output = false;

/**
 * Block any output statements temporarily
 *
 * @param bool $block should output be blocked
 */
function set_block_new_output($block)
{
	global $block_new_output;
	$block_new_output = $block;
}

/**
 * Raw output (unprocessed) appended to the output buffer
 *
 * @param string $indata
 */
function rawoutput($indata) {
	global $output, $block_new_output;

	if ($block_new_output) return;

	$output .= $indata . "\n";
}

/**
 * Handles color and style encoding, and appends to the output buffer ($output)
 *
 * @param string|array $indata If an array is passed then the format for sprintf is assumed otherwise a simple string is assumed
 *
 * @see sprintf, apponencode
 */
function output_notl($indata){
	global $output, $block_new_output;

	if ($block_new_output) return;

	$args = func_get_args();
	//pop true off the end if we have it
	$last = array_pop($args);
	if ($last!==true){
		array_push($args,$last);
		$priv = false;
	}else{
		$priv = true;
	}
	$out = $indata;
	$args[0]=&$out;
	//apply variables
	if (count($args)>1){
		//special case since we use `% as a color code so often.
		$out = str_replace("`%","`%%",$out);
		$out = call_user_func_array("sprintf",$args);
	}
	//holiday text
	if ($priv==false) $out = holidayize($out,'output');
	//`1`2 etc color & formatting
	$out = appoencode($out,$priv);
	//apply to the page.
	$output.=tlbutton_pop().$out;
	$output.="\n";
}

/**
 * Outputs a translated, color/style encoded string to the browser.
 *
 * @param string|array What to output. If an array is passed then the format used by sprintf is assumed
 *
 * @see output_notl
 *
 */
function output(){
	global $block_new_output;

	if ($block_new_output) return;
	$args = func_get_args();
	if (is_array($args[0])) $args = $args[0];
	if (is_bool($args[0]) && array_shift($args)) {
		$schema= array_shift($args);
		$args[0] = translate($args[0],$schema);
	} else {
		$args[0] = translate($args[0]);
	}
	call_user_func_array("output_notl",$args);
}

/**
 * Generate debug output for players who have the SU_DEBUG_OUTPUT flag set in the superuser mask
 *
 * @param string $text The string to output
 * @param bool   $force If true, force debug output even for non SU/non flagged
 */
function debug($text, $force=false){
	global $user, $block_new_output;
	$temp = $block_new_output;
	set_block_new_output(false);
	if ($force || $user->superuser & SU_DEBUG_OUTPUT){
		if (is_array($text)){
			require_once("lib/dump_item.php");
			$text = appoencode(dump_item($text),true);
		}
		rawoutput("<div class='debug'>$text</div>");
	}
	set_block_new_output($temp);
}

/**
 * Generates the appropriate output based on the LOGD coding system (ie: `b: Bold, `i: Italic)
 *
 * @param string $data The string to be output
 * @param bool $priv Indicates if the passed string ($data) contains HTML
 * @return string An output (HTML) formatted string
 */
function appoencode($data,$priv=false){
	$start = 0;
	$out="";
	if( ($pos = strpos($data, "`")) !== false) {
		global $nestedtags;

		if (empty($nestedtags)) {
			$nestedtags = array(
				'fontstack' => array(),
				'colorstack' => array(),
				'font' => false,
				'color' => false,
				'b' => false,
				'i' => false,
				'h' => false,
				'c' => false,
				'<' => false,
				'>' => false,
				'H' => false,
			);
		}

		static $colors = array(
			"1" => "colDkBlue",
			"2" => "colDkGreen",
			"3" => "colDkCyan",
			"4" => "colDkRed",
			"5" => "colDkMagenta",
			"6" => "colDkYellow",
			"7" => "colDkWhite",
			"8" => "colMedRed",
			"~" => "colBlack",
			"!" => "colLtBlue",
			"@" => "colLtGreen",
			"#" => "colLtCyan",
			"\$" => "colLtRed",
			"%" => "colLtMagenta",
			"^" => "colLtYellow",
			"&" => "colLtWhite",
			")" => "colLtBlack",
			"e" => "colDkRust",
			"E" => "colLtRust",
			"g" => "colXLtGreen",
			"G" => "colXLtGreen",
			"j" => "colMdGrey",
			"J" => "colMdBlue",
			"k" => "colaquamarine",
			"K" => "coldarkseagreen",
			"l" => "colDkLinkBlue",
			"L" => "colLtLinkBlue",
			"m" => "colwheat",
			"M" => "coltan",
			"p" => "collightsalmon",
			"P" => "colsalmon",
			"q" => "colDkOrange",
			"Q" => "colLtOrange",
			"r" => "colRose",
			"R" => "colRose",
			"t" => "colLtBrown",
			"T" => "colDkBrown",
			"v" => "coliceviolet",
			"V" => "colBlueViolet",
			"x" => "colburlywood",
			"X" => "colbeige",
			"y" => "colkhaki",
			"Y" => "coldarkkhaki",
 		);

		static $tagstarts = array(
			'b' => "<b>",
			'i' => "<i>",
			'h' => "<em>",
			'c' => "<div align='center'>",
			'<' => "<div style='float: left; clear: left;'>",
			'>' => "<div style='float: right; clear: right;'>",
		);

		static $tagends = array(
			'b' => "</b>",
			'i' => "</i>",
			'h' => "</em>",
			'c' => "</div>",
			'<' => "</div>",
			'>' => "</div>",
		);

		do {
			++$pos;
			if ($priv === false){
				$out .= HTMLEntities(substr($data, $start, $pos - $start - 1), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			} else {
				$out .= substr($data, $start, $pos - $start - 1);
			}
			$start = $pos + 1;
			$code = $data[$pos];
			if(isset($colors[$code])) {
				if ($nestedtags['font']) $out.="</span>";
				else $nestedtags['font']=true;
				$nestedtags['color'] = $colors[$code];
				$out .= "<span class='{$colors[$code]}'>";
			} elseif (isset($tagstarts[$code])) {
				if ($nestedtags['font'])
					$out .= "</span>";
				if ($nestedtags[$code]) {
					$out .= $tagends[$code];
					$nestedtags[$code] = false;
				} else {
					$out .= $tagstarts[$code];
					$nestedtags[$code] = true;
				}
				if ($nestedtags['font'])
					$out .= "<span class='{$nestedtags['color']}'>";
			} else {
				switch($code){
				case "n":
					$out.="<br>\n";
					break;
				case "0":
					if ($nestedtags['font']) $out.="</span>";
					$nestedtags['font'] = false;
					break;
				case "H":
					if ($nestedtags['H']) {
						$out.="</span>";
						$nestedtags['H']=false;
					}else{
						$nestedtags['H']=true;
						$out.="<span class='navhi'>";
					}
					break;
				case "{":
					$nestedtags['fontstack'][] = $nestedtags['font'];
					$nestedtags['colorstack'][] = $nestedtags['color'];
					$nestedtags['font'] = false;
					break;
				case "}":
					if ($nestedtags['font']) {
						$out.="</span>";
					}
					if (count($nestedtags['fontstack'])) {
						$nestedtags['font'] = array_pop($nestedtags['fontstack']);
						$nestedtags['color'] = array_pop($nestedtags['colorstack']);
					} else {
						$nestedtags['font'] = false;
					}
					break;
				case "`":
					$out.="`";
					++$pos;
					break;
				default:
					$out.="`{$code}";
				}
			}
		} while( ($pos = strpos($data, "`", $pos)) !== false);
	}
	if ($priv === false){
		$out .= HTMLEntities(substr($data, $start), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
	} else {
		$out .= substr($data, $start);
	}
	return $out;
}

function appoclosetags() {
	global $nestedtags;

	$out = "";

	// We don't have enough information to close tags in the right
	// order; if there's at most one unclosed then we'll be fine.
	// Otherwise hope for the best.  (Of course, the only tag which
	// should remain unclosed is one font one.)  Close the font tags
	// first since they are most likely to be inner.

	$nestedtags['fontstack'][] = array_key_exists('font', $nestedtags) ? $nestedtags['font'] : false;
	foreach (array_reverse($nestedtags['fontstack']) as $font) {
		if ($font)
			$out .= "</span>";
	}

	// Should handle 'H' separately and use $endtags from appoencode(),
	// but it's not overly worth globalising the former just for this.
	$appotags = array(
		'H' => "span",
		'i' => "i",
		'b' => "b",
		'h' => "em",
		'c' => "div",
		'<' => "div",
		'>' => "div",
	);
	foreach ($appotags as $appo => $tag) {
		if (!empty($nestedtags[$appo]))
			$out .= "</{$tag}>";
	}

	$nestedtags = array();

	return $out;
}

$blockednavs = array(
		'blockpartial'=>array(),
		'blockfull'=>array(),
		'blockcat'=>array(),
		'unblockpartial'=>array(),
		'unblockfull'=>array()
	);

/**
 * Called to block the display of a nav
 * if $partial is true, it will block any nav that begins with the given $link.
 * if $partial is false, it will block only navs that have exactly the given $link.
 *
 * @param string $link The URL to block
 * @param bool $partial
 */
function blocknav($link,$partial=false){
	//prevents a script from being able to generate navs on the given $link.
	global $blockednavs;
	$p = ($partial?'partial':'full');
	$blockednavs["block$p"][$link] = true;
	//eliminate any unblocked navs that match this description.
	if (isset($blockednavs["unblock$p"][$link])) {
		unset($blockednavs["unblock$p"][$link]);
	}
	if ($partial){
		foreach ($blockednavs['unblockpartial'] as $val) {
			if (substr($link,0,strlen($val))==$val ||
					substr($val,0,strlen($link))==$link){
				unset($blockednavs['unblockpartial'][$val]);
			}
		}
	}
}

/**
 * Unlocks a nav from the blocked navs Array
 * if $partial is true, it will unblock any nav that begins with the given $link.
 * if $partial is false, it will unblock only navs that have exactly the given $link.
 *
 * @param string $link The nav to unblock
 * @param bool $partial If the passed nav is partial or not
 */
function unblocknav($link,$partial=false){
	//prevents a link that was otherwise blocked with blocknav() from
	//actually being blocked.
	global $blockednavs;
	$p = ($partial?'partial':'full');
	$blockednavs["unblock$p"][$link] = true;
	//eliminate any blocked navs that match this description.
	if (isset($blockednavs["block$p"][$link])) {
		unset($blockednavs["block$p"][$link]);
	}
	if ($partial){
		foreach ($blockednavs['blockpartial'] as $val) {
			if (substr($link,0,strlen($val))==$val ||
					substr($val,0,strlen($link))==$link){
				unset($blockednavs['blockpartial'][$val]);
			}
		}
	}
}

/**
 * Called to block the display of an entire category of navs
 *
 * @param string $cat The category to block
 */
function blocknavcat($cat){
	//prevents a script from being able to generate navs on the given $cat.
	global $blockednavs;
	if(!isset($blockednavs["blockcat"][$cat])) $blockednavs["blockcat"][$cat] = true;
}

/**
 * Unlocks a nav category from the blocked navs Array
 *
 * @param string $cat The category to unblock
 */
function unblocknavcat($cat){
	//prevents a category that was otherwise blocked with blocknavcat() from
	//actually being blocked.
	global $blockednavs;
	//eliminate any blocked nav cats that match this description.
	$blockednavs["blockcat"][$cat] = false;
}

function appendcount($link) {
	global $user;
	if(!$user->loggedin) {
		return $link;
	}
	return appendlink($link, "c=" . Session::get('counter') . "-" . date("His"));
}

function appendlink($link, $new)
{
	if (strpos($link, "?") !== false) {
		return $link . '&' . $new;
	} else {
		return $link . '?' . $new;
	}
}

$navsection="";
$navbysection = array();
$navschema = array();
$navnocollapse = array();
$block_new_navs = false;

/**
 * Allow header/footer code to block/unblock additional navs
 *
 * @param bool $block should new navs be blocked
 */
function set_block_new_navs($block)
{
	global $block_new_navs;
	$block_new_navs = $block;
}

/**
 * Generate and/or store a nav banner for the player
 *
 * @param string $text the display string for the nav banner
 * @param collapse $collapse (default true) can the nav section collapse
 */
function addnavheader($text, $collapse=true,$translate=TRUE)
{
	global $navsection,$navbysection,$translation_namespace;
	global $navschema,$navnocollapse, $block_new_navs,$notranslate;

	if ($block_new_navs) return;

	if (is_array($text)){
		$text = "!array!".serialize($text);
	}
	$navsection=$text;
	if (!isset($navschema[$text]))
		$navschema[$text] = $translation_namespace;
	//So we can place sections with out adding navs to them.
	if (!isset($navbysection[$navsection]))
		$navbysection[$navsection] = array();
	if ($collapse === false) {
		$navnocollapse[$text] = true;
	}
	if ($translate === false) {
		if (!isset($notranslate))
			$notranslate = array();
		array_push($notranslate,array($text,""));
	}
}

/**
 * Generate and/or store the allowed navs or nav banners for the player.
 * If $link is missing - then a banner will be displayed in the nav list
 * If $text is missing - the nav will be stored in the allowed navs for the player but not displayed
 * <B>ALL</B> internal site links that are displayed <B>MUST</B> also call addnav or badnav will occur.
 *
 * @param string $text (optional) The display string for the nav or nav banner
 * @param string $link (optional) The URL of the link
 * @param bool $priv Indicates if the name contains HTML
 * @param bool $pop Indicates if the URL should generate a popup
 * @param string $popsize If a popup - the size of the popup window
 *
 * @see badnav, apponencode
 */

function addnav_notl($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	global $navsection,$navbysection,$notranslate;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text,TRUE,FALSE);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			private_addnav($text, $link, $priv, $pop, $popsize);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			if (!isset($notranslate))
				$notranslate = array();
			array_push($navbysection[$navsection],$args);
			array_push($notranslate,$args);
		}
	}
}
function addnav($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	global $navsection,$navbysection,$translation_namespace,$navschema;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			private_addnav($text, $link, $priv, $pop, $popsize);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			$t = $args[0];
			if (is_array($t)) {
				$t = $t[0];
			}
			if (!isset($navschema[$t]))
				$navschema[$t] = $translation_namespace;
			array_push($navbysection[$navsection],array_merge($args,array("translate"=>false)));
		}
	}
}
/**
 * Determine if a nav/URL is blocked
 *
 * @param string $link The nav to check
 * @return bool
 */
function is_blocked($link)
{
	global $blockednavs;
	if (isset($blockednavs['blockfull'][$link])) return true;
	foreach (array_keys($blockednavs['blockpartial']) as $l) {
		if (substr($link,0,strlen($l))==$l) {
			if (isset($blockednavs['unblockfull'][$link]) &&
					$blockednavs['unblockfull'][$link]) return false;
			foreach (array_keys($blockednavs['unblockpartial']) as $l2) {
				if (substr($link,0,strlen($l2))==$l2){
					return false;
				}
			}
			return true;
		}
	}
	return false;
}


/**
 * Determine how many navs are available
 *
 * @param string $section The nav section to check
 * @return int
 */
function count_viable_navs($section)
{
	global $navbysection, $blockednavs;
	
	if (isset($blockednavs['blockcat'][$section]) && $blockednavs['blockcat'][$section] == true) {
		return 0;
	}
	$count = 0;
	$val = $navbysection[$section];
	if (count($val) > 0) {
		foreach ($val as $nav) {
			if (is_array($nav) && count($nav) > 0) {
				$link = $nav[1]; // [0] is the text, [1] is the link
				if (!is_blocked($link)) $count++;
			}
		}
	}
	return $count;
}


/**
 * Determins if there are any navs for the player
 *
 * @return bool
 */
function checknavs() {
	global $navbysection;

	// If we already have navs entered (because someone stuck raw links in)
	// just return true;
	if (is_array(Session::get('allowednavs')) &&
			count(Session::get('allowednavs')) > 0) return true;

	// If we have any links which are going to be stuck in, return true
	foreach ($navbysection as $key=>$val) {
		if (count_viable_navs($key) > 0) {
			foreach ($val as $v) {
				if (is_array($v) && count($v) > 0) return true;
			}
		}
	}

	// We have no navs.
	return false;
}

/**
 * Builds navs for display
 *
 * @return string Output formatted navs
 */
function buildnavs(){
	global $navbysection, $navschema, $navnocollapse;
	$builtnavs="";
	foreach ($navbysection as $key=>$val) {
		$tkey = $key;
		$navbanner="";
		if (count_viable_navs($key)>0){
			if ($key>"") {
				if (Session::get('loggedin')) tlschema($navschema[$key]);
				if (substr($key,0,7)=="!array!"){
					$key = unserialize(substr($key,7));
				}
				$navbanner = private_addnav($key);
				if (Session::get('loggedin')) tlschema();
			}

			$style = "default";
			$collapseheader = "";
			$collapsefooter = "";

			if ($tkey > "" && (!isset($navnocollapse[$tkey]) || !$navnocollapse[$tkey])) {
				// Generate the collapsable section header
				$args = array("name"=>"nh-{$key}",
						"title"=>($key ? $key : "Unnamed Navs"));
				$args = modulehook("collapse-nav{", $args);
				if (isset($args['content']))
					$collapseheader = $args['content'];
				if (isset($args['style']))
					$style = $args['style'];
				if (!($key > "") && $style == "classic") {
					$navbanner = "<TR><TD>";
				}
			}

			$sublinks = "";
			foreach ($val as $v) {
				if (is_array($v) && count($v)>0){
					$sublinks .=   call_user_func_array("private_addnav",$v);
				}//end if
			}//end foreach

			// Generate the enclosing collapsable section footer
			if ($tkey > "" && (!isset($navnocollapse[$tkey]) || !$navnocollapse[$tkey])) {
				$args = modulehook("}collapse-nav");
				if (isset($args['content']))
					$collapsefooter = $args['content'];
			}

			switch ($style) {
			case "classic":
				$navbanner = str_replace("</tr>","",$navbanner);
				$navbanner = str_replace("</td>","",$navbanner);
				// Build the nav section
				$builtnavs .= "{$navbanner}{$collapseheader}<TABLE align='left'>{$sublinks}</TABLE>{$collapsefooter}</TR></TD>\n";
				break;
			case "default":
			default:
				// Is style isn't set (should the module not be active)
				// - this catches it
				// Build the nav section
				$builtnavs .= "{$navbanner}{$collapseheader}{$sublinks}{$collapsefooter}\n";
				break;
			}
		}//end if
	}//end foreach
	$navbysection = array();
	return $builtnavs;
}//end function

$accesskeys=array();
$quickkeys=array();
/**
 * Private functions (Undocumented)
 *
 * @param string $text
 * @param string $link
 * @param bool $priv
 * @param bool $pop
 * @param bool $popsize
 * @return mixed
 */
function private_addnav($text,$link=false,$priv=false,$pop=false,$popsize="500x300"){
	//don't call this directly please.  I'll break your thumbs if you do.
	global $nav,$accesskeys,$quickkeys,$navschema,$notranslate,$user;

	if (is_blocked($link)) return false;

	$thisnav = "";
	$unschema = 0;
	$translate=true;
	if (isset($notranslate)) {
		if (in_array(array($text,$link),$notranslate)) $translate=false;
	}

	if (is_array($text)){
		if ($text[0] && Session::get('loggedin')) {
			if ($link === false) $schema = "!array!" . serialize($text);
			else $schema = $text[0];
			if ($translate) {
				tlschema($navschema[$schema]);
				$unschema = 1;
			}
		}
		if ($link != "!!!addraw!!!") {
			if ($translate) $text[0] = translate($text[0]);
			$text = call_user_func_array("sprintf",$text);
		} else {
			$text = call_user_func_array("sprintf",$text);
		}
	}else{
		if ($text && Session::get('loggedin') && $translate) {
			tlschema($navschema[$text]);
			$unschema = 1;
		}
		if ($link != "!!!addraw!!!" && $text>"" && $translate) $text = translate($text); //leave the hack in here for now, use addnav_notl please
	}

	$extra="";
	$ignoreuntil="";
	if ($link===false){
		$text = holidayize($text,'nav');
		$thisnav.=tlbutton_pop().templatereplace("navhead",array("title"=>appoencode($text,$priv)));
	}elseif ($link === "") {
		$text = holidayize($text,'nav');
		$thisnav.=tlbutton_pop().templatereplace("navhelp",array("text"=>appoencode($text,$priv)));
	} elseif ($link == "!!!addraw!!!") {
		$thisnav .= $text;
	}else{
		if ($text!=""){
			$extra="";
			if (strpos($link,"?")){
				$extra="&c=" . Session::get('counter');
			}else{
				$extra="?c=" . Session::get('counter');
			}
			
			$extra.="-".date("His");
			if(!$user->loggedin) {
				$extra = '';
			}
			//hotkey for the link.
			$key="";
			if ($text[1]=="?") {
				// check to see if a key was specified up front.
				$hchar = strtolower($text[0]);
				if ($hchar==' ' || isset($accesskeys[$hchar]) && $accesskeys[$hchar]==1){
					$text = substr($text,2);
					$text = holidayize($text,'nav');
					if ($hchar == ' ') $key = " ";
				}else{
					$key = $text[0];
					$text = substr($text,2);
					$text = holidayize($text,'nav');
					$found=false;
					$text_len = strlen($text);
					for ($i=0;$i<$text_len; ++$i){
						$char = $text[$i];
						if ($ignoreuntil == $char){
							$ignoreuntil="";
						}else{
							if ($ignoreuntil<>""){
								if ($char=="<") $ignoreuntil=">";
								if ($char=="&") $ignoreuntil=";";
								if ($char=="`") $ignoreuntil=$text[$i+1];
							}else{
								if ($char==$key) {
									$found=true;
									break;
								}
							}
						}
					}
					if ($found==false) {
						//the hotkey for this link wasn't actually in the
						//text, prepend it in parens.
						if (strpos($text, "__") !== false) {
							$text=str_replace("__", "(".$key.") ", $text);
						}else{
							$text="(".strtoupper($key).") ".$text;
						}
						$i=strpos($text, $key);
					}
				}
			} else {
				$text = holidayize($text,'nav');
			}

			if ($key==""){
				//we have no previously defined key.  Look for a new one.
				for ($i=0;$i<strlen($text); $i++){
					$char = substr($text,$i,1);
					if ($ignoreuntil == $char) {
						$ignoreuntil="";
					}else{
						if ((isset($accesskeys[strtolower($char)]) && $accesskeys[strtolower($char)]==1) || (strpos("abcdefghijklmnopqrstuvwxyz0123456789", strtolower($char)) === false) || $ignoreuntil<>"") {
							if ($char=="<") $ignoreuntil=">";
							if ($char=="&") $ignoreuntil=";";
							if ($char=="`") $ignoreuntil=substr($text,$i+1,1);
						}else{
							break;
						}
					}
				}
			}
			if (!isset($i)) $i=0;
			if ($i<strlen($text) && $key != ' '){
				$key=substr($text,$i,1);
				$accesskeys[strtolower($key)]=1;
				$keyrep=" accesskey=\"$key\" ";
			}else{
				$key="";
				$keyrep="";
			}

			if ($key=="" || $key==" "){
			}else{
				$pattern1 = "/^" . preg_quote($key, "/") . "/";
				$pattern2 = "/([^`])" . preg_quote($key, "/") . "/";
				$rep1 = "`H$key`H";
				$rep2 = "\$1`H$key`H";
				$text = preg_replace($pattern1, $rep1, $text, 1);
				if (strpos($text, "`H") === false) {
					$text = preg_replace($pattern2, $rep2, $text, 1);
				}
				if ($pop){
					if ($popsize==""){
						$quickkeys[$key]="window.open('$link')";
					}else{
						$quickkeys[$key]=popup($link,$popsize);
					}
				}else{
					$quickkeys[$key]="window.location='$link$extra'";
				}
			}
			$n= templatereplace("navitem",array(
				"text"=>appoencode($text,$priv),
				"link"=>HTMLEntities($link.($pop!=true?$extra:""), ENT_COMPAT, getsetting("charset", "ISO-8859-1")),
				"accesskey"=>$keyrep,
				"popup"=>($pop==true ? "target='_blank'".($popsize>""?" onClick=\"".popup($link,$popsize)."; return false;\"":"") : "")
				));
			$n = str_replace("<a ",tlbutton_pop()."<a ",$n);
			$thisnav.=$n;
		}
		$navs = Session::get('allowednavs');
		$navs[$link.$extra]=true;
		$navs[str_replace(" ", "%20", $link).$extra]=true;
		$navs[str_replace(" ", "+", $link).$extra]=true;
		if (($pos = strpos($link, "#")) !== false) {
			$sublink = substr($link, 0, $pos);
			$navs[$sublink.$extra]=true;
		}
		Session::set('allowednavs', $navs);
	}
	if ($unschema) tlschema();
	$nav .= $thisnav;
	return $thisnav;
}

/**
 * Determine how many navs are available
 *
 * @return int The number of legal navs
 */
function navcount(){
	//returns count of total navs added, be it they are pending addition or
	//actually added.
	global $navbysection;
	$c=count(Session::get('allowednavs'));
	foreach ($navbysection as $val) {
		if (is_array($val)) $c+=count($val);
	}
	return $c;
}

/**
 * Reset and wipe the navs
 *
 */
function clearnav(){
	Session::set('allowednavs', array());
}

/**
 * Reset the output and wipe the navs
 *
 */
function clearoutput(){
	global $output,$nestedtags,$header,$nav;

	clearnav();
	$output="";
	$nestedtags=array();
	$header="";
	$nav="";
}

/**
* addnavafter and addnavafter_notl allowes adding a nav to a detailed position in a nav-group between two entries
* (to ensure the correct ordering)
*/
function addnavafter($text,$link=false,$priv=false,$pop=false,$popsize="500x300", $afterlink){
	global $navsection,$navbysection,$translation_namespace,$navschema;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			private_addnav($text, $link, $priv, $pop, $popsize);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			$t = $args[0];
			if (is_array($t)) {
				$t = $t[0];
			}
			if (!array_key_exists($t,$navschema)) {
				$navschema[$t] = $translation_namespace;
			}
			if (isset($afterlink)) {
				
				$temparray=$navbysection[$navsection];
				$navbysection[$navsection] = array();
				
				foreach ($temparray as $val) {
					if ($val[1]==$afterlink) {
						array_push($navbysection[$navsection],$val);
						array_push($navbysection[$navsection],array_merge($args,array("translate"=>false)));
					} else {
						array_push($navbysection[$navsection],$val);
					}
				}
			} else {
				array_push($navbysection[$navsection],array_merge($args,array("translate"=>false)));
			}
		}
	}
}

function addnavafter_notl($text,$link=false,$priv=false,$pop=false,$popsize="500x300", $afterlink){
	global $navsection,$navbysection,$notranslate;
	global $block_new_navs;

	if ($block_new_navs) return;

	if ($link===false) {
		// Don't do anything if text is ""
		if ($text != "") {
			addnavheader($text,TRUE,FALSE);
		}
	}else{
		$args = func_get_args();
		if ($text==""){
			//if there's no text to display, may as well just stick this on
			//the nav stack now.
			private_addnav($text, $link, $priv, $pop, $popsize);
		}else{
			if (!isset($navbysection[$navsection]))
				$navbysection[$navsection] = array();
			if (!isset($notranslate))
				$notranslate = array();
				
			if (isset($afterlink)) {
				$temparray=$navbysection[$navsection];
				$navbysection[$navsection] = array();
				
				foreach ($temparray as $val) {
					if ($val[1]==$afterlink) {
						array_push($navbysection[$navsection],$val);
						array_push($navbysection[$navsection],$args);
					} else {
						array_push($navbysection[$navsection],$val);
					}
				}
			} else {
				array_push($navbysection[$navsection],$args);
			}
			array_push($notranslate,$args);
		}
	}
}
?>
