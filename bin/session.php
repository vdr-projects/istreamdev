<?php

function sessioncreate($type, $url, $mode)
{
	global $httppath, $ffmpegpath, $segmenterpath, $quality, $maxencodingprocesses, $ffmpegdebug, $ffmpegdebugfile;

	addlog("Creating a new session for \"" .$url ."\" (" .$type .", " .$mode .")");

	// Check url
	if (!isurlvalid($url, $type))
		return "";

	// Check that the max number of session is not reached yet
	$nbencprocess = exec("find ../ram/ -name segmenter.pid | wc | awk '{ print $1 }'");
	if ($nbencprocess >= $maxencodingprocesses)
	{
		addlog("Error: Cannot create sesssion, too much sessions already encoding");
		return "";
	}

	// Get a free session
	$i=0;
	for ($i=0; $i<1000; $i++)
	{
		$session = "session" .$i;
		if (!file_exists('../ram/' .$session))
			break;
	}

	if ($i == 1000)
	{
		addlog("Error: Cannot find a new session name");
		return "";
	}

	// Default
	$qparams = $quality['3g'];

	// Get parameters
	foreach ($quality as $qn => $qp)
	{
		if ($qn == $mode)
		{
			$qparams = $qp;
			break;
		}
	}

	// Create session
	addlog("Creating new session dir ram/" .$session);
	exec('mkdir ../ram/' .$session);
	// Extract $channame if needed
        switch ($type)
        {
                case 'tv':
                        $urlarray = explode("/", $url);
                        $channum = $urlarray[count($urlarray)-1];
                        $channame = vdrgetchanname($channum);
                        break;
                case 'rec':
                        list($channame, $title, $desc, $recorded) = vdrgetrecinfo($url);
                        break;
                default:
                        $channame = "";
                        break;
        }

	// Create logo
        if ($type == 'vid')
                generatelogo($type, $url, '../ram/' .$session .'/thumb.png');
        else
                generatelogo($type, $channame, '../ram/' .$session .'/thumb.png');

	// FFMPEG debug
	if ($ffmpegdebug)
		$ffdbg = $ffmpegdebugfile;
	else
		$ffdbg = "";

	// Start encoding
	$url = str_replace("\\'", "'", $url);
	switch ($type)
	{
		case 'tv':
			$cmd = "./istream.sh \"" .$url ."\" " .$qparams ." " .$httppath ." 2 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \"" .$ffdbg ."\" \"\" >/dev/null 2>&1 &";
			break;
		case 'rec':
			$cmd = "./istream.sh - " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \"" .$ffdbg ."\" \"" .$url ."/0* \" >/dev/null 2>&1 &";
			break;
		case 'vid':
			$cmd = "./istream.sh \"" .$url ."\" " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \"" .$ffdbg ."\" \"\" >/dev/null 2>&1 &";
                        break;
		default:
			$cmd = "";
	}

	addlog("Sending encoding command: " .$cmd);

	$cmd = str_replace('%', '%%', $cmd);
	exec ($cmd);

	// Give the time to the scrip to create pids
	exec ("sleep 2");

	// Write streaminfo
	writeinfostream($session, $type, $mode, $url, $channame);

	return $session;
}

function sessiondelete($session)
{
	$ret = array();
	
	if ($session == 'all')
	{
		$dir_handle = @opendir('../ram/');
		if ($dir_handle)
		{
			while ($session = readdir($dir_handle))
			{
				if($session == "." || $session == ".." || $session == 'lost+found')
					continue;

				if (!is_dir('../ram/' .$session))
					continue;

				// Get info
				list($type, $mode, $url, $channame) = readinfostream($session);

				if ($type != "none")
					sessiondeletesingle($session);
			}
		}
	}
	else
		sessiondeletesingle($session);

	$ret['status'] = "ok";
	$ret['message'] = "Successfully stopped broadcast";

	return $ret;

}

