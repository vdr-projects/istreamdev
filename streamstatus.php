<?php

if (file_exists('config.php'))
	include ('config.php');
else
	include ('config_default.php');

global $maxencodingprocesses;

header('Content-Type: text/xml'); 
echo "<?xml version=\"1.0\"?>\n";

echo "<status>\n";

// First check that we are allowed to create a new encoding process
$nbencprocess = exec("find -name segmenter.pid | wc | awk '{ print $1 }'");
if ($nbencprocess > $maxencodingprocesses)
	echo "<streamstatus>error</streamstatus><message>Error: maximun number of sessions reached</message>\n";
else
{
	$session = $_REQUEST['session'];

	$cnt = 0;
	while ( ( count(glob('ram/' .$session . '/*.ts')) < 2 ) && ( $cnt < 25 ) )
	{
		// wait for stream available
		sleep(1);
		$cnt++;
	}

	if ( count(glob('ram/' .$session '/*.ts')) < 2 )
		echo "<streamstatus>error</streamstatus><message>Error: encoding did not start correclty</message>\n";
	else
		echo "<streamstatus>ok</streamstatus>\n";
}

echo "</status>\n";

?>
