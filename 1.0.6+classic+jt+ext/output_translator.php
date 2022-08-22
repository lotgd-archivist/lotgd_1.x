<?php
// PRIVATE_CODE
// translator ready
// mail ready
// addnews ready
function list_all_files($dirname,$inmask="*.php"){
	if (is_link($dirname)) return array();
	$mask = str_replace(".","\.",$inmask);
	$mask = str_replace("*",".*",$mask);
	$mask = str_replace("?",".",$mask);
	$mask = "/^$mask\$/";
	$d = dir($dirname);
	$dirs = array();
	$output=array();
	while (($entry = $d->read())!==false){
		if (is_dir($d->path."/".$entry) && !is_link($d->path."/".$entry)){
			if ($entry!="." && $entry!="..") 
				array_push($dirs,$d->path."/".$entry);
		}else{
			if (preg_match($mask,$entry)){
				array_push($output,$d->path."/".$entry);
			}
		}
	}
	$d->close();
	reset($dirs);
	while (list($key,$val)=each($dirs)){
		$output = array_merge($output,list_all_files($val,$inmask));
	}
	return $output;
}

$files = list_all_files(".","*.php");
reset($files);
$pattern =  ''
			.'(?<=\\()\s*$[[:alnum:]_\\[\\]\'"-]+' //output($variable...
			.'|'
			.'{\\$[^}]+}' //output(" {$variable} ")
			.'|'
//			.'(?<!\\\\)"\s*\\.\s*\\$[[:alnum:]]+(?:\\[[\'"]?[[:alnum:]_-]+[\'"]\])*\s*\\.\s*\\"' //output(" ".$variable." ")
			.'(?<!\\\\)"\s*\\.\s*[^\"]+\s*\\.\s*\\"' //output(" ".$variable." ")
			.'|'
			.'(?<!\\\\)\\$[[:alnum:]_-]+' //output(" $variable ")
			;
echo str_replace("|","<br>|<br>",htmlentities($pattern));
echo "<table border='1'><tr><td>Original</td><td>Vars</td><td>Final</td></tr>";
while (list($key,$val)=each($files)){
	echo "<tr bgcolor='#CCCCCC'><td colspan='3'><b>".$val."</b></td></tr>";
	$file = @join(@file($val),"\n");
//	echo htmlentities(substr($file,0,50));
	$count = preg_match_all("/\boutput\(.*\\\$.*\);/",$file,$matches);
	$m = $matches[0];
	while (list($k,$from)=each($m)){
		$to = $from;
		preg_match_all("/($pattern).*\\\"/",$from,$matches);
		if (count($matches[1])>0){
			echo "<tr>";
			echo "<td>".htmlentities($from)."</td>";
			$vars=array();
			while(preg_match("/($pattern)/",$to,$matches,PREG_OFFSET_CAPTURE)){
			//preg_match("/($pattern)/",$to,$matches,PREG_OFFSET_CAPTURE);
			//echo "<pre>";
			//var_dump($matches);
			//echo "</pre>";
				$x = $matches[1][1];
				$var = $matches[1][0];
				$to = substr($to,0,$x)."%s".substr($to,$x+strlen($var));
				//echo htmlentities($to)."<br>";
				if (substr($var,0,1)=="{" && substr($var,-1)=="}")
					$var = substr($var,1,strlen($var)-2);
				$var = preg_replace('/^"\s*\.\s*|\s*\.\s*"$/','',$var);
				array_push($vars,$var);
			}
			$strvars = join($vars,", ");
			echo "<td>".htmlentities($strvars)."</td>";
			echo "<td>";
			if (count($vars>0)){
				/*
				if (preg_match("/,\s*true\s*\\);/",$to,$matches)){
					$startto = substr($to,0,strlen($matches[0][0]));
					$endto = substr($to,-strlen($matches[0][0]));
				}else{
					$startto = substr($to,0,strlen($to)-2);
					$endto = substr($to,-2);
				}
				echo htmlentities($startto.", ".join($vars,", ").$endto);
				*/
				$to = preg_replace("/^(.*?)(,\s*true\s*)?(\\);)$/","\\1, ".str_replace("\\","\\\\",$strvars)."\\2\\3",$to);
				echo htmlentities($to);
			}else{
				echo "Couldn't find any vars -- that's odd.";
			}
			echo "</td>";
			echo "</tr>";
		}
	}
}
echo "</table>";
?>
