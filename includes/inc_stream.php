<?php

global $vdrstreamdev, $quality;

// Check if we are running
if (infostreamexist())
{
	// Get current stream info
	list($type, $realname, $title, $desc, $mode, $category, $url, $mediapath, $subdir) = readinfostream();

	print "<body onorientationchange=\"updateOrientation();\" onload=\"ajax();\">\r\n";
	
	print "<div id=\"topbar\" class=\"transparent\">\r\n";
	print "<div id=\"leftnav\">\r\n";
	print "<a href=\"javascript:sendForm('stopstream');\">Stop Stream</a></div>\r\n";
	print "<div id=\"title\">iStreamdev</div>\r\n";
	print "</div>\r\n";

	print "<div id=\"content\">\r\n";
        print " <span class=\"graytitle\">Now streaming</span>\r\n";

	// Print the right logo
	print " <ul class=\"pageitem\">\r\n";

	switch ($type)
	{
		case 1:
			$channoslash = preg_replace("$/$", " ", $realname);
			$logopath = "logos/" .$channoslash .".png";
			if (!file_exists($logopath))
				$logopath = "logos/nologoTV.png";
			break;
		case 2:
			$channoslash = preg_replace("$/$", " ", $realname);
			$logopath = "logos/" .$channslash .".png";
			if (!file_exists($logopath))
				$logopath = "logos/nologoREC.png";
			break;
		case 3:
		        if (file_exists("ram/stream-tb.png"))
		                $logopath = "ram/stream-tb.png";
		        else if (file_exists("ram/stream-tb.jpg"))
		                $logopath = "ram/stream-tb.jpg";
		        else
		                $logopath = "logos/nologoMEDIA.png";
	}

	print " <center><video id=\"videofeed\" poster=\"{$logopath}\" /></center>\r\n";

	print " </ul>\r\n";

	print " <ul class=\"pageitem\">\r\n";
	print " <li class=\"textbox\"><span class=\"header\">{$realname}</span><p><strong>{$title}</strong>\r\n";
	print " <br>{$desc}</p></li></ul>\r\n";
	
	print " <ul class=\"pageitem\">\r\n";
	print " <li id=\"modetext\" class=\"textbox\"><span class=\"header\">Mode</span>\r\n";
	print " <p id='streamtitle'>{$mode}</p></li></ul>\r\n";

	print " </div>\r\n";

	print " <form name=\"stopstream\" id=\"stopstream\" method=\"post\" action=\"index.php\">";
	print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stopstream\" />";
	print "    <input name=\"actionafterstop\" type=\"hidden\" id=\"actionafterstop\" value=\"stream\" />";
	print "    <input name=\"type\" type=\"hidden\" id=\"type\" value={$type} />";
	switch ($type)
	{
		case 1:
			print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$realname}\" />";
			break;
		case 3:
			print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
			print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
			// NO BREAK
		case 2:	
			print "   <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$url}\" />";
			break;
			
	}
	print " </form>\r\n";
}
else
{
	$category = $_SESSION['currentcat'];

        $type = $_REQUEST['type'];
	$name = $_REQUEST['name'];

	switch ($type)
	{
		// Live TV
		case 1:
			list($title, $desc, $realname) = vdrgetinfostream($name, 1);
			$channum = vdrgetchannum($realname);
			break;
		// Recording
		case 2:
			list($title, $desc, $realname) = vdrgetinfostream($name, 0);
			break;
		// Media
		case 3:
			list($title, $desc) = mediagetinfostream($name);
			$realname = basename($name);
			break;
		default:
			$realname = "";
			$title = "";
			$desc = "";
			$channame = "";
	}

	print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";

	print "<div id=\"topbar\" class=\"transparent\">\r\n";
	print "<div id=\"leftnav\">\r\n";

	print "<a href=\"javascript:sendForm('getback')\">Back</a></div>\r\n";
	print "<div id=\"rightnav\">\r\n";
	print "<a href=\"index.php\"><img alt=\"home\" src=\"images/home.png\" /></a></div>\r\n";

	print "<div id=\"title\">iStreamdev</div>\r\n";
	print "</div>\r\n";

	print "<div id=\"content\">\r\n";

	print " <span class=\"graytitle\">Select stream mode</span>\r\n";

	// Print the right logo
	print " <ul class=\"pageitem\">\r\n";

        switch ($type)
        {
                case 1:
			$channoslash = preg_replace("$/$", " ", $realname);
                        $logopath = "logos/" .$channoslash .".png";
                        if (!file_exists($logopath))
                                $logopath = "logos/nologoTV.png";
                        break;
                case 2:
			$channoslash = preg_replace("$/$", " ", $realname);
                        $logopath = "logos/" .$channoslash .".png";
                        if (!file_exists($logopath))
                                $logopath = "logos/nologoREC.png";
                        break;
                case 3:
                        if (file_exists("ram/stream-tb.png"))
                                $logopath = "ram/stream-tb.png";
                        else if (file_exists("ram/stream-tb.jpg"))
                                $logopath = "ram/stream-tb.jpg";
                        else
                                $logopath = "logos/nologoMEDIA.png";
        }

	print " <center><img src=\"{$logopath}\"></img></center>\r\n";

	print " </ul>\r\n";

	// Check if running to display the start buttons
	print " <div id=\"tributton\">\r\n";
	print "         <div class=\"links\">\r\n";

	foreach ($quality as $qname => $qparams)
		print "<a href=\"javascript:sendForm('$qname')\">{$qname}</a>";
	print "\r\n";

	print " </div></div>\r\n";

	print " <ul class=\"pageitem\">\r\n";
	print " <li class=\"textbox\"><span class=\"header\">{$realname}</span><p><strong>{$title}</strong>\r\n";
	print " <br>{$desc}</p></li></ul>\r\n";

	print " </div>\r\n";

	foreach ($quality as $qname => $qparams)
	{
		print "  <form name=\"{$qname}\" id=\"{$qname}\" method=\"post\" action=\"index.php\">\r\n";
		print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"startstream\" />\r\n";
		print "    <input name=\"type\" type=\"hidden\" id=\"type\" value={$type} />\r\n";
		print "    <input name=\"name\" type=\"hidden\" id=\"name\" value=\"{$realname}\" />\r\n";
		print "    <input name=\"title\" type=\"hidden\" id=\"title\" value=\"{$title}\" />\r\n";
		print "    <input name=\"desc\" type=\"hidden\" id=\"desc\" value=\"{$desc}\" />\r\n";
		print "    <input name=\"qname\" type=\"hidden\" id=\"qname\" value=\"{$qname}\" />\r\n";
		print "    <input name=\"qparams\" type=\"hidden\" id=\"qparams\" value=\"{$qparams}\" />\r\n";
		print "    <input name=\"category\" type=\"hidden\" id=\"category\" value=\"{$category}\" />\r\n";
		switch ($type)
		{
			case 1:
				print "    <input name=\"url\" type=\"hidden\" id=\"url\" value=\"{$vdrstreamdev}{$channum}\" />\r\n";
				break;
			case 3:
				$mediapath = $_REQUEST['mediapath'];
				$subdir = $_REQUEST['subdir'];
				print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
				print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
				// NO BREAK
			case 2:
				print "    <input name=\"url\" type=\"hidden\" id=\"url\" value=\"{$name}\" />\r\n";
                                break;
		}
		print "  </form>";
	}

	print "  <form name=\"getback\" id=\"getback\" method=\"post\" action=\"index.php\">";
        switch ($type)
        {
                case 1:
			print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"listchannels\" />";
                        print "    <input name=\"cat\"type=\"hidden\" id=\"cat\" value=\"{$category}\" />";
                        break;
                case 2:
			$dir = dirname($name);
			print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"recordings\" />";
                        print "    <input name=\"dir\"type=\"hidden\" id=\"dir\" value=\"{$dir}\" />";
                        break;
		case 3:
			$mediapath = $_REQUEST['mediapath'];
			$subdir = $_REQUEST['subdir'];
			print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"video\" />";
			print "    <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$mediapath}\" />\r\n";
			print "    <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"{$subdir}\" />\r\n";
                        break;
        }
        print "  </form>\r\n";

}

?>
