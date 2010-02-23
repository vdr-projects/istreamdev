<?php

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
	        exec("cp " .substr($stream, 0, -4) .".tbn ram/stream-tb.jpg");
	else  if (file_exists($path ."/poster.jpg"))
	        exec("cp " .$path ."/poster.jpg ram/stream-tb.jpg");
	else  if (file_exists($path ."/folder.jpg"))
	        exec("cp " .$path ."/folder.jpg ram/stream-tb.jpg");
	else
	        exec($ffmpegpath ." -y -i \"" .$stream ."\" -an -ss 00:00:05.00 -r 1 -vframes 1 -s 128x72 -f mjpeg ram/stream-tb.png");
	
	return array($title, $info);
}


