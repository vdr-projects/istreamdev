<?php

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
print "<div id=\"title\">iStream</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\"> <span class=\"graytitle\">Edit Timer</span>\r\n";

$timer = $_REQUEST['timer'];

list($type, $channame, $date, $stime, $etime, $desc) = vdrgettimerinfo($timer);

// Timer name
print "<form name=\"timer\" id=\"timer\" method=\"post\" action=\"index.php\">\r\n";
print "  <ul class=\"pageitem\">\r\n";
print "    <li class=\"textbox\"><span class=\"header\">Recording name</span></li>\r\n";
print "    <li class=\"bigfield\">\r\n";
print "      <input type=\"text\" placeholder=\"Enter recording name\" name=\"timer_name\" value=\"{$desc}\" />\r\n";
print "    </li>\r\n";
print "  </ul>\r\n";
print "  <ul class=\"pageitem\">\r\n";
print "    <li class=\"textbox\"><span class=\"header\">Channel</span></li>\r\n";
print "    <li class=\"select\">\r\n";

// Channel selection
print "      <select name=\"timer_chan\">\r\n";

vdrlistchannelsdrop($channame);

print "      </select>\r\n";
print "      <span class=\"arrow\"></span>";
print "    </li>\r\n";
print "  </ul>\r\n";

// Date selection
print "  <ul class=\"pageitem\">\r\n";
print "    <li class=\"textbox\"><span class=\"header\">Date</span></li>\r\n";

$datearray = explode("-", $date);

print "    <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:openSelectDate({$datearray[0]},{$datearray[1]},{$datearray[2]})\">\r\n";
print "      <span class=\"name\" id=\"layer_date\">{$date}</span><span class=\"arrow\"></span></a>";
print "    </li>\r\n";
print "  </ul>\r\n";

// Start/End time selection
print "  <ul class=\"pageitem\">\r\n";
print "    <li class=\"textbox\"><span class=\"header\">Start time</span></li>\r\n";

$smin = substr($stime, 0, 2);
$ssec = substr($stime, 2);

print "    <li class=\"menu\">";
print "      <a class=\"noeffect\" href=\"javascript:openSelectTime('layer_starttime',{$smin}, {$ssec})\">\r\n";
print "        <span class=\"name\" id=\"layer_starttime\">{$smin}{$ssec}</span>";
print "        <span class=\"arrow\"></span>";
print "      </a>";
print "    </li>\r\n";
print "  </ul>\r\n";
print "  	<ul class=\"pageitem\">\r\n";
print "	 <li class=\"textbox\"><span class=\"header\">End time</span></li>\r\n";

$emin = substr($etime, 0, 2);
$esec = substr($etime, 2);

print "   <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:openSelectTime('layer_endtime',{$emin},{$esec})\">\r\n";
print "  <span class=\"name\" id=\"layer_endtime\">{$emin}{$esec}</span><span class=\"arrow\"></span></a></li>\r\n";
print "  </ul>\r\n";

print "<input name=\"action\" type=\"hidden\" id=\"action\" value=\"addtimer\"/>\r\n";
print "<input name=\"timer_date\" type=\"hidden\" id=\"timer_date\" value=\"{$date}\" />\r\n";
print "<input name=\"timer_starttime\" type=\"hidden\" id=\"timer_starttime\" value=\"{$smin}h{$ssec}\" />\r\n";
print "<input name=\"timer_endtime\" type=\"hidden\" id=\"timer_endtime\" value=\"{$smin}h{$ssec}\" />\r\n";
print "<input name=\"prevtimer\" type=\"hidden\" id=\"prevtimer\" value=\"{$timer}\" />\r\n";

print "<ul class=\"pageitem\">\r\n";
print "<li class=\"button\">\r\n";
if ($timer == -1)
	print "  <input name=\"Submit\" type=\"submit\" value=\"Submit\" /></li>\r\n";
else
	print "  <input name=\"Update\" type=\"Submit\" value=\"Update\" /></li>\r\n";
print "</ul>\r\n";
print "</form>\r\n";

if ($timer != -1)
{
	print "<form name=\"deltimer\" id=\"deltimer\" method=\"post\" action=\"index.php\">\r\n";
	print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"deletetimer\" />\r\n";
	print "  <input name=\"timer\" type=\"hidden\" id=\"timer\" value=\"{$timer}\" />\r\n";
	print "  <ul class=\"pageitem\">\r\n";
	print "  <li class=\"button\">\r\n";
	print "  <input name=\"Submit\" type=\"submit\" value=\"Delete\" /></li>\r\n";
	print "</ul>\r\n";
	print "</form>\r\n";
}

print "</div>\r\n";
print "<form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"timers\" /></form>\r\n";

?>
