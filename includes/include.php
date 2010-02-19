<?php

if (file_exists('config.php'))
	include ('config.php');
else
	include ('config_default.php');
include ('includes/inc_utils.php');
include ('includes/inc_auth.php');
include ('includes/inc_vdr.php');
include ('includes/inc_files.php');
include ('includes/inc_streaminfo.php');

function selectpage()
{
	$action = $_REQUEST['action'];
	
	if ($action == "stopstream")
	{
		$cmd= "killall segmenter && killall -9 ffmpeg ; rm ram/stream*";
		exec ($cmd);

		$action = $_REQUEST['actionafterstop'];
	}

	if (infostreamexist())
		$action = "stream";

	switch ($action)
	{
		case ("stream"):
			gen_stream();
			break;
		case ("listcategory"):
			gen_category();
			break;
		case ("listchannels"):
			gen_channels();
			break;
		case ("recordings"):
			gen_recordings();
			break;
		case ("media"):
			gen_media();
			break;
		case ("startstream"):
			$type = $_REQUEST['type'];
			$name = $_REQUEST['name'];
			$title = $_REQUEST['title'];
			$desc = stripslashes ($_REQUEST['desc']);
			$qname = $_REQUEST['qname'];
			$qparams = $_REQUEST['qparams'];
			$category = $_REQUEST['category'];
			$url = $_REQUEST['url'];
			start_stream($type, $name, $title, $desc, $qname, $qparams, $category, $url);
			break;
		 default:
                        gen_home();
                        break;
	}
}

function gen_home()
{
	$_SESSION['currentcat'] = NULL;
	include('includes/inc_home.php');
}

function gen_category()
{
	include('includes/inc_cat.php');
}

function gen_channels()
{
	include('includes/inc_chan.php');
}

function gen_stream()
{
	include('includes/inc_stream.php');
}


function gen_recordings()
{
	include('includes/inc_rec.php');
}

function gen_media()
{
	include('includes/inc_media.php');
}

function start_stream($type, $name, $title, $desc, $qname, $qparams, $category, $url)
{
	global $httppath, $ffmpegpath;

	switch ($type)
	{
		case 1:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh '" .$url ."' " .$qparams ." " .$httppath ." 2 " .$ffmpegpath ." \" | at now";
			break;
		case 2:
			$cmd = "export SHELL=\"/bin/sh\";printf \"cat \\\"" .$url ."\\\"/0* | ./istream.sh - " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." \" | at now";
			break;
		case 3:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh '" .$url ."' " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." \" | at now";
                        break;
		default:
			$cmd = "";
	}
	exec ($cmd);

	// Write streaminfo
	writeinfostream($type, $name, $title, $desc, $qname, $category, $url);
	
	include('includes/inc_stream.php');
}

?>
