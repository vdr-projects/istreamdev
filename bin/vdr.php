<?php
include ('./svdrp.php');

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

	$channum = 1;

	while ($line = fgets($fp, 1024))
	{
		if (!$cat_found)
		{
			// 2 case where we dont increment channum
			if (! (($line[0] == "") || (($line[0] == ":") && ($line[1] != "@"))) )
				$channum++;

			if ($line[0] != ":")
				continue;

			// Get category name			
			$cat = substr($line, 1, -1);
			if($cat[0] == '@')
			{
				$catarray = explode(' ', $cat);
				$cat = substr($cat, strlen($catarray[0])+1);

				$channum = substr($catarray[0], 1);
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
			$tmpchan['number'] = $channum;
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

			$channum++;
		}
	}

	fclose($fp);

	return $chanlist;
}

function vdrgetchannum($chan)
{
	if ($_SESSION['channels'] == "")
		$_SESSION['channels'] = vdrsendcommand("LSTC");

	// Get channel number
	$chans = preg_grep(quotemeta('"'.$chan.';|'.$chan.':"'), $_SESSION['channels']);

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

function vdrgetchancat($chaname)
{
        global $vdrchannels;

	if (!file_exists($vdrchannels))
                return "";

	$fp = fopen ($vdrchannels,"r");
	if (!fp)
		return "";

	$cat = "";

	while ($line = fgets($fp, 1024))
	{
		if ($line[0] == ":")
		{
			$cat = substr($line, 1, -1);
			if($cat[0] == '@')
			{
				$catarray = explode(' ', $cat);
				$cat = substr($cat, strlen($catarray[0])+1);
                        }
			if (!is_utf8($cat))
				$cat = utf8_encode($cat);

			continue;
		}
		
		$name = explode(":", $line);
		$name = explode(";", $name[0]);
		if ($name[0] == $chaname)
			break;
	}

	return $cat;
}

function vdrgetchaninfo($channum)
{
	$info = array();

	$info['name'] = vdrgetchanname($channum);
	$info['number'] = $channum;
	list($info['now_time'], $info['now_title'], $info['now_desc']) = vdrgetepgat($channum, "now");
	list($info['next_time'], $info['next_title'], $info['next_desc']) = vdrgetepgat($channum, "next");

	return $info;
}

function vdrgetepgat($channum, $at)
{
	$cmd = "LSTE " .$channum ." " .$at;

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

			$date = date('Y\/m\/d', $timearray[1]);

			$endtime = $timearray[1]+$timearray[2];
		}
	}
	
	// Convert if needed
	if (!is_utf8($title))
		$title = utf8_encode(title);
	if (!is_utf8($desc))
		$desc = utf8_encode($desc);

	return array($date, $time, $title, $desc, $endtime);
}

function vdrgetfullepgat($channel, $at, $programs)
{
	$chanentry = array();
	$chanepg = array();
	$epgout = array();
	
	$addedchans = array();

	if ($channel == "all")
	{
		// Update full EPG is needed
		if ($_SESSION['fullepg'] == "")
			$_SESSION['fullepg'] = vdrsendcommand("LSTE");
		
		$epgin = $_SESSION['fullepg'];
	}
	else
	{
		if ($_SESSION['epg' .$channel] == "")
			$_SESSION['epg' .$channel] = vdrsendcommand("LSTE " .$channel);
		$epgin = $_SESSION['epg' .$channel];
	
		$channelname = vdrgetchanname($channel);
	}

	// For all epg
	$count1 = count($epgin);
	for ($i = 0; $i < $count1; $i++)
	{
		// Find chan
		if(ereg("^C", $epgin[$i]))
                {
                        $channame = substr($epgin[$i], 2);
                        $channames = explode(" ", $channame);
                        $channame = substr($channame, strlen($channames[0])+1);

			if (($channel != "all") && ($channame != $channelname))
				continue;

			// Dont add chans twice
			if (count(preg_grep(quotemeta('"' .$channame .'"'), $addedchans)))
				continue;

			// Create a new chan entry
			$chanentry['name'] = $channame;
			$chanentry['number'] = vdrgetchannum($channame);
			$chanentry['category'] = vdrgetchancat($channame);
			$chanentry['epg'] = array();
			
			$programscounter = 0;

			$validepg = 0;
			$validchan = 1;

			continue;
                }

		// Close chan
		if(ereg("^c", $epgin[$i]))
		{
			if ($programscounter && $validchan)
			{
				// Add new entry
				$epgout[] = $chanentry;
				$addedchans[] = $chanentry['name'];

				if ($channel != "all")
					break;
			}
	
			$validchan = 0;
                        
			continue;
		}

		// Continue to parse chan ?
		if (!$validchan)
			continue;

		// Dont get more programs for current chan
		if (is_numeric($programs))
		{
			if ($programscounter >= $programs)
				continue;
		}

		// Find a new EPG entry
		if(ereg("^E", $epgin[$i]))
		{
			$time = substr($epgin[$i], 2);
			$timearray = explode(" ", $time);

			$starttime = $timearray[1];
			$endtime = $timearray[1]+$timearray[2];

			switch ($programs)
			{
				case "all":
					$validepg = 1;
					break;
				case "day":
					if (($endtime >= $at) && ($starttime < ($at + 3600*24)))
						$validepg = 1;
					else
						$validepg = 0;
					break;
				default:
					if ($endtime >= $at)
						$validepg = 1;
					else
						$validepg = 0;
					break;
			} 

			if (!$validepg)
				continue;

			// New valid epg found
			$chanepg['title'] = "";
			$chanepg['time'] = date('H\hi', $timearray[1]) ."-" .date('H\hi', $timearray[1]+$timearray[2]);

			continue;
		}

		if(ereg("^T", $epgin[$i]) && $validepg)
		{
			$chanepg['title'] = substr($epgin[$i], 2);
			if (!is_utf8($chanepg['title']))
				$chanepg['title'] = utf8_encode($chanepg['title']);

			continue;
		}

		// Add a new epg
		if(ereg("^e", $epgin[$i]))
		{
			if ($validepg)
			{
				$chanentry['epg'][] = $chanepg;
				$programscounter++;

				$validepg = 0;
			}

			continue;
		}
	}

	if (count($epgout))
	{
		// Sort it
		foreach ($epgout as $key => $row)
			$channum[$key] = $row['number'];

		array_multisort($channum, SORT_ASC, $epgout);
	}

	return $epgout;
}

