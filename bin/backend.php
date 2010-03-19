<?php

header('Content-Type: application/json; charset: utf-8');

if (file_exists('../config.php'))
	include ('../config.php');
else
	include ('../config_default.php');
include ('../getid3/getid3.php');
include ('./utils.php');
include ('./files.php');
include ('./streaminfo.php');
include ('./vdr.php');
include ('./session.php');
include ('./jsonapi.php');

$action=$_REQUEST['action'];
switch ($action)
        {
	        case ("getGlobals"):
			$tree = getGlobals();
			print $tree;
			break;
		case ("getRunningSessions"):
			$tree = getRunningSessions();
			print $tree;
			break;
		case ("getTvCat"):
			$tree =  getTvCat();
        	        print $tree;
			break;
		
		case ("getFullChanList"):
			$tree = getFullChanList();
			print $tree;
			break;
		
		case ("getTvChan"):
			$tree = GetTvChan($_REQUEST['cat']);
        	        print $tree;
			break;
		
		case ("getChanInfo"):
			$tree = getChanInfo($_REQUEST['chan']);
        	        print $tree;
			break;
		
		case ("getRecInfo"):
			$tree = getRecInfo($_REQUEST['rec']);
	       	        print $tree;
			break;
		
		case ("getVidInfo"):
			$tree = getVidInfo($_REQUEST['file']);
        	        print $tree;
			break;
		
		case ("getStreamInfo"):
			$tree = getStreamInfo($_REQUEST['session']);
			print $tree;
			break;
		
		case ("startBroadcast"):
			$tree = startBroadcast($_REQUEST['type'], $_REQUEST['url'], $_REQUEST['mode']);
			print $tree;
			break;
		
		case ("stopBroadcast"):
			$tree = stopBroadcast($_REQUEST['session']);
			print $tree;
			break;
		
		case ("getStreamStatus"):
			$tree= getStreamStatus($_REQUEST['session'], $_REQUEST['msg']);
			print $tree;
			break;
		
		case ("getTimers"):
			$tree = getTimers();
                	print $tree;
			break;
		
		case ("editTimer"):
			$tree = editTimer($_REQUEST['id'], $_REQUEST['name'], $_REQUEST['active'], $_REQUEST['channumber'], $_REQUEST['date'], $_REQUEST['starttime'], $_REQUEST['endtime']);
			print $tree;
			break;
		
		case ("delTimer"):
			$tree = delTimer($_REQUEST['id']);
	                print $tree;
			break;
		
		case ("browseFolder"):
			$tree = browseFolder(stripslashes($_REQUEST['path']));
			print $tree;
			break;
		
		case ("streamAudio"):
			$tree = streamAudio($_REQUEST['path'], $_REQUEST['file']);
			print $tree;
			break;
	}

?>
