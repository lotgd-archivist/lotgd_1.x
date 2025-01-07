<?php
$pdktotal = 0;
$pdkneg = false;
modulehook("pdkpointrecalc");
foreach($labels as $type=>$label) {
	$pdktotal += (int)$pdks[$type];
	if((int)$pdks[$type] < 0) $pdkneg = true;
}
if ($pdktotal == $dkills-$dp && !$pdkneg) {
	$dp += $pdktotal;
	$user->maxhitpoints += (5 * $pdks["hp"]);
	$user->attack += $pdks["at"];
	$user->defense += $pdks["de"];
	foreach($labels as $type=>$label) {
		$count = 0;
		if (isset($pdks[$type])) $count = (int)$pdks[$type];
		while($count) {
			$count--;
			$user->addDragonPoints($type);
		}
	}
}else{
	output("`\$Error: Please spend the correct total amount of dragon points.`n`n");
}
