<?php

global $vdrrecpath;

$dir = $_REQUEST['dir'];
if ($dir == "")
	$dir = $vdrrecpath;

// Get current subdir
$subdir = preg_replace("'" .quotemeta($vdrrecpath) ."'", '', $dir);

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
if ($dir == $vdrrecpath)
	print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
else
	print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";

print "<div id=\"rightnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print " <span class=\"graytitle\">Recordings</span>\r\n";
print "<br>";
print " <span class=\"graytitle\">{$subdir}</span>\r\n";
print " <ul class=\"pageitem\">";

$dir_handle = @opendir($dir) or die("Unable to open $dir");

while ($recname = readdir($dir_handle))
{
	if($recname == "." || $recname == ".." || $recname == "epg.data" || $recname == 'lost+found')
		continue;
	
	$recname2=addslashes($recname);

	if (strstr($recname, ".rec") == ".rec")
	{
		print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$recname2');\"><span class=\"name\">$recname</span><span class=\"arrow\"></span></a></li>\r\n";
		print "<form name=\"$recname\" id=\"$recname\" method=\"post\" action=\"index.php\">";
		print "   <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stream\"/>";
                print "   <input name=\"type\" type=\"hidden\" id=\"type\" value=2 />";
                print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$dir}/{$recname}\" />";
		print "</form>\r\n";
	}
	else
	{
		print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$recname2');\"><span class=\"name\">$recname</span><span class=\"arrow\"></span></a></li>\r\n";
		print "<form name=\"$recname\" id=\"$recname\" method=\"post\" action=\"index.php\">";
		print "   <input name=\"action\" type=\"hidden\" id=\"action\" value=\"recordings\"/>";
		print "   <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$dir}/{$recname}\" />";
		print "</form>\r\n";
	}
}

$updir = dirname($dir);

print "<form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"recordings\"/><input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$updir}/\" /></form>\r\n";
closedir($dir_handle);

print "</ul></div>\r\n";
?>
