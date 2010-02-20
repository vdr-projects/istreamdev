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

print " <li class=\"textbox\"><span class=\"header\">Incoming feature</span";
print " <p>This is just a template...</p></li></ul>";
print " <ul class=\"pageitem\">";
print "  <li class=\"textbox\"> <span class=\"header\">Current timers</span> </li>";
print "  <li class=\"menu\"><a href=\"edit_timer.html\"> <img alt=\"list\" src=\"images/pictos/timers.png\" /><span class=\"name\">23.02.2010: TF1 Gran Torino</span><span class=\"arrow\"></span></a></li>";
print "  <li class=\"menu\"><a href=\"edit_timer.html\"> <img src=\"images/pictos/timers.png\" /><span class=\"name\">12.03.2010: Canal + Les Guignols de l'Info </span><span class=\"arrow\"></span></a></li>";
print "</ul>";
print "<ul class=\"pageitem\">";
print " <li class=\"menu\"><a href=\"new_timer.html\"><span class=\"name\">New Timer</span><span class=\"arrow\"></span></a></li>";
print "</ul>";

print "</div>\r\n";
?>

