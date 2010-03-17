<?php

header('Content-Type: application/json; charset: utf-8');

if (file_exists('../config.php'))
	include ('../config.php');
else
	include ('../config_default.php');
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
		$tree = file_get_contents("textfiles/getVidInfo.txt");
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
			$time = time();
			$session = $_REQUEST['session'];
			$prevmsg = $_REQUEST['msg'];
			while((time() - $time) < 29)
			{
				// Get current status
				$status = getStreamStatus($session);
	
				$statusdec = json_decode($status);
				if (($statusdec->message != $prevmsg) || ($statusdec->status == "ready"))
				{
					print $status;
					break;
				}

				usleep(1000);
			}
			break;
		
		case ("getTimers"):
		$tree = file_get_contents("textfiles/getTimers.txt");
                print $tree;
		break;
		
		case ("editTimer"):
		$id = $_REQUEST['id'];
		if (id) {
		$tree = file_get_contents("textfiles/editTimer.txt");
                }
		else {
		$tree = file_get_contents("textfiles/addTimer.txt");
		}
		print $tree;
		break;
		
		case ("deltimer"):
		$tree = file_get_contents("textfiles/delTimer.txt");
                print $tree;
		break;
		
		case ("browseFolder"):
			$path = $_REQUEST['path'];
			$tree = browseFolder($_REQUEST['path']);
			print $tree;
			break;
		
		case ("browseRec"):
		$tree = file_get_contents("textfiles/browseRec.txt");
                print $tree;
		break;
		
		case ("browseAudio"):
		$tree = file_get_contents("textfiles/browseAudio.txt");
                print $tree;
		break;
		
		case ("streamAudio"):
		$tree = file_get_contents("textfiles/streamAudio.txt");
                print $tree;
		break;
	}
?>
