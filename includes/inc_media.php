<?php

$mediapath = $_REQUEST['mediapath'];
$subdir = $_REQUEST['subdir'];

/* Add last slash to dirs */
if ($mediapath[strlen($mediapath)-1] != '/')
        $mediapath = $mediapath .'/';
if ($subdir[strlen($subdir)-1] != '/')
        $subdir = $subdir .'/';

// Use the right media type 
if (mediadirhasaudio($mediapath .$subdir))
	include ('includes/inc_audio.php');
else
	include ('includes/inc_video.php');
?>
