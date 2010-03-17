<?php
include ('./svdrp_old.php');

function vdrsendcommand($cmd)
{
        global $svdrpip, $svdrpport;

        $svdrp = new SVDRP($svdrpip, $svdrpport);
        $svdrp->Connect();
        $ret = $svdrp->Command($cmd);
        $svdrp->Disconnect();

        return $ret;
}

function vdrgetcategories()
{
	global $vdrchannels;

	$catlist = array();

	if (!file_exists($vdrchannels))
	{
		print "Error: channels file not found";
		return $catlist;
	}

	$fp = fopen ($vdrchannels,"r");
	if (!fp)
	{
		print "Unable to open channels file";
		return $catlist;
	}

	$curcat = "";
	$curcatchancount = 0;

	while ($line = fgets($fp, 1024))
	{
		// Check if it is a categorie
		if ($line[0] == ":")
		{
			// Close current category
			if ($curcat != "")
			{
				$tmpcat = array();
				$tmpcat['name'] = $curcat;
				$tmpcat['channels'] = $curcatchancount;
				$catlist[] = $tmpcat;

				$curcatchancount = 0;
			}

			// Remove : and @
			$curcat = substr($line, 1, -1);
			if($curcat[0] == '@')
			{
				$catarray = explode(' ', $curcat);
				$curcat = substr($curcat, strlen($catarray[0])+1);
			}

			if (!is_utf8($curcat))
				$curcat = utf8_encode($curcat);
                }
		else if ($line[0] != "")
			$curcatchancount++;
        }

	// Close last cat
	if ($curcat != "")
	{
		$tmpcat = array();
		$tmpcat['name'] = $curcat;
		$tmpcat['channels'] = $curcatchancount;
		$catlist[] = $tmpcat;
	}

        fclose($fp);

	return $catlist;
}

function vdrgetchannels($category, $now)
{
        global $vdrchannels;

	$chanlist=array();

        if (!file_exists($vdrchannels))
        {
                print "Error: channels file not found";
                return $chanlist;
        }

        $fp = fopen ($vdrchannels,"r");
        if (!fp)
        {
                print "Unable to open channels file";
		return $chanlist;
        }

	$cat_found = 0;

	// Get NOW epg
	if ($now)
		$epgnow = vdrsendcommand("LSTE NOW");

	while ($line = fgets($fp, 1024))
	{
		if (!$cat_found)
		{
			if ($line[0] != ":")
				continue;

			// Get category name			
			$cat = substr($line, 1, -1);
			if($cat[0] == '@')
			{
				$catarray = explode(' ', $cat);
				$cat = substr($cat, strlen($catarray[0])+1);
			}

			 if (!is_utf8($cat))
                                $cat = utf8_encode($cat);

			if ($cat == $category)
				$cat_found = 1;
		}
		else if ($line[0] != "")
		{
			if ($line[0] == ":")
				break;

			$channame = explode(":", $line);
			$channame = explode(";", $channame[0]);
			$channame = $channame[0];

			$tmpchan = array();
			$tmpchan['name'] = $channame;
			$tmpchan['number'] = vdrgetchannum($channame);
			if ($now)
			{
				// Extract now
				$chanfound = 0;
				$count = count($epgnow);
				$info = "";
				for ($i = 0; $i < $count; $i++)
				{
					// Find the right chan (take the first one)
					if ($chanfound == 0)
					{
						if (strstr($epgnow[$i], $channame) == $channame)
							$chanfound = 1;
					}
					else
					{
						// Now find T or C
						if(ereg("^C", $epgnow[$i]))
						{
							if (!strstr($epgnow[$i], $channame) == $channame)
							{
								$chanfound = 0;
								continue;
							}
						}
						else if(ereg("^T", $epgnow[$i]))
						{
							$info=substr($epgnow[$i], 2);
							if (!is_utf8($info))
								$info = utf8_encode($info);
							break;
						}
					}
				}

				$tmpchan['now_title'] = $info;
			}

			if (!is_utf8($tmpchan['name']))
				$tmpchan['name'] = utf8_encode($tmpchan['name']);

			$chanlist[] = $tmpchan;
		}
	}

	fclose($fp);

	return $chanlist;
}

