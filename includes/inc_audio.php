<?php

global $httppath;

$mediapath = $_REQUEST['mediapath'];
$subdir = $_REQUEST['subdir'];

/* Add last slash to dirs */
if ($mediapath[strlen($mediapath)-1] != '/')
        $mediapath = $mediapath .'/';
if ($subdir[strlen($subdir)-1] != '/')
        $subdir = $subdir .'/';

print "<body class=\"ipodlist\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";

print "<div id=\"leftnav\">\r\n";
if ($subdir == '/')
	print "  <a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
else
{
	print "	 <a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
	print "<div id=\"rightnav\">\r\n";
	print "	<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";
}

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print "	 <ul>\r\n";

$dir_handle = @opendir($mediapath .$subdir);
if (!$dir_handle)
	print "Unable to open $mediapath .$subdir";
else while ($medianame = readdir($dir_handle))
{
	// Add only mp3 files and dirs
        if($medianame == "." || $medianame == ".." || $medianame == 'lost+found')
                continue;
	
	$type = mediagettype($mediapath .$subdir .$medianame);
	if (($type != 2) && ($type != 3))
		continue;

        $medianame_array[] = $medianame;
}

if ($medianame_array[0])
{
        // Alphabetical sorting
        sort($medianame_array);

        $count = count($medianame_array);

	// Directories
        for ($cnt=0; $cnt < $count; $cnt++)
        {
                if (mediagettype($mediapath .$subdir .$medianame_array[$cnt]) == 3)
                {
			$medianame2=addslashes($medianame_array[$cnt]);

                        print "  <li>\r\n";
                        print "    <a class=\"noeffect\" href=\"javascript:sendForm('dir_$medianame2');\">\r\n";
			print "        <span class=\"name\">{$medianame_array[$cnt]}</span><span class=\"time\">></span>\r\n";
                        print "    </a>\r\n";
                        print "  </li>\r\n";
                        print "  <form name=\"dir_{$medianame_array[$cnt]}\" id=\"dir_{$medianame_array[$cnt]}\" method=\"post\" action=\"index.php\">\r\n";
                        print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"audio\"/>\r\n";
                        print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
                        print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}{$medianame_array[$cnt]}\" />\r\n";
                        print "  </form>\r\n";
                }
        }

	
	// Music files
        $idx = 1;
	for ($cnt=0; $cnt < $count; $cnt++)
        {
		$medianame2=addslashes($medianame_array[$cnt]);
		$mediapath2=addslashes($mediapath);
		$subdir2=addslashes($subdir);

		// Audio
		if (mediagettype($mediapath .$subdir .$medianame_array[$cnt]) == 2)
		{
			print "	 <li>\r\n";
		
			unset($track);
					
			for ($cnt2=$cnt; $cnt2<$count; $cnt2++)
			{
				if (mediagettype($mediapath .$subdir .$medianame_array[$cnt2]) == 2)
					$track[$cnt2-$cnt] = $httppath ."playlist/" .addslashes($medianame_array[$cnt2]);
			
			}
			$jsarray = php2js($track);
			
			print "	<a class=\"noeffect\" href=\"javascript:var myarray = new Array({$jsarray});addplayer('{$mediapath2}{$subdir2}','{$medianame2}',myarray);document.player.Play();\">\r\n";
			
			// Get song info
			list($name, $duration) = mediagetmusicinfo($mediapath .$subdir .$medianame_array[$cnt]);

			print "	     <span class=\"number\">$idx</span><span class=\"stop\"></span><span class=\"name\">{$name}</span><span class=\"time\">{$duration}</span>\r\n";
			print "	   </a>\r\n";
			print "  </li>\r\n";
		
			$idx++;
		}
	}
}

print "</div>\r\n";

$upsubdir = dirname($subdir);

print "  <form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\">\r\n";
print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"audio\" />\r\n";
print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$upsubdir}\" />\r\n";
print "  </form>\r\n";

print "<div style=\"position:absolute; left:0; top:0\" name=\"div_player\" id=\"div_player\">\r\n";

print("</div>");

?>

