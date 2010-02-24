<?php 
//set headers to mp3 
$dir = $_GET['dir'];
$file = $_GET['file'];
header("Content-Transfer-Encoding: binary");  
header("Content-Type: audio/mp3");
header('Content-length: ' . filesize($dir.$file)); 
header('Content-Disposition: attachment; filename="track.mp3"'); 
header('X-Pad: avoid browser bug'); 
Header('Cache-Control: no-cache');

readfile($dir.$file); 

$URL="index.php?action=media&dir=$dir"; 
header ("Location: $URL");
?> 
