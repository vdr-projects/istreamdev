<?php

global $vdrstreamdev, $quality;

// Get $session if we are not directly included from startstream
if ($session == "")
	$session = $_REQUEST['session'];

$ram = "ram/" .$session ."/";

// Get current stream info
list($type, $realname, $title, $desc, $mode, $category, $url, $mediapath, $subdir) = readinfostream($session);

print "<body onorientationchange=\"updateOrientation();\" onload=\"ajax('{$session}');\">\r\n";
	
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "<a href=\"javascript:sendForm('stopstream');\">Stop Stream</a></div>\r\n";

print "<div id=\"rightnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";

print "<div id=\"content\">\r\n";
print " <span class=\"graytitle\">Now streaming</span>\r\n";

// Print the right logo
print " <ul class=\"pageitem\">\r\n";

$logopath=$ram ."/logo.png";
$logowidth = mediagetwidth($logopath);	
print " <center><video id=\"videofeed\" width=\"{$logowidth}\" poster=\"{$logopath}\" ></video></center>\r\n";

print " </ul>\r\n";

print " <ul class=\"pageitem\">\r\n";
print " <li class=\"textbox\"><span class=\"header\">{$realname}</span><p><strong>" .stripslashes($title). "</strong>\r\n";
print " <br>{$desc}</p></li></ul>\r\n";
	
print " <ul class=\"pageitem\">\r\n";
print " <li id=\"modetext\" class=\"textbox\"><span class=\"header\">Mode</span>\r\n";
print " <p id='streamtitle'>{$mode}</p></li></ul>\r\n";

print " </div>\r\n";

print " <form name=\"stopstream\" id=\"stopstream\" method=\"post\" action=\"index.php\">";
print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stopstream\" />";
print "    <input name=\"session\" type=\"hidden\" id=\"session\" value=\"{$session}\" />";
print "    <input name=\"type\" type=\"hidden\" id=\"type\" value={$type} />";
switch ($type)
{
	case 1:
		print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$realname}\" />";
		break;
	case 3:
		print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
		print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
		// NO BREAK
	case 2:	
		print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$url}\" />";
	break;
		
}

print " </form>\r\n";
?>
