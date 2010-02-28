<?php

function sessioncreate($type, $name, $title, $desc, $qname, $qparams, $category, $url, $mediapath, $subdir)
{
	global $httppath, $ffmpegpath, $segmenterpath;

	// Get a free session
	$i=0;
	for ($i=0; $i<1000; $i++)
	{
		$session = "session" .$i;
		if (!file_exists('ram/' .$session))
			break;
	}

	// Create session
	exec('mkdir ram/' .$session);
	$url = str_replace("\\'", "'", $url);
	switch ($type)
	{
		case 1:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh \\\"" .$url ."\\\" " .$qparams ." " .$httppath ." 2 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
			break;
		case 2:
			$cmd = "export SHELL=\"/bin/sh\";printf \"cat \\\"" .$url ."\\\"/0* | ./istream.sh - " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
			break;
		case 3:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh \\\"" .$url ."\\\" " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." " .$session ." \" | at now";
                        break;
		default:
			$cmd = "";
	}
	
	$cmd = str_replace('%', '%%', $cmd);
	print $cmd;
	exec ($cmd);

	// Write streaminfo
	writeinfostream($session, $type, $name, $title, $desc, $qname, $category, $url, $mediapath, $subdir);

	// Create logo
	if ($type == 3)
		generatelogo($type, $url, 'ram/' .$session .'/logo.png');
	else
		generatelogo($type, $name, 'ram/' .$session .'/logo.png');

	return $session;
}

function sessiondelete($session)
{
	if ($session == 'all')
	{
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

				if ($type)
					sessiondeletesingle($session);
			}
		}
	}
	else
		return sessiondeletesingle($session);
}

function sessiondeletesingle($session)
{
	$ram = "ram/" .$session ."/";

	// Get segmenter PID if any
	if (file_exists($ram ."segmenter.pid"))
		$cmd = "kill `cat " .$ram ."segmenter.pid`; rm " .$ram ."segmenter.pid; ";

	$cmd .= "rm -rf " .$ram;
	exec ($cmd);
}

?>
