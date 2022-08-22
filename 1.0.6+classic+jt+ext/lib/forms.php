<?php
function previewfield($name, $startdiv=false, $talkline="says", $charsleft=true, $info=false) {
	global $schema,$session;
	$talkline = translate_inline($talkline, $schema);
	$youhave = translate_inline("You have ");
	$charsleft = translate_inline(" characters left.");

	if ($startdiv === false)
		$startdiv = "";
	$args = modulehook("commentary-js", array("speech"=>"", "emote"=>""));
	$speech = $args['speech'];
	$emote = $args['emote'];
	rawoutput("<script language='JavaScript'>
				function previewtext$name(t,l){
					var out = \"<span class=\'colLtWhite\'>".addslashes(appoencode($startdiv))." \";
					var end = '</span>';
					var x=0;
					var y='';
					var z='';
					var max=document.getElementById('input$name');
					var charsleft='';
					var speech='$speech';
					var emote='$emote';");
	if ($talkline !== false) {
		rawoutput("	if (t.substr(0,2)=='::'){
						x=2;
						t = t.replace(/::/, '::'+emote);
						out += '</span><span class=\'colLtWhite\'>';
					}else if (t.substr(0,1)==':'){
						x=1;
						t = t.replace(/:/, ':'+emote);
						out += '</span><span class=\'colLtWhite\'>';
					}else if (t.substr(0,3)=='/me'){
						x=3;
						t = t.replace(/\/me/, '/me'+emote);
						out += '</span><span class=\'colLtWhite\'>';");
		if ($session['user']['superuser']&SU_IS_GAMEMASTER) {
			rawoutput("
					}else if (t.substr(0,5)=='/game'){
						x=5;
						out = '<span class=\'colLtWhite\'>';");
		}
		rawoutput("	}else{
						t = speech+t;
						out += '</span><span class=\'colDkCyan\'>".addslashes(appoencode($talkline)).", \"</span><span class=\'colLtCyan\'>';
						end += '</span><span class=\'colDkCyan\'>\"';
					}");
	}
	if ($charsleft == true) {
		rawoutput("	if (x!=0) {
						if (max.maxLength!=200) max.maxLength=200;
						l=200;
					} else {
						max.maxLength=l;
					}
					if (l-t.length<0) charsleft +='<span class=\'colLtRed\'>';
					charsleft += '".$youhave."'+(l-t.length)+'".$charsleft."<br>';
					if (l-t.length<0) charsleft +='</span>';
					document.getElementById('charsleft$name').innerHTML=charsleft+'<br/>';");
	}
	rawoutput("		for (; x < t.length; x++){
						y = t.substr(x,1);
						if (y=='<'){
							out += '&lt;';
							continue;
						}else if(y=='>'){
							out += '&gt;';
							continue;
						}else if (y=='`'){
							if (x < t.length-1){
								z = t.substr(x+1,1);
								if (z=='0'){
									out += '</span>';
								}else if (z=='1'){
									out += '</span><span class=\'colDkBlue\'>';
								}else if (z=='2'){
									out += '</span><span class=\'colDkGreen\'>';
								}else if (z=='3'){
									out += '</span><span class=\'colDkCyan\'>';
								}else if (z=='4'){
									out += '</span><span class=\'colDkRed\'>';
								}else if (z=='5'){
									out += '</span><span class=\'colDkMagenta\'>';
								}else if (z=='6'){
									out += '</span><span class=\'colDkYellow\'>';
								}else if (z=='7'){
									out += '</span><span class=\'colDkWhite\'>';
								}else if (z=='q'){
									out += '</span><span class=\'colDkOrange\'>';
								}else if (z=='!'){
									out += '</span><span class=\'colLtBlue\'>';
								}else if (z=='@'){
									out += '</span><span class=\'colLtGreen\'>';
								}else if (z=='#'){
									out += '</span><span class=\'colLtCyan\'>';
								}else if (z=='$'){
									out += '</span><span class=\'colLtRed\'>';
								}else if (z=='%'){
									out += '</span><span class=\'colLtMagenta\'>';
								}else if (z=='^'){
									out += '</span><span class=\'colLtYellow\'>';
								}else if (z=='&'){
									out += '</span><span class=\'colLtWhite\'>';
								}else if (z=='Q'){
									out += '</span><span class=\'colLtOrange\'>';
								}else if (z==')'){
									out += '</span><span class=\'colLtBlack\'>';
								}
								x++;
							}
						}else{
							out += y;
						}
					}
					document.getElementById(\"previewtext$name\").innerHTML=out+end+'<br/>';
				}
				</script>
				");
	if ($charsleft == true) {
		rawoutput("<span id='charsleft$name'></span>");
	}
	if (!is_array($info)) {
		rawoutput("<input name='$name' id='input$name' maxlength='255' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,200);'>");
	} else {
		if (isset($info['maxlength'])) {
			$l = $info['maxlength'];
		} else {
			$l=200;
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			rawoutput("<textarea name='$name' id='input$name' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,$l);' ");
		} else {
			rawoutput("<input name='$name' id='input$name' onKeyUp='previewtext$name(document.getElementById(\"input$name\").value,$l);' ");
		}
		foreach ($info as $key=>$val){
			rawoutput("$key='$val'");
		}
		if (isset($info['type']) && $info['type'] == 'textarea') {
			rawoutput("></textarea>");
		} else {
			rawoutput(">");
		}
	}
	rawoutput("<div id='previewtext$name'></div>");
}
?>
