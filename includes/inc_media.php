<?php

global $mediapath;

$dir = $_REQUEST['dir'];
if ($dir == "")
	$dir = $mediapath;

// Get current subdir
$subdir = preg_replace("'" .quotemeta($mediapath) ."'", '', $dir);

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
if ($dir == $mediapath)
	print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
else
	print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print " <span class=\"graytitle\">Media</span>\r\n";
print "<br>";
print " <span class=\"graytitle\">{$subdir}</span>\r\n";
print " <ul class=\"pageitem\">";

$dir_handle = @opendir($dir) or die("Unable to open $dir");

while ($medianame = readdir($dir_handle))
{
	if($medianame == "." || $medianame == ".." || $medianame == 'lost+found')
		continue;
	
	$medianame2=addslashes($medianame);

	if (strstr($medianame, ".avi") == ".avi")
	{
		print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$medianame2');\"><span class=\"name\">$medianame</span><span class=\"arrow\"></span></a></li>\r\n";
		print "<form name=\"$medianame\" id=\"$medianame\" method=\"post\" action=\"index.php\">";
		print "   <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stream\"/>";
                print "   <input name=\"type\" type=\"hidden\" id=\"type\" value=3 />";
                print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$dir}/{$medianame}\" />";
		print "</form>\r\n";
	}
	else
	{
		print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$medianame2');\"><span class=\"name\">$medianame</span><span class=\"arrow\"></span></a></li>\r\n";
		print "<form name=\"$medianame\" id=\"$medianame\" method=\"post\" action=\"index.php\">";
		print "   <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\"/>";
		print "   <input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$dir}/{$medianame}\" />";
		print "</form>\r\n";
	}
}

$updir = dirname($dir);

print "<form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\"/><input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$updir}/\" /></form>\r\n";
closedir($dir_handle);

print "</ul></div>\r\n";
?>
