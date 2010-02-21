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
print "  <li class=\"menu\"><a href=\"javascript:sendForm('23.02.2010: TF1 Gran Torino')\"> <img alt=\"list\" src=\"images/pictos/timers.png\" /><span class=\"name\">23.02.2010: TF1 Gran Torino</span><span class=\"arrow\"></span></a></li>";
print "<form name=\"23.02.2010: TF1 Gran Torino\" id=\"23.02.2010: TF1 Gran Torino\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"edit_timer\"/><input name=\"timer\" type=\"hidden\" id=\"timer\" value=\"23.02.2010: TF1 Gran Torino\" /></form>";
print "  <li class=\"menu\"><a href=\"javascript:sendForm('23.02.2010: TF1 Gran Torino')\"> <img src=\"images/pictos/timers.png\" /><span class=\"name\">12.03.2010: Canal + Les Guignols de l'Info </span><span class=\"arrow\"></span></a></li>";
print "<form name=\">12.03.2010: Canal + Les Guignols de l'Info\" id=\">12.03.2010: Canal + Les Guignols de l'Info\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"edit_timer\"/><input name=\"timer\" type=\"hidden\" id=\"timer\" value=\">12.03.2010: Canal + Les Guignols de l'Info\" /></form>";
print "</ul>";
print "<ul class=\"pageitem\">";
print " <li class=\"menu\"><a href=\"javascript:sendForm('new_timer')\"><span class=\"name\">New Timer</span><span class=\"arrow\"></span></a></li>";
print "<form name=\"new_timer\" id=\"new_timer\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"new_timer\"/></form>";

print "</ul>";

print "</div>\r\n";
?>

