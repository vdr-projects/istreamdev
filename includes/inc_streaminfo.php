<?php

/*
  Types:
	0 : Not running
	1 : VDR live
	2 : VDR recording
	3 : Media
*/

function writeinfostream($session, $type=0, $name="", $title="", $desc="", $mode="", $category="", $url="", $mediapath="", $subdir="")
{
	$ram = "ram/" .$session ."/";

	$infofile = fopen($ram ."streaminfo", 'w');

	fwrite($infofile, "type=" .$type ."\n");
	fwrite($infofile, "name=" .$name ."\n");
	fwrite($infofile, "title=" .$title ."\n");
	fwrite($infofile, "desc=" .$desc ."\n");
	fwrite($infofile, "mode=" .$mode ."\n");
	fwrite($infofile, "category=" .$category ."\n");
	fwrite($infofile, "url=" .$url ."\n");
	fwrite($infofile, "mediapath=" .$mediapath ."\n");
	fwrite($infofile, "subdir=" .$subdir ."\n");

	fclose($infofile);
}


function readinfostream($session)
{
	$ram = "ram/" .$session ."/";

	if (!file_exists($session ."streaminfo"))
		return array(0, "", "", "", "");

	$infofile = fopen($session ."streaminfo", 'r');	
	if (!$infofile)
		return array(0, "", "", "", "");

	while ($line = fgets($infofile, 1024))
        {
		if (!strncmp($line, "type=", strlen("type=")))
			$type = substr($line, strlen("type="), -1);
		else if (!strncmp($line, "name=", strlen("name=")))
			$name = substr($line, strlen("name="), -1);
		else if (!strncmp($line, "title=", strlen("title=")))
			$title = substr($line, strlen("title="), -1);
		else if (!strncmp($line, "desc=", strlen("desc=")))
			$desc = substr($line, strlen("desc="), -1);
		else if (!strncmp($line, "mode=", strlen("mode=")))
			$mode = substr($line, strlen("mode="), -1);
		else if (!strncmp($line, "category=", strlen("category=")))
                        $category = substr($line, strlen("category="), -1);
		else if (!strncmp($line, "url=", strlen("url=")))
			$url = substr($line, strlen("url="), -1);
		else if (!strncmp($line, "mediapath=", strlen("mediapath=")))
			$mediapath = substr($line, strlen("mediapath="), -1);
		else if (!strncmp($line, "subdir=", strlen("subdir=")))
			$subdir = substr($line, strlen("subdir="), -1);
	}
	
	fclose($infofile);

	return array($type, $name, $title, $desc, $mode, $category, $url, $mediapath, $subdir);
}

?>
