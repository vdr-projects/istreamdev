<?php
	// Http configuration
	$user = 'istreamdev';		// Login
	$pass = 'iguest';		// Password
	$httppath = '/istreamdev/';	// Path to the index.php file

	// VDR configuration

	$vdrenabled=1;	// enable/disable VDR features
	$vdrchannels='/etc/vdr/channels.conf';	// VDR channel list
	$svdrpport=2001;					// SVDRP port
	$svdrpip='127.0.0.1';					// SVDRP ip
	$vdrstreamdev='http://127.0.0.1:3000/TS/';		// VDR streamdev URL
	$vdrrecpath='/video/';	//VDR recording directory

	// Media configuration
	$mediainfopath='/usr/bin/mediainfo';
	$videotypes='avi mkv ts mov mp4 wmv flv mpg mpeg mpeg2 mpv ';	// Supported video extensions (must finish with a space)
	//			Source name	Source path
	$mediasources=array (	'Video'	=>	'/mnt/media/movies',
				'Audio'	=>	'/mnt/media/music');

	// Encoding
	//			Name		Video	Audio	Audio channels	Resolution
	$quality=array (	'Edge'	=>	'128k	64k	1		240x160',
				'3g'	=>	'350k	64k	1		408x272',
				'Wifi'	=>	'512k	128k	2		480x320');

	// Misc
	$ffmpegpath = '/usr/bin/ffmpeg';	//path to ffmpeg binary
	$segmenterpath = '/usr/bin/segmenter';	//path to segmenter binary

	// Version
	$isdversion = "0.3.7-dev";
?>
