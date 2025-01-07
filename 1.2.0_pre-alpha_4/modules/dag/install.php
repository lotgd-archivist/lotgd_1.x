<?php
function dag_install_private(){
	module_addhook("inn-desc");
	module_addhook("inn");
	module_addhook("superuser");
	module_addhook("newday");
	module_addhook("pvpwin");
	module_addhook("dragonkill");
	module_addhook("showsettings");
	module_addhook("delete_character");
	debug("Creating Bounty Table");
	$sql = "SHOW TABLES";
	$result = DB::query($sql);
	$bountytableisthere=false;
	while ($row = DB::fetch_assoc($result)){
		list(,$val)=each($row);
		if ($val==DB::prefix("bounty")){
			$bountytableisthere=true;
			break;
		}
	}
	if ($bountytableisthere){
		debug("The bounty table already exists on your server, not overwriting it.`n");
	}else{
		debug("Creating the bounty table.`n");
		$sql="CREATE TABLE " . DB::prefix("bounty") . " (
			bountyid int(11) unsigned NOT NULL auto_increment,
			amount int(11) unsigned NOT NULL default '0',
			target int(11) unsigned NOT NULL default '0',
			setter int(11) unsigned NOT NULL default '0',
			setdate datetime NOT NULL default '0000-00-00 00:00:00',
			status int(11) unsigned NOT NULL default '0',
			winner int(11) unsigned NOT NULL default '0',
			windate datetime NOT NULL default '0000-00-00 00:00:00',
			PRIMARY KEY (bountyid),
			INDEX(status),
			INDEX(target),
			INDEX(status,target)
		) ENGINE=INNODB";
		DB::query($sql);
	}
	//look to see if we're migrating bounties from the old system.
	$sql = "DESCRIBE " . DB::prefix("accounts");
	$result = DB::query($sql);
	while ($row = DB::fetch_assoc($result)){
		if ($row['Field']=="bounty"){
			$sql = "INSERT INTO " . DB::prefix("bounty") . " (amount,target,setdate) SELECT bounty,acctid,'".date("Y-m-d H:i:s")."' FROM " . DB::prefix("accounts") . " WHERE " . DB::prefix("accounts") . ".bounty > 0";
			debug("The bounty column was found in your accounts table, migrating its values to the bounty table.`n");
			DB::query($sql);
			debug("Dropping accounts column from the user table.`n");
			$sql = "ALTER TABLE " . DB::prefix("accounts") . " DROP bounty";
			DB::query($sql);
		}elseif ($row['Field']=="bounties"){
			$sql = "SELECT bounties,acctid FROM " . DB::prefix("accounts") . " WHERE bounties>0";
			$result1 = DB::query($sql);
			debug("Migrating bounty counts.`n");
			while ($row1 = DB::fetch_assoc($result1)){
				$sql = "INSERT INTO " . DB::prefix("module_userprefs") . " (modulename,setting,userid,value) VALUES ('dag','bounties',{$row1['acctid']},{$row1['bounties']})";
				DB::query($sql);
			}//end while
			debug("Dropping bounty count from the user table.`n");
			$sql = "ALTER TABLE " . DB::prefix("accounts") . " DROP bounties";
			DB::query($sql);
		}//end if
	}//end while
	return true;
}
