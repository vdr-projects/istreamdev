<?php
	header('Content-Type: text/xml'); 
	echo "<?xml version=\"1.0\"?>\n";
	echo "<status>\n";

	$cnt = 0;
	while ( ( count(glob('*.ts')) < 2 ) && ( $cnt < 25 ) )
	{
		// wait for stream available
		sleep(1);
		$cnt++;
	}

	if ( count(glob('*.ts')) < 2 )
		echo "<streamstatus>error</streamstatus>\n";
        else
		echo "<streamstatus>ok</streamstatus>\n";

	echo "</status>\n";

?>
