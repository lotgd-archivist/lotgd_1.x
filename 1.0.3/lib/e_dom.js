/*
 * translator ready
 * addnews ready
 * mail ready
 */

//Javascript Generic DOM
//By Eric Stevens
//
function fetchDOM(filename){
	var xmldom;
	if (document.implementation && 
			document.implementation.createDocument){
		//Mozilla style browsers
		xmldom = document.implementation.createDocument("", "", null);
	} else if (window.ActiveXObject) {
		//IE style browsers
		xmldom = new ActiveXObject("Microsoft.XMLDOM");
	}
		 
	xmldom.async=false;
	try {
		xmldom.load(filename);
	} catch(e){
		xmldom.parseXML("<b>Failed to load "+filename+"</b>");
	}
	return xmldom;
}
function fetchDOMasync(filename,theCode){
	var xmldom;
	if (document.implementation && 
			document.implementation.createDocument){
		//Mozilla style browsers
		xmldom = document.implementation.createDocument("", "", null);
		xmldom.async=true;
		xmldom.onload=theCode;
	} else if (window.ActiveXObject) {
		//IE style browsers
		xmldom = new ActiveXObject("Microsoft.XMLDOM");
		xmldom.async=true;
		xmldom.onreadystatechange = function(){
			if (xmldom.readyState == 4) theCode();
		};
	}
	
	xmldom.load(filename);
	return xmldom;
}
function createXML(node){
	if (node.xml)
		return node.xml;
	var out = "";
	if (node.nodeType==1){
		var x=0;
		out = "<" + node.nodeName;
		for (x=0; x < node.attributes.length; x++){
			out = out + " " + node.attributes[x].name + "=\"" + node.attributes[x].nodeValue + "\"";
		}
		out = out + ">";
		for (x=0; x < node.childNodes.length; x++){
			out = out + createXML(node.childNodes[x]);
		}
		out = out + "</" + node.nodeName + ">";
	}else if(node.nodeType==3){
		out = out + node.nodeValue;
	}
	return out;
}
function selectSingleNode(node,name){
	for (var x=0; x<node.childNodes.length; x++){
		if (node.childNodes[x].nodeName == name) return node.childNodes[x];
	}
}
function nodeText(node){
	var out="";
	for (y=0; y<node.childNodes.length; y++){
		if (node.childNodes[y].nodeType==3){
			out+=node.childNodes[y].nodeValue;
		}else if(node.childNodes[y].nodeType==1){
			out += nodeText(node.childNodes[y]);
		}
	}
	return out;
}
function parseRSS(xml,htmlescape){
	var rss = selectSingleNode(xml,"rss");
	var channel = selectSingleNode(rss,"channel");

	var feed = new Array();
	//collect rss headers
	feed["title"] = HTMLencode(nodeText(selectSingleNode(channel,"title")),htmlescape);
	feed["link"] = HTMLencode(nodeText(selectSingleNode(channel,"link")),htmlescape);
	feed["description"] = HTMLencode(nodeText(selectSingleNode(channel,"description")),htmlescape);
	var image = selectSingleNode(channel,"image");
	feed["image"] = new Array();
	feed["image"]["title"] = HTMLencode(nodeText(selectSingleNode(image,"title")),htmlescape);
	feed["image"]["url"] = HTMLencode(nodeText(selectSingleNode(image,"url")),htmlescape);
	feed["image"]["link"] = HTMLencode(nodeText(selectSingleNode(image,"link")),htmlescape);
	feed["items"] = new Array();
	//collect rss items
	var node;
	var y=0;
	for (var x=0; x<channel.childNodes.length; x++){
		node = channel.childNodes[x];
		if (node.nodeType==1){ //standard element
			if (node.nodeName == "item"){
				feed['items'][y] = new Array();
				feed['items'][y]['title'] = HTMLencode(nodeText(selectSingleNode(node,"title")),htmlescape);
				feed['items'][y]['link'] = HTMLencode(nodeText(selectSingleNode(node,"link")),htmlescape);
				feed['items'][y]['description'] = HTMLencode(nodeText(selectSingleNode(node,"description")),htmlescape);
				feed['items'][y]['pubdate'] = HTMLencode(nodeText(selectSingleNode(node,"pubDate")),htmlescape);
				y=y+1;
			}
		}
	}
	return feed;
}
function HTMLencode(input,doit){
	if (doit){
		return input.replace(/"/g,"&quot;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/&/g,"&amp;");
	}else{
		return input;
	}
}
