<?php

global $vdrenabled, $vdrrecpath;

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
if ($vdrenabled)
{
	print "	<span class=\"graytitle\">VDR</span>\r\n";
	print "	<ul class=\"pageitem\">\r\n";
	print "		<li class=\"menu\"><a href=\"javascript:sendForm('channels');\"><img src=\"images/pictos/tv.png\" /><span class=\"name\">Channels</span><span class=\"arrow\"></span></a></li>\r\n";
	print "		<form name=\"channels\" id=\"channels\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"listcategory\" /></form>\r\n";
	print "		<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('recordings');\"><img src=\"images/pictos/record.png\" /><span class=\"name\">Recordings</span><span class=\"arrow\"></span></a></li>\r\n";
	print "		<form name=\"recordings\" id=\"recordings\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"recordings\" /><input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$vdrrecpath}\" /></form>\r\n";
	print "         <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('epg');\"><img src=\"images/pictos/epg.png\" /><span class=\"name\">Program Guide</span><span class=\"arrow\"></span></a></li>\r\n";
	print "         <form name=\"epg\" id=\"epg\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"epg\" /></form>\r\n";
	print "         <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('timers');\"><img src=\"images/pictos/timers.png\" /><span class=\"name\">Timers</span><span class=\"arrow\"></span></a></li>\r\n";
	print "         <form name=\"timers\" id=\"timers\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"timers\" /></form>\r\n";
	print "	</ul>";
}
print " <span class=\"graytitle\">MEDIA</span>\r\n";
print " <ul class=\"pageitem\">\r\n";
print "         <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('media');\"><img src=\"images/pictos/media.png\" /><span class=\"name\">Media</span><span class=\"arrow\"></span></a></li>\r\n";
print "         <form name=\"media\" id=\"media\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\" /></form>\r\n";
print " </ul>";
print "</div>";
?>
