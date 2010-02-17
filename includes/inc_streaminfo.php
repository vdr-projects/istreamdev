<?php

/*
  Types:
	0 : Not running
	1 : VDR live
	2 : VDR recording
	3 : Media
*/

function writeinfostream($type=0, $name="", $title="", $desc="", $mode="", $category="", $url="")
{
	$infofile = fopen("ram/streaminfo", 'w');

	fwrite($infofile, "type=" .$type ."\n");
	fwrite($infofile, "name=" .$name ."\n");
	fwrite($infofile, "title=" .$title ."\n");
	fwrite($infofile, "desc=" .$desc ."\n");
	fwrite($infofile, "mode=" .$mode ."\n");
	fwrite($infofile, "category=" .$category ."\n");
	fwrite($infofile, "url=" .$url ."\n");

	fclose($infofile);
}


function readinfostream()
{
	$infofile = fopen("ram/streaminfo", 'r');	
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
	}
	
	fclose($infofile);

	return array($type, $name, $title, $desc, $mode, $category, $url);
}

function infostreamexist()
{
	return  file_exists("ram/streaminfo");
}

?>
