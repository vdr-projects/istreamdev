<?php
include ('includes/inc_svdrp.php');

function vdrgetinfostream($stream = "NULL", $ischan = 1)
{
	global $allepg, $allepgfilled, $svdrpip, $svdrpport;

	if ($ischan)
	{
		// Fill epg if not yet done
		if ($allepgfilled == 0)
		{
			$svdrp = new SVDRP($svdrpip, $svdrpport);
			$svdrp->Connect();
			$allepg = $svdrp->Command("LSTE NOW");
			$svdrp->Disconnect();
			$allepgfilled = 1;
		}

		$channame = $stream;
	}
	else
	{
		$infofile = $stream ."/info"; 
		if (file_exists($infofile))
			$info= file_get_contents($infofile);
		else
		{
			$infofile = $stream ."/info.vdr";
			if (file_exists($infofile))
				$info= file_get_contents($infofile);
			else
				$info="";
		}
	
		$allepg = explode("\n", $info);
	}

	if ($ischan)	
		$chanfound = 0;
	else	
		$chanfound = 1;
	$epgtitlefound = 0;
	
	$epgtitle="";
	$epgdesc="";
	
	// For all epg
	$count = count($allepg);
	for ($i = 0; $i < $count; $i++)
	{
		// Find the right chan (take the first one)
		if ($chanfound == 0)
		{
			$streamArray = explode(",",$stream);
			if (strstr($allepg[$i], $streamArray[0]) == $streamArray[0])
				$chanfound = 1;
		}
		else
		{
			// Now find T or C
			if(ereg("^C", $allepg[$i]))
			{
				// Check if it is our chan too, else search again
				if ($ischan)
				{
					if(!ereg("$stream$", $allepg[$i]))
					{
						$chanfound = 0;
						continue;
					}
				}
				else
				{
					$channame = substr($allepg[$i], 2);
					$channames = explode(" ", $channame);
					$channame = substr($channame, strlen($channames[0])+1);
				}
			}
			else if(ereg("^T", $allepg[$i]))
				$epgtitle=substr($allepg[$i], 2);
			else if(ereg("^D", $allepg[$i]))
				$epgdesc=substr($allepg[$i], 2);
		}
	}
	
	// Convert if needed
	if (!is_utf8($epgtitle))
		$epgtitle = utf8_encode($epgtitle);
	if (!is_utf8($epgdesc))
		$epgdesc = utf8_encode($epgdesc);


	return array($epgtitle, $epgdesc, $channame);
}

function vdrgettimerinfo($timernum=0)
{
	global $svdrpip, $svdrpport;

	$svdrp = new SVDRP($svdrpip, $svdrpport);
	$svdrp->Connect();
	$timer = $svdrp->Command("LSTT " .$timernum);
	$svdrp->Disconnect();

	$timerarray = explode(":", $timer);

	$typearray = explode(" ", $timerarray[0]);
	$type = $typearray[1];
	$channel = $timerarray[1];
	$channame = vdrgetchanname($channel);
	$date = $timerarray[2];
	$starthour = $timerarray[3];
	$endhour = $timerarray[4];
	$desc = $timerarray[7];

	return array($type, $channame, $date, $starthour, $endhour, $desc);
}

function vdrgetchannum($chan = "NULL")
{
	global $svdrpip, $svdrpport;
	
	$svdrp = new SVDRP($svdrpip, $svdrpport);
	$svdrp->Connect();
	$channels = $svdrp->Command("LSTC");
	$svdrp->Disconnect();

	// Get channel number
	$channels = preg_grep(quotemeta('"'.$chan.';|'.$chan.':"'), $channels);
	reset($channels);

	$channels = explode(" ", $channels[key($channels)]);
	$channum = $channels[0];

	return $channum;
}

function vdrgetchanname($channum = 0)
{
	global $svdrpip, $svdrpport;

	$svdrp = new SVDRP($svdrpip, $svdrpport);
	$svdrp->Connect();
	$channel = $svdrp->Command("LSTC " .$channum);
	$svdrp->Disconnect();

	// Get channel name
	$chanarray = explode(":", $channel);
	$chanarray = explode(";", $chanarray[0]);
	$channame = $chanarray[0];
	$channame = substr($channame, strlen($channum)+1);

        return $channame;
}


