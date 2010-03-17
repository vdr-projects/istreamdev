<?php

function getGlobals()
{
	global $vdrstreamdev, $vdrrecpath, $mediasource;

	$ret = array();
	$ret['streamdev_server'] = $vdrstreamdev;
	$ret['rec_path'] = $vdrrecpath;
	$ret['video_path'] = "/mnt/media/Video/";
	$ret['audio_path'] = "/mnt/media/Music/";

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
	
	$info = array();
	$ret['program'] = vdrgetchaninfo($channum);

	return json_encode($ret);
}

function startBroadcast($type, $url, $mode)
{
	$ret = array();

	$ret['session'] = sessioncreate($type, $url, $mode);

	return json_encode($ret); 
}


?>
