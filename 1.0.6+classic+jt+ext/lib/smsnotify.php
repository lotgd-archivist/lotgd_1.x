<?php
// PRIVATE_CODE
// This is only useful for Eric
// translator ready
// addnews ready
// mail ready

require_once("lib/http.php");

if (file_exists("/tmp/smsnotify")){
	$last = filemtime("/tmp/smsnotify");
}else{
	$last = 0;
}
if ($last < strtotime("now -30 min") && file_exists("sms.yes")){
	touch("/tmp/smsnotify");
	$smsmessage = httpget('message');
	
	//$smstype = "post";
	$smstype = "email";
	
	if ($smstype="post"){
		$info = array(
			"DOMAIN_NAME"=>"@vtext.com",
			"min"=>"[redacted by archivist]",
			"subject"=>"LoGD Alert",
			"text"=>$smsmessage,
			"callback"=>"[redacted by archivist]",
			"trackResponses"=>No,
			"sender"=>"trash@mightye.org",
			"disclaimer_submit.x"=>22,
			"disclaimer_submit.y"=>15
		);
		$data = "";
		$i=0;
		while (list($key,$val)=each($info)){
			$i++;
			if ($i>1) $data.="&";
			$data.=rawurlencode($key)."=".rawurlencode($val);
		}
		$cmd = 'echo "Content-Type: application/x-www-form-urlencoded
	Content-Length: '.strlen($data).'

'.$data.'
---
---
"| lynx -source http://www.vtext.com/customer_site/jsp/disclaimer.jsp -post_data 2>&1 > /dev/null';
		`$cmd`;
	}elseif($smstype="email"){
		mail("[redacted by archivist]@vtext.com","LoGD Alert",$smsmessage);
	}
}else{
	//sent sms too recently.
}
?>
