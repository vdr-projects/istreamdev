<?php
	// Http configuration
	$user = 'istreamdev';		// Login
	$pass = 'iguest';		// Password
	$httppath = '/istreamdev/';	// Absolute path to the index.php file. 					//Don't put http://yourdomain !!

	// VDR configuration
	$vdrchannels='/etc/vdr/channels.conf';			// VDR channel list
	$svdrpport=2001;					// SVDRP port
	$svdrpip='127.0.0.1';					// SVDRP ip
	$vdrstreamdev='http://127.0.0.1:3000/TS/';		// VDR streamdev URL (set to "" to disable the VDR feature)
	$vdrrecpath='/video/';					//VDR recording directory

	// Media configuration
	$videotypes='avi mkv ts mov mp4 wmv flv mpg mpeg mpeg2 mpv ';	// Supported video extensions (must finish with a space)
	$audiotypes='mp3 aac wav ';					// Supported audio extensions
	$videosource = '/mnt/media/movies/';				// Video files directory
	$audiosource = '/mnt/media/music/';				// Audio files directory

	// Encoding (The name cannot be changed)
	//			Name		Video	Audio	Audio channels	Resolution
	$quality=array (	'edge'	=>	'128k	64k	1		240x160',
				'3g'	=>	'350k	64k	1		408x272',
				'wifi'	=>	'512k	128k	2		480x320');
	$maxencodingprocesses=10;		// Max simultaneous encoding processes

	// Misc
	$ffmpegpath = '/usr/bin/ffmpeg';	//path to ffmpeg binary
	$segmenterpath = '/usr/bin/segmenter';	//path to segmenter binary

	// Version
	$isdversion = "0.3.7";
?>
