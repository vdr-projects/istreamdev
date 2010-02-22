<?php
print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print " <span class=\"graytitle\">Timers</span>\r\n";

print " <ul class=\"pageitem\">";
print "  <li class=\"textbox\">";
print "   <span class=\"header\">Current timers</span>";
print "  </li>";

vdrlisttimers();

print "</ul>";

print "<ul class=\"pageitem\">";
print " <li class=\"menu\">";
print "  <a href=\"javascript:sendForm('new_timer')\">";
print "   <span class=\"name\">New Timer</span>";
print "   <span class=\"arrow\"></span>";
print "  </a>";
print " </li>";
print " <form name=\"new_timer\" id=\"new_timer\" method=\"post\" action=\"index.php\">";
print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"new_timer\"/>";
print " </form>";
print "</ul>";

print "</div>\r\n";
a
?>