function vdrgetchannum($chan)
{
	global $channels;

	if ($channels == "")
		$channels = vdrsendcommand("LSTC");

	// Get channel number
	$chans = preg_grep(quotemeta('"'.$chan.';|'.$chan.':"'), $channels);

	$chans = explode(" ", $chans[key($chans)]);
	$channum = $chans[0];

	return $channum;
}

function vdrgetchanname($channum)
{
        $channel = vdrsendcommand("LSTC " .$channum);

        // Get channel name
        $chanarray = explode(":", $channel);
        $chanarray = explode(";", $chanarray[0]);
        $channame = $chanarray[0];
        $channame = substr($channame, strlen($channum)+1);

	if(!is_utf8($channame))
		$channame = utf8_encode($channame);

        return $channame;
}

function vdrgetchaninfo($channum)
{
	$info = array();

	$info['name'] = vdrgetchanname($channum);
	$info['number'] = $channum;
	list($info['now_time'], $info['now_title'], $info['now_desc']) = vdrgetchanepg($channum, 1);
	list($info['next_time'], $info['next_title'], $info['next_desc']) = vdrgetchanepg($channum, 0);

	return $info;
}

function vdrgetchanepg($channum, $now)
{
	if ($now)
		$cmd = "LSTE " .$channum ." NOW";
	else
		$cmd = "LSTE " .$channum ." NEXT";

	$epg = vdrsendcommand($cmd);

	$time="";
	$title="";
	$desc="";

	// For all epg
	$count = count($epg);
	for ($i = 0; $i < $count; $i++)
	{
		if(ereg("^T ", $epg[$i]))
			$title = substr($epg[$i], 2);
		else if(ereg("^D ", $epg[$i]))
			$desc = substr($epg[$i], 2);
		else if(ereg("^E ", $epg[$i]))
		{
			$time = substr($epg[$i], 2);
			$timearray = explode(" ", $time);

			$time = date('H\hi', $timearray[1]) ."-" .date('H\hi', $timearray[1]+$timearray[2]);
		}
	}
	
	// Convert if needed
	if (!is_utf8($title))
		$title = utf8_encode(title);
	if (!is_utf8($desc))
		$desc = utf8_encode($desc);

	return array($time, $title, $desc);
}

function vdrgetrecinfo($rec)
{
	$infofile = $rec ."/info"; 
	if (file_exists($infofile))
		$info= file_get_contents($infofile);
	else
	{
		$infofile = $rec ."/info.vdr";
		if (file_exists($infofile))
			$info= file_get_contents($infofile);
		else
			$info="";
	}

	$allepg = explode("\n", $info);

	$epgtitle="";
	$epgdesc="";
	
	// For all epg
	$count = count($allepg);
	for ($i = 0; $i < $count; $i++)
	{
		// Now find T or C
		if(ereg("^C", $allepg[$i]))
		{
			$channame = substr($allepg[$i], 2);
			$channames = explode(" ", $channame);
			$channame = substr($channame, strlen($channames[0])+1);
		}
		else if(ereg("^T", $allepg[$i]))
			$epgtitle=substr($allepg[$i], 2);
		else if(ereg("^D", $allepg[$i]))
			$epgdesc=substr($allepg[$i], 2);
	}
	
	// Convert if needed
	if (!is_utf8($epgtitle))
		$epgtitle = utf8_encode($epgtitle);
	if (!is_utf8($epgdesc))
		$epgdesc = utf8_encode($epgdesc);


	return array($channame, epgtitle, $epgdesc);
}











































