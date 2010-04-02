<?php
	// Enable/disable features
	$enablevdr=1;			// VDR features
	$enablemediavideo=1;		// Video files streaming
	$enablemediaaudio=1;		// Audio files streaming

	// Debug mode
	$debug=0;			// Debug all action 
	$debugfile="/tmp/istreamdev.log"; // Debug file
	$ffmpegdebug=0;			// Debug ffmpeg
	$ffmpegdebugfile="/tmp/istreamdev-ffmpeg.log"; // FFmpeg debug file

	// Http configuration
	$user='istreamdev';		// Login
	$pass='iguest';			// Password
	$httppath='/istreamdev/';	// Absolute path to the index.php file. Don't put http://yourdomain !!

	// VDR configuration
	$vdrchannels='/etc/vdr/channels.conf';			// VDR channel list
	$svdrpport=2001;					// SVDRP port
	$svdrpip='127.0.0.1';					// SVDRP ip
	$vdrstreamdev='http://127.0.0.1:3000/TS/';		// VDR streamdev URL
	$vdrrecpath='/video/';					// VDR recording directory
	$vdrepgmaxdays=10;					// Number of days to get from EPG

	// Media configuration
	$videotypes='avi mkv ts mov mp4 wmv flv mpg mpeg mpeg2 mpv ';	// Supported video extensions (must finish with a space)
	$audiotypes='mp3 aac wav ';					// Supported audio extensions
	$videosource='/mnt/media/movies/';				// Video files directory
	$audiosource='/mnt/media/music/';				// Audio files directory

	// Encoding (The name cannot be changed)
	//			Name		Video	Audio	Resolution
	$quality=array	(	'edge'	=>	'128k	64k	240x160',
				'3g'	=>	'350k	64k	408x272',
				'wifi'	=>	'512k	128k	480x320');
	$maxencodingprocesses=3;		// Max simultaneous encoding processes

	// Misc
	$ffmpegpath='/usr/bin/ffmpeg';		//path to ffmpeg binary
	$segmenterpath='/usr/bin/segmenter';	//path to segmenter binary

	// Version
	$isdversion = "1.0.1";
?>
