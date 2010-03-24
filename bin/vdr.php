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

function vdrgetfullepgat($at, $programs)
{
	$chanentry = array();
	$chanepg = array();
	$fullepg = array();

	// Update EPG is needed
	if ($_SESSION['fullepg'] == "")
		$_SESSION['fullepg'] = vdrsendcommand("LSTE");

	$validepg = 0;

	// For all epg
	$count = count($_SESSION['fullepg']);
	for ($i = 0; $i < $count; $i++)
	{
		// Find chan
		if(ereg("^C", $_SESSION['fullepg'][$i]))
                {
                        $channame = substr($_SESSION['fullepg'][$i], 2);
                        $channames = explode(" ", $channame);
                        $channame = substr($channame, strlen($channames[0])+1);

			// Create a new chan entry
			$chanentry['name'] = $channame;
			$chanentry['number'] = "TODO";
			$chanentry['category'] = "TODO";
			$chanentry['epg'] = array();
			
			$programscounter = 0;
			$validepg = 0;

			continue;
                }

		// Close chan
		if(ereg("^c", $_SESSION['fullepg'][$i]))
		{
			// Add new entry
			$fullepg[] = $chanentry;
                        
			continue;
		}

		// Dont get more programs for current chan		
		if ($programscounter >= $programs)
			continue;

		// Find a new EPG entry
		if(ereg("^E", $_SESSION['fullepg'][$i]))
		{
			$time = substr($_SESSION['fullepg'][$i], 2);
			$timearray = explode(" ", $time);

			// Dont use this one
			if ($timearray[1] < $at)
			{
				$validepg = 0;
				continue;
			}

			// New valid epg found
			$chanepg['title'] = "";
			$chanepg['time'] = date('H\hi', $timearray[1]) ."-" .date('H\hi', $timearray[1]+$timearray[2]);

			$validepg = 1;

			continue;
		}

		if(ereg("^T", $_SESSION['fullepg'][$i]) && $validepg)
		{
			$chanepg['title'] = substr($_SESSION['fullepg'][$i], 2);
			if (!is_utf8($chanepg['title']))
				$chanepg['title'] = utf8_encode($chanepg['title']);

			continue;
		}

		// Add a new epg
		if(ereg("^e", $_SESSION['fullepg'][$i]) && $validepg)
		{
			$chanentry['epg'][] = $chanepg;
			$programscounter++;

			$validepg = 0;

			continue;
		}
	}

	return $fullepg;
}

function vdrgetepg($channel, $time, $day, $programs, $extended)
{
	// Compute time
	$currentdate = gettimeofday();

	// Remove current day minutes
	$currentday = $currentdate['sec'] - ($currentdate['sec'] % (3600*24));

	$requestedhours = (int) substr($time, 0, 2);
	$requestedmins = (int) substr($time, 2);
	$requestedtime = ($requestedhours * 3600) + ($requestedmins * 60);

	// Comput requested date
	$requesteddate = $currentday + ((int)$day * (3600*24)) + $requestedtime - 3600;

	// Single chan case
	if ($channel != "all")
	{
		// Create a new chan entry in epg
		$chanentry = array();
		$chanentry['name'] = vdrgetchanname($channel);
		$chanentry['number'] = $channel;
		$chanentry['category'] = "Unknown";

		$chanentry['epg'] = array();

		if ($extended)
	                list ($chanentry['date'], $chanentry['time'], $chanentry['title'], $chanentry['desc']) = vdrgetepgat($channel, "at " .$requesteddate);
		else
		{
			$chanentry['epg'] = array();
			$attime = $requesteddate;
			$nbprograms = 0;
			while ($nbprograms < $programs)
			{
				list ($date, $chanepg['time'], $chanepg['title'], $desc, $endtime) = vdrgetepgat($channel, "at " .$attime);
				$chanentry['epg'][] = $chanepg;

				$attime = $endtime+1;
				$nbprograms ++;
			}
		}
		
		return $chanentry;
	}

	// All chans
	return vdrgetfullepgat($requesteddate, $programs);
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
