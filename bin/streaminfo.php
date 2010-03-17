<?php

/*
  Types:
	0 : Not running
	tv : VDR live
	rec : VDR recording
	vid : Media
*/

function writeinfostream($session, $type, $mode, $url, $channame)
{
	$ram = "../ram/" .$session ."/";

	$infofile = fopen($ram ."streaminfo", 'w');

	fwrite($infofile, "type=" .$type ."\n");
	fwrite($infofile, "mode=" .$mode ."\n");
	fwrite($infofile, "url=" .$url ."\n");
	fwrite($infofile, "channame=" .$channame ."\n");

	fclose($infofile);
}


function readinfostream($session)
{
	$ram = "../ram/" .$session ."/";

	if (!file_exists($ram ."streaminfo"))
		return array(0);

	$infofile = fopen($ram ."streaminfo", 'r');	
	if (!$infofile)
		return array(0);

	while ($line = fgets($infofile, 1024))
        {
		if (!strncmp($line, "type=", strlen("type=")))
			$type = substr($line, strlen("type="), -1);
		else if (!strncmp($line, "mode=", strlen("mode=")))
			$mode = substr($line, strlen("mode="), -1);
		else if (!strncmp($line, "url=", strlen("url=")))
			$url = substr($line, strlen("url="), -1);
		else if (!strncmp($line, "channame=", strlen("channame=")))
			$channame = substr($line, strlen("channame="), -1);
	}
	
	fclose($infofile);

	return array($type, $mode, $url, $channame);
}

?>
