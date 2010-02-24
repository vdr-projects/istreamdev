<?php

$play =$_REQUEST['play'];
if ($play == "")
	print "<body class=\"ipodlist\">\r\n";
else
	print "<body class=\"ipodlist\">\r\n";

print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"leftnav\">\r\n";
print "	 <a href=\"javascript:sendForm('getback')\">Back</a>\r\n";
print "</div>\r\n";
print "<div id=\"rightnav\">\r\n";
print "	<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

global $httppath;

print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";
print "	 <ul>\r\n";

$mediapath = $_REQUEST['mediapath'];
$subdir = $_REQUEST['subdir'];

$dir_handle = @opendir($mediapath .$subdir);
if (!$dir_handle)
	print "Unable to open $mediapath .$subdir";
else while ($medianame = readdir($dir_handle))
{
	// Add only mp3 files	

        if(strstr($medianame, ".mp3")  != ".mp3")
                continue;

        $medianame_array[] = $medianame;
}

if ($medianame_array[0])
{
        // Alphabetical sorting
        sort($medianame_array);

	exec('rm playlist/*.mp3');
	exec('ln -s ' .addcslashes(quotemeta($mediapath .$subdir), " ") .'*.mp3 playlist');

	$cnt = 1;
        foreach($medianame_array as $value)
        {
                $medianame2=addslashes($value);

		print "	 <li>\r\n";
		print "    <a class=\"noeffect\" href=\"javascript:document.s{$cnt}.Play();\">\r\n";
		print "	     <span class=\"number\">1</span><span class=\"auto\"></span><span class=\"name\">{$value}</span><span class=\"time\">???</span>\r\n";
		print "	   </a>\r\n";
		print "  </li>\r\n";

		$cnt++;
	}

	print "</div>\r\n";

	print "<div style=\"position:absolute; left:0; top:0\">\r\n";

        $count = count($medianame_array);
        for ($cnt=0; $cnt < $count; $cnt++)
	{
		$idx=$cnt+1;

		print "<embed enablejavascript=\"true\" autoplay=\"false\" height=\"0\" name=\"s{$idx}\"";
//		print " src=\"streammusic.php?mediapath={$mediapath}&subdir={$subdir}&file={$medianame_array[$cnt]}\"";
		print " src=\"{$httppath}playlist/{$medianame_array[$cnt]}\"";
		print " width=\"0\" loop=\"true\" controller=\"false\"";

		$next=1;
		for ($cnt2=$cnt+1; $cnt2<$count; $cnt2++)
		{
//			print "	qtnext{$next}=\"<streammusic.php?mediapath={$mediapath}&subdir={$subdir}&file={$medianame_array[$cnt2]}\"";
			print " qtnext{$next}=\"<{$httppath}playlist/{$medianame_array[$cnt2]}>\"";
			$next++;
		}

		print " />\r\n";
	}

	print "</div>";
}
else
	 print "</div>\r\n";

print "  <form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\">";
print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"media\" />";
print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
print "  </form>\r\n";

?>

