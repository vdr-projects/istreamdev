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
	exec("rm ram/stream-tb.png");
	exec($ffmpegpath ." -y -i \"" .$stream ."\" -an -ss 00:00:05.00 -r 1 -vframes 1 -s 128x72 -f mjpeg ram/stream-tb.png");
	
	return array($title, $info);
}