function sessiongetinfo($session)
{
	$info = array();

	addlog("Getting info for session " .$session);

	// Get some info
	list($type, $mode, $url, $channame) = readinfostream($session);
	
	// Fill common info
	$info['session'] = $session;
	$info['type'] = $type;
	$info['mode'] = $mode;

	// Get info
	$getid3 = new getID3;
	$fileinfo = $getid3->analyze('../ram/' .$session .'/thumb.png');
	$info['thumbwidth'] = $fileinfo['video']['resolution_x'];
	$info['thumbheight'] = $fileinfo['video']['resolution_y']; 

	// Type info
	switch ($type)
	{
		case 'tv':
			$info['name'] = $channame;
			$channum = vdrgetchannum($channame);
			list($date, $info['now_time'], $info['now_title'], $info['now_desc']) = vdrgetepgat($channum, "now");
			list($date, $info['next_time'], $info['next_title'], $info['next_desc']) = vdrgetepgat($channum, "next");
			break;
		case 'rec':
			$info['channel'] = $channame;
			list($channame, $info['name'], $info['desc'], $info['recorded']) = vdrgetrecinfo($url);
			break;
		case 'vid':
			$infovid = mediagetinfostream($url);
			$info['name'] = basename($url);
			$info['desc'] = $infovid['desc'];
			$info['duration'] = $infovid['duration'];
			$info['format'] = $infovid['format'];
			$info['video'] = $infovid['video'];
			$info['audio'] = $infovid['audio'];
			$info['resolution'] = $infovid['resolution'];
			break;
	}

	return $info;
}


function sessiondeletesingle($session)
{
	addlog("Deleting session " .$session);

	$ram = "../ram/" .$session ."/";
	$cmd = "";

	// First kill ffmpeg
	if (is_pid_running($ram ."ffmpeg.pid"))
		$cmd .= " kill `cat " .$ram ."ffmpeg.pid`; rm " .$ram ."ffmpeg.pid; ";

	// Then kill segmenter
	if (is_pid_running($ram ."segmenter.pid"))
		$cmd .= " kill `cat " .$ram ."segmenter.pid`; rm " .$ram ."segmenter.pid; ";

	addlog("Sending session kill command: " .$cmd);

	$cmd .= "rm -rf " .$ram;
	exec ($cmd);
}

function getstreamingstatus($session)
{
	global $maxencodingprocesses, $httppath;

	$status = array();

	$path = '../ram/' .$session;

	// Check that session exists
	if (!count(glob($path)))
	{
		$status['status'] = "error";

	        $nbencprocess = exec("find ../ram/ -name segmenter.pid | wc | awk '{ print $1 }'");
	        if ($nbencprocess >= $maxencodingprocesses)
			$status['message'] = "<b>Error: too much sessions</b>";
	        else
			$status['message'] = "<b>Error: could not create session folder</b>";
	}
	else
	{
		// Get stream info
		list($type, $mode, $url, $channame) = readinfostream($session);

		if (count(glob($path . '/*.ts')) < 2)
		{
			if (!is_pid_running($path .'/ffmpeg.pid') || !is_pid_running($path .'/segmenter.pid'))
			{
				$status['status'] = "error";
				$status['message'] = "<b>Error: streaming could not start correclty</b>";
			}
			else
			{
				$status['status'] = "wait";
				switch ($type)
				{
					case 'tv':
						$status['message'] = "<b>Live: requesting " .$channame ."</b>";
						break;
					case 'rec':
						$status['message'] = "<b>Rec: requesting " .$channame ."</b>";
						break;
					case 'vid':
						$status['message'] = "<b>Vid: requesting " .$url ."</b>";
						break;
				}
			}

			$status['message'] .= "<br>";

			$status['message'] .= "<br>  * FFmpeg: ";
			if (is_pid_running($path .'/ffmpeg.pid'))
				$status['message'] .= "<i>running</i>";
			else
				$status['message'] .= "<i>stopped</i>";
			$status['message'] .= "<br>  * Segmenter: ";
			if (is_pid_running($path .'/segmenter.pid'))
			{
				$status['message'] .= "<i>running</i> (";
				$status['message'] .= count(glob($path . '/*.ts')) ."/2)</i>";
			}
			else
				$status['message'] .= "<i>stopped</i>";
		}
		else
		{
			$status['status'] = "ready";

			$status['message'] = "<b>Broadcast ready</b><br>";

			$status['message'] .= "<br>  * Quality: <i>" .$mode ."</i>";
			$status['message'] .= "<br>  * Status: ";
			if (is_pid_running($path .'/segmenter.pid'))
				$status['message'] .= "<i>encoding...</i>";
			else
				$status['message'] .= "<i>fully encoded</i>";

			$status['url'] = $httppath ."ram/" .$session ."/stream.m3u8";

		}
	}

	return $status;
}

