<?php
/**
 * ModerateCommentary class file
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage commentary
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
/**
 * ModerateCommentary class
 *
 * This class provides the moderation
 * comment display. Mainly by overriding
 * parent methods to extend the basic
 * functionality to include the moderation
 * extras. 
 *
 * @copyright Copyright © 2002-2005, Eric Stevens & JT Traub, © 2006-2008, Dragonprime Development Team
 * @version Lotgd 1.2.0 DragonPrime Edition
 * @package Core
 * @subpackage commentary
 * @license http://creativecommons.org/licenses/by-nc-sa/2.0/legalcode
 */
class ModerateCommentary extends Commentary {
	/**
	 * @var array An array of sections that shouldn't get displayed. 0 = display, 1 = exact match, 2 = partial match.
	 */
	protected $block = array('pet-'=>2);
	
	/**
	 * Initialises the class.
	 * 
	 * No say line or intro are used, so you
	 * can't set them.
	 * 
	 * Enables links to bios.
	 * 
	 * Disables the [Del] link.
	 * 
	 * @param string $section The section that the class should display. A false value displays all.
	 * @param int $limit How much comments to disply per section.
	 * @param array $extraBlock Any sections to block. See {@link ModerateCommentary::$block} for more information.
	 */
	public function __construct($section = '', $limit=100, $extraBlock = false) {
		parent::__construct($section, "", "", $limit);
		$this->linkbios = true;
		$this->showDelete = false;
		if(is_array($extraBlock)) {
			$this->block = array_merge($this->block, $extraBlock);
		}
	}

	/**
	 * Adds a comment to the database.
	 * 
	 * If the class is displaying all sections,
	 * the httppost() check would fail, so grabs
	 * the section, calls {@link
	 * Commentary::addcommentary()} then unsets
	 * the section.
	 * 
	 * Otherwise, just calls {@link
	 * Commentary::addcommentary()}
	 */
	public function addcommentary() {
		if(!$this->section) {
			$this->section = httppost('section');
			parent::addcommentary();
			$this->section = "";
		} else {
			parent::addcommentary();
		}
	}
	
	/**
	 * Displays comscroll only if not displaying all sections.
	 */
	public function comscroll() {
		if($this->section) parent::comscroll();
	}

