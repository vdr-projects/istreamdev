<?php

global $httppath;

print "<body class=\"ipodlist\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "	 <a href=\"javascript:sendForm('getback')\">Back</a>\r\n";
print "</div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "	<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

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

	exec('rm playlist/*');
	exec('ln -s ' .addcslashes(quotemeta($mediapath .$subdir), " &") .'* playlist');

        $count = count($medianame_array);

        for ($cnt=0; $cnt < $count; $cnt++)
        {
                // Dirs
                if (mediagettype($mediapath .$subdir .$medianame_array[$cnt]) == 3)
                {
                        print "  <li>\r\n";
                        print "    <a class=\"noeffect\" href=\"javascript:sendForm('dir_{$medianame_array[$cnt]}');\">\r\n";
			print "        <span class=\"name\">{$medianame_array[$cnt]}</span><span class=\"time\">></span>\r\n";
                        print "    </a>\r\n";
                        print "  </li>\r\n";
                        print "  <form name=\"dir_{$medianame_array[$cnt]}\" id=\"dir_{$medianame_array[$cnt]}\" method=\"post\" action=\"index.php\">\r\n";
                        print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\"/>\r\n";
                        print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
                        print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}{$medianame_array[$cnt]}\" />\r\n";
                        print "  </form>\r\n";
                }
        }

        $idx = 1;
	for ($cnt=0; $cnt < $count; $cnt++)
        {
		// Audio
		if (mediagettype($mediapath .$subdir .$medianame_array[$cnt]) == 2)
		{
			print "	 <li>\r\n";
			print "    <a class=\"noeffect\" href=\"javascript:document.s{$idx}.Play();\">\r\n";
			print "	     <span class=\"number\">$idx</span><span class=\"stop\"></span><span class=\"name\">{$medianame_array[$cnt]}</span>\r\n";
			print "	   </a>\r\n";
			print "  </li>\r\n";
		
			$idx++;
		}
	}

	print("</div>");

	print "<div style=\"position:absolute; left:0; top:0\">\r\n";
	$idx = 1;
        for ($cnt=0; $cnt < $count; $cnt++)
	{
                // Audio files
		if (mediagettype($mediapath .$subdir .$medianame_array[$cnt]) == 2)
		{
			print "<embed enablejavascript=\"true\" autoplay=\"false\" height=\"0\" name=\"s{$idx}\"";
			print " src=\"{$httppath}playlist/{$medianame_array[$cnt]}\"";
			print " width=\"0\" loop=\"true\" controller=\"false\"";

			$next=1;
			for ($cnt2=$idx+1; $cnt2<$count; $cnt2++)
			{
				if (mediagettype($mediapath .$subdir .$medianame_array[$cnt2]) == 2)
				{
					print " qtnext{$next}=\"<{$httppath}playlist/{$medianame_array[$cnt2]}>\"";
					$next++;
				}
			}

			print " />\r\n";

			$idx++;
		}
	}
        print("</div>");
}
else
	print "</div>\r\n";

$upsubdir = dirname($subdir);

print "  <form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\">\r\n";
print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\" />\r\n";
print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$upsubdir}\" />\r\n";
print "  </form>\r\n";

?>

