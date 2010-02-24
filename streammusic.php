<?php 
//set headers to mp3 
$file = $_GET['file'];
header("Content-Transfer-Encoding: binary");  
header("Content-Type: audio/mp3");
header('Content-length: ' . filesize($file)); 
header('Content-Disposition: attachment; filename="track.mp3"'); 
header('X-Pad: avoid browser bug'); 
Header('Cache-Control: no-cache');

readfile($file); 
die(); 

?> 
