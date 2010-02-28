<?php

global $vdrenabled, $vdrrecpath, $mediasources;

print "<body onorientationchange=\"updateOrientation();\" onload=\"updateOrientation();\">\r\n";
print "<div id=\"topbar\" class=\"transparent\">\r\n";
print "<div id=\"title\">iStreamdev</div>\r\n";
print "</div>\r\n";
print "<div id=\"content\">\r\n";

$sessioncnt = 0;

print "  <span class=\"graytitle\">SESSIONS</span>\r\n";
print "  <ul class=\"pageitem\">\r\n";

// Streaming in progress
$dir_handle = @opendir('ram/');
if ($dir_handle)
{
	while ($session = readdir($dir_handle))
	{
		if($session == "." || $session == ".." || $session == 'lost+found')
			continue;

		if (!is_dir('ram/' .$session))
			continue;
	
		// Get info
		list($type, $realname, $title, $desc, $mode, $category, $url, $mediapath, $subdir) = readinfostream($session);
		
		switch ($type)
		{
			case 1:
				$sessionname = "Live: ";
				$picto = "tv";
				break;
			case 2:	
				$sessionname = "Rec: ";
				$picto = "record";
				break;
			case 3: 
				$sessionname = "Media: ";
				$picto = "media";
				break;
			default:
				continue;
		}

		// Check if encoding
		if (file_exists('ram/' .$session .'/segmenter.pid'))
			$sessionname = "*" .$sessionname;

		$sessioncnt++;

		$sessionname .= $realname;

		print "    <li class=\"menu\">";
		print "      <a href=\"javascript:sendForm('{$session}');\">";
		print "        <img src=\"images/pictos/{$picto}.png\" />";
		print "          <span class=\"name\">{$sessionname}</span><span class=\"arrow\"></span>";
		print "      </a>";
		print "    </li>\r\n";
		print "    <form name=\"{$session}\" id=\"{$session}\" method=\"post\" action=\"index.php\">";
		print "      <input name=\"action\" type=\"hidden\" id=\"action\" value=\"streaming\" />";
		print "      <input name=\"session\" type=\"hidden\" id=\"session\" value=\"{$session}\" />";
		print "    </form>\r\n";
	}

	closedir($dir_handle);
}

if (!$sessioncnt)
{
	print "  <li class=\"textbox\"><p>none</p></li>\r\n";
	print "</ul>\r\n";
}
else
{
        print "</ul>\r\n";
        print "<form name=\"killall\" id=\"killall\" method=\"post\" action=\"index.php\">\r\n";
        print "  <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stopallstreamings\" />\r\n";
        print "  <ul class=\"pageitem\">\r\n";
        print "  <li class=\"button\">\r\n";
        print "  <input name=\"Submit\" type=\"submit\" value=\"Delete all sessions\" /></li>\r\n";
        print "</ul>\r\n";
        print "</form>\r\n";
}

// VDR menus
if ($vdrenabled)
{
	print "  <span class=\"graytitle\">VDR</span>\r\n";
	print "  <ul class=\"pageitem\">\r\n";
	print "    <li class=\"menu\"><a href=\"javascript:sendForm('channels');\"><img src=\"images/pictos/tv.png\" /><span class=\"name\">Channels</span><span class=\"arrow\"></span></a></li>\r\n";
	print "	   <form name=\"channels\" id=\"channels\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"listcategory\" /></form>\r\n";
	print "	   <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('recordings');\"><img src=\"images/pictos/record.png\" /><span class=\"name\">Recordings</span><span class=\"arrow\"></span></a></li>\r\n";
	print "	   <form name=\"recordings\" id=\"recordings\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"recordings\" /><input name=\"dir\" type=\"hidden\" id=\"dir\" value=\"{$vdrrecpath}\" /></form>\r\n";
	print "    <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('epg');\"><img src=\"images/pictos/epg.png\" /><span class=\"name\">Program Guide</span><span class=\"arrow\"></span></a></li>\r\n";
	print "    <form name=\"epg\" id=\"epg\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"epg\" /></form>\r\n";
	print "    <li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('timers');\"><img src=\"images/pictos/timers.png\" /><span class=\"name\">Timers</span><span class=\"arrow\"></span></a></li>\r\n";
	print "    <form name=\"timers\" id=\"timers\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"timers\" /></form>\r\n";
	print "	 </ul>";
}

// Media menus
print "  <span class=\"graytitle\">MEDIA</span>\r\n";
print "  <ul class=\"pageitem\">\r\n";
foreach($mediasources as $source)
{
	$stype = $source[0];
	$sname = $source[1];
	$spath = $source[2];

	print "    <li class=\"menu\">\r\n";
	print "      <a class=\"noeffect\" href=\"javascript:sendForm('media {$sname} {$spath}');\">\r\n";
	if ($stype == 1)
		print "        <img src=\"images/pictos/video.png\" />\r\n";
	else
		print "        <img src=\"images/pictos/audio.png\" />\r\n";
	print "        <span class=\"name\">{$sname}</span>\r\n";
	print "        <span class=\"arrow\"></span>\r\n";
	print "      </a>\r\n";
	print "    </li>\r\n";
	print "    <form name=\"media\" id=\"media {$sname} {$spath}\" method=\"post\" action=\"index.php\">\r\n";
	if ($stype == 1)
		print "      <input name=\"action\" type=\"hidden\" id=\"action\" value=\"video\" />\r\n";
	else
		print "      <input name=\"action\" type=\"hidden\" id=\"action\" value=\"audio\" />\r\n";
	print "      <input name=\"mediapath\" type=\"hidden\" id=\"mediapath\" value=\"{$spath}\" />\r\n";
	print "      <input name=\"subdir\" type=\"hidden\" id=\"subdir\" value=\"/\" />\r\n";
	print "    </form>\r\n";
}
print "  </ul>";
print "</div>";
?>
