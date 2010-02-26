<?php

$audiotypes='mp3 aac wav ';

function mediagetinfostream($stream = "")
{
	global $ffmpegpath;

	// Get info
	$getid3 = new getID3;
	$fileinfo = $getid3->analyze($stream);

	$title = "Media:";
	$info = "Duration: <i>" .sec2hms($fileinfo['playtime_seconds']) ."</i><br>";
	if ($fileinfo['fileformat'])
		$info .= "Format: <i>" .$fileinfo['fileformat'] ."</i><br>";
	if ($fileinfo['video']['codec'])
		$info .= "Video: <i>" .$fileinfo['video']['codec'] ."</i><br>";
	if ($fileinfo['audio']['codec'])
		$info .= "Audio: <i>" .$fileinfo['audio']['codec'] ."</i><br>";
	if ($fileinfo['video']['resolution_x'])
		$info .= "Resolution: <i>" .$fileinfo['video']['resolution_x'] ."x" .$fileinfo['video']['resolution_y'] ."</i><br>";

	// Extract a thumbnail
	exec("rm ram/stream-tb.*");
	$path = dirname($stream);

	// Get the right Y resolution
	if ($fileinfo['video']['resolution_y'] && $fileinfo['video']['resolution_x'])
		$resy = ($fileinfo['video']['resolution_y'] * 180) / $fileinfo['video']['resolution_x'];
	else
		$resy = 100;

	if (file_exists(substr($stream, 0, -4) .".tbn"))
		exec("cp \"" .substr($stream, 0, -4) .".tbn\" ram/stream-tb-tmp.jpg;  " .$ffmpegpath ." -y -i ram/stream-tb-tmp.jpg -s 128x180 ram/stream-tb.jpg");
	else  if (file_exists($path ."/poster.jpg"))
		exec($ffmpegpath ." -y -i \"" .$path ."/poster.jpg\" -s 128x180 ram/stream-tb.jpg");
	else  if (file_exists($path ."/folder.jpg"))
	        exec($ffmpegpath ." -y -i \"" .$path ."/folder.jpg\" -s 128x180 ram/stream-tb.jpg");
	else
	        exec($ffmpegpath ." -y -i \"" .$stream ."\" -an -ss 00:00:05.00 -r 1 -vframes 1 -s 180x" .$resy ." -f mjpeg ram/stream-tb.png");
	
	return array($title, $info);
}

function mediagettype($file)
{
	global $videotypes, $audiotypes;

	// Get file extension
	$fileext = end(explode(".", $file));

	if (is_dir($file))
		return 3;
	if (preg_match("/" .$fileext ." /", $videotypes))
		return 1;
	else if (preg_match("/" .$fileext ." /", $audiotypes))
		return 2;
	else
		return 0;
}

function mediadirhasaudio($dir)
{
	global $audiotypes;

	$audioextarray = explode(' ', $audiotypes);

	foreach ($audioextarray as $num => $audioext)
	{
        	if (glob($dir .'*.' .$audioext))
			return 1;
	}

	return 0;
}

function mediagetmusicinfo($file ="")
{
	// Get info
	$getid3 = new getID3;
	$fileinfo = $getid3->analyze($file);

	$name = $fileinfo['tags']['id3v2']['title'][0];
	if ($name == "")
	{
		$name = $fileinfo['tags']['id3v1']['title'][0];
		if ($name == "")
		{
			$name = $fileinfo['filename'];
			if ($name == "")
				$name = "unknown";
		}
	}

	if (!is_utf8($name))
		$name = utf8_encode($name);

	$duration = $fileinfo['playtime_string'];

	return array ($name, $duration);
}
