<?php
	global $user, $pass, $vdrchannels, $vdrstreamdev, $quality, $httppath, $svdrpip, $svdrpport;

	// Http configuration
	$user = 'istreamdev';		// Login
	$pass = 'iguest';		// Password
	$httppath = '/istreamdev/';	// Path to the index.php file

	// VDR configuration
	$vdrchannels='/etc/vdr/channels.conf';	// VDR channel list
	$svdrpport=2001;					// SVDRP port
	$svdrpip='127.0.0.1';					// SVDRP ip
	$vdrstreamdev='http://127.0.0.1:3000/TS/';		// VDR streamdev URL
	$vdrrecpath='/video/';

	// Encoding
	//			Name		Video	Audio	Audio channels	Resolution
	$quality = array(	'Edge'	=>	'128k	64k	1		240x160',
				'3g'	=>	'350k	64k	1		408x272',
				'Wifi'	=>	'512k	128k	2		480x320'
			);

	// Version
	$isdversion = "0.3.5";
?>
