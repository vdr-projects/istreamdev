<?php

global $videotypes, $audiotypes;

$subdir = $_REQUEST['subdir'];
$mediapath = $_REQUEST['mediapath'];

/* Add last slash to dirs */
if ($mediapath[strlen($mediapath)-1] != '/')
	$mediapath = $mediapath .'/';
if ($subdir[strlen($subdir)-1] != '/')
	$subdir = $subdir .'/';

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";

if ($subdir == '/')
	print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
else
{
	print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
	print "<div id=\"rightnav\">\r\n";
	print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
}

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print " <span class=\"graytitle\">Media</span>\r\n";
print "<br>";
print " <ul class=\"pageitem\">";
print " <li class=\"textbox\"><span class=\"header\">Current path:</span><p>{$subdir}</p></li>";

$dir_handle = @opendir($mediapath .$subdir);
if (!$dir_handle)
{
	print "Unable to open $mediapath .$subdir";
}
else while ($medianame = readdir($dir_handle))
{
	if($medianame == "." || $medianame == ".." || $medianame == 'lost+found')
		continue;

	$medianame_array[] = $medianame;
}

if ($medianame_array[0])
{
	// Alphabetical sorting
	sort($medianame_array);
	
	foreach($medianame_array as $value)
	{	
		$medianame2=addslashes($value);

		// Directories
		if (is_dir($mediapath .$subdir .$value))
		{
			print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$medianame2');\"><span class=\"name\">$value</span><span class=\"arrow\"></span></a></li>\r\n";
			print "<form name=\"$value\" id=\"$value\" method=\"post\" action=\"index.php\">";
			print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\"/>";
			print "  <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />";
			print "  <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}{$value}\" />\r\n";
			print "</form>\r\n";
		}
		else
		{
			// Get file extension
			$fileext = end(explode(".", $value));

			// Check if it is supported
			if (	preg_match("'" .$fileext ." '", $videotypes)
			    ||	preg_match("'" .$fileext ." $'", $videotypes)
			   )
			{
				print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$medianame2');\"><img src=\"images/pictos/video.png\" /><span class=\"name\">$value</span><span class=\"arrow\"></span></a></li>\r\n";
				print "<form name=\"$value\" id=\"$value\" method=\"post\" action=\"index.php\">";
				print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stream\"/>";
	                	print "  <input name=\"type\" type=\"hidden\" id=\"type\" value=3 />";
				print "  <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />";
				print "  <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
        		        print "  <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$mediapath}{$subdir}{$value}\" />";
				print "</form>\r\n";
			}
			else if (  preg_match("'" .$fileext ." '", $audiotypes)
                            ||  preg_match("'" .$fileext ." $'", $audiotypes)
                           )
			{
			 print "<li class=\"menu\"><a href=\"streammusic.php?dir={$mediapath}{$subdir}&file={$value}\"><img src=\"images/pictos/audio.png\" /><span class=\"name\">$value</span><span class=\"arrow\"></span></a></li>\r\n";
			}
		}
	}
}

$upsubdir = dirname($subdir);

print "<form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\">\r\n";
print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\"/>\r\n";
print "  <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
print "  <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$upsubdir}\" />\r\n";
print "</form>\r\n";

if ($dir_handle)
	closedir($dir_handle);

print "</ul></div>\r\n";
?>
