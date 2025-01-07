<?php
/**
 * Commentary class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage commentary
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * Commentary class
 *
 * This class provides the methods for displaying,
 * adding and removing comments.
 * 
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage commentary
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class Commentary {
	/**
	 * @static 
	 * @var array The clan rank colours
	 */
	public static $clanrankcolors = array("`!","`#","`^","`&","`\$");
	/**
	 * @var string The intro message to display
	 */
	protected $intro;
	/**
	 * @var string The section of commentary to display
	 */
	protected $section;
	/**
	 * @var string The message displayed just above the talkform
	 */
	protected $message;
	/**
	 * @var int The amount of comments to display per page
	 */
	protected $limit;
	/**
	 * @var string The string prepended to the comment
	 */
	protected $talkline;
	/**
	 * @var string|bool The translation schema
	 */
	protected $schema;
	/**
	 * @var bool If commentary displaying is blocked
	 */
	protected $blocked = false;
	/**
	 * @var bool Possible double post
	 */
	protected $doublepost=false;
	/**
	 * @var bool Empty post
	 */
	protected $emptypost=false;
	/**
	 * @var int The amount of comments in the section
	 */
	protected $commentsInSection=0;
	/**
	 * @var int The total amount of pages in the section
	 */
	protected $pages=0;
	/**
	 * @var bool Link names to bios 
	 */
	protected $linkbios=true;
	/**
	 * @var int The amount of comments posted by the user today
	 */
	protected $counttoday=0;
	/**
	 * @var Show the delete link
	 */
	protected $showDelete = true;

	/**
	 * Initialises the class
	 * 
	 * @param string $section The commentary section to display
	 * @param string $intro An introduction to the commentary
	 * @param string $message The message displayed just above the talkform
	 * @param int $limit The amount of comments to display per page
	 * @param string $talkline The string prepended to the comment
	 * @param string|bool $schema The translation schema
	 */
	public function __construct($section, $intro = "", $message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
		$this->section = $section;
		$this->intro = $intro;
		$this->message="`n`@{$message}`n";
		$this->limit = (int)$limit;
		$this->talkline = $talkline;
		$this->schema = $schema;
		$this->commentsInSection = $this->countTotalComments();
		$this->pages = ceil($this->commentsInSection/$this->limit) - 1;
		// Let's add a hook for modules to block commentary sections
		$args = modulehook("blockcommentarea", array("section"=>$section));
		if (isset($args['block']) && ($args['block'] == "yes")) $this->blocked = true;

		$nobios = array("motd.php"=>true);

		if(isset($nobios[basename($_SERVER['SCRIPT_NAME'])]) && $nobios[basename($_SERVER['SCRIPT_NAME'])]) {
			$this->linkbios = false;
		} else {
			$this->linkbios = true;
		}
	}

	/**
	 * Adds a comment
	 * 
	 * Checks to make sure that the comment
	 * is valid, than injects it.
	 * 
	 * Also used to remove comments using
	 * the delete link.
	 * 
	 * @uses Commentary::injectcomment()
	 */
	public function addcommentary() {
		global $user;
		$remove = URLDecode(httpget('removecomment'));
		if ($remove>0) {
			$return = '/' . httpget('returnpath');
			$section = httpget('section');
			$sql = "SELECT " .
			DB::prefix("commentary").".*,".DB::prefix("accounts").".name,".
			DB::prefix("accounts").".acctid, ".DB::prefix("accounts").".clanrank,".
			DB::prefix("clans").".clanshort FROM ".DB::prefix("commentary").
                " INNER JOIN ".DB::prefix("accounts")." ON ".
			DB::prefix("accounts").".acctid = " . DB::prefix("commentary").
                ".author LEFT JOIN ".DB::prefix("clans")." ON ".
			DB::prefix("clans").".clanid=".DB::prefix("accounts").
                ".clanid WHERE commentid=$remove";
			$row = DB::fetch_assoc(DB::query($sql));
			$sql = "INSERT LOW_PRIORITY INTO ".DB::prefix("moderatedcomments").
			" (moderator,moddate,comment) VALUES ('{$user->acctid}','".date("Y-m-d H:i:s")."','".addslashes(serialize($row))."')";
			DB::query($sql);
			$sql = "DELETE FROM ".DB::prefix("commentary")." WHERE commentid='$remove';";
			DB::query($sql);
			massinvalidate("comments-$section");
			invalidatedatacache("comments-or11");
			$user->specialinc==''; //just to make sure he was not in a special
			$return = cmd_sanitize($return);
			$return = substr($return,strrpos($return,"/")+1);
			if (strpos($return,"?")===false && strpos($return,"&")!==false){
				$x = strpos($return,"&");
				$return = substr($return,0,$x-1)."?".substr($return,$x+1);
			}
			redirect($return);
		}
		$section = httppost('section');
		$talkline = httppost('talkline');
		$comment = trim(httppost('insertcommentary'));
		$counter = httppost('counter');
		if ($section != $this->section) return;
		if (Session::get('commentcounter') == $counter || is_a($this, 'ModerateCommentary')) {
			if ($section || $talkline || $comment) {
				$tcom = color_sanitize($comment);
				if ($tcom == "" || $tcom == ":" || $tcom == "::" || $tcom == "/me" || $tcom == "/game") {
					$this->emptypost = 1;
				} else {
					$this->injectcomment($comment);
				}
			}
		}
	}

	/**
	 * Determines the type of comment and adds it to the database
	 * 
	 * @uses Commentary::injectsystemcomment()
	 * @uses Commentary::injectrawcomment()
	 * @param string $comment The comment
	 */
	public function injectcomment($comment) {
		global $user;
		if ($this->schema===false) {
			global $translation_namespace;
			$this->schema = $translation_namespace;
		}
		// Make the comment pristine so that we match on it correctly.
		$comment = stripslashes($comment);
		tlschema("commentary");
		$this->doublepost = 0;
		$this->emptypost = 0;
		$colorcount = 0;
		if ($comment !="") {

			$commentary = str_replace("`n","",soap($comment));
			$y = strlen($commentary);
			for ($x=0;$x<$y;$x++){
				if (substr($commentary,$x,1)=="`"){
					$colorcount++;
					if ($colorcount>=getsetting("maxcolors",10)){
						$commentary = substr($commentary,0,$x).color_sanitize(substr($commentary,$x));
						$x=$y;
					}
					$x++;
				}
			}

			$args = array('commentline'=>$commentary, 'commenttalk'=>translate_inline($this->talkline,$this->schema));
			$args = modulehook("commentary", $args);
			$commentary = $args['commentline'];
			$talkline = $args['commenttalk'];
			tlschema($this->schema);
			$talkline = translate_inline($talkline);
			tlschema();


			$commentary = preg_replace("'([^[:space:]]{45,45})([^[:space:]])'","\\1 \\2",$commentary);
			$commentary = addslashes($commentary);
			// do an emote if the area has a custom talkline and the user
			// isn't trying to emote already.
			if ($talkline!="says" && substr($commentary,0,1)!=":" &&
			substr($commentary,0,2)!="::" &&
			substr($commentary,0,3)!="/me" &&
			substr($commentary,0,5) != "/game") {
				$commentary = ":`3$talkline, \\\"`#$commentary`3\\\"";
			}
			if (substr($commentary,0,5)=="/game" && ($user->superuser&SU_IS_GAMEMASTER)==SU_IS_GAMEMASTER) {
				//handle game master inserts now, allow double posts
				$this->injectsystemcomment($commentary);
			} else {
				$sql = "SELECT comment,author FROM " . DB::prefix("commentary") . " WHERE section='{$this->section}' ORDER BY commentid DESC LIMIT 1";
				$result = DB::query($sql);
				$row = DB::fetch_assoc($result);
				DB::free_result($result);
				if ($row['comment']!=stripslashes($commentary) ||
				$row['author']!=$user->acctid){
					$this->injectrawcomment($user->acctid,
					$commentary);
					$user->laston=date("Y-m-d H:i:s");
				} else {
					$this->doublepost = 1;
				}
			}
			tlschema();
		}
	}

	/**
	 * Adds a comment from a gamemaster to the database
	 * 
	 * @uses Commentary::injectrawcomment()
	 * @param string $comment The comment
	 */
	protected function injectsystemcomment($comment) {
		//function lets gamemasters put in comments without a user association...be careful, it is not trackable who posted it
		if (strncmp($comment, "/game", 5) !== 0) {
			$comment = "/game" . $comment;
		}
		$this->injectrawcomment(0,$comment);
	}

	/**
	 * Inserts a comment into the database
	 * 
	 * @param int $author The id of the author
	 * @param string $comment The comment
	 */
	protected function injectrawcomment($author, $comment) {
		$sql = "INSERT INTO " . DB::prefix("commentary") . " (postdate,section,author,comment) VALUES ('".date("Y-m-d H:i:s")."','{$this->section}',$author,\"$comment\")";
		DB::query($sql);
		massinvalidate("comments-{$this->section}");
		// invalidate moderation screen also.
		invalidatedatacache("comments-or11");
	}

	/**
	 * Returns the total comments in the section
	 * 
	 * @return int
	 */
	public function countTotalComments() {
		$sql = "SELECT count(commentid) AS total FROM " .
		DB::prefix("commentary") . " LEFT JOIN " .
		DB::prefix("accounts") . " ON " .
		DB::prefix("accounts") .  ".acctid = " .
		DB::prefix("commentary"). ".author " .
		"WHERE section = '{$this->section}' AND " .
			"( ".DB::prefix("accounts") . ".locked=0 OR ".DB::prefix("accounts") .".locked is null ) ";
		$res = DB::query_cached($sql, "comments-{$this->section}-count");
		return $res[0]['total'];
	}

	/**
	 * Gets comments in the section for a given page
	 * 
	 * Two ways of getting the page:
	 * 
	 * If $cid is set, the page starting with the comment
	 * with id $cid is return.
	 * 
	 * Otherwise, the page starting at $page * $limit is
	 * returned.
	 * 
	 * @param int $page The page
	 * @param int $cid The comment id
	 * @return array
	 */
	protected function getComments($page=0, $cid=0) {
		$sql = "SELECT ". DB::prefix("commentary") . ".*, " .
		DB::prefix("accounts").".name, " .
		DB::prefix("accounts").".acctid, " .
		DB::prefix("accounts").".clanrank, " .
		DB::prefix("clans") .  ".clanshort FROM " .
		DB::prefix("commentary") . " LEFT JOIN " .
		DB::prefix("accounts") . " ON " .
		DB::prefix("accounts") .  ".acctid = " .
		DB::prefix("commentary"). ".author LEFT JOIN " .
		DB::prefix("clans") . " ON " .
		DB::prefix("clans") . ".clanid=" .
		DB::prefix("accounts") .
			".clanid WHERE section = '{$this->section}' AND " .
			"( ".DB::prefix("accounts") . ".locked=0 OR ".DB::prefix("accounts") .".locked is null ) ";
		if($cid) {
			$sql .= "AND commentid > '$cid' " .
			"ORDER BY commentid ASC LIMIT {$this->limit}";
		} else {
			$sql .= "ORDER BY commentid DESC LIMIT " .
			($page*$this->limit).",{$this->limit}";
		}
		if ($page && !$cid) {
			$result = DB::query_cached($sql,"comments-{$this->section}-{$page}");
		} else {
			$result = DB::query($sql);
		}
		return $result;
	}

	/**
	 * Prepares a comment for display
	 * 
	 * Adds the author's name, clan, time stamp
	 * and a link to the author's bio.
	 * 
	 * @param array $row The row from the database containing the comment
	 * @return string
	 */
	protected function prepComment($row) {
		$row['comment'] = comment_sanitize($row['comment']);

		$link = "bio.php?char=" . $row['acctid'] .
			"&ret=".URLEncode($_SERVER['REQUEST_URI']);

		$ft = "";
		if (substr($row['comment'],0,2)=="::") {
			$ft = '::';
		} elseif ($row['comment']{0}==":") {
			$ft = ':';
		} elseif (substr($row['comment'],0,3)=="/me") {
			$ft = '/me';
		} elseif (substr($row['comment'],0,5)=="/game") {
			$ft = '/game';
		}

		$comment = "";

		$row['comment'] = holidayize($row['comment'],'comment');
		$row['name'] = holidayize($row['name'],'comment');
		if ($row['clanrank']) {
			$row['name'] = ($row['clanshort']>"" ? self::$clanrankcolors[ceil($row['clanrank']/10)] . "&lt;`2{$row['clanshort']}" . self::$clanrankcolors[ceil($row['clanrank']/10)] . "&gt; `&" : "").$row['name'];
		}
		if ($ft=="::" || $ft=="/me" || $ft==":"){
			$x = strpos($row['comment'],$ft);
			if ($x!==false){
				if ($this->linkbios) {
					$comment = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0<a href='$link' style='text-decoration: none'>\n`&{$row['name']}`0</a>\n`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				} else {
					$comment = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&{$row['name']}`0`& ".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
				}
			}
		}

		if ($ft=="/game" && !$row['name']) {
			$x = strpos($row['comment'],$ft);
			if ($x!==false){
				$comment = str_replace("&amp;","&",HTMLEntities(substr($row['comment'],0,$x), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`&".str_replace("&amp;","&",HTMLEntities(substr($row['comment'],$x+strlen($ft)), ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`0`n";
			}
		}

		if($comment == "") {
			if ($this->linkbios) {
				$comment = "`0<a href='$link' style='text-decoration: none'>`&{$row['name']}`0</a>`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
			} elseif (substr($ft,0,5)=='/game' && !$row['name']) {
				$comment = str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
			} else {
				$comment = "`&{$row['name']}`3 says, \"`#".str_replace("&amp;","&",HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1")))."`3\"`0`n";
			}
		}

		global $user;

		if (!array_key_exists('timestamp', $user->prefs)) {
			$user->prefs['timestamp'] = 0;
		}

		$user->addPref('timeoffset', round($user->prefs['timeoffset'],1));

		if ($user->prefs['timestamp']==1) {
			if (!isset($user->prefs['timeformat'])) {
				$user->prefs['timeformat'] = "[m/d h:ia]";
			}
			$time = strtotime($row['postdate']) + ($user->prefs['timeoffset'] * 60 * 60);
			$s=date("`7" . $user->prefs['timeformat'] . "`0 ",$time);
			$comment = $s.$comment;
		}elseif ($user->prefs['timestamp']==2) {
			$s=reltime(strtotime($row['postdate']));
			$comment = "`7($s)`0 ".$comment;
		}
		if ($row['postdate']>=$user->recentcomments) {
			$comment="<img src='images/new.gif' alt='&gt;' width='3' height='5' align='absmiddle'> ".$comment;
		}
		addnav("",$link);

		return $comment;
	}

	/**
	 * Displays the comments
	 */
	public function display_comments() {
		// Check to see if we're blocked
		if ($this->blocked)	{
			return;
		}

		output($this->intro);

		rawoutput("<a name='{$this->section}'></a>");

		if ($this->schema === false) {
			global $translation_namespace;
			$this->schema = $translation_namespace;
		}

		tlschema("commentary");

		if ($this->doublepost) {
			output("`\$`bDouble post?`b`0`n");
		}

		if ($this->emptypost) {
			output("`\$`bWell, they say silence is a virtue.`b`0`n");
		}

		// Needs to be here because scrolling through the commentary pages, entering a bio, then scrolling again forward
		// then re-entering another bio will lead to $com being smaller than 0 and this will lead to an SQL error later on.
		$com=(int)httpget("comscroll");

		if ($com < 0) {
			$com = 0;
		}

		global  $user;

		if (httpget("comscroll") !==false && (int)Session::get('lastcom') ==$com+1) {
			$cid = (int)Session::get('lastcommentid');
		} else {
			$cid = 0;
		}

		Session::set('lastcom', $com);

		$commentbuffer = array();
		$result = $this->getComments($com, $cid);
		while($row = DB::fetch_assoc($result)) {
			$commentbuffer[] = $row;
		}
		if(count($commentbuffer) == 0) {
			if($cid) {
				$cid -= $this->limit;
				if($cid < 0) {
					$cid = 0;
				}
			}
			$result = $this->getComments(0, $cid);
			while($row = DB::fetch_assoc($result)) {
				$commentbuffer[] = $row;
			}
		}
		if ($cid != 0) {
			$commentbuffer = array_reverse($commentbuffer);
		}

		if (isset($user->prefs['topcomment']) && $user->prefs['topcomment']) {
			$commentbuffer = array_reverse($commentbuffer);
		}

		$rowcount = count($commentbuffer);
		if ($rowcount > 0) {
			Session::set('lastcommentid', $commentbuffer[0]['commentid']);
		}

		$this->counttoday=0;
		$comments = array();

		for ($i=0; $i < $rowcount; $i++){
			$row = $commentbuffer[$i];
			if (date("Y-m-d",strtotime($row['postdate']))==date("Y-m-d")){
				if ($row['author']==$user->acctid) {
					$this->counttoday++;
				}
			}
			$comments[$i] = $this->prepComment($row);
			$commentids[$i] = $row['commentid'];
		}

		$i--;

		$del=translate_inline("Del");
		$scriptname=substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
		$pos=strpos($_SERVER['REQUEST_URI'],"?");
		$return=$scriptname.($pos==false?"":substr($_SERVER['REQUEST_URI'],$pos));
		$one=(strstr($return,"?")==false?"?":"&");

		for (;$i>=0;$i--){
			$out="";
			if ($user->superuser & SU_EDIT_COMMENTS && $this->showDelete) {
				$out.="`2[<a href='".$return.$one."removecomment={$commentids[$i]}&section={$this->section}&returnpath=".URLEncode($return)."'>$del</a>`2]`0&nbsp;";
				addnav("",$return.$one."removecomment={$commentids[$i]}&section={$this->section}&returnpath=".URLEncode($return)."");
			}
			$out.=$comments[$i];

			$args = array('commentline'=>$out);
			$args = modulehook("viewcommentary", $args);
			$val = $args['commentline'];
			output_notl($val, true);
		}
	}

	/**
	 * Adds navigation links to change page
	 */
	public function comscroll() {
		global $REQUEST_URI, $user;
		$jump = false;
		if (!isset($user->prefs['nojump']) || $user->prefs['nojump'] == false) {
			$jump = true;
		}
		tlschema("commentary");
		$firstu = translate_inline("&lt;&lt; First Unseen");
		$prev = translate_inline("&lt; Previous");
		$ref = translate_inline("Refresh");
		$next = translate_inline("Next &gt;");
		$lastu = translate_inline("Last Page &gt;&gt;");
		$currentPage = (int)httpget('comscroll');
		if($currentPage > $this->pages) {
			$currentPage = $this->pages;
		} elseif ($currentPage < 0) {
			$currentPage = 0;
		}
		$sql = "SELECT count(commentid) AS c FROM " . DB::prefix("commentary") . " WHERE section='{$this->section}' AND postdate > '{$user->recentcomments}'";
		$r = DB::query($sql);
		$val = DB::fetch_assoc($r);
		$val = floor($val['c'] / $this->limit) - 1;
		if ($val>0 && $currentPage < $val){
			$first = comscroll_sanitize($REQUEST_URI)."&comscroll=".($val);
			$first = str_replace("?&","?",$first);
			if (!strpos($first,"?")) $first = str_replace("&","?",$first);
			$first .= "&refresh=1";
			if ($jump) {
				$first .= "#{$this->section}";
			}
			output_notl("<a href=\"$first\">$firstu</a>",true);
			addnav("",$first);
		} else {
			output_notl($firstu,true);
		}
		if($currentPage < $this->pages) {
			$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($currentPage+1);
			$req = str_replace("?&","?",$req);
			if (!strpos($req,"?")) $req = str_replace("&","?",$req);
			$req .= "&refresh=1";
			if ($jump) {
				$req .= "#{$this->section}";
			}
			output_notl("<a href=\"$req\">$prev</a>",true);
			addnav("",$req);
		} else {
			output_notl($prev, true);
		}
		$last = appendlink(comscroll_sanitize($REQUEST_URI),"refresh=1");

		$last = appendcount($last);

		$last = str_replace("?&","?",$last);
		if ($jump) {
			$last .= "#{$this->section}";
		}
		output_notl("&nbsp;<a href=\"$last\">$ref</a>&nbsp;",true);
		addnav("",$last);
		if ($currentPage > 0){
			$req = comscroll_sanitize($REQUEST_URI)."&comscroll=".($currentPage-1);
			$req = str_replace("?&","?",$req);
			if (!strpos($req,"?")) $req = str_replace("&","?",$req);
			$req .= "&refresh=1";
			if ($jump) {
				$req .= "#{$this->section}";
			}
			output_notl(" <a href=\"$req\">$next</a>",true);
			addnav("",$req);
			output_notl(" <a href=\"$last\">$lastu</a>",true);
		}else{
			output_notl("$next $lastu",true);
		}
		tlschema();
	}

	/**
	 * Displays the form for posting comments
	 */
	public function talkform() {
		global $user;
		if ($user->loggedin) {
			$args = modulehook("insertcomment", array("section"=>$this->section,'talkline'=>$this->talkline,'schema'=>$this->schema));
			if (isset($args['mute']) && $args['mute'] && !($user->superuser & SU_EDIT_COMMENTS)) {
				output_notl("%s", $args['mutemsg']);
			} elseif ($this->counttoday<($this->limit/2) || ($user->superuser&~SU_DOESNT_GIVE_GROTTO) || !getsetting('postinglimit',1)) {
				output($this->message);

				if ($this->schema===false) {
					global $translation_namespace;
					$this->schema=$translation_namespace;
				}

				tlschema("commentary");

				$jump = false;
				if (isset($user->prefs['nojump']) && $user->prefs['nojump'] == true) {
					$jump = true;
				}

				if (round($this->limit/2,0)-$this->counttoday <= 0 && getsetting('postinglimit',1)){
					if ($user->superuser&~SU_DOESNT_GIVE_GROTTO){
						output("`n`)(You'd be out of posts if you weren't a superuser or moderator.)`n");
					}else{
						output("`n`)(You are out of posts for the time being.  Once some of your existing posts have moved out of the comment area, you'll be allowed to post again.)`n");
						return;
					}
				}

				if (translate_inline($this->talkline,$this->schema)!="says") {
					$tll = strlen(translate_inline($this->talkline,$this->schema))+11;
				} else {
					$tll=0;
				}

				global $REQUEST_URI;
				require_once("lib/forms.php");

				$req = comscroll_sanitize($REQUEST_URI)."&comment=1";
				$req = str_replace("?&","?",$req);
				if (!strpos($req,"?")) {
					$req = str_replace("&","?",$req);
				}
				if ($jump) {
					$req .= "#{$this->section}";
				}
				addnav("",$req);
				output_notl("<form action=\"$req\" method='post' autocomplete='false'>",true);
				$maxlength=getsetting("chatlength",200);
				previewfield("insertcommentary", $user->name, $this->talkline, true, array("size"=>"40", "maxlength"=>$maxlength-$tll));
				rawoutput("<input type='hidden' name='talkline' value='" . translate_inline($this->talkline,$this->schema) . "'>");
				rawoutput("<input type='hidden' name='counter' value='" . Session::get('counter') . "'>");
				Session::set('commentcounter', Session::get('counter'));
				output_notl("<input type='hidden' name='section' value='{$this->section}'>",true);

				$add = htmlentities(translate_inline("Add"), ENT_QUOTES, getsetting("charset", "ISO-8859-1"));

				output_notl("<input type='submit' class='button' value='$add'>`n",true);

				if (round($this->limit/2,0)-$this->counttoday < 3 && getsetting('postinglimit',1)){
					output("`)(You have %s posts left today)`n`0",(round($this->limit/2,0)-$this->counttoday));
				}

				rawoutput("<div id='previewtext'></div></form>");
				tlschema();
			} else {
				output($this->message);
				output("Sorry, you've exhausted your posts in this section for now.`0`n");
			}
		}
	}

	/**
	 * Returns the name of a section, given the section
	 * 
	 * @static 
	 */
	static public function getSectionName($section) {
		static $secs;
		tlschema("commentary");
		switch ($section) {
			case 'village':
				$section = sprintf_translate("%s Square", getsetting("villagename", LOCATION_FIELDS));
				break;
			case 'superuser':
				$section = translate_inline('Grotto');
				break;
			case 'shade':
				$section = translate_inline("Land of the Shades");
				break;
			case 'grassyfield':
				$section = translate_inline("Grassy Field");
				break;
			case 'inn':
				$section = getsetting("innname", LOCATION_INN);
				break;
			case 'motd':
				$section = translate_inline("MotD");
				break;
			case 'veterans':
				$section = translate_inline("Veterans Club");
				break;
			case 'hunterlodge':
				$section = translate_inline("Hunter's Lodge");
				break;
			case 'gardens':
				$section = translate_inline("Gardens");
				break;
			case 'waiting':
				$section = translate_inline("Clan Hall Waiting Area");
				break;
			case 'beta':
				$section = translate_inline("Pavilion");
				break;
			default:
				if(!is_array($secs) || !count($secs)) {
					$secs = modulehook('moderate', array());
				}
				if(isset($secs[$section]) && $secs[$section]) $section = $secs[$section];
				break;
		}
		tlschema();
		return $section;
	}
}
