<?php

function mediagetinfostream($stream)
{
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

        return array($title, $info);
}

function mediagentb($stream, $dest)
{
	global $ffmpegpath;

	// Get info
	$getid3 = new getID3;
	$fileinfo = $getid3->analyze($stream);

	exec("rm " .$dest);
	$path = dirname($stream);

	if (file_exists(substr($stream, 0, -4) .".tbn"))
		$file = substr($stream, 0, -4) .".tbn";
	else if (file_exists($path ."/poster.jpg"))
		$file = $path ."/poster.jpg";
	else if (file_exists($path ."/folder.jpg"))
		$file = $path ."/folder.jpg";
	else
		$file = "";

	$resx = 180;
	$resy = 100;

	if ($file)
	{
		$getid3 = new getID3;
		$fileinfo = $getid3->analyze($file);
	}
	
	if ($fileinfo['video']['resolution_y'] && $fileinfo['video']['resolution_x'])
	{
		if ($fileinfo['video']['resolution_y'] < $fileinfo['video']['resolution_x'])
		{
			$resx = 180;
			$resy = round(($fileinfo['video']['resolution_y'] * 180) / $fileinfo['video']['resolution_x']);
		}
		else
		{
			$resx = round (($fileinfo['video']['resolution_x'] * 100) / $fileinfo['video']['resolution_y']);
			$resy = 100;
		}
	}

	if ($file)
		exec("cp \"" .$file ."\" ram/stream-tb-tmp.jpg;  " .$ffmpegpath ." -y -i ram/stream-tb-tmp.jpg -s " .$resx ."x" .$resy ." " .$dest ." ; rm ram/stream-tb-tmp.jpg");
	else
	        exec($ffmpegpath ." -y -i \"" .$stream ."\" -an -ss 00:00:05.00 -r 1 -vframes 1 -s " .$resx ."x" .$resy ." -f mjpeg " .$dest);

	if (!file_exists($dest))
		exec('cp logos/nologoMEDIA.png ' .$dest);
}

function mediagetwidth($file)
{

	$getid3 = new getID3;
	$fileinfo = $getid3->analyze($file);

	return $fileinfo['video']['resolution_x'];
}

function filegettype($file)
{
	global $videotypes, $audiotypes;

	// Get file extension
	$fileext = end(explode(".", $file));
	$file = str_replace("\\'", "'", $file);

	if (is_dir($file))
	{
		if ($fileext == "rec")
			return "rec";
		else
			return 'folder';
	}
	else if (preg_match("$/$", $fileext))
		return 'none';
	else if (preg_match("/" .$fileext ." /", $videotypes))
		return 'video';
	else if (preg_match("/" .$fileext ." /", $audiotypes))
		return 'audio';
	else
		return 'unknown';
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

function generatelogo($type, $name, $dest)
{
        switch ($type)
        {
                case 'tv':
                        $channoslash = preg_replace("$/$", " ", $name);
                        $logopath = "../logos/" .$channoslash .".png";
                        if (!file_exists($logopath))
                                $logopath = "../logos/nologoTV.png";
                        exec("cp \"" .$logopath ."\" " .$dest);
                        break;
                case 'rec':
                        $channoslash = preg_replace("$/$", " ", $name);
                        $logopath = "../logos/" .$channoslash .".png";
                        if (!file_exists($logopath))
                                $logopath = "../logos/nologoREC.png";
                        exec("cp \"" .$logopath ."\" " .$dest);
                        break;
                case 'vid':
                        // Generate TB
                        mediagentb($name, $dest);
                        break;
        }
}

function filesgetlisting($dir)
{
	$listing = array();

	$dir_handle = @opendir($dir);
	if (!$dir_handle)
		return $listing;

	while ($medianame = readdir($dir_handle))
	{
		if($medianame == "." || $medianame == ".." || $medianame == 'lost+found')
			continue;

		$medianame_array[] = $medianame;
	}

	if ($medianame_array[0] == NULL)
		return $listing;

	// Alphabetical sorting
	sort($medianame_array);
	
	foreach($medianame_array as $value)
	{	
		$newentry = array();

		$newentry['name'] = $value;
		$newentry['path'] = $dir ."/" .$value;

		$type = filegettype($dir ."/" .$value);

		switch ($type)
		{
			case 'audio':
			case 'video':
			case 'rec':
			case 'folder':
				$newentry['type'] = $type;
				break;
			default:
				continue;

		}

		// Add new entry
		$listing[] = $newentry;
	}

	return $listing;
}

?>
