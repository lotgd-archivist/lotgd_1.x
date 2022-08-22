<?php
// PRIVATE_CODE
// addnews ready
// mail ready
// translator ready
function store_getmoduleinfo(){
	$info = array(
		"name"=>"LoGD Store",
		"allowanonymous"=>true,
		"author"=>"Eric Stevens",
		"version"=>"1.0",
		"category"=>"General",
		"settings"=>array(
			"LOGD Store Settings,title",
			"storeserver"=>"Server that holds the store data?|http://lotgd.net"
			),
	);
	return $info;
}

function store_install(){
	module_addhook("index");
	module_addhook("footer-shades");
	module_addhook("village");
	return true;
}

function store_uninstall(){
	return true;
}

function store_dohook($hookname,$args){
	if ($hookname == "village") {
		tlschema($args['schemas']['othernav']);
		addnav($args['othernav']);
		tlschema();
	} else {
		addnav("Other");
	}
	addnav("\$?LoGD Merchandise","runmodule.php?module=store&op=shop");
	return $args;
}

function store_run(){
	$op = httpget("op");
	if ($op=="shop"){
		page_header("LoGD Merchandise");
		if (get_module_setting("storeserver")>""){
			if (getsetting("usedatacache",0)){
				if (($store = datacache("mod-store",60))===false){
					require_once("lib/pullurl.php");
					$data = pullurl(get_module_setting("storeserver")."/runmodule.php?module=store&op=list");
					if ($data === false) $data = array();
					$store = unserialize(join("", $data));
					updatedatacache("mod-store",$store);
				}
			}else{
				output("You must turn on data caching in order to use the store.");
			}
		}else{
			$store = store_getstore();
		}
		if (is_array($store) && count($store)>0){
			reset($store);
			$cols=3;
			$oldcategory = "";
			$i=0;
			$j=0;
			rawoutput("<table border='0' cellpadding='7' cellspacing='0'>");
			while (list($name,$category)=each($store)){
				rawoutput("<tr class='trhead'><td colspan='$cols'>$name</td></tr>");
				rawoutput("<tr>");
				$i=0;
				while (list($index,$item)=each($category)){
					if ($i == 0){
						rawoutput("<tr>");
					}
					rawoutput("<td class='".($j%2?"trlight":"trdark")."' align='center'>");
					if (!is_array($item)){
						rawoutput($item);
					}else{
						rawoutput("<a href='".$item['link']."' target='_blank'><img src='".$item['img']."' width='".($item['width']==""?"150":$item['width'])."' height='".($item['height']==""?"150":$item['height'])."' alt=\"".htmlentities($item['name'])."\" border='0'><br>". $item['name'] . "</a><br><b>".$item['cost']."</b>");
					}
					rawoutput("</td>");
					if ($i == $cols-1){
						rawoutput("</tr>");
					}
					$i=($i+1)%$cols;
					$j++;
				}//end while
				for ($x=0;$x<$cols-($i%$cols);$x++){
					rawoutput("<td class='".($j%2?"trlight":"trdark")."'>&nbsp;</td>");
					$j++;
				}
				rawoutput("</tr>");
			}//end while
			rawoutput("</table>");
			
		}else{
			output("`bThere are no items in the LoGD store!`b");
			rawoutput(serialize($store));
		}
		global $session,$REQUEST_URI;
		if ($session['user']['loggedin']){
			if ($session['user']['hitpoints'] > 0) {
				require_once("lib/villagenav.php");
				villagenav();
			} else {
				addnav("S?Return to the Shades","shades.php");
			}
			addnav("",$REQUEST_URI);
		}else{
			addnav("L?Return to Login","index.php");
		}
		page_footer();
	}elseif($op=="list"){
		echo serialize(store_getstore());
		exit();
	}
}

