<?php

function sessioncreate($type, $name, $title, $desc, $qname, $qparams, $category, $url, $mediapath, $subdir)
{
	global $httppath, $ffmpegpath, $segmenterpath;

	// TODO: Get a session
	$session = session0;

	// Create session
	exec('mkdir ram/' .$session);

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
	exec($cmd);

	// Write streaminfo
	writeinfostream($session, $type, $name, $title, $desc, $qname, $category, $url, $mediapath, $subdir);

	return $session;
}

function sessiondelete($session)
{
	$ram = "ram/" .$session ."/";
	$subcmd = "";  

	// Get segmenter PID if any
	if (file_exists($ram ."streamsegmenterpid"))
	{
		$pidfile = fopen($ram ."streamsegmenterpid", 'r');
		if ($pidfile)
		{
			$pid = fgets($pidfile);
			$pid = substr($pid, 0, -1);
			$subcmd = "kill " .$pid ." ; ";
			fclose($pidfile);
		}
	}

	$cmd= $subcmd ."rm -rf " .$ram;
	exec ($cmd);
?>
