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
			include('includes/inc_stream.php');
			break;
		case ("listcategory"):
			include('includes/inc_cat.php');
			break;
		case ("listchannels"):
			include('includes/inc_chan.php');
			break;
		case ("recordings"):
			include('includes/inc_rec.php');
			break;
		case ("media"):
			include('includes/inc_media.php');
			break;
		case ("epg"):
			include('includes/inc_epg.php');
			break;
		case ("timers"):
			include('includes/inc_timers.php');
			break;
		case ("edittimer"):
			include('includes/inc_edittimer.php');
			break;
		case ("deletetimer"):
			$timer = $_REQUEST['timer'];
			delete_timer($timer);
			include('includes/inc_timers.php');
			break;
		case ("addtimer"):
			$active = $_REQUEST['timer_active'];
			$channame = $_REQUEST['timer_chan'];
			$date = $_REQUEST['timer_date'];
			$stime = $_REQUEST['timer_starttime'];
			$etime = $_REQUEST['timer_endtime'];
			$desc = $_REQUEST['timer_name'];
			$prevtimer = $_REQUEST['prevtimer'];
			set_timer($active, $channame, $date, $stime, $etime, $desc, $prevtimer);
			include('includes/inc_timers.php');
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
			$mediapath = $_REQUEST['mediapath'];
			$subdir = $_REQUEST['subdir'];
			start_stream($type, $name, $title, $desc, $qname, $qparams, $category, $url, $mediapath, $subdir);
			include('includes/inc_stream.php');
			break;
		case ("playdir"):
			include('includes/inc_mp3.php');
			break;
		default:
			$_SESSION['currentcat'] = NULL;
			include('includes/inc_home.php');
                        break;
	}
}

function start_stream($type, $name, $title, $desc, $qname, $qparams, $category, $url, $mediapath, $subdir)
{
	global $httppath, $ffmpegpath, $segmenterpath;

	switch ($type)
	{
		case 1:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh '" .$url ."' " .$qparams ." " .$httppath ." 2 " .$ffmpegpath ." " .$segmenterpath ." \" | at now";
			break;
		case 2:
			$cmd = "export SHELL=\"/bin/sh\";printf \"cat \\\"" .$url ."\\\"/0* | ./istream.sh - " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." \" | at now";
			break;
		case 3:
			$cmd = "export SHELL=\"/bin/sh\";printf \"./istream.sh '" .$url ."' " .$qparams ." " .$httppath ." 1260 " .$ffmpegpath ." " .$segmenterpath ." \" | at now";
                        break;
		default:
			$cmd = "";
	}
	
	$cmd = str_replace('%', '%%', $cmd);
	exec ($cmd);

	// Write streaminfo
	writeinfostream($type, $name, $title, $desc, $qname, $category, $url, $mediapath, $subdir);
	
	include('includes/inc_stream.php');
}

function delete_timer($timer)
{
	$ret = vdrdeltimer($timer);

	$message = " <li class=\"textbox\"><p><font color='black'>Timer deleted successfully</font></p></li>";

	include('includes/inc_timers.php');
}

function set_timer($active, $channame, $date, $stime, $etime, $desc, $prevtimer)
{
	$ret = vdrsettimer($prevtimer, $channame, $date, $stime, $etime, $desc, $active);

	if ($prevtimer == -1)
		$settype = "creat";
	else
		$settype = "edit";

	$retarray = explode(":", $ret);

	if (!is_numeric(substr($retarray[0], 0, 1)))
		$message = " <li class=\"textbox\"><p><font color='red'>{$retarray[0]}</font></p></li>";
	else
		$message = " <li class=\"textbox\"><p>Timer {$settype}ed successfully</p></li>";
}

?>
