<?php
	header('Content-Type: text/xml'); 
	echo "<?xml version=\"1.0\"?>\n";
	echo "<status>\n";

	$path = $_REQUEST['path'];
	$name = $_REQUEST['name'];

        exec('rm playlist/*');
        exec('ln -s ' .addcslashes(quotemeta($path), " &") .'* playlist');

	$dir_handle = @opendir($path);
	if (!$dir_handle)
		echo "<m3u>error</m3u>";
	else
	{
		$found = 0;
		while ($medianame = readdir($dir_handle))
		{
			if (mediagettype($path .$name) == 2)
			{
				if ($medianame == $name)
					$found = 1;		
		
				if ($found)
				        $medianame_array[] = $medianame;
			}
		}

		if ($medianame_array[0])
		{
	
			// Alphabetical sorting
			sort($medianame_array);

			$plfile = fopen("playlist/playlist.m3u", 'w');
			if (!$plfile)
				echo "<m3u>error</m3u>";
			else
			{
				$count = count($medianame_array);
				for ($cnt=0; $cnt < $count; $cnt++)
        				fwrite($plfile, "playlist/" .$medianame_array[$cnt] ."\n");

			        fclose($plfile);
	
				echo "<m3u>ok</m3u>\n";
			}
		}
		else
			echo "<m3u>error</m3u>";
	}

	echo "</status>\n";

?>