function vdrgetepg($channel, $time, $day, $programs, $extended)
{
	// Compute time
	$currentdate = time();

	// Remove current day seconds
	$currentday = $currentdate - ($currentdate % (3600*24));

	switch ($programs)
	{
		case "all":
			// Get all entries
			$requesteddate = 0;
			break;

		case "day":
			// Get all day
			$requesteddate = $currentdate - ($currentdate % (3600*24)) + ($day * (3600*24)) - 3600;
			break;

		default:
			// Get exact time
			switch ($time)
			{
				case "now":
					$requesteddate = $currentdate;
					break;
				default:
					$requestedday = $currentdate - ($currentdate % (3600*24)) + ($day * (3600*24));
					$requestedtime = ((int) substr($time, 0, 2) * 3600) + ((int) substr($time, 2) * 60);
					$requesteddate = $requestedday + $requestedtime - 3600;
					break;
			}
	}

	if ($extended)
	{
		list ($chanentry['date'], $chanentry['time'], $chanentry['title'], $chanentry['desc']) = vdrgetepgat($channel, "at " .$requesteddate);
		return $chanentry;
	}
	else
		return vdrgetfullepgat($channel, $requesteddate, $programs);
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
		else if(ereg("^E ", $allepg[$i]))
		{
			$time = substr($allepg[$i], 2);
			$timearray = explode(" ", $time);

			$recorded = date('Y\/m\/d \a\t H\hi', $timearray[1]);
                }

	}
	
	// Convert if needed
	if (!is_utf8($epgtitle))
		$epgtitle = utf8_encode($epgtitle);
	if (!is_utf8($epgdesc))
		$epgdesc = utf8_encode($epgdesc);

	return array($channame, $epgtitle, $epgdesc, $recorded);
}

function vdrlisttimers()
{
	$timerslist = array();

	$timers = vdrsendcommand("LSTT");

	if (gettype($timers) == "string")
	{
		if (!is_numeric(substr($timers,0,1)))
			return $timerslist;
		else
			$timersarray[] = $timers;
	}
	else
		$timersarray = $timers;

	foreach($timersarray as $timer)
	{
		$newtimer = array();

		// Extract timer info
		$timerarray = explode(" ", $timer);

		$newtimer['id'] = $timerarray[0];

		$timerarray = explode(":", $timer);

                $typearray = explode(" ", $timerarray[0]);
		$newtimer['name'] = $timerarray[7];
		$newtimer['active'] = ($typearray[1] & 0x1)?"1":0;
		$newtimer['channumber'] = $timerarray[1];
		$newtimer['channame'] = vdrgetchanname($timerarray[1]);
		$newtimer['date'] = $timerarray[2];
		$newtimer['starttime'] = $timerarray[3];
		$newtimer['endtime'] = $timerarray[4];
		$newtimer['running'] = ($typearray[1] & 0x8)?1:0;

		$timerslist[] = $newtimer;
	}

	return $timerslist;
}

function vdrdeltimer($timer)
{
	$ret = array();

	$message = vdrsendcommand("DELT " .$timer);

	if (preg_match("/deleted/", $message))
	{
		$ret['status'] = "Ok";
		$ret['message'] = "Timer successfully deleted";
	}
	else
	{
		$ret['status'] = "Error";
		$ret['message'] = $message;
	}

	return $ret;
}

function vdrsettimer($prevtimer, $channum, $date, $stime, $etime, $desc, $active)
{
	$ret = array();

	if ($prevtimer == "")
		$command = "NEWT " .$active .":" .$channum .":" .$date .":" .$stime .":" .$etime .":99:99:" .$desc;
	else
		$command = "MODT " .$prevtimer ." " .$active .":" .$channum .":" .$date .":" .$stime .":" .$etime .":99:99:" .$desc;

	$message = vdrsendcommand($command);

	if (is_numeric(substr($message, 0, 1)))
	{
		$ret['status'] = "Ok";
		if ($prevtimer == "")
			$ret['message'] = "Timer created successfully";
		else
			$ret['message'] = "Timer edited successfully";
	}
	else
	{
		$ret['status'] = "Error";
		$ret['message'] = $message;
	}

	return $ret;
}

?>
