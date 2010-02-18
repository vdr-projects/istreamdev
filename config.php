<?php
	global $user, $pass, $vdrchannels, $vdrstreamdev, $quality, $httppath;
	global $svdrpip, $svdrpport, $ffmpegpath, $videotypes, $audiotypes, $mediainfopath;

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

	// Media configuration
	$mediainfopath='/usr/bin/mediainfo';
	$mediapath='/mnt/Storage/';
	$videotypes='avi mkv ts mov mp4 wmv flv mpg mpeg mpeg2 mpv';
	$audiotypes='mp3 wav aac flac';

	// Encoding
	//			Name		Video	Audio	Audio channels	Resolution
	$quality = array(	'Edge'	=>	'128k	64k	1		240x160',
				'3g'	=>	'350k	64k	1		408x272',
				'Wifi'	=>	'512k	128k	2		480x320'
			);

	// Misc
	$ffmpegpath = '/usr/bin/ffmpeg';

	// Version
	$isdversion = "0.3.6-alpha";
?>
