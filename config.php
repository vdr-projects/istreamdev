<?php
	// Http configuration
	$user = 'noos';		// Login
	$pass = 'noos';		// Password
	$httppath = '/';	// Path to the index.php file

	// VDR configuration
	$vdrenabled=1;
	$vdrchannels='/home/BoB/vdr/conf/channels.conf';	// VDR channel list
	$svdrpport=2001;					// SVDRP port
	$svdrpip='127.0.0.1';					// SVDRP ip
	$vdrstreamdev='http://127.0.0.1:3000/TS/';		// VDR streamdev URL
	$vdrrecpath='/home/BoB/vdr/video/';

	// Media configuration
	$videotypes='mkv ts mov mp4 wmv avi ';			// Supported video extensions
	//                      1:vid 2:aud	Source name     Source path
	$mediasource=array();
	$mediasources[]=array ( 1,              'Foot',		'/home/storage/Foot');
	$mediasources[]=array (	1,		'Series',	'/home/storage-2/Series');
	$mediasources[]=array ( 2,              'Storage-MP3',	'/home/storage/Download/MP3');
	$mediasources[]=array ( 2,              'Site-MP3',	'/home/www/mp3/');

	// Encoding
	//			Name		Video	Audio	Audio channels	Resolution
	$quality = array(	'Edge'	=>	'128k	64k	1		240x160',
				'3g'	=>	'350k	64k	1		408x272',
				'Wifi'	=>	'496k	96k	2		480x320'
			);
	$maxencodingprocesses=10;				// Max simultaneous encoding processes

	// Misc
	$ffmpegpath = '/usr/local/bin/ffmpeg';
	$segmenterpath = '/usr/local/bin/segmenter';  //path to segmenter binary

	// Version
	$isdversion = "0.3.6-alpha";
?>
