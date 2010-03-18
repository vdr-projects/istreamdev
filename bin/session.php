<?php

function sessioncreate($type, $url, $mode)
{
	global $httppath, $ffmpegpath, $segmenterpath, $quality;

	// Get a free session
	$i=0;
	for ($i=0; $i<1000; $i++)
	{
		$session = "session" .$i;
		if (!file_exists('../ram/' .$session))
			break;
	}

	// Default
	$qparams = $quality[0];

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
	exec('mkdir ../ram/' .$session);
	$url = str_replace("\\'", "'", $url);
	switch ($type)
	{
		case 'tv':
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh \\\"" .$url ."\\\" " .$qparams ." " .$httppath ." 2 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
			break;
		case 'rec':
			$cmd = "export SHELL=\"/bin/sh\";printf \"cat \\\"" .$url ."\\\"/0* | ./istream.sh - " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
			break;
		case 'vid':
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh \\\"" .$url ."\\\" " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
                        break;
		default:
			$cmd = "";
	}
	
	$cmd = str_replace('%', '%%', $cmd);
	exec ($cmd);
	
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

	// Write streaminfo
	writeinfostream($session, $type, $mode, $url, $channame);

	// Create logo
	if ($type == 'vid')
		generatelogo($type, $url, '../ram/' .$session .'/thumb.png');
	else
		generatelogo($type, $channame, '../ram/' .$session .'/thumb.png');

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
			list($info['now_time'], $info['now_title'], $info['now_desc']) = vdrgetchanepg($channum, 1);
			list($info['next_time'], $info['next_title'], $info['next_desc']) = vdrgetchanepg($channum, 0);
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
	$ram = "../ram/" .$session ."/";

	// Get segmenter PID if any
	if (file_exists($ram ."segmenter.pid"))
		$cmd = "/usr/local/bin/fw;kill `cat " .$ram ."segmenter.pid`; rm " .$ram ."segmenter.pid; ";

	$cmd .= "rm -rf " .$ram;
	exec ($cmd);
}

function sessiongetstatus($session)
{
	global $maxencodingprocesses, $httppath;

	$status = array();

	$path = '../ram/' .$session;

	// Check that session exists
	if (($session == "") || !count(glob($path)))
	{
		$status['status'] = "error";

	        $nbencprocess = exec("find ../ram/ -name segmenter.pid | wc | awk '{ print $1 }'");
	        if ($nbencprocess >= $maxencodingprocesses)
			$status['message'] = "Error: maximun number of sessions reached";
	        else
			$status['message'] = "Error: cannot create session";
	}
	else
	{
		// Get stream info
		list($type, $mode, $url, $channame) = readinfostream($session);

		if (count(glob($path . '/*.ts')) < 2)
		{
			$status['status'] = "wait";
			switch ($type)
			{
				case 'tv':
					$status['message'] = "Requesting live channel " .$channame;
					break;
				case 'rec':
					$status['message'] = "Requesting recording channel " .$channame;
					break;
				case 'vid':
					$status['message'] = "Requesting video file " .$url;
					break;
			}
		}
		else
		{
			$status['status'] = "ready";
			$status['message'] = "Broadcast ready (" .$mode .")";

			$status['url'] = $httppath ."ram/" .$session ."/stream.m3u8";

		}
	}

	return $status;
}

function sessiongetlist()
{
	$sessions = array();

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

			$newsession = array();
			$newsession['session'] = substr($session, strlen("session"));
			$newsession['type'] = $type;
			if ($type == "vid")
				$newsession['name'] = basename($url);
			else
				$newsession['name'] = $channame;

			// Check if encoding
			if (file_exists('../ram/' .$session .'/segmenter.pid'))
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