function store_getstore(){
	/*
			array("img"=>"","name"=>"","link"=>"","cost"=>""),
	*/
	$store = array(//woo hack hack hack.
		"Staff Favorites</td></tr><tr class='trlight'><td colspan='3'>These items are the favorites of lotgd.net's staff.  
		The same items can be found below this section along with all the other merchandise, but we duped them up here
		for convenience sake.<br><br>
		I resisted the urge to start up a LoGD merchandise shop for a long time, I didn't really want to commercialize
		the game like that, but one day when I casually mentioned this to a friend, they pointed out that by <i>not</i>
		offering merchandise, I was robbing the players of the opportunity to have LoGD merchandise if they wanted it.
		I could understand it this way, and the previews I've given a few trusted individuals have been very positive,
		so I suppose it's time to unleash this on the world.
		<br><br>
		I know some of the items are a little pricey, but that's because some of the base items from CafePress were pricey too.
		I apologize for anything you see that might look too expensive, it wasn't my intention to rip you off, and I only
		put a small markup on any of these items."=>array(
			array("img"=>"http://storetn.cafepress.com/0/10889820_F_store.jpg","name"=>"Mr. Bear in LoGD Medallion Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889820","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/4/10889774_F_store.jpg","name"=>"LoGD Medallion Wall Clock","link"=>"http://www.cafeshops.com/logdmedallion.10889774","cost"=>"$15.99"),
			//array("img"=>"http://storetn.cafepress.com/4/10641564_B_store.jpg","name"=>"LotGD.net Stainless Steel Travel Mug","link"=>"http://www.cafeshops.com/logd.10641564","cost"=>"$18.99"),
			array("img"=>"http://storetn.cafepress.com/3/10888463_B_store.jpg","name"=>"LoGD Medallion Mug","link"=>"http://www.cafeshops.com/logdmedallion.10888463","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/9/8805229_B_store.jpg","name"=>"LotGD.net Mug","link"=>"http://www.cafeshops.com/logd.8805229","cost"=>"$14.99"),

		),
		"Apparel"=>array(
			array("img"=>"http://storetn.cafepress.com/7/10889697_F_store.jpg","name"=>"LoGD Medallion Jr. Raglan","link"=>"http://www.cafeshops.com/logdmedallion.10889697","cost"=>"$19.95"),
			array("img"=>"http://storetn.cafepress.com/2/10641492_F_store.jpg","name"=>"LotGD.net Jr. Raglan","link"=>"http://www.cafeshops.com/logd.10641492","cost"=>"$19.99"),
			array("img"=>"http://storetn.cafepress.com/9/10961059_B_store.jpg","name"=>"LoGD Medallion Jr. Hoodie","link"=>"http://www.cafeshops.com/logdmedallion.10961059","cost"=>"$27.99"),
			array("img"=>"http://storetn.cafepress.com/7/10889607_F_store.jpg","name"=>"LoGD Medallion Baseball Jersey","link"=>"http://www.cafeshops.com/logdmedallion.10889607","cost"=>"$19.99"),
			array("img"=>"http://storetn.cafepress.com/8/10641428_B_store.jpg","name"=>"LotGD.net Baseball Jersey","link"=>"http://www.cafeshops.com/logd.10641428","cost"=>"$19.99"),
			array("img"=>"http://storetn.cafepress.com/9/10889629_F_store.jpg","name"=>"LoGD Medallion White T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889629","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/5/10987505_F_store.jpg","name"=>"LotGD.net White T-Shirt   ","link"=>"http://www.cafeshops.com/logd.10987505","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/9/10961069_F_store.jpg","name"=>"LoGD Medallion Ash Grey T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10961069","cost"=>"$16.99"),
			array("img"=>"http://storetn.cafepress.com/6/10889596_F_store.jpg","name"=>"LoGD Medallion Golf Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889596","cost"=>"$19.99"),
			array("img"=>"http://storetn.cafepress.com/8/10961038_F_store.jpg","name"=>"LoGD Medallion Long Sleeve T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10961038","cost"=>"$21.99"),
			array("img"=>"http://storetn.cafepress.com/4/10889564_F_store.jpg","name"=>"LoGD Medallion Jr. Baby Doll T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889564","cost"=>"$19.99"),
			array("img"=>"http://storetn.cafepress.com/9/10961079_F_store.jpg","name"=>"LoGD Medallion Women's T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10961079","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/8/10641578_F_store.jpg","name"=>"LotGD.net Women's T-Shirt","link"=>"http://www.cafeshops.com/logd.10641578","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/4/10961214_F_store.jpg","name"=>"LoGD Medallion Jr. Spaghetti Tank","link"=>"http://www.cafeshops.com/logdmedallion.10961214","cost"=>"$18.99"),
			array("img"=>"http://storetn.cafepress.com/3/10961073_F_store.jpg","name"=>"LoGD Medallion Hooded Sweatshirt","link"=>"http://www.cafeshops.com/logdmedallion.10961073","cost"=>"$26.99"),
			array("img"=>"http://storetn.cafepress.com/2/10961112_F_store.jpg","name"=>"LoGD Medallion Sweatshirt","link"=>"http://www.cafeshops.com/logdmedallion.10961112","cost"=>"$22.99"),
			array("img"=>"http://storetn.cafepress.com/6/10889716_F_store.jpg","name"=>"LoGD Medallion Camisole","link"=>"http://www.cafeshops.com/logdmedallion.10889716","cost"=>"$19.99"),
		),
		"Baby Wear"=>array(
			array("img"=>"http://storetn.cafepress.com/5/10889845_F_store.jpg","name"=>"LoGD Medallion Infant/Toddler T-Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889845","cost"=>"$9.99"),
			array("img"=>"http://storetn.cafepress.com/5/10889815_F_store.jpg","name"=>"LoGD Medallion Infant Creeper","link"=>"http://www.cafeshops.com/logdmedallion.10889815","cost"=>"$9.99"),
			array("img"=>"http://storetn.cafepress.com/3/10889713_F_store.jpg","name"=>"LoGD Medallion Bib","link"=>"http://www.cafeshops.com/logdmedallion.10889713","cost"=>"$6.99"),
		
		),
		"Housewares"=>array(
			array("img"=>"http://storetn.cafepress.com/0/10889720_B_store.jpg","name"=>"LoGD Medallion Large Mug ","link"=>"http://www.cafeshops.com/logdmedallion.10889720","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/3/10641573_B_store.jpg","name"=>"LotGD.net Large Mug","link"=>"http://www.cafeshops.com/logd.10641573","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/3/10888463_B_store.jpg","name"=>"LoGD Medallion Mug","link"=>"http://www.cafeshops.com/logdmedallion.10888463","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/9/8805229_B_store.jpg","name"=>"LotGD.net Mug","link"=>"http://www.cafeshops.com/logd.8805229","cost"=>"$14.99"),
			//array("img"=>"http://storetn.cafepress.com/2/10889762_B_store.jpg","name"=>"LoGD Medallion Stainless Steel Travel Mug","link"=>"http://www.cafeshops.com/logdmedallion.10889762","cost"=>"$18.99"),
			//array("img"=>"http://storetn.cafepress.com/4/10641564_B_store.jpg","name"=>"LotGD.net Stainless Steel Travel Mug","link"=>"http://www.cafeshops.com/logd.10641564","cost"=>"$18.99"),
			array("img"=>"http://storetn.cafepress.com/1/10889581_B_store.jpg","name"=>"LoGD Medallion Stein","link"=>"http://www.cafeshops.com/logdmedallion.10889581","cost"=>"$17.99"),
			array("img"=>"http://storetn.cafepress.com/6/10641476_B_store.jpg","name"=>"LotGD.net Stein","link"=>"http://www.cafeshops.com/logd.10641476","cost"=>"$17.99"),
			array("img"=>"http://storetn.cafepress.com/6/10889676_F_store.jpg","name"=>"LoGD Medallion Tile Coaster","link"=>"http://www.cafeshops.com/logdmedallion.10889676","cost"=>"$5.50"),
			array("img"=>"http://storetn.cafepress.com/2/10889692_F_store.jpg","name"=>"LoGD Medallion Tile Box","link"=>"http://www.cafeshops.com/logdmedallion.10889692","cost"=>"$18.99"),
			array("img"=>"http://storetn.cafepress.com/3/10889553_F_store.jpg","name"=>"LoGD Medallion Mousepad ","link"=>"http://www.cafeshops.com/logdmedallion.10889553","cost"=>"$13.99"),
			array("img"=>"http://storetn.cafepress.com/4/10889774_F_store.jpg","name"=>"LoGD Medallion Wall Clock","link"=>"http://www.cafeshops.com/logdmedallion.10889774","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/8/10641598_F_store.jpg","name"=>"LotGD.net Wall Clock","link"=>"http://www.cafeshops.com/logd.10641598","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/0/10889820_F_store.jpg","name"=>"Mr. Bear in LoGD Medallion Shirt","link"=>"http://www.cafeshops.com/logdmedallion.10889820","cost"=>"$15.99"),
			//array("img"=>"http://storetn.cafepress.com/4/10961014_F_store.jpg","name"=>"LoGD Medallion Lunchbox","link"=>"http://www.cafeshops.com/logdmedallion.10961014","cost"=>"$15.99"),
			array("img"=>"http://storetn.cafepress.com/1/10961261_F_store.jpg","name"=>"LoGD Medallion BBQ Apron","link"=>"http://www.cafeshops.com/logdmedallion.10961261","cost"=>"$16.99"),
			//array("img"=>"http://storetn.cafepress.com/4/10889784_F_store.jpg","name"=>"LoGD Medallion Flying Disc","link"=>"http://www.cafeshops.com/logdmedallion.10889784","cost"=>"$8.49"),
		
		),
		"Hats"=>array(
			array("img"=>"http://storetn.cafepress.com/9/10889619_F_store.jpg","name"=>"LoGD Medallion Trucker Hat","link"=>"http://www.cafeshops.com/logdmedallion.10889619","cost"=>"$13.99"),
			//array("img"=>"http://storetn.cafepress.com/1/10889731_F_store.jpg","name"=>"LoGD Medallion Baseball Cap","link"=>"http://www.cafeshops.com/logdmedallion.10889731","cost"=>"$14.99"),
			//array("img"=>"http://storetn.cafepress.com/5/10837275_F_store.jpg","name"=>"LotGD.net Baseball Cap","link"=>"http://www.cafeshops.com/logd.10837275","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/6/10889796_F_store.jpg","name"=>"LoGD Medallion Black Cap","link"=>"http://www.cafeshops.com/logdmedallion.10889796","cost"=>"$15.99"),
		
		),
		"Bags"=>array(
			array("img"=>"http://storetn.cafepress.com/4/10889804_F_store.jpg","name"=>"LoGD Medallion Tote Bag","link"=>"http://www.cafeshops.com/logdmedallion.10889804","cost"=>"$14.99"),
			array("img"=>"http://storetn.cafepress.com/7/10961127_F_store.jpg","name"=>"LoGD Medallion Messenger Bag","link"=>"http://www.cafeshops.com/logdmedallion.10961127","cost"=>"$20.99"),
		
		),
		"Misc"=>array(
			array("img"=>"http://storetn.cafepress.com/4/10961064_F_store.jpg","name"=>"LoGD Medallion Journal","link"=>"http://www.cafeshops.com/logdmedallion.10961064","cost"=>"$8.49"),
			array("img"=>"http://storetn.cafepress.com/6/10961146_F_store.jpg","name"=>"LoGD Medallion Postcards (Package of 8)","link"=>"http://www.cafeshops.com/logdmedallion.10961146","cost"=>"$6.99"),
		
		),
	);
	return $store;
}
?>
