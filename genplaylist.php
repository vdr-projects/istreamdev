<?php
	
	if (file_exists('config.php'))
		include ('config.php');
	else	
		include ('config_default.php');
	include ('includes/inc_files.php');
	header('Content-Type: text/xml'); 
	echo "<?xml version=\"1.0\"?>\n";
	echo "<status>\n";

	$path = $_REQUEST['path'];
	$name = $_REQUEST['name'];

	exec('rm playlist/*');
	exec('echo > playlist/dummy.txt');
	exec('ln -s ' .addcslashes(quotemeta($path), " &'") .'* playlist');

	echo "<m3u>ok</m3u>\n";

	echo "</status>\n";

?>
