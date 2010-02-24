<?php 

//set headers to mp3 

$mediapath = $_GET['mediapath'];
$subdir = $_GET['subdir'];
$file = $_GET['file'];

header("Content-Transfer-Encoding: binary");  
header("Content-Type: audio/mp3");
header('Content-length: ' . filesize($mediapath .$subdir .$file)); 
header('Content-Disposition: attachment; filename="streaming.mp3"'); 
header('X-Pad: avoid browser bug'); 
Header('Cache-Control: no-cache');

readfile($mediapath .$subdir .$file); 

?> 
