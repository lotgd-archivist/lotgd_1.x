<?php
output_notl("<form action='mail.php?op=write' method='post'>",true);
output("`b`2Address:`b`n");
$to = translate_inline("To: ");
$search = htmlentities(translate_inline("Search"), ENT_COMPAT, getsetting("charset", "ISO-8859-1"));
output_notl("`2$to <input name='to' id='to' value=\"".htmlentities(stripslashes(httpget('prepop')), ENT_COMPAT, getsetting("charset", "ISO-8859-1"))."\">",true);
output_notl("<input type='submit' class='button' value=\"$search\">", true);
rawoutput("</form>");
rawoutput("<script type='text/javascript'>document.getElementById(\"to\").focus();</script>");
?>