function sessiongetstatus($session, $prevmsg)
{
	$time = time();

	// Check if we need to timeout on the sesssion creation */
	$checkstart = preg_match("/requesting/", $prevmsg);
	
	while((time() - $time) < 29)
	{

		// Get current status
		$status = getstreamingstatus($session);
	
		// Alway return ready
		if ($status['status'] == "ready")
		{
			addlog("Returning status: " .$status['message']);
			return $status;
		}

		// Status change
		if ($status['message'] != $prevmsg)
		{
			addlog("Returning status: " .$status['message']);
			return $status;
		}

		// Check session creation timeout
		if ($checkstart && ((time() - $time) >= 15))
		{
			$status['status'] = "error";
			$status['message'] = "Error: session could not start";

                        
			$status['message'] .= "<br>";
                        
			$status['message'] .= "<br>  * FFmpeg: ";

			if (is_pid_running('../ram/' .$session .'/ffmpeg.pid'))
				$status['message'] .= "<i>running</i>";
			else
				$status['message'] .= "<i>stopped</i>";
			$status['message'] .= "<br>  * Segmenter: ";
                        
			if (is_pid_running('../ram/' .$session .'/segmenter.pid'))
			{
				$status['message'] .= "<i>running</i> (";
				$status['message'] .= count(glob('../ram/' .$session .'/*.ts')) ."/2)</i>";
			}
			else
				$status['message'] .= "<i>stopped</i>";

			addlog("Returning status: " .$status['message']);
			return $status;
		}

		usleep(10000);
	}

	/* Time out */
	$status['status'] = "wait";
	$status['message'] = $prevmsg;

	addlog("Returning status: " .$status['message']);
	return $status;
}

function sessiongetlist()
{
	$sessions = array();

	addlog("Listing sessions");

	$dir_handle = @opendir('../ram/');
	if ($dir_handle)
	{
		while ($session = readdir($dir_handle))
		{
			if($session == "." || $session == ".." || $session == 'lost+found')
				continue;

			if (!is_dir('../ram/' .$session))
				continue;

			// Get info
			list($type, $mode, $url, $channame) = readinfostream($session);
			if ($type == "none")
				continue;

			// Get status
			$status = getstreamingstatus($session);

			$newsession = array();
			$newsession['session'] = substr($session, strlen("session"));
			$newsession['type'] = $type;
			if ($type == "vid")
				$newsession['name'] = basename($url);
			else
				$newsession['name'] = $channame;

			if ($status['status'] == "error")
				$newsession['name'] = "Error: " .$newsession['name'];

			// Check if encoding
			if (is_pid_running('../ram/' .$session .'/segmenter.pid') && ($status['status'] != "error"))
				$newsession['encoding'] = 1;
			else
				$newsession['encoding'] = 0;

			$sessions[] = $newsession;
		}
	}

	return $sessions;
}

function streammusic($path, $file)
{
	global $httppath;

	addlog("Streaming music from path \"" .$path ."\"");

	if (!isurlvalid($path, "media"))
		return array();

	$files = array();

	// Create all symlinks
	exec('mkdir ../playlist');
        exec('rm ../playlist/*');
        exec('ln -s ' .addcslashes(quotemeta($path), " &'") .'/* ../playlist');

	// Generate files

	// Get listing
	$filelisting = filesgetlisting($path);
	
	$addfiles = 0;
	
	foreach ($filelisting as $f)
	{
		if ($f['type'] != 'audio')
			continue;

		if ($f['name'] == $file)
			$addfiles = 1;

		if ($addfiles)
		{
			$newfile = array();
			$newfile['file'] = $httppath ."playlist/" . $f['name'];
			$files[] = $newfile;
		}
	}

	return $files;
}

?>