function vdrlistcategories()
{
	global $vdrchannels;

	// All chans
	print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('All');\"><span class=\"name\">All channels</span><span class=\"arrow\"></span></a></li>\r\n";
	print "<form name=\"All channels\" id=\"All\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"listchannels\"/><input name=\"cat\" type=\"hidden\" id=\"cat\" value=\"All\" /></form>\r\n";

	$fp = fopen ($vdrchannels,"r");
	while ($line = fgets($fp, 1024))
	{
		// Check if it is a categorie
		if ($line[0] == ":")
		{
			// Remove : and @
			$cat = substr($line, 1, -1);
			if($cat[0] == '@')
			{
				$cat_array = explode(' ', $cat);
				$cat = substr($cat, strlen($cat_array[0])+1);
			}
			
			$cat2 = addslashes($cat);
				
                        print "<li class=\"menu\"><a class=\"noeffect\" href=\"javascript:sendForm('$cat2');\"><span class=\"name\">$cat</span><span class=\"arrow\"></span></a></li>\r\n";
                        print "<form name=\"$cat\" id=\"$cat\" method=\"post\" action=\"index.php\"><input name=\"action\" type=\"hidden\" id=\"action\" value=\"listchannels\"/><input name=\"cat\" type=\"hidden\" id=\"cat\" value=\"$cat\" /></form>\r\n";
                }
        }
        fclose($fp);
}

function vdrlistchannels($category = "NULL")
{
	global $epgtitle;
        global $vdrchannels;

	if ($category == "All")
		$cat_found=1;
	else
		$cat_found=0;

	$fp = fopen ($vdrchannels,"r");
	while ($line = fgets($fp, 1024))
	{
		if ($cat_found)
		{
			if ($line[0] == ":")
			{
				if ($category == "All")
					continue;
				else
					break;
			}
			
			$channels = explode(":", $line);
			$channels = explode(";", $channels[0]);
			$chan = $channels[0];

			// Get EPG title
			$epgtitle = NULL;
			list($epgtitle, $epgdesc, $channame) = vdrgetinfostream($chan, 1);	
			print "<li class=\"withimage\">";
			$chan2=addslashes($chan);
			print "	<a class=\"noeffect\" href=\"javascript:sendForm('$chan2');\">\r\n";
			if (!file_exists('logos/'.$chan.'.png'))
				print " <img src=\"logos/nologoTV.png\" />\r\n";
			else
				print "	<img src=\"logos/{$chan}.png\" />\r\n";
			print " <span class=\"name\">$chan</span>\r\n";
			print " <span class=\"comment\">$epgtitle</span><span class=\"arrow\"></span></a>\r\n</li>\r\n";
			print "	<form name=\"$chan\" id=\"$chan\" method=\"post\" action=\"index.php\">";
			print "    <input name=\"action\" type=\"hidden\" id=\"action\" value=\"stream\" />";
			print "    <input name=\"type\" type=\"hidden\" id=\"type\" value=1 />";
			print "    <input name=\"name\" type=\"hidden\" id=\"name\" value=\"$chan\" />";
			print " </form>\r\n";
		}
		else
		{
			if ($line[0] == ":")
			{
				// Remove : and @
				$cat = substr($line, 1, -1);
				if($cat[0] == '@')
				{
					$cat_array = explode(' ', $cat);
					$cat = substr($cat, strlen($cat_array[0])+1);
				}

				// Check category
				if ("$cat" == "$category")
					$cat_found = 1;
			}
		}
	}
	fclose($fp);
}

function vdrlisttimers()
{
	global $svdrpip, $svdrpport;

	$svdrp = new SVDRP($svdrpip, $svdrpport);
	$svdrp->Connect();
	$timers = $svdrp->Command("LSTT");
	$svdrp->Disconnect();

	foreach($timers as $timer)
	{
		// Extract timer #
		$timerarray = explode(" ", $timer);
		$timernum = $timerarray[0];

		list($type, $channame, $date, $starthour, $endhour, $desc) = vdrgettimerinfo($timernum);

		print "<li class=\"menu\">";
		print " <a href=\"javascript:sendForm('timer {$timernum}')\">";
		print "  <img alt=\"list\" src=\"images/pictos/timers.png\" />";

		print "  <span class=\"name\">{$date}: {$channame}</span><span class=\"arrow\"></span>";

		print " </a>";
		print "</li>";

		print "<form name=\"timer {$timernum}\" id=\"timer {$timernum}\" method=\"post\" action=\"index.php\">";
		print " <input name=\"action\" type=\"hidden\" id=\"action\" value=\"edit_timer\"/>";
		print " <input name=\"timer\" type=\"hidden\" id=\"timer\" value=\"{$timernum}\" />";
		print "</form>";
	}
}




?>
