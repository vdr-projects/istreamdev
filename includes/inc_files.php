<?php

$audiotypes='mp3 aac wav ';

function mediagetinfostream($stream = "")
{

	global $mediainfopath, $ffmpegpath;

	// Get info
	exec($mediainfopath ." \"" .$stream ."\"", $mediainfo);

	$info = "";
	$title = "";

	// Parse info
	$count = count($mediainfo);
	for ($i = 0; $i < $count; $i++)
	{
		if (!strncmp($mediainfo[$i], "Video", strlen("Video")) || !strncmp($mediainfo[$i], "Audio", strlen("Audio")))
			break;

		if (!strncmp($mediainfo[$i], "Format", strlen("Format")))
			$title = substr(strstr($mediainfo[$i], ": "), 2);
		else if (!strncmp($mediainfo[$i], "Format/Info", strlen("Format/Info")))
			$title = substr(strstr($mediainfo[$i], ": "), 2);
		else if (!strncmp($mediainfo[$i], "Duration", strlen("Duration")))
			$info = substr(strstr($mediainfo[$i], ": "), 2);
	}

	// Extract a thumbnail
	exec("rm ram/stream-tb.*");

	$path = dirname($stream);

	if (file_exists(substr($stream, 0, -4) .".tbn"))
		exec("cp \"" .substr($stream, 0, -4) .".tbn\" ram/stream-tb-tmp.jpg;  " .$ffmpegpath ." -y -i ram/stream-tb-tmp.jpg -s 128x180 ram/stream-tb.jpg");
	else  if (file_exists($path ."/poster.jpg"))
		exec($ffmpegpath ." -y -i \"" .$path ."/poster.jpg\" -s 128x180 ram/stream-tb.jpg");
	else  if (file_exists($path ."/folder.jpg"))
	        exec($ffmpegpath ." -y -i \"" .$path ."/folder.jpg\" -s 128x180 ram/stream-tb.jpg");
	else
	        exec($ffmpegpath ." -y -i \"" .$stream ."\" -an -ss 00:00:05.00 -r 1 -vframes 1 -s 180x100 -f mjpeg ram/stream-tb.png");
	
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
