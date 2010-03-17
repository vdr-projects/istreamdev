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
	exec ('echo "' .$cmd .'" > /tmp/a');
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
			list($channame, $title, $desc) = vdrgetrecinfo($url);
			break;
		default:
			$channame = "";
			break;
	}

	// Write streaminfo
	writeinfostream($session, $type, $mode, $url, $channame);

	// Create logo
	if ($type == 'vid')
		generatelogo($type, $url, '../ram/' .$session .'/logo.png');
	else
		generatelogo($type, $channame, '../ram/' .$session .'/logo.png');

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

				if ($type)
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

	// Type info
	switch ($type)
	{
		case 'tv':
			$info['name'] = $channame;
			$info['thumbwidth'] = 80;
			$info['thumbheight'] = 80;
			$channum = vdrgetchannum($channame);
			list($info['now_time'], $info['now_title'], $info['now_desc']) = vdrgetchanepg($channum, 1);
			list($info['next_time'], $info['next_title'], $info['next_desc']) = vdrgetchanepg($channum, 0);

			break;
		case 'rec':
			$info['channel'] = $channame;
			$info['thumbwidth'] = 80;
			$info['thumbheight'] = 80;
			list($channame, $info['name'], $info['desc']) = vdrgetrecinfo($url);
			break;
		case 'vid':
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
			$status['message'] = $mode ." Broadcast ready";

			$status['url'] = $httppath ."ram/" .$session ."/stream.m3u8";

		}
	}

	return $status;
}


?>
