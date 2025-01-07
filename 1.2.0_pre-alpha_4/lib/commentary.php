<?php
// translator ready
// addnews ready
// mail ready
require_once("lib/datetime.php");
require_once("lib/sanitize.php");
require_once("lib/http.php");

require_once 'lib/classes/commentary.php';

function commentdisplay($intro, $section, $message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
	$comments = new Commentary($section, $intro, $message, $limit, $talkline, $schema);
	$comments->addcommentary();
	$comments->display_comments();
	$comments->talkform();
	$comments->comscroll();
}

function viewcommentary($section,$message="Interject your own commentary?",$limit=10,$talkline="says",$schema=false) {
	commentdisplay("", $section, $message, $limit, $talkline, $schema);
}

function talkform() {
}

function addcommentary() {

}

function injectcommentary($section, $talkline, $message) {
	$commments = new ModerateCommentary($section);
	$_POST['section'] = $section;
	httppostset('section', $section);
	$_POST['insertcommentary'] = $message;
	httppostset('insertcommentary', $message);
	$commments->addcommentary();
}
?>