	/**
	 * Prepares a comment for display
	 * 
	 * Adds the delete checkbox and ban link, then
	 * calls {@link Commentary::prepComment()}
	 * 
	 * @param array $row The row from the database containing the comment
	 * @return string
	 */
	public function prepComment($row) {
		global $user;
		if ($user->superuser & SU_EDIT_USERS){
			$rawc = HTMLEntities($row['comment'], ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
			$rawc = full_sanitize($rawc);
			$rawc = htmlentities($rawc, ENT_QUOTES, getsetting("charset", "ISO-8859-1"));
			$out ="`0[ <input type='checkbox' name='comment[{$row['commentid']}]'> | <a href='user.php?op=setupban&userid=".$row['author']."&reason=".rawurlencode($rawc)."'>Ban</a> ]&nbsp;";
			addnav("","user.php?op=setupban&userid={$row['author']}&reason=".rawurlencode($rawc));
		}else{
			$out ="`0[ <input type='checkbox' name='comment[{$row['commentid']}]'> ]&nbsp;";
		}
		return $out . "`0({$row['section']}) " . parent::prepComment($row);
	}

	/**
	 * Displays the comments
	 */
	public function display_comments() {
		if($this->section) {
			output_notl("`n<hr><a href='moderate.php?area=%s'>`b`^%s`0`b</a>`n",
			$this->section, self::getSectionName($this->section), true);
			addnav("", "moderate.php?area=" . $this->section);
			parent::display_comments();
			return;
		}
		foreach (self::getSections() as $section) {
			foreach ($this->block as $pattern=>$level) {
				if($level == 1 && $section == $pattern) {
					continue 2;
				}
				if($level == 2 && $pattern == substr($section, 0, strlen($pattern))) {
					continue 2;
				}
			}
			$this->section = $section;
			output_notl("`n<hr><a href='moderate.php?area=%s'>`b`^%s`0`b</a>`n",
			$this->section, self::getSectionName($this->section), true);
			addnav("", "moderate.php?area=" . $this->section);

			parent::display_comments();
		}
		$this->section = "";
	}

	/**
	 * Displays the talkform, including section dropdown if needed
	 */
	public function talkform() {
		global $user;
		modulehook("insertcomment", array("section"=>$this->section));
		if ($this->schema===false) {
			global $translation_namespace;
			$this->schema=$translation_namespace;
		}

		tlschema("commentary");

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
		addnav("",$req);
		output_notl("<form action=\"$req\" method='post' autocomplete='false'>",true);
		$maxlength=getsetting("chatlength",200);
		previewfield("insertcommentary", $user->name, $this->talkline, true, array("size"=>"40", "maxlength"=>$maxlength-$tll));
		rawoutput("<input type='hidden' name='talkline' value='" . translate_inline($this->talkline,$this->schema) . "'>");
		rawoutput("<input type='hidden' name='counter' value='" . Session::get('counter') ."'>");
		Session::set('commentcounter', Session::get('counter'));
		if($this->section) {
			output_notl("<input type='hidden' name='section' value='{$this->section}'>",true);
		} else {
			rawoutput("<select name='section'>");
			foreach (self::getSections() as $section) {
				rawoutput("<option value='$section'>" . self::getSectionName($section) . "</option>");
			}
			rawoutput("</select>");
		}

		$add = htmlentities(translate_inline("Add"), ENT_QUOTES, getsetting("charset", "ISO-8859-1"));

		output_notl("<input type='submit' class='button' value='$add'>`n",true);

		rawoutput("<div id='previewtext'></div></form>");
		tlschema();
	}

	/**
	 * Outputs the buttons and raw HTML that start the moderation form
	 */
	static public function startForm() {
		$scriptname=substr($_SERVER['SCRIPT_NAME'],strrpos($_SERVER['SCRIPT_NAME'],"/")+1);
		addnav("","$scriptname?op=commentdelete&return=".URLEncode($_SERVER['REQUEST_URI']));
		$mod_Del1 = htmlentities(translate_inline("Delete Checked Comments"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del2 = htmlentities(translate_inline("Delete Checked & Ban (3 days)"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del_confirm = addslashes(htmlentities(translate_inline("Are you sure you wish to ban this user and have you specified the exact reason for the ban, i.e. cut/pasted their offensive comments?"), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
		$mod_reason = translate_inline("Reason:");
		$mod_reason_desc = htmlentities(translate_inline("Banned for comments you posted."), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));

		output_notl("<form action='$scriptname?op=commentdelete&return=".URLEncode($_SERVER['REQUEST_URI'])."' method='POST'>",true);
		output_notl("<input type='submit' class='button' value=\"$mod_Del1\">",true);
		output_notl("<input type='submit' class='button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">",true);
		output_notl("`n$mod_reason <input name='reason0' size='40' value=\"$mod_reason_desc\" onChange=\"document.getElementById('reason').value=this.value;\">`n",true);
	}

	/**
	 * Outputs the buttons and raw HTML that end the moderation form
	 */
	public function endForm() {
		$mod_Del1 = htmlentities(translate_inline("Delete Checked Comments"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del2 = htmlentities(translate_inline("Delete Checked & Ban (3 days)"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
		$mod_Del_confirm = addslashes(htmlentities(translate_inline("Are you sure you wish to ban this user and have you specified the exact reason for the ban, i.e. cut/pasted their offensive comments?"), ENT_COMPAT, getsetting("charset", "ISO-8859-1")));
		$mod_reason = translate_inline("Reason:");
		$mod_reason_desc = htmlentities(translate_inline("Banned for comments you posted."), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));

		output_notl("`n");
		rawoutput("<input type='submit' class='button' value=\"$mod_Del1\">");
		rawoutput("<input type='submit' class='button' name='delnban' value=\"$mod_Del2\" onClick=\"return confirm('$mod_Del_confirm');\">");
		output_notl("`n%s ", $mod_reason);
		rawoutput("<input name='reason' size='40' id='reason' value=\"$mod_reason_desc\">");
		if(!$this->section) {
			output_notl('`n');
			parent::comscroll();
		}
		rawoutput("</form>");
		output_notl("`n");
	}

	/**
	 * Returns all commentary sections
	 * 
	 * @static 
	 * @return array
	 */
	static public function getSections() {
		$sql = "SELECT `section` FROM `" . DB::prefix('commentary') . "` GROUP BY section";
		$res = DB::query_cached($sql, 'comment-sections', 86400);
		$return = array();
		while ($row = DB::fetch_assoc($res)) {
			$return[] = $row['section'];
		}
		return $return;
	}
}
