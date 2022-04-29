<?php
// addnews ready
// translator ready
// mail ready
require_once("common.php");
require_once("lib/commentary.php");
require_once("lib/villagenav.php");

tlschema("gardens");

page_header("The Gardens");

addcommentary();
checkday();

output("`b`c`2The Gardens`0`c`b");

output("`n`nYou walk through a gate and on to one of the many winding paths that makes its way through the well-tended gardens.");
output("From the flowerbeds that bloom even in darkest winter, to the hedges whose shadows promise forbidden secrets, these gardens provide a refuge for those seeking out the Green Dragon; a place where they can forget their troubles for a while and just relax.`n`n");
output("One of the fairies buzzing about the garden flies up to remind you that the garden is a place for roleplaying and peaceful conversation, and to confine out-of-character comments to the other areas of the game.`n`n");
villagenav();
modulehook("gardens", array());

viewcommentary("gardens","Whisper here",30,"whispers");

page_footer();
?>
