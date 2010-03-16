<?php
header('Content-type: text/html');
$action=$_REQUEST['action'];
switch ($action)
        {
	        case ("getGlobals"):
		$tree = file_get_contents("textfiles/getGlobals.txt");
		print $tree;
		break;

		case ("getTvCat"):
		$tree = file_get_contents("textfiles/getTvCat.txt");
                print $tree;
		break;
		
		case ("getFullChanList"):
		$tree = file_get_contents("textfiles/getFullChanList.txt");
		print $tree;
		break;
		
		case ("getTvChan"):
		$tree = file_get_contents("textfiles/getTvChan.txt");
                print $tree;
		break;
		
		case ("getChanInfo"):
		$tree = file_get_contents("textfiles/getChanInfo.txt");
                print $tree;
		break;
		
		case ("getRecInfo"):
		$tree = file_get_contents("textfiles/getRecInfo.txt");
                print $tree;
		break;
		
		case ("getVidInfo"):
		$tree = file_get_contents("textfiles/getVidInfo.txt");
                print $tree;
		break;
		
		case ("getStreamInfo"):
		$session = $_REQUEST['session'];
		$tree = file_get_contents("textfiles/getStreamInfo-" . $session . ".txt");	
                print $tree;
		break;
		
		case ("startBroadcast"):
		$type = $_REQUEST['type'];
		$url = $_REQUEST['url'];
		$tree = file_get_contents("textfiles/startBroadcast-" . $type . ".txt");
                print $tree;
		break;
		
		case ("stopBroadcast"):
		$tree = file_get_contents("textfiles/stopBroadcast.txt");
                print $tree;
		break;
		
		case ("getStreamStatus"):
		$time = time();
		$session = $_REQUEST['session'];
		$prevmsg = $_REQUEST['msg'];
		while((time() - $time) < 29)
		{
			$tree = file_get_contents("textfiles/getStreamStatus.txt");
                	$data = json_decode($tree);
			$message = $data->message;
			$status = $data->status;
			if ($prevmsg != $message) {
				print $tree;
				break;
			}
			else if ($status == "ready")
			{
				print $tree;
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
		if ( $path == "/video/" ) {
		$tree = file_get_contents("textfiles/browseFolder-rec.txt");
                }
		else if ( $path == "/mnt/media/Videos/" ) {
		$tree = file_get_contents("textfiles/browseFolder-vid.txt");
		}
		else if ( $path == "/mnt/media/Music/" ) {
		$tree = file_get_contents("textfiles/browseFolder-aud.txt");
		}
		else
		{
		$tree = file_get_contents("textfiles/browseFolder-rec.txt");
		}
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