function vdrgetinfostream($stream = "NULL", $ischan = 1)
{
	global $allepg, $allepgfilled;
	$stream=stripslashes($stream);
	if ($ischan)
	{
		// Fill epg if not yet done
		if ($allepgfilled == 0)
		{
			$allepg = vdrsendcommand("LSTE NOW");
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
			$streamArray = explode(";",$stream);
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

function vdrgettimerinfo($timernum=-1)
{
	if ($timernum != -1)
	{
		$timer = vdrsendcommand("LSTT " .$timernum);

		$timerarray = explode(":", $timer);

		$typearray = explode(" ", $timerarray[0]);
		$type = $typearray[1];
		$channel = $timerarray[1];
		$date = $timerarray[2];
		$stime = $timerarray[3];
		$etime = $timerarray[4];
		$desc = $timerarray[7];
	}
	else
	{
		$type = 1;
		$channel = 1;
		$date = date('Y-m-d');
		$stime = date('Hi');
		$etime = date('Hi');
		$desc = "New timer";
	}
	
	$channame = vdrgetchanname($channel);
	
	return array($type, $channame, $date, $stime, $etime, $desc);
}

function vdrlistchannels($category = "NULL")
{
	global $epgtitle;
        global $vdrchannels;

	if ($category == "All")
		$cat_found=1;
	else
		$cat_found=0;

        if (!file_exists($vdrchannels))
        {
                print "Error: channels file not found";
                return;
        }

        $fp = fopen ($vdrchannels,"r");
        if (!fp)
        {
                print "Unable to open channels file";
                return;
        }
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
			$channoslash = preg_replace("$/$", " ", $chan);
			if (!file_exists('logos/'.$channoslash.'.png'))
				print " <img src=\"logos/nologoTV.png\" />\r\n";
			else
				print "	<img src=\"logos/{$channoslash}.png\" />\r\n";
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

function vdrlistchannelsdrop($chansel = "")
{
	global $vdrchannels;

	$chanselected = 0;

        if (!file_exists($vdrchannels))
        {
                print "Error: channels file not found";
                return;
        }

        $fp = fopen ($vdrchannels,"r");
        if (!fp)
        {
                print "Unable to open channels file";
                return;
        }
	while ($line = fgets($fp, 1024))
        {
		if ($line[0] == ":")
			continue;
		
		$channels = explode(":", $line);
		$channels = explode(";", $channels[0]);
		$chan = $channels[0];
		if (($chan == $chansel) && !$chanselected)
		{
			print "<option selected value=\"{$chan}\">{$chan}</option>";
			$chanselected = 1;
		}
		else
			print "<option value=\"{$chan}\">{$chan}</option>";
	}
}

function vdrlisttimers()
{
	$timers = vdrsendcommand("LSTT");

	if (gettype($timers) == "string")
	{
		if (!is_numeric(substr($timers,0,1)))
		{
			print "<li class=\"textbox\"><p>none</p></li>\r\n";
			return;
		}
		else
			$timersarray[] = $timers;
	}
	else
		$timersarray = $timers;

	foreach($timersarray as $timer)
	{
		// Extract timer #
		$timerarray = explode(" ", $timer);
		$timernum = $timerarray[0];

		list($type, $channame, $date, $starthour, $endhour, $desc) = vdrgettimerinfo($timernum);

		print "<li class=\"menu\">";
		print " <a href=\"javascript:sendForm('timer {$timernum}')\">\r\n";
		
		if ($type & 0x8)
			print "  <img alt=\"list\" src=\"images/pictos/timerrec.png\" />\r\n";
		else if ($type & 0x1)
			print "  <img alt=\"list\" src=\"images/pictos/timeron.png\" />\r\n";
		else
			print "  <img alt=\"list\" src=\"images/pictos/timeroff.png\" />\r\n";

		print "  <span class=\"name\">{$date}: {$desc}</span><span class=\"arrow\"></span>\r\n";

		print " </a>\r\n";
		print "</li>\r\n";

		print "<form name=\"timer {$timernum}\" id=\"timer {$timernum}\" method=\"post\" action=\"index.php\">\r\n";
		print " <input name=\"action\" type=\"hidden\" id=\"action\" value=\"edittimer\"/>\r\n";
		print " <input name=\"timer\" type=\"hidden\" id=\"timer\" value=\"{$timernum}\" />\r\n";
		print "</form>\r\n";
	}
}

function vdrdeltimer($timer=0)
{
	return vdrsendcommand("DELT " .$timer);
}

function vdrsettimer($prevtimer, $channame, $date, $stime, $etime, $desc, $active)
{
	$channum = vdrgetchannum($channame);
	if ($active)	
		$type = "1";
	else
		$type = "0";

	if ($prevtimer == -1)
		$command = "NEWT " .$type .":" .$channum .":" .$date .":" .$stime .":" .$etime .":99:99:" .$desc;
	else
		$command = "MODT " .$prevtimer ." " .$type .":" .$channum .":" .$date .":" .$stime .":" .$etime .":99:99:" .$desc;

	return vdrsendcommand($command);
}

?>
