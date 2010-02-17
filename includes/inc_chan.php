<?php

$cat = $_REQUEST['cat'];

$_SESSION['currentcat'] = $cat;

print "<body class=\"list\" onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";

print "<div id=\"leftnav\">\r\n";
print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print "	<ul><li class=\"title\">Channels</li>\r\n";

vdrlistchannels($cat);

print "	</ul>";
print "</div>\r\n";
print " <form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"listcategory\" /></form>\r\n";
?>
