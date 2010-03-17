<?php

function getGlobals()
{
	global $vdrstreamdev, $vdrrecpath, $mediasource;

	$ret = array();
	$ret['streamdev_server'] = $vdrstreamdev;
	$ret['rec_path'] = $vdrrecpath;
	$ret['video_path'] = "/home/storage/Foot/";
	$ret['audio_path'] = "/home/www/mp3/";

	return json_encode($ret);
}

function getTvCat()
{
	$ret = array();
	$ret['categories'] = vdrgetcategories();
	
	return json_encode($ret);
}

function getFullChanList()
{
	$catlist = array();

	// Get all categories
	$categories = vdrgetcategories();

	// For all categories
	$count = count($categories);
	for ($i = 0; $i < $count; $i++)
	{
		$tmpcat = array();

		$tmpcat['name'] = $categories[$i]['name'];
		$tmpcat['channel'] = vdrgetchannels($tmpcat['name'], 0);

		$catlist[] = $tmpcat;
	}

	$ret = array();
	$ret['category'] = $catlist;

	return json_encode($ret);
}

function getTvChan($cat)
{
	$ret = array();
	$ret['channel'] = vdrgetchannels($cat, 1);

	return json_encode($ret);
}

function getChanInfo($channum)
{
	$ret = array();
	
	$ret['program'] = vdrgetchaninfo($channum);

	return json_encode($ret);
}

function getRecInfo($rec)
{
	$ret = array();

	$info = array();
	list($info['channel'], $info['name'], $info['desc'], $info['recorded']) = vdrgetrecinfo($rec);

	$ret['program'] = $info;

	return json_encode($ret);
}

function getVidInfo($file)
{
	$ret = array();

	// Generate logo
	generatelogo('vid', $file, '../ram/temp-logo.png');
	
	$ret['program'] = mediagetinfostream($file);

	return json_encode($ret);
}

function startBroadcast($type, $url, $mode)
{
	$ret = array();

	$ret['session'] = substr(sessioncreate($type, $url, $mode), strlen("session"));

	return json_encode($ret); 
}

function stopBroadcast($session)
{
	$ret = array();

	if ($session == "all")
		$ret = sessiondelete($session);	
	else
		$ret = sessiondelete("session" .$session);

        return json_encode($ret);
}

function getStreamInfo($session)
{
	$ret = array();

	$info = sessiongetinfo("session" .$session);
	$info['session'] = substr($info['session'], strlen("session"));
	$ret['stream'] = $info;

	return json_encode($ret);
}

function getStreamStatus($session)
{
	$ret = sessiongetstatus("session" .$session);

	return json_encode($ret);
}

function getRunningSessions()
{
	$ret = array();

	$ret['broadcast'] = sessiongetlist();

        return json_encode($ret);

}

function browseFolder($path)
{
	$ret = array();

	$ret['list'] = filesgetlisting($path);
	
	return json_encode($ret);
}
?>
